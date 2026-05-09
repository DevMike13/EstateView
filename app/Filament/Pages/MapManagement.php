<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class MapManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static string $view = 'filament.pages.map-management';
    
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Property Management';

    protected ?string $heading = 'Property Management';

    protected ?string $subheading = 'Manage house models, subdivision lots, and client properties';

    protected static ?string $slug = 'property-management';
}
