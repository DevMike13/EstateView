<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PaymentQRCodes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?int $navigationSort = 8;
    protected static string $view = 'filament.pages.payment-q-r-codes';

    protected static ?string $navigationLabel = 'QR Codes';

    public function getTitle(): string
    {
        return '';
    }
}
