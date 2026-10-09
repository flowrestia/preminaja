<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="flex justify-start pt-4">
            <x-filament::button type="submit">
                Simpan Perubahan Pengaturan
            </x-filament::button>
        </div>
    </x-filament-panels::form>

    <div class="mt-8">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
