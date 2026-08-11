<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AgentManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static string $view = 'filament.pages.agent-management';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role == 'admin';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }
}
