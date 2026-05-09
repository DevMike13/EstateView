<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class StaffManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.staff-management';
}
