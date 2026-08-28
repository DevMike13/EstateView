<?php

namespace App\Livewire\FilPages\Map;

use App\Models\Lot;
use App\Models\Map;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;

class LeafletMapView extends Component
{
    use WithFilePond, Actions, WithFileUploads;

    /*
    |--------------------------------------------------------------------------
    | MAP
    |--------------------------------------------------------------------------
    */

    public $map;
    public $lots;

    /*
    |--------------------------------------------------------------------------
    | CREATE LOT
    |--------------------------------------------------------------------------
    */

    public $lotName;
    public $lotType;
    public $lotImage;
    public $lotPrice;
    public $lotArea;

    public $userId;
    public $houseModelId;

    public bool $isUnderConstruction = false;

    public $lotStatus = 'available';

    /*
    | Leaflet coordinates:
    |
    | [
    |     [13.9197000, 121.4213070],
    |     [13.9197100, 121.4214000],
    |     [13.9196500, 121.4214100]
    | ]
    */
    public array $newGeoCoords = [];

    /*
    |--------------------------------------------------------------------------
    | EDIT LOT
    |--------------------------------------------------------------------------
    */

    public ?int $editLotId = null;

    public $editLotName;
    public $editLotType;

    public $editLotImagePreview;
    public $editLotImage;

    public $editLotPrice;
    public $editLotArea;

    public $editUserId;
    public $editHouseModelId;

    public $editLotStatus;

    public bool $editIsUnderConstruction = false;

    public array $editGeoCoords = [];

    public bool $isEditing = false;

    /*
    |--------------------------------------------------------------------------
    | COLORS
    |--------------------------------------------------------------------------
    */

    public $typeColors = [
        'Playground & Community Amenities' => '#f2b879',
        'Model House' => '#c8c9c3',
        'Lot Only' => '#c4e0b7',
        'House & Lot' => '#f8e89c',
        'Sold' => '#e9b4ae',
    ];

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->loadMap();
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD MAP
    |--------------------------------------------------------------------------
    */

    public function loadMap(): void
    {
        $this->map = Map::query()
            ->with([
                'lots.user',
                'lots.houseModel',
            ])
            ->first();

        $this->lots =
            $this->map?->lots ?? collect();
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE LOT
    |--------------------------------------------------------------------------
    */

    public function createLotArea(): void
    {
         /*
        |--------------------------------------------------------------------------
        | Make sure lot boundary was drawn
        |--------------------------------------------------------------------------
        */

        if (
            !is_array($this->newGeoCoords) ||
            count($this->newGeoCoords) < 3
        ) {
            Notification::make()
                ->title('Lot Required')
                ->body(
                    'Please draw the lot on the map before saving.'
                )
                ->warning()
                ->send();

            return;
        }
        /*
        |--------------------------------------------------------------------------
        | Clean currency
        |--------------------------------------------------------------------------
        */

        if (
            $this->lotPrice !== null &&
            $this->lotPrice !== ''
        ) {
            $this->lotPrice = str_replace(
                ',',
                '',
                $this->lotPrice
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $this->validate([
            'lotName' => [
                'required',
                'string',
                'max:255',
            ],

            'lotType' => [
                'required',
                'string',
            ],

            'newGeoCoords' => [
                'required',
                'array',
                'min:3',
            ],

            'newGeoCoords.*' => [
                'required',
                'array',
                'size:2',
            ],

            'newGeoCoords.*.0' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'newGeoCoords.*.1' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'lotStatus' => [
                'required',
                'in:available,sold,reserved',
            ],

            'lotPrice' => [
                'exclude_if:lotType,Model House',
                'exclude_if:lotType,Playground & Community Amenities',
                'required',
                'numeric',
                'gt:0',
                'max:99999999.99',
            ],

            'lotArea' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999.99',
            ],

            'userId' => [
                'nullable',
                'exists:users,id',
            ],

            'houseModelId' => [
                'nullable',
                'required_if:lotType,Model House',
                'exists:house_models,id',
            ],

            'isUnderConstruction' => [
                'nullable',
                'boolean',
            ],

            'lotImage' => [
                'nullable',
                'image',
                'max:20480',
            ],
        ], [
            'newGeoCoords.required' =>
                'Please draw the lot boundary on the map.',

            'newGeoCoords.min' =>
                'The lot boundary requires at least three points.',

            'lotPrice.required' =>
                'Please enter the lot price.',

            'lotPrice.numeric' =>
                'The lot price must be a valid number.',

            'lotPrice.gt' =>
                'The lot price must be greater than ₱0.',

            'lotPrice.max' =>
                'The maximum price is ₱99,999,999.99.',

            'lotArea.required' =>
                'Please enter the lot area.',

            'lotArea.numeric' =>
                'The lot area must be a valid number.',

            'lotArea.gt' =>
                'The lot area must be greater than 0 sqm.',

            'lotArea.max' =>
                'The maximum lot area is 99,999,999.99 sqm.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Map must exist
        |--------------------------------------------------------------------------
        */

        if (!$this->map) {
            Notification::make()
                ->title('Subdivision Map Not Found')
                ->body(
                    'No subdivision map record is available.'
                )
                ->danger()
                ->send();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Server-side boundary validation
        |--------------------------------------------------------------------------
        |
        | Never rely only on JavaScript for this.
        |
        */

        if (
            !$this->polygonInsideSubdivisionBoundary(
                $this->newGeoCoords
            )
        ) {
            Notification::make()
                ->title('Outside Subdivision Boundary')
                ->body(
                    'The lot must be completely inside the Manhattan Residences boundary.'
                )
                ->danger()
                ->send();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Server-side overlap validation
        |--------------------------------------------------------------------------
        */

        foreach ($this->lots as $lot) {

            if (
                !is_array($lot->geo_coords) ||
                count($lot->geo_coords) < 3
            ) {
                continue;
            }

            if (
                $this->geoPolygonsOverlap(
                    $this->newGeoCoords,
                    $lot->geo_coords
                )
            ) {
                Notification::make()
                    ->title('Lot Area Already Mapped')
                    ->body(
                        "This area overlaps with \"{$lot->name}\"."
                    )
                    ->danger()
                    ->send();

                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Upload image
        |--------------------------------------------------------------------------
        */

        $imagePath = null;

        if ($this->lotImage) {
            $imagePath =
                $this->lotImage->storeAs(
                    'modelImages',
                    Str::uuid() .
                        '.' .
                        $this->lotImage
                            ->getClientOriginalExtension(),
                    'public'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Property-specific fields
        |--------------------------------------------------------------------------
        */

        $hideCommercialFields = in_array(
            $this->lotType,
            [
                'Model House',
                'Playground & Community Amenities',
            ]
        );

        $supportsHouseModel = in_array(
            $this->lotType,
            [
                'Model House',
                // 'House & Lot',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Create actual Lot
        |--------------------------------------------------------------------------
        */

        $lot = Lot::create([
            'map_id' =>
                $this->map->id,

            'name' =>
                $this->lotName,

            /*
             * This lot was created using GIS, therefore it does not
             * have legacy image/pixel coordinates.
             */
            'coords' =>
                null,

            'geo_coords' =>
                $this->newGeoCoords,

            'type' =>
                $this->lotType,

            'image' =>
                $imagePath,

            'status' =>
                $hideCommercialFields
                    ? 'available'
                    : $this->lotStatus,

            'price' =>
                $hideCommercialFields
                    ? null
                    : $this->lotPrice,

            'lot_area' =>
                $this->lotArea,

            'user_id' =>
                (
                    !$hideCommercialFields &&
                    $this->lotStatus !== 'available'
                )
                    ? $this->userId
                    : null,

            'house_model_id' =>
                $supportsHouseModel
                    ? $this->houseModelId
                    : null,

            'is_under_construction' =>
                $this->isUnderConstruction,
        ]);

        $this->resetCreate();

        session()->flash(
            'gis_success',
            "\"{$lot->name}\" was successfully added to the map."
        );

        $this->dispatch('gis-create-success');
    }

    public function startEditGeoMapping(
        int $lotId
    ): void {
        $lot = Lot::query()
            ->with([
                'user',
                'houseModel',
            ])
            ->findOrFail($lotId);

        if (
            $this->map &&
            $lot->map_id != $this->map->id
        ) {
            Notification::make()
                ->title('Invalid Lot')
                ->body(
                    'This lot does not belong to the current subdivision.'
                )
                ->danger()
                ->send();

            return;
        }

        if (
            !is_array($lot->geo_coords) ||
            count($lot->geo_coords) < 3
        ) {
            Notification::make()
                ->title('No GIS Mapping')
                ->body(
                    'This lot does not have a GIS polygon.'
                )
                ->warning()
                ->send();

            return;
        }

        $this->editLotId =
            $lot->id;

        $this->editLotName =
            $lot->name;

        $this->editLotType =
            $lot->type;

        $this->editLotPrice =
            $lot->price;

        $this->editLotArea =
            $lot->lot_area;

        $this->editLotStatus =
            $lot->status;

        $this->editUserId =
            $lot->user_id;

        $this->editHouseModelId =
            $lot->house_model_id;

        $this->editIsUnderConstruction =
            (bool) $lot->is_under_construction;

        $this->editLotImage =
            null;

        $this->editLotImagePreview =
            $lot->image;

        $this->editGeoCoords =
            $lot->geo_coords;

        $this->isEditing =
            true;

        $this->dispatch(
            'gis-edit-lot',
            lotId: $lot->id,
            coords: $lot->geo_coords
        );
    }

    public function updateLot(): void
    {

        if (
            $this->editLotPrice !== null &&
            $this->editLotPrice !== ''
        ) {
            $this->editLotPrice = str_replace(
                ',',
                '',
                $this->editLotPrice
            );
        }

        $this->validate([
            'editLotId' => [
                'required',
                'exists:lots,id',
            ],

            'editLotName' => [
                'required',
                'string',
                'max:255',
            ],

            'editLotType' => [
                'required',
                'string',
            ],

            'editGeoCoords' => [
                'required',
                'array',
                'min:3',
            ],

            'editGeoCoords.*' => [
                'required',
                'array',
                'size:2',
            ],

            'editGeoCoords.*.0' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'editGeoCoords.*.1' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'editLotStatus' => [
                'required',
                'in:available,sold,reserved',
            ],

            'editLotPrice' => [
                'exclude_if:editLotType,Model House',
                'exclude_if:editLotType,Playground & Community Amenities',
                'required',
                'numeric',
                'gt:0',
                'max:99999999.99',
            ],

            'editLotArea' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999.99',
            ],

            'editUserId' => [
                'nullable',
                'exists:users,id',
            ],

            'editHouseModelId' => [
                'nullable',
                'required_if:editLotType,Model House',
                // 'required_if:editLotType,House & Lot',
                'exists:house_models,id',
            ],

            'editIsUnderConstruction' => [
                'nullable',
                'boolean',
            ],

            'editLotImage' => [
                'nullable',
                'image',
                'max:20480',
            ],
        ], [
            'editLotPrice.required' =>
                'Please enter the lot price.',

            'editLotPrice.numeric' =>
                'The lot price must be a valid number.',

            'editLotPrice.gt' =>
                'The lot price must be greater than ₱0.',

            'editLotPrice.max' =>
                'The maximum price is ₱99,999,999.99.',
            
            'editLotArea.required' =>
                'Please enter the lot area.',

            'editLotArea.numeric' =>
                'The lot area must be a valid number.',

            'editLotArea.gt' =>
                'The lot area must be greater than 0 sqm.',

            'editLotArea.max' =>
                'The maximum lot area is 99,999,999.99 sqm.',
        ]);

        $lot = Lot::query()
            ->findOrFail(
                $this->editLotId
            );

        if (
            $this->map &&
            $lot->map_id != $this->map->id
        ) {
            Notification::make()
                ->title('Invalid Lot')
                ->body(
                    'This lot does not belong to this subdivision.'
                )
                ->danger()
                ->send();

            return;
        }

        if (
            !$this->polygonInsideSubdivisionBoundary(
                $this->editGeoCoords
            )
        ) {
            Notification::make()
                ->title('Outside Subdivision Boundary')
                ->body(
                    'The edited lot must remain completely inside the subdivision boundary.'
                )
                ->danger()
                ->send();

            return;
        }

        foreach ($this->lots as $existingLot) {

            if (
                $existingLot->id ==
                $this->editLotId
            ) {
                continue;
            }

            if (
                !is_array(
                    $existingLot->geo_coords
                ) ||
                count(
                    $existingLot->geo_coords
                ) < 3
            ) {
                continue;
            }

            if (
                $this->geoPolygonsOverlap(
                    $this->editGeoCoords,
                    $existingLot->geo_coords
                )
            ) {
                Notification::make()
                    ->title('Lot Area Already Mapped')
                    ->body(
                        "This area overlaps with \"{$existingLot->name}\"."
                    )
                    ->danger()
                    ->send();

                return;
            }
        }

        $hideCommercialFields = in_array(
            $this->editLotType,
            [
                'Model House',
                'Playground & Community Amenities',
            ]
        );

        $supportsHouseModel = in_array(
            $this->editLotType,
            [
                'Model House',
                'House & Lot',
            ]
        );

        $data = [
            'name' =>
                $this->editLotName,

            'type' =>
                $this->editLotType,

            'geo_coords' =>
                $this->editGeoCoords,

            'lot_area' =>
                $this->editLotArea,

            'status' =>
                $hideCommercialFields
                    ? 'available'
                    : $this->editLotStatus,

            'price' =>
                $hideCommercialFields
                    ? null
                    : $this->editLotPrice,

            'user_id' =>
                (
                    !$hideCommercialFields &&
                    $this->editLotStatus !== 'available'
                )
                    ? $this->editUserId
                    : null,

            'house_model_id' =>
                $supportsHouseModel
                    ? $this->editHouseModelId
                    : null,

            'is_under_construction' =>
                $this->editIsUnderConstruction,
        ];

        if ($this->editLotImage) {

            $data['image'] =
                $this->editLotImage->storeAs(
                    'modelImages',
                    Str::uuid() .
                        '.' .
                        $this->editLotImage
                            ->getClientOriginalExtension(),
                    'public'
                );
        }

        $lot->update($data);

        $lotName =
            $lot->name;

        $this->resetEdit();

        session()->flash(
            'gis_success',
            "\"{$lotName}\" was successfully updated."
        );

        $this->dispatch('gis-edit-success');
    }

    public function deleteLotConfirmation(
        int $id,
        string $lotName
    ): void {
        $this->dialog()->confirm([
            'title' =>
                'Are you sure?',

            'description' =>
                'Do you want to permanently delete this lot: ' .
                html_entity_decode(
                    '<span class="text-red-600 underline">' .
                    e($lotName) .
                    '</span>'
                ) .
                '?',

            'icon' =>
                'error',

            'acceptLabel' =>
                'Yes, delete it',

            'rejectLabel' =>
                'Cancel',

            'method' =>
                'deleteLot',

            'params' =>
                $id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE ACTUAL LOT
    |--------------------------------------------------------------------------
    */

    public function deleteLot(
        int $id
    ): void {
        $lot = Lot::query()
            ->find($id);

        if (!$lot) {
            return;
        }

        if (
            $this->map &&
            $lot->map_id != $this->map->id
        ) {
            Notification::make()
                ->title('Invalid Lot')
                ->body(
                    'This lot does not belong to this subdivision.'
                )
                ->danger()
                ->send();

            return;
        }

        $lotName =
            $lot->name;

        $lot->delete();

        $this->resetMappingState();
        $this->loadMap();

        Notification::make()
            ->title('Lot Deleted')
            ->body(
                "\"{$lotName}\" was successfully removed."
            )
            ->danger()
            ->send();

        $this->dispatch(
            'gis-lot-saved'
        );
    }

    public function cancelMapping(): void
    {
        $this->resetMappingState();

        $this->dispatch(
            'gis-mapping-cancelled'
        );
    }

    public function resetCreate(): void
    {
        $this->lotName =
            null;

        $this->lotType =
            null;

        $this->lotImage =
            null;

        $this->lotPrice =
            null;

        $this->lotArea =
            null;

        $this->userId =
            null;

        $this->houseModelId =
            null;

        $this->isUnderConstruction =
            false;

        $this->lotStatus =
            'available';

        $this->newGeoCoords =
            [];
    }

    public function resetEdit(): void
    {
        $this->editLotId =
            null;

        $this->editLotName =
            null;

        $this->editLotType =
            null;

        $this->editLotImagePreview =
            null;

        $this->editLotImage =
            null;

        $this->editLotPrice =
            null;

        $this->editLotArea =
            null;

        $this->editUserId =
            null;

        $this->editHouseModelId =
            null;

        $this->editLotStatus =
            null;

        $this->editIsUnderConstruction =
            false;

        $this->editGeoCoords =
            [];

        $this->isEditing =
            false;
    }

    private function resetMappingState(): void
    {
        $this->resetCreate();
        $this->resetEdit();
    }

    private function subdivisionBoundary(): array
    {
        return [
            [13.920650, 121.420350],
            [13.920720, 121.421820],
            [13.920300, 121.422300],
            [13.919150, 121.422250],
            [13.918720, 121.421700],
            [13.918800, 121.420500],
            [13.919350, 121.420100],
        ];
    }

    private function polygonInsideSubdivisionBoundary(
        array $polygon
    ): bool {
        if (count($polygon) < 3) {
            return false;
        }

        $boundary =
            $this->subdivisionBoundary();

        foreach ($polygon as $point) {

            if (
                !is_array($point) ||
                count($point) < 2
            ) {
                return false;
            }

            if (
                !$this->geoPointInPolygon(
                    $point,
                    $boundary
                )
            ) {
                return false;
            }
        }

        return true;
    }

    private function geoPolygonsOverlap(
        array $polygonA,
        array $polygonB
    ): bool {
        if (
            count($polygonA) < 3 ||
            count($polygonB) < 3
        ) {
            return false;
        }

        if (
            !$this->geoBoundingBoxesOverlap(
                $polygonA,
                $polygonB
            )
        ) {
            return false;
        }

        $countA =
            count($polygonA);

        $countB =
            count($polygonB);

        for (
            $i = 0;
            $i < $countA;
            $i++
        ) {
            $a1 =
                $polygonA[$i];

            $a2 =
                $polygonA[
                    ($i + 1) % $countA
                ];

            for (
                $j = 0;
                $j < $countB;
                $j++
            ) {
                $b1 =
                    $polygonB[$j];

                $b2 =
                    $polygonB[
                        ($j + 1) % $countB
                    ];

                if (
                    $this->geoSegmentsIntersect(
                        $a1,
                        $a2,
                        $b1,
                        $b2
                    )
                ) {
                    return true;
                }
            }
        }

        if (
            $this->geoPointInPolygon(
                $polygonA[0],
                $polygonB
            )
        ) {
            return true;
        }

        if (
            $this->geoPointInPolygon(
                $polygonB[0],
                $polygonA
            )
        ) {
            return true;
        }

        return false;
    }

    private function geoBoundingBoxesOverlap(
        array $polygonA,
        array $polygonB
    ): bool {
        $aLat =
            array_column(
                $polygonA,
                0
            );

        $aLng =
            array_column(
                $polygonA,
                1
            );

        $bLat =
            array_column(
                $polygonB,
                0
            );

        $bLng =
            array_column(
                $polygonB,
                1
            );

        return !(
            max($aLat) < min($bLat) ||
            max($bLat) < min($aLat) ||
            max($aLng) < min($bLng) ||
            max($bLng) < min($aLng)
        );
    }

    private function geoCrossProduct(
        array $a,
        array $b,
        array $c
    ): float {

        $ax = (float) $a[1];
        $ay = (float) $a[0];

        $bx = (float) $b[1];
        $by = (float) $b[0];

        $cx = (float) $c[1];
        $cy = (float) $c[0];

        return
            ($bx - $ax) *
            ($cy - $ay)
            -
            ($by - $ay) *
            ($cx - $ax);
    }

    private function geoPointOnSegment(
        array $a,
        array $b,
        array $point
    ): bool {
        $epsilon = 0.0000000001;

        if (
            abs(
                $this->geoCrossProduct(
                    $a,
                    $b,
                    $point
                )
            ) > $epsilon
        ) {
            return false;
        }

        $px = (float) $point[1];
        $py = (float) $point[0];

        $ax = (float) $a[1];
        $ay = (float) $a[0];

        $bx = (float) $b[1];
        $by = (float) $b[0];

        return
            $px >= min($ax, $bx) - $epsilon &&
            $px <= max($ax, $bx) + $epsilon &&
            $py >= min($ay, $by) - $epsilon &&
            $py <= max($ay, $by) + $epsilon;
    }

    /*
    |--------------------------------------------------------------------------
    | SEGMENTS INTERSECT
    |--------------------------------------------------------------------------
    */

    private function geoSegmentsIntersect(
        array $p1,
        array $p2,
        array $p3,
        array $p4
    ): bool {
        $epsilon =
            0.0000000001;

        $d1 =
            $this->geoCrossProduct(
                $p3,
                $p4,
                $p1
            );

        $d2 =
            $this->geoCrossProduct(
                $p3,
                $p4,
                $p2
            );

        $d3 =
            $this->geoCrossProduct(
                $p1,
                $p2,
                $p3
            );

        $d4 =
            $this->geoCrossProduct(
                $p1,
                $p2,
                $p4
            );

        if (
            (
                ($d1 > $epsilon && $d2 < -$epsilon) ||
                ($d1 < -$epsilon && $d2 > $epsilon)
            )
            &&
            (
                ($d3 > $epsilon && $d4 < -$epsilon) ||
                ($d3 < -$epsilon && $d4 > $epsilon)
            )
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Touching also counts as overlap.
        |--------------------------------------------------------------------------
        */

        if (
            abs($d1) <= $epsilon &&
            $this->geoPointOnSegment(
                $p3,
                $p4,
                $p1
            )
        ) {
            return true;
        }

        if (
            abs($d2) <= $epsilon &&
            $this->geoPointOnSegment(
                $p3,
                $p4,
                $p2
            )
        ) {
            return true;
        }

        if (
            abs($d3) <= $epsilon &&
            $this->geoPointOnSegment(
                $p1,
                $p2,
                $p3
            )
        ) {
            return true;
        }

        if (
            abs($d4) <= $epsilon &&
            $this->geoPointOnSegment(
                $p1,
                $p2,
                $p4
            )
        ) {
            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | POINT IN POLYGON
    |--------------------------------------------------------------------------
    */

    private function geoPointInPolygon(
        array $point,
        array $polygon
    ): bool {
        if (count($polygon) < 3) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | Boundary itself counts as inside
        |--------------------------------------------------------------------------
        */

        $count =
            count($polygon);

        for (
            $i = 0;
            $i < $count;
            $i++
        ) {
            $a =
                $polygon[$i];

            $b =
                $polygon[
                    ($i + 1) % $count
                ];

            if (
                $this->geoPointOnSegment(
                    $a,
                    $b,
                    $point
                )
            ) {
                return true;
            }
        }

        $x =
            (float) $point[1];

        $y =
            (float) $point[0];

        $inside =
            false;

        for (
            $i = 0,
            $j = $count - 1;
            $i < $count;
            $j = $i++
        ) {
            $xi =
                (float) $polygon[$i][1];

            $yi =
                (float) $polygon[$i][0];

            $xj =
                (float) $polygon[$j][1];

            $yj =
                (float) $polygon[$j][0];

            $intersects =
                (($yi > $y) !== ($yj > $y))
                &&
                (
                    $x <
                    (
                        ($xj - $xi) *
                        ($y - $yi) /
                        (
                            ($yj - $yi)
                            ?: PHP_FLOAT_EPSILON
                        )
                        +
                        $xi
                    )
                );

            if ($intersects) {
                $inside =
                    !$inside;
            }
        }

        return $inside;
    }

    public function showMapNotification(
        string $title,
        string $message,
        string $type = 'warning'
    ): void {
        $notification = Notification::make()
            ->title($title)
            ->body($message);

        match ($type) {
            'success' => $notification->success(),
            'danger'  => $notification->danger(),
            'info'    => $notification->info(),
            default   => $notification->warning(),
        };

        $notification->send();
    }

    public function render()
    {
        return view(
            'livewire.fil-pages.map.leaflet-map-view',
            [
                'typeColors' =>
                    $this->typeColors,
            ]
        );
    }
}