<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PaymentVerification extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.payment-verification';
}
