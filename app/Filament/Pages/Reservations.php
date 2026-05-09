<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Reservations extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.reservations';
}
