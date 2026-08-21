<?php

namespace App\Livewire\Map;

use App\Models\Lot;
use App\Models\Map;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;

class ClientMapViewPage extends Component
{
    use WithFilePond, Actions, WithFileUploads;

    // LOT MANAGEMENT
    public $map;
    public $lots;
    public $lotName;
    public $lotType;
    public $lotImage;
    public $lotPrice;
    public $lotArea;
    public $userId;
    public $houseModelId;
    public $lotStatus = 'available';
    public $lotCoordinates = '';
    public $points = [];
    public $lotCounts = [];
    // LOT MANEGEMENT END

    // EDIT
    public $activeLotId;

    public $lastLotVersion = null;

    public $typeColors = [
        'Playground & Community Amenities' => '#f2b879',
        'Model House' => '#c8c9c3',
        'Lot Only' => '#c4e0b7',
        'House & Lot' => '#f8e89c',
        'Sold' => '#e9b4ae',
    ];

    public function mount()
    {
        $this->map = Map::with([
            'lots.user',
            'lots.houseModel',
        ])->first();

        $this->lots = $this->map?->lots ?? [];

        $this->generateLotCounts();

        $this->lastLotVersion = $this->getLotVersion();
    }

    public function generateLotCounts()
    {
        $this->lotCounts = collect(array_keys($this->typeColors))
            ->mapWithKeys(fn ($type) => [$type => 0])
            ->toArray();

        $counts = collect($this->lots)
            ->groupBy('type')
            ->map(fn ($lots) => $lots->count())
            ->toArray();

        $this->lotCounts = array_merge($this->lotCounts, $counts);
    }

    public function refreshLots(): void
    {
        $newVersion = $this->getLotVersion();

        /*
         * Nothing changed.
         * Do not let Livewire render the component,
         * because replacing the canvas causes the overlay to disappear.
         */
        if ($newVersion === $this->lastLotVersion) {
            $this->skipRender();

            return;
        }

        /*
         * Something changed.
         */
        $this->lastLotVersion = $newVersion;

        $this->map = Map::with([
            'lots.user',
            'lots.houseModel',
        ])->first();

        $this->lots = $this->map?->lots ?? [];

        $this->generateLotCounts();

        /*
         * After Livewire morphs the new <area> elements,
         * redraw the responsive map overlay.
         */
        $this->dispatch('refresh-client-map');
    }

    private function getLotVersion(): string
    {
        return Lot::query()
            ->orderBy('id')
            ->get([
                'id',
                'updated_at',
            ])
            ->map(
                fn ($lot) =>
                    $lot->id . '-'
                    . optional($lot->updated_at)->timestamp
            )
            ->implode('|');
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

    public function setActiveLot($id)
    {
        $this->activeLotId = $id;
    }

    /* =========================
        HELPERS
    ==========================*/

    private function reloadMap()
    {
        $this->map = Map::with('lots')->first();
        $this->lots = $this->map->lots;
    }

    public function reloadWeb()
    {
        $this->dispatch('reload');

        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.map.client-map-view-page', [
            'typeColors' => $this->typeColors,
        ]);
    }
}