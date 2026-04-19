<?php

namespace App\Livewire\FilPages;

use App\Models\Lot;
use App\Models\Map;
use Filament\Notifications\Notification;
use Livewire\Component;

class MapManagementPage extends Component
{
    
    public $map;
    public $lots;

    public $lotName;
    public $lotCoordinates = '';
    public $points = [];

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

        $this->dispatch('redraw-modal-points');
    }

    public function createLotArea()
    {
        Lot::create([
            'map_id' => $this->map->id,
            'name' => $this->lotName,
            'coords' => $this->lotCoordinates,
        ]);

        $this->resetPoints();
        $this->dispatch('refreshMap');
        $this->dispatch('reload');
    }

    public function render()
    {
        return view('livewire.fil-pages.map-management-page');
    }
}
