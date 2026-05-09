<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Appointments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.appointments';
}
