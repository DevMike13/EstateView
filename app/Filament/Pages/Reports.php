<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Reports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?int $navigationSort = 8;
    protected static string $view = 'filament.pages.reports';

    public function getTitle(): string
    {
        return '';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role == 'admin';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }
}
