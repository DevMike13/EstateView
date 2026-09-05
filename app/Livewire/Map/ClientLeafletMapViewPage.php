<?php

namespace App\Livewire\Map;

use App\Models\Block;
use App\Models\Lot;
use App\Models\Map;
use App\Models\LotReservation;
use Livewire\Component;

class ClientLeafletMapViewPage extends Component
{
    public $map;

    public $lots;

    public $blocks;

    public $activeLotId = null;

    public $lastLotVersion = null;

    public $lotCounts = [];

    public $typeColors = [
        'Playground & Community Amenities' => '#f2b879',
        'Model House' => '#c8c9c3',
        'Lot Only' => '#c4e0b7',
        'House & Lot' => '#f8e89c',
        'Internal Road' => '#ffffff',
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
            'lots.reservations',
            'blocks',
        ])->first();


        $this->lots =
            $this->map?->lots
            ?? collect();


        $this->blocks =
            $this->map?->blocks
            ?? collect();


        $this->generateLotCounts();
    }


    public function generateLotCounts(): void
    {
        $this->lotCounts =
            collect(
                array_keys(
                    $this->typeColors
                )
            )
                ->mapWithKeys(
                    fn ($type) => [
                        $type => 0,
                    ]
                )
                ->toArray();


        $counts =
            collect(
                $this->lots
            )
                ->groupBy(
                    'type'
                )
                ->map(
                    fn ($lots) =>
                        $lots->count()
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
            'refresh-client-leaflet-map',
            lots: $this->getClientLeafletLots()
        );
    }


    private function getClientLeafletLots(): array
    {
        $currentUserId =
            auth()->id();


        return collect(
            $this->lots
        )
            ->map(
                function ($lot) use ($currentUserId) {
                    $belongsToCurrentUser =
                        $currentUserId
                        &&
                        $lot->user_id
                        &&
                        (int) $lot->user_id ===
                            (int) $currentUserId;


                    return [
                        'id' =>
                            $lot->id,

                        'name' =>
                            $lot->name,

                        'lot_number' =>
                            $lot->lot_number,

                        'geo_coords' =>
                            $lot->geo_coords,

                        'type' =>
                            $lot->type,

                        'block_id' =>
                            $lot->block_id,

                        'status' =>
                            $lot->status,

                        'has_ongoing_reservation' =>
                            $lot->reservations
                                ->whereIn(
                                    'status',
                                    [
                                        'awaiting_reservation_fee',
                                        'reservation_fee_submitted',
                                        'approved',
                                    ]
                                )
                                ->isNotEmpty(),

                        'price' =>
                            $lot->price,

                        'lot_area' =>
                            $lot->lot_area,

                        'image' =>
                            $lot->image
                                ? asset(
                                    'storage/' .
                                    $lot->image
                                )
                                : null,

                        'is_under_construction' =>
                            (bool) $lot->is_under_construction,

                        'user' =>
                            $belongsToCurrentUser
                            &&
                            $lot->user
                                ? [
                                    'id' =>
                                        $lot->user->id,

                                    'name' =>
                                        $lot->user->name,

                                    'picture' =>
                                        $lot->user->profile_picture
                                            ? asset(
                                                $lot->user->profile_picture
                                            )
                                            : null,
                                ]
                                : null,

                        'belongs_to_current_user' =>
                            (bool) $belongsToCurrentUser,

                        'house_model' =>
                            $lot->houseModel
                                ? [
                                    'id' =>
                                        $lot->houseModel->id,

                                    'name' =>
                                        $lot->houseModel->model_name,

                                    'image' =>
                                        $lot->houseModel->image
                                            ? asset(
                                                'storage/' .
                                                $lot->houseModel->image
                                            )
                                            : null,
                                ]
                                : null,
                    ];
                }
            )
            ->values()
            ->toArray();
    }


    private function getLotVersion(): string
    {
        $lotVersion =
            Lot::query()
                ->orderBy(
                    'id'
                )
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
                ->implode(
                    '|'
                );


        $reservationVersion =
            LotReservation::query()
                ->whereIn(
                    'status',
                    [
                        'awaiting_reservation_fee',
                        'reservation_fee_submitted',
                        'approved',
                    ]
                )
                ->orderBy(
                    'id'
                )
                ->get([
                    'id',
                    'lot_id',
                    'status',
                    'updated_at',
                ])
                ->map(
                    fn ($reservation) =>
                        $reservation->id
                        . '-'
                        . $reservation->lot_id
                        . '-'
                        . $reservation->status
                        . '-'
                        . optional(
                            $reservation->updated_at
                        )->timestamp
                )
                ->implode(
                    '|'
                );


        return
            $lotVersion
            . '::'
            . $reservationVersion;
    }


    public function setActiveLot(
        $id
    ): void
    {
        $this->activeLotId =
            $id;
    }


    public function render()
    {
        return view(
            'livewire.map.client-leaflet-map-view-page',
            [
                'typeColors' =>
                    $this->typeColors,

                'blocks' =>
                    $this->blocks,
            ]
        );
    }
}
