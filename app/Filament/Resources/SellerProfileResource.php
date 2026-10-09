<?php

namespace App\Filament\Resources;

use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Filament\Resources\SellerProfileResource\Pages;
use App\Models\SellerProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SellerProfileResource extends Resource
{
    protected static ?string $model = SellerProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $navigationLabel = 'Toko & Profil Seller';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('store_name')
                        ->label('Nama Toko')
                        ->disabled(),
                    Forms\Components\TextInput::make('store_slug')
                        ->label('Slug Toko')
                        ->disabled(),
                    Forms\Components\Select::make('user_id')
                        ->label('Pemilik Akun')
                        ->relationship('user', 'name')
                        ->disabled(),
                    Forms\Components\TextInput::make('bank_name')
                        ->label('Bank')
                        ->disabled(),
                    Forms\Components\TextInput::make('verification_status')
                        ->label('Status Verifikasi')
                        ->disabled(),
                    Forms\Components\Textarea::make('verification_notes')
                        ->label('Catatan Verifikasi')
                        ->disabled()
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store_name')
                    ->label('Nama Toko')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pemilik')
                    ->searchable()
                    ->description(fn (SellerProfile $record) => $record->user->email ?? '-'),

                Tables\Columns\TextColumn::make('verification_status')
                    ->label('Status Verifikasi')
                    ->badge()
                    ->color(fn (VerificationStatus $state): string => match ($state) {
                        VerificationStatus::APPROVED => 'success',
                        VerificationStatus::PENDING => 'warning',
                        VerificationStatus::REJECTED => 'danger',
                    }),

                Tables\Columns\TextColumn::make('user.status')
                    ->label('Status Akun')
                    ->badge()
                    ->color(fn (UserStatus $state): string => match ($state) {
                        UserStatus::ACTIVE => 'success',
                        UserStatus::BLOCKED => 'danger',
                    }),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Total Produk')
                    ->counts('products')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('verification_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // Action Approve Seller
                Action::make('approveSeller')
                    ->label('Setujui Toko')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SellerProfile $record): bool => $record->verification_status !== VerificationStatus::APPROVED)
                    ->action(function (SellerProfile $record) {
                        $record->update([
                            'verification_status' => VerificationStatus::APPROVED,
                            'verification_notes' => 'Toko disetujui oleh admin ' . auth()->user()->name . ' pada ' . now()->toDateTimeString(),
                        ]);

                        Notification::make()
                            ->title('Seller Berhasil Disetujui')
                            ->success()
                            ->send();
                    }),

                // Action Reject Seller
                Action::make('rejectSeller')
                    ->label('Tolak Verifikasi')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (SellerProfile $record): bool => $record->verification_status !== VerificationStatus::REJECTED)
                    ->form([
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Alasan Penolakan')
                            ->helperText('Tuliskan alasan penolakan verifikasi toko ini.')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (SellerProfile $record, array $data) {
                        $record->update([
                            'verification_status' => VerificationStatus::REJECTED,
                            'verification_notes' => $data['verification_notes'],
                        ]);

                        Notification::make()
                            ->title('Verifikasi Seller Ditolak')
                            ->danger()
                            ->send();
                    }),

                // Action Blokir User Seller
                Action::make('blockSeller')
                    ->label('Blokir Akun')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Memblokir akun seller ini akan otomatis menyembunyikan semua produknya dari etalase katalog publik.')
                    ->visible(fn (SellerProfile $record): bool => $record->user->status === UserStatus::ACTIVE)
                    ->action(function (SellerProfile $record) {
                        $record->user->update(['status' => UserStatus::BLOCKED]);

                        Notification::make()
                            ->title('Akun Seller Berhasil Diblokir')
                            ->success()
                            ->send();
                    }),

                // Action Buka Blokir Akun Seller
                Action::make('unblockSeller')
                    ->label('Buka Blokir')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SellerProfile $record): bool => $record->user->status === UserStatus::BLOCKED)
                    ->action(function (SellerProfile $record) {
                        $record->user->update(['status' => UserStatus::ACTIVE]);

                        Notification::make()
                            ->title('Blokir Seller Dibuka')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSellerProfiles::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
