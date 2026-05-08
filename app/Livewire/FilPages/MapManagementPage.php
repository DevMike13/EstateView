<?php

namespace App\Livewire\FilPages;

use App\Models\Lot;
use App\Models\Map;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;

class MapManagementPage extends Component
{
    use WithFilePond;
    
    public $map;
    public $lots;

    public $lotName;
    public $lotType;
    public $lotImage;

    public $lotCoordinates = '';
    public $points = [];

    public $typeColors = [
        'Playground & Community Amenities' => '#f2b879',
        'Model House' => '#c8c9c3',
        'Lot Only' => '#c4e0b7',
        'House & Lot' => '#f8e89c',
        'Sold' => '#e9b4ae',
    ];

    public function mount()
    {
        $this->map = Map::with('lots')->first();
        $this->lots = $this->map?->lots ?? [];
    }

    public function openLot($id)
    {
        $lot = Lot::find($id);

        if (!$lot) {
            return; // Safety check
        }
        
        Notification::make()
            ->title('Success!')
            ->body("You clicked on lot: {$lot->name}")
            ->success()
            ->send();
    }

    public function addPoint($xPercent, $yPercent)
    {
        if (!$this->map || !$this->map->image_path) return;

        [$naturalWidth, $naturalHeight] = getimagesize(public_path($this->map->image_path));

        $x = round($xPercent * $naturalWidth);
        $y = round($yPercent * $naturalHeight);

        $this->points[] = ['x' => $x, 'y' => $y];

        $flat = [];
        foreach ($this->points as $p) {
            $flat[] = $p['x'];
            $flat[] = $p['y'];
        }

        $this->lotCoordinates = implode(',', $flat);

        $this->dispatch('redraw-modal-points');
    }

    public function resetPoints()
    {
        $this->points = [];
        $this->lotCoordinates = '';
        $this->lotName = '';
        $this->lotType = null;
        $this->lotImage = null;

        $this->dispatch('redraw-modal-points');
    }

    public function createLotArea()
    {
        $imagePath = null;

        if ($this->lotImage) {

            $imagePath = $this->lotImage->storeAs(
                'modelImages',
                Str::uuid() . '.' . $this->lotImage->getClientOriginalExtension(),
                'public'
            );
        }

        Lot::create([
            'map_id' => $this->map->id,
            'name' => $this->lotName,
            'coords' => $this->lotCoordinates,
            'type' => $this->lotType,
            'image' => $imagePath,
        ]);

        $this->resetPoints();

        $this->map = Map::with('lots')->first();
        $this->lots = $this->map->lots;

        $this->dispatch('refreshMap');
        $this->dispatch('reload');
    }

    public function render()
    {
        return view('livewire.fil-pages.map-management-page', [
            'typeColors' => $this->typeColors,
        ]);
    }
}
