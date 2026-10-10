<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $navigationLabel = 'Pengguna & Buyer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('email')
                        ->label('Alamat Email')
                        ->email()
                        ->required()
                        ->disabled() // Email tidak boleh diubah sembarangan
                        ->maxLength(255),

                    Forms\Components\TextInput::make('phone')
                        ->label('Nomor WhatsApp / HP')
                        ->tel()
                        ->maxLength(25),

                    // Role TIDAK BISA DIUBAH DARI SINI sesuai aturan
                    Forms\Components\TextInput::make('role')
                        ->label('Role Pengguna')
                        ->disabled()
                        ->helperText('Role dikelola oleh sistem dan tidak dapat diubah dari resource ini.'),

                    Forms\Components\TextInput::make('status')
                        ->label('Status Akun')
                        ->disabled(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (UserRole $state): string => match ($state) {
                        UserRole::SUPERADMIN => 'danger',
                        UserRole::SELLER => 'warning',
                        UserRole::BUYER => 'info',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (UserStatus $state): string => match ($state) {
                        UserStatus::ACTIVE => 'success',
                        UserStatus::BLOCKED => 'danger',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'superadmin' => 'Superadmin',
                        'seller' => 'Seller',
                        'buyer' => 'Buyer',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'blocked' => 'Diblokir',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Action Blokir User
                Action::make('blockUser')
                    ->label('Blokir')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pemblokiran Pengguna')
                    ->modalDescription('Apakah Anda yakin ingin memblokir akun ini? Jika user adalah seller, seluruh produknya akan otomatis hilang dari etalase katalog publik.')
                    ->visible(function (User $record): bool {
                        // Tidak bisa memblokir diri sendiri atau superadmin lain
                        if ($record->id === auth()->id() || $record->hasRole('superadmin')) {
                            return false;
                        }
                        return $record->status === UserStatus::ACTIVE;
                    })
                    ->action(function (User $record) {
                        // Cegah mutlak jika superadmin
                        if ($record->id === auth()->id() || $record->hasRole('superadmin')) {
                            Notification::make()
                                ->title('Aksi Ditolak')
                                ->body('Superadmin tidak dapat memblokir dirinya sendiri atau sesama superadmin.')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Model save() to trigger UserObserver
                        $record->status = UserStatus::BLOCKED;
                        $record->save();

                        \App\Models\ModerationLog::log('block_user', $record, 'Blokir akun dari UserResource');

                        Notification::make()
                            ->title('Pengguna Berhasil Diblokir')
                            ->success()
                            ->send();
                    }),

                // Action Buka Blokir
                Action::make('unblockUser')
                    ->label('Buka Blokir')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => $record->status === UserStatus::BLOCKED)
                    ->action(function (User $record) {
                        $record->status = UserStatus::ACTIVE;
                        $record->save();

                        \App\Models\ModerationLog::log('unblock_user', $record, 'Buka blokir akun dari UserResource');

                        Notification::make()
                            ->title('Status Pengguna Diaktifkan Kembali')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulkBlock')
                    ->label('Blokir Terpilih')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $blockedCount = 0;
                        foreach ($records as $user) {
                            // Lewati diri sendiri atau superadmin lain
                            if ($user->id === auth()->id() || $user->hasRole('superadmin')) {
                                continue;
                            }

                            // Pastikan lewat instance model agar UserObserver men-trigger revokeTokens
                            $user->status = UserStatus::BLOCKED;
                            $user->save();

                            \App\Models\ModerationLog::log('block_user', $user, 'Blokir akun via Bulk Action');
                            $blockedCount++;
                        }

                        Notification::make()
                            ->title("{$blockedCount} pengguna berhasil diblokir")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // User registrasi secara mandiri via web/API
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // User tidak boleh dihapus permanen, hanya diblokir
    }

    public static function canDeleteAny(): bool
    {
        return false; // Nonaktifkan bulk delete
    }
}
