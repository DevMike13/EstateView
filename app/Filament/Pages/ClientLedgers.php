<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ClientLedgers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.client-ledgers';
    public function getTitle(): string
    {
        return '';
    }
}
