<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatus;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Katalog & Moderasi';

    protected static ?string $navigationLabel = 'Moderasi Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Produk')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk')
                            ->disabled(),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->disabled(),
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->disabled(),
                        Forms\Components\TextInput::make('delivery_type')
                            ->label('Tipe Pengiriman')
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('status')
                            ->label('Status Saat Ini')
                            ->disabled(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan (Jika ada)')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Varian & Riwayat Perubahan Harga')
                    ->schema([
                        Forms\Components\Repeater::make('variants')
                            ->relationship('variants')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Varian')
                                    ->disabled(),
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Aktif (Rp)')
                                    ->disabled(),
                                Forms\Components\TextInput::make('stock')
                                    ->label('Jumlah Stok')
                                    ->disabled(),
                            ])
                            ->columns(3)
                            ->disabled()
                            ->deletable(false)
                            ->addable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Product $record): string => $record->seller->store_name ?? 'Seller'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ProductStatus $state): string => match ($state) {
                        ProductStatus::PUBLISHED => 'success',
                        ProductStatus::PENDING_REVIEW => 'warning',
                        ProductStatus::REJECTED => 'danger',
                        ProductStatus::DRAFT => 'gray',
                        ProductStatus::ARCHIVED => 'zinc',
                        ProductStatus::INACTIVE => 'gray',
                    }),
                Tables\Columns\TextColumn::make('delivery_type')
                    ->label('Pengiriman')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending_review' => 'Pending Review',
                        'published' => 'Published',
                        'rejected' => 'Rejected',
                        'draft' => 'Draft',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                // Action Approve
                Action::make('approve')
                    ->label('Setujui (Publish)')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Product $record) => $record->status !== ProductStatus::PUBLISHED)
                    ->action(function (Product $record) {
                        $record->update([
                            'status' => ProductStatus::PUBLISHED,
                            'rejection_reason' => null,
                        ]);

                        \App\Models\ModerationLog::log('approve_product', $record);

                        Notification::make()
                            ->title('Produk Disetujui & Diterbitkan')
                            ->success()
                            ->send();
                    }),

                // Action Reject with mandatory reason modal
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Product $record) => $record->status !== ProductStatus::REJECTED)
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->helperText('Wajib memberikan alasan yang jelas kepada seller mengapa produk ini ditolak.')
                            ->required()
                            ->minLength(5)
                            ->rows(3),
                    ])
                    ->action(function (Product $record, array $data) {
                        $record->update([
                            'status' => ProductStatus::REJECTED,
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        \App\Models\ModerationLog::log('reject_product', $record, $data['rejection_reason']);

                        Notification::make()
                            ->title('Produk Ditolak')
                            ->danger()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Produk hanya dibuat oleh seller via Seller Center
    }
}
