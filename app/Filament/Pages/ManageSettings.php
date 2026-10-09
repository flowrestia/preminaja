<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Models\SettingHistory;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageSettings extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan Platform';

    protected static ?string $title = 'Pengaturan Platform & Biaya';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'platform_fee_percentage' => Setting::get('platform_fee_percentage', 5.0),
            'escrow_holding_days' => Setting::get('escrow_holding_days', 3),
            'min_withdrawal' => Setting::get('min_withdrawal', 50000.0),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('platform_fee_percentage')
                        ->label('Komisi Platform (%)')
                        ->helperText('Rentang 0% - 50%. Komisi baru hanya berlaku untuk pesanan baru.')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(50)
                        ->required(),

                    TextInput::make('escrow_holding_days')
                        ->label('Hari Penahanan Escrow')
                        ->helperText('Rentang 0 - 30 hari. Jumlah hari dana ditahan sebelum otomatis dicairkan ke saldo seller.')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(30)
                        ->required(),

                    TextInput::make('min_withdrawal')
                        ->label('Minimal Penarikan Dana (Rp)')
                        ->helperText('Nominal minimum harus lebih dari 0.')
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::set('platform_fee_percentage', $state['platform_fee_percentage'], auth()->id());
        Setting::set('escrow_holding_days', $state['escrow_holding_days'], auth()->id());
        Setting::set('min_withdrawal', $state['min_withdrawal'], auth()->id());

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SettingHistory::query()->latest())
            ->heading('Riwayat Audit Perubahan Pengaturan')
            ->columns([
                TextColumn::make('key')
                    ->label('Kunci Pengaturan')
                    ->badge(),
                TextColumn::make('user.name')
                    ->label('Diubah Oleh')
                    ->default('Sistem / Superadmin'),
                TextColumn::make('old_value')
                    ->label('Nilai Lama'),
                TextColumn::make('new_value')
                    ->label('Nilai Baru')
                    ->weight('bold'),
                TextColumn::make('created_at')
                    ->label('Waktu Perubahan')
                    ->dateTime('d M Y H:i:s'),
            ])
            ->paginated([5, 10, 25]);
    }
}
