<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class StaffManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.staff-management';

    protected ?string $subheading = 'Manage staff accounts and permissions';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role == 'admin';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }
}
