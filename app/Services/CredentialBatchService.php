<?php

namespace App\Services;

use App\Models\AutomatedCredential;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CredentialBatchService
{
    /**
     * Parse plaintext rows into array of entries (one row per line).
     *
     * @return array<int, string>
     */
    public function parseLines(string $rawText): array
    {
        $lines = preg_split("/\r\n|\n|\r/", $rawText);
        $result = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                $result[] = $trimmed;
            }
        }

        return $result;
    }

    /**
     * Process bulk credentials from text input.
     *
     * @return array{inserted: int, duplicates: array<int>, errors: array<string>}
     */
    public function importFromText(ProductVariant $variant, string $rawText, ?string $instructions = null): array
    {
        $lines = $this->parseLines($rawText);

        if (count($lines) > 1000) {
            throw new InvalidArgumentException('Maksimal baris kredensial yang dapat diinput sekaligus adalah 1.000 baris.');
        }

        return $this->storeBatch($variant, $lines, $instructions);
    }

    /**
     * Process bulk credentials from streaming CSV.
     * Validasi: Maksimal 1.000 baris, maksimal 1 MB, streaming read via fopen.
     *
     * @return array{inserted: int, duplicates: array<int>, errors: array<string>}
     */
    public function importFromCsv(ProductVariant $variant, UploadedFile $file, ?string $instructions = null): array
    {
        // 1. Validasi ukuran (maks 1 MB = 1048576 bytes)
        if ($file->getSize() > 1048576) {
            throw new InvalidArgumentException('Ukuran file CSV tidak boleh lebih dari 1 MB.');
        }

        // 2. Validasi MIME / extension
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = $file->getMimeType();
        $validMimes = ['text/csv', 'text/plain', 'application/vnd.ms-excel', 'text/x-csv'];

        if ($extension !== 'csv' && !in_array($mime, $validMimes, true)) {
            throw new InvalidArgumentException('Format file harus berupa CSV.');
        }

        // 3. Baca secara streaming
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        if (! $handle) {
            throw new InvalidArgumentException('Gagal membaca file CSV.');
        }

        $lines = [];
        $rowCount = 0;

        while (($row = fgetcsv($handle, 4096, ',')) !== false) {
            $rowCount++;
            if ($rowCount > 1000) {
                fclose($handle);
                throw new InvalidArgumentException('File CSV melebihi batas maksimal 1.000 baris.');
            }

            // Kolom pertama dianggap kredensial (atau gabungan baris)
            $credential = trim($row[0] ?? '');
            if ($credential !== '') {
                $lines[] = $credential;
            }
        }

        fclose($handle);

        return $this->storeBatch($variant, $lines, $instructions);
    }

    /**
     * Simpan kredensial batch secara aman ke database.
     * Menggunakan HMAC-SHA256 hash untuk deteksi duplikasi tanpa pernah mengekspos data kredensial.
     *
     * @param array<int, string> $lines
     * @return array{inserted: int, duplicates: array<int>, errors: array<string>}
     */
    protected function storeBatch(ProductVariant $variant, array $lines, ?string $instructions = null): array
    {
        $insertedCount = 0;
        $duplicateLines = [];
        $batchHashes = [];

        // Ambil hash yang sudah ada di varian ini
        $existingHashes = AutomatedCredential::where('product_variant_id', $variant->id)
            ->pluck('credential_hash')
            ->flip()
            ->all();

        DB::transaction(function () use (
            $variant,
            $lines,
            $instructions,
            $existingHashes,
            &$insertedCount,
            &$duplicateLines,
            &$batchHashes
        ) {
            foreach ($lines as $index => $plainCredential) {
                $lineNumber = $index + 1;
                $hash = AutomatedCredential::generateHash($plainCredential);

                // Cek duplikasi di DB atau di batch yang sama
                if (isset($existingHashes[$hash]) || isset($batchHashes[$hash])) {
                    $duplicateLines[] = $lineNumber;
                    continue;
                }

                $batchHashes[$hash] = true;

                AutomatedCredential::create([
                    'product_variant_id' => $variant->id,
                    'credential_data' => $plainCredential, // Disimpan terenkripsi oleh model cast
                    'credential_hash' => $hash,
                    'instructions' => $instructions,
                    'status' => 'available',
                ]);

                $insertedCount++;
            }

            // Update stok otomatis pada varian
            $currentAvailable = $variant->availableCredentialsCount();
            $variant->update(['stock' => $currentAvailable]);
        });

        return [
            'inserted' => $insertedCount,
            'duplicates' => $duplicateLines,
            'errors' => empty($duplicateLines)
                ? []
                : array_map(fn ($line) => "Baris {$line} duplikat dan dilewati", $duplicateLines),
        ];
    }
}
