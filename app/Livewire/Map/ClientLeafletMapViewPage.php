<?php

namespace App\Livewire\Map;

use App\Models\Lot;
use App\Models\Map;
use Livewire\Component;

class ClientLeafletMapViewPage extends Component
{
    public $map;
    public $lots;

    public $activeLotId = null;
    public $lastLotVersion = null;

    public $lotCounts = [];

    public $typeColors = [
        'Playground & Community Amenities' => '#f2b879',
        'Model House' => '#c8c9c3',
        'Lot Only' => '#c4e0b7',
        'House & Lot' => '#f8e89c',
        'Sold' => '#e9b4ae',
    ];

    public function mount()
    {
        $this->loadLots();

        $this->lastLotVersion =
            $this->getLotVersion();
    }

    private function loadLots(): void
    {
        $this->map = Map::with([
            'lots.user',
            'lots.houseModel',
        ])->first();

        $this->lots =
            $this->map?->lots ?? collect();

        $this->generateLotCounts();
    }

    public function generateLotCounts(): void
    {
        $this->lotCounts =
            collect(array_keys($this->typeColors))
                ->mapWithKeys(
                    fn ($type) => [$type => 0]
                )
                ->toArray();

        $counts =
            collect($this->lots)
                ->groupBy('type')
                ->map(
                    fn ($lots) => $lots->count()
                )
                ->toArray();

        $this->lotCounts =
            array_merge(
                $this->lotCounts,
                $counts
            );
    }

    public function refreshLots(): void
    {
        $newVersion =
            $this->getLotVersion();

        /*
        |--------------------------------------------------------------------------
        | Nothing changed
        |--------------------------------------------------------------------------
        |
        | Do not re-render the component unnecessarily because Leaflet owns
        | the map DOM inside wire:ignore.
        |
        */

        if (
            $newVersion ===
            $this->lastLotVersion
        ) {
            $this->skipRender();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Something changed
        |--------------------------------------------------------------------------
        */

        $this->lastLotVersion =
            $newVersion;

        $this->loadLots();

        /*
        |--------------------------------------------------------------------------
        | Tell JavaScript to rebuild the Leaflet polygons
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'refresh-client-leaflet-map'
        );
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
                    $lot->id
                    . '-'
                    . optional(
                        $lot->updated_at
                    )->timestamp
            )
            ->implode('|');
    }

    public function setActiveLot($id): void
    {
        $this->activeLotId = $id;
    }

    public function render()
    {
        return view(
            'livewire.map.client-leaflet-map-view-page',
            [
                'typeColors' =>
                    $this->typeColors,
            ]
        );
    }
}