<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ClientRecords extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.client-records';
}
