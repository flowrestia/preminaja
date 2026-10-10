<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModerationLogResource\Pages;
use App\Models\ModerationLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ModerationLogResource extends Resource
{
    protected static ?string $model = ModerationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $navigationLabel = 'Log Moderasi';

    public static function form(Form $form): Form
    {
        return $form->schema([]); // Halaman hanya untuk read-only
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('actor.name')
                    ->label('Aktor (Admin)')
                    ->searchable()
                    ->default('Sistem'),

                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approve_product', 'approve_seller', 'unblock_user' => 'success',
                        'reject_product', 'reject_seller', 'block_user' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('target_type')
                    ->label('Tipe Objek')
                    ->formatStateUsing(fn (string $state) => class_basename($state)),

                Tables\Columns\TextColumn::make('target_id')
                    ->label('ID Target'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan / Catatan')
                    ->wrap()
                    ->default('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'approve_product' => 'Approve Produk',
                        'reject_product' => 'Reject Produk',
                        'approve_seller' => 'Approve Seller',
                        'reject_seller' => 'Reject Seller',
                        'block_user' => 'Blokir User',
                        'unblock_user' => 'Buka Blokir User',
                        'update_setting' => 'Ubah Pengaturan',
                    ]),
            ])
            ->actions([])
            ->bulkActions([]); // Tidak ada aksi bulk edit atau bulk delete
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModerationLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Read-only
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Tidak boleh dihapus
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
