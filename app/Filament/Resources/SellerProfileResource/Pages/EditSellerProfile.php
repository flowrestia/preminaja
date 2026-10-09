<?php

namespace App\Filament\Resources\SellerProfileResource\Pages;

use App\Filament\Resources\SellerProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSellerProfile extends EditRecord
{
    protected static string $resource = SellerProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
