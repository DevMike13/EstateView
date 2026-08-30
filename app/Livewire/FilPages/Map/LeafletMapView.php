<?php

namespace App\Livewire\FilPages\Map;

use App\Models\Block;
use App\Models\Lot;
use App\Models\Map;
use Illuminate\Validation\Rule;
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
    public $blocks;

    /*
    |--------------------------------------------------------------------------
    | SUBDIVISION BOUNDARY
    |--------------------------------------------------------------------------
    */

    public array $newBoundaryGeoCoords = [];
    public array $editBoundaryGeoCoords = [];
    public bool $isEditingBoundary = false;

    /*
    |--------------------------------------------------------------------------
    | BLOCK
    |--------------------------------------------------------------------------
    */

    public $blockName;
    public array $newBlockGeoCoords = [];

    public ?int $editBlockId = null;
    public $editBlockName;
    public array $editBlockGeoCoords = [];
    public bool $isEditingBlock = false;

    /*
    |--------------------------------------------------------------------------
    | CREATE LOT
    |--------------------------------------------------------------------------
    */

    public $lotNumber;
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

    public $editLotNumber;
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
        'Internal Road' => '#9ca3af',
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
                'blocks',
                'lots.block',
                'lots.user',
                'lots.houseModel',
            ])
            ->first();

        $this->lots =
            $this->map?->lots ?? collect();

        $this->blocks =
            $this->map?->blocks ?? collect();
    }

    /*
    |--------------------------------------------------------------------------
    | SUBDIVISION BOUNDARY
    |--------------------------------------------------------------------------
    */

    public function createBoundary(): void
    {
        if (!$this->map) {
            Notification::make()
                ->title('Subdivision Map Not Found')
                ->body('No subdivision map record is available.')
                ->danger()
                ->send();

            return;
        }

        if (count($this->subdivisionBoundary()) >= 3) {
            Notification::make()
                ->title('Boundary Already Exists')
                ->body('Edit the existing subdivision boundary instead.')
                ->warning()
                ->send();

            return;
        }

        $this->validate([
            'newBoundaryGeoCoords' => ['required', 'array', 'min:3'],
            'newBoundaryGeoCoords.*' => ['required', 'array', 'size:2'],
            'newBoundaryGeoCoords.*.0' => ['required', 'numeric', 'between:-90,90'],
            'newBoundaryGeoCoords.*.1' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $this->map->boundary_geo_coords = $this->newBoundaryGeoCoords;
        $this->map->save();
        $this->map->refresh();

        $this->newBoundaryGeoCoords = [];

        session()->flash(
            'gis_success',
            'Subdivision boundary was successfully created.'
        );

        $this->dispatch('gis-boundary-create-success');
    }

    public function startEditBoundary(): void
    {
        $boundary = $this->subdivisionBoundary();

        if (count($boundary) < 3) {
            Notification::make()
                ->title('Boundary Not Found')
                ->body('Create the subdivision boundary first.')
                ->warning()
                ->send();

            return;
        }

        $this->editBoundaryGeoCoords = $boundary;
        $this->isEditingBoundary = true;

        $this->dispatch(
            'gis-edit-boundary',
            coords: $boundary
        );
    }

    public function updateBoundary(): void
    {
        $this->validate([
            'editBoundaryGeoCoords' => ['required', 'array', 'min:3'],
            'editBoundaryGeoCoords.*' => ['required', 'array', 'size:2'],
            'editBoundaryGeoCoords.*.0' => ['required', 'numeric', 'between:-90,90'],
            'editBoundaryGeoCoords.*.1' => ['required', 'numeric', 'between:-180,180'],
        ]);

        foreach ($this->blocks as $block) {
            if (
                is_array($block->geo_coords) &&
                count($block->geo_coords) >= 3 &&
                !$this->polygonInsidePolygon(
                    $block->geo_coords,
                    $this->editBoundaryGeoCoords
                )
            ) {
                Notification::make()
                    ->title('Invalid Subdivision Boundary')
                    ->body("\"{$block->name}\" would be outside the edited subdivision boundary.")
                    ->danger()
                    ->send();

                return;
            }
        }

        foreach ($this->lots as $lot) {
            if (
                is_array($lot->geo_coords) &&
                count($lot->geo_coords) >= 3 &&
                !$this->polygonInsidePolygon(
                    $lot->geo_coords,
                    $this->editBoundaryGeoCoords
                )
            ) {
                Notification::make()
                    ->title('Invalid Subdivision Boundary')
                    ->body("\"{$lot->name}\" would be outside the edited subdivision boundary.")
                    ->danger()
                    ->send();

                return;
            }
        }

        $this->map->update([
            'boundary_geo_coords' => $this->editBoundaryGeoCoords,
        ]);

        $this->editBoundaryGeoCoords = [];
        $this->isEditingBoundary = false;

        session()->flash(
            'gis_success',
            'Subdivision boundary was successfully updated.'
        );

        $this->dispatch('gis-boundary-edit-success');
    }

    public function deleteBoundaryConfirmation(): void
    {
        $this->dialog()->confirm([
            'title' => 'Delete Subdivision Boundary?',
            'description' =>
                'This will remove the subdivision boundary. Existing blocks and lots will remain in the database.',
            'icon' => 'error',
            'acceptLabel' => 'Yes, delete it',
            'rejectLabel' => 'Cancel',
            'method' => 'deleteBoundary',
        ]);
    }

    public function deleteBoundary(): void
    {
        if (!$this->map) {
            return;
        }

        if ($this->blocks->isNotEmpty()) {
            Notification::make()
                ->title('Boundary Cannot Be Deleted')
                ->body('Delete all blocks first before removing the subdivision boundary.')
                ->danger()
                ->send();

            return;
        }

        $gisLots = $this->lots->filter(
            fn ($lot) =>
                is_array($lot->geo_coords) &&
                count($lot->geo_coords) >= 3
        );

        if ($gisLots->isNotEmpty()) {
            Notification::make()
                ->title('Boundary Cannot Be Deleted')
                ->body('There are still mapped lots. Delete them before removing the subdivision boundary.')
                ->danger()
                ->send();

            return;
        }

        $this->map->boundary_geo_coords = null;
        $this->map->save();
        $this->map->refresh();

        session()->flash(
            'gis_success',
            'Subdivision boundary was successfully deleted.'
        );

        $this->redirect(
            request()->header('Referer') ?? url()->current(),
            navigate: false
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | BLOCK
    |--------------------------------------------------------------------------
    */

    public function createBlock(): void
    {
        if (!$this->map || count($this->subdivisionBoundary()) < 3) {
            Notification::make()
                ->title('Subdivision Boundary Required')
                ->body('Create the subdivision boundary before creating blocks.')
                ->warning()
                ->send();

            return;
        }

        $this->validate([
            'blockName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blocks', 'name')->where(
                    fn ($query) => $query->where('map_id', $this->map->id)
                ),
            ],
            'newBlockGeoCoords' => ['required', 'array', 'min:3'],
            'newBlockGeoCoords.*' => ['required', 'array', 'size:2'],
            'newBlockGeoCoords.*.0' => ['required', 'numeric', 'between:-90,90'],
            'newBlockGeoCoords.*.1' => ['required', 'numeric', 'between:-180,180'],
        ]);

        if (!$this->polygonInsideSubdivisionBoundary($this->newBlockGeoCoords)) {
            Notification::make()
                ->title('Outside Subdivision Boundary')
                ->body('The block must be completely inside the subdivision boundary.')
                ->danger()
                ->send();

            return;
        }

        foreach ($this->blocks as $block) {
            if (
                is_array($block->geo_coords) &&
                count($block->geo_coords) >= 3 &&
                $this->geoPolygonsOverlap(
                    $this->newBlockGeoCoords,
                    $block->geo_coords
                )
            ) {
                Notification::make()
                    ->title('Block Area Already Mapped')
                    ->body("This block overlaps with \"{$block->name}\".")
                    ->danger()
                    ->send();

                return;
            }
        }

        $block = Block::create([
            'map_id' => $this->map->id,
            'name' => $this->blockName,
            'geo_coords' => $this->newBlockGeoCoords,
        ]);

        $this->blockName = null;
        $this->newBlockGeoCoords = [];

        session()->flash(
            'gis_success',
            "\"{$block->name}\" was successfully added."
        );

        $this->dispatch('gis-block-create-success');
    }

    public function startEditBlock(int $blockId): void
    {
        $block = Block::query()
            ->where('map_id', $this->map?->id)
            ->findOrFail($blockId);

        $this->editBlockId = $block->id;
        $this->editBlockName = $block->name;
        $this->editBlockGeoCoords = $block->geo_coords;
        $this->isEditingBlock = true;

        $this->dispatch(
            'gis-edit-block',
            blockId: $block->id,
            coords: $block->geo_coords
        );
    }

    public function updateBlock(): void
    {
        $this->validate([
            'editBlockId' => ['required', 'exists:blocks,id'],
            'editBlockName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('blocks', 'name')
                    ->where(
                        fn ($query) => $query->where('map_id', $this->map->id)
                    )
                    ->ignore($this->editBlockId),
            ],
            'editBlockGeoCoords' => ['required', 'array', 'min:3'],
            'editBlockGeoCoords.*' => ['required', 'array', 'size:2'],
            'editBlockGeoCoords.*.0' => ['required', 'numeric', 'between:-90,90'],
            'editBlockGeoCoords.*.1' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $block = Block::query()
            ->with('lots')
            ->where('map_id', $this->map->id)
            ->findOrFail($this->editBlockId);

        if (!$this->polygonInsideSubdivisionBoundary($this->editBlockGeoCoords)) {
            Notification::make()
                ->title('Outside Subdivision Boundary')
                ->body('The edited block must remain completely inside the subdivision boundary.')
                ->danger()
                ->send();

            return;
        }

        foreach ($this->blocks as $existingBlock) {
            if ($existingBlock->id == $block->id) {
                continue;
            }

            if (
                is_array($existingBlock->geo_coords) &&
                count($existingBlock->geo_coords) >= 3 &&
                $this->geoPolygonsOverlap(
                    $this->editBlockGeoCoords,
                    $existingBlock->geo_coords
                )
            ) {
                Notification::make()
                    ->title('Block Area Already Mapped')
                    ->body("The edited block overlaps with \"{$existingBlock->name}\".")
                    ->danger()
                    ->send();

                return;
            }
        }

        foreach ($block->lots as $lot) {
            if (
                is_array($lot->geo_coords) &&
                count($lot->geo_coords) >= 3 &&
                !$this->polygonInsidePolygon(
                    $lot->geo_coords,
                    $this->editBlockGeoCoords
                )
            ) {
                Notification::make()
                    ->title('Invalid Block Boundary')
                    ->body("\"{$lot->name}\" would be outside the edited block.")
                    ->danger()
                    ->send();

                return;
            }
        }

        $block->update([
            'name' => $this->editBlockName,
            'geo_coords' => $this->editBlockGeoCoords,
        ]);

        foreach ($block->lots as $lot) {
            if ($lot->lot_number !== null) {
                $lot->update([
                    'name' =>
                        $block->name .
                        ', Lot ' .
                        $lot->lot_number,
                ]);
            }
        }

        $blockName = $block->name;

        $this->editBlockId = null;
        $this->editBlockName = null;
        $this->editBlockGeoCoords = [];
        $this->isEditingBlock = false;

        session()->flash(
            'gis_success',
            "\"{$blockName}\" was successfully updated."
        );

        $this->dispatch('gis-block-edit-success');
    }

    public function deleteBlockConfirmation(
        int $id,
        string $blockName
    ): void {
        $this->dialog()->confirm([
            'title' => 'Delete Block?',
            'description' =>
                'Do you want to permanently delete this block: ' .
                html_entity_decode(
                    '<span class="text-red-600 underline">' .
                    e($blockName) .
                    '</span>'
                ) .
                '?',
            'icon' => 'error',
            'acceptLabel' => 'Yes, delete it',
            'rejectLabel' => 'Cancel',
            'method' => 'deleteBlock',
            'params' => $id,
        ]);
    }

    public function deleteBlock(int $id): void
    {
        $block = Block::query()
            ->withCount('lots')
            ->where('map_id', $this->map?->id)
            ->find($id);

        if (!$block) {
            return;
        }

        if ($block->lots_count > 0) {
            Notification::make()
                ->title('Block Cannot Be Deleted')
                ->body(
                    "\"{$block->name}\" still contains {$block->lots_count} lot(s). Move or delete those lots first."
                )
                ->danger()
                ->send();

            return;
        }

        $blockName = $block->name;

        $block->delete();

        session()->flash(
            'gis_success',
            "\"{$blockName}\" was successfully deleted."
        );

        $this->redirect(
            request()->header('Referer') ?? url()->current(),
            navigate: false
        );

        return;
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
            'lotNumber' => [
                'exclude_if:lotType,Internal Road',
                'required',
                'integer',
                'min:1',
                'max:999999',
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
                'exclude_if:lotType,Internal Road',
                'required',
                'numeric',
                'gt:0',
                'max:99999999.99',
            ],

            'lotArea' => [
                'exclude_if:lotType,Internal Road',
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
        | INTERNAL ROAD MUST NOT BE INSIDE / OVERLAP ANY BLOCK
        |--------------------------------------------------------------------------
        */

        if (
            $this->lotType === 'Internal Road'
        ) {
            foreach (
                $this->blocks as $existingBlock
            ) {
                if (
                    !is_array(
                        $existingBlock->geo_coords
                    )
                    ||
                    count(
                        $existingBlock->geo_coords
                    ) < 3
                ) {
                    continue;
                }

                if (
                    $this->geoPolygonsOverlap(
                        $this->newGeoCoords,
                        $existingBlock->geo_coords
                    )
                ) {
                    Notification::make()
                        ->title(
                            'Invalid Internal Road Location'
                        )
                        ->body(
                            "Internal Road cannot be inside, cross, or overlap \"{$existingBlock->name}\"."
                        )
                        ->danger()
                        ->send();

                    return;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FIND BLOCK FOR NORMAL LOT TYPES
        |--------------------------------------------------------------------------
        */

        $block =
            $this->lotType === 'Internal Road'
                ? null
                : $this->findContainingBlock(
                    $this->newGeoCoords
                );
        
            if ($block) {
                $duplicateLotNumber = Lot::query()
                    ->where('map_id', $this->map->id)
                    ->where('block_id', $block->id)
                    ->where('lot_number', $this->lotNumber)
                    ->exists();

                if ($duplicateLotNumber) {
                    Notification::make()
                        ->title('Lot Number Already Exists')
                        ->body(
                            "\"{$block->name}, Lot {$this->lotNumber}\" already exists."
                        )
                        ->danger()
                        ->send();

                    return;
                }
            }

        if (
            $this->lotType !== 'Internal Road' &&
            !$block
        ) {
            Notification::make()
                ->title('Block Required')
                ->body('The property must be completely inside one block.')
                ->danger()
                ->send();

            return;
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
                'Internal Road',
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

            'block_id' =>
                $this->lotType === 'Internal Road'
                    ? null
                    : $block?->id,

            'lot_number' =>
                $this->lotType === 'Internal Road'
                    ? null
                    : $this->lotNumber,

            'name' =>
                $this->lotType === 'Internal Road'
                    ? 'Internal Road'
                    : (
                        $block
                            ? $block->name . ', Lot ' . $this->lotNumber
                            : 'Lot ' . $this->lotNumber
                    ),

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
                $this->lotType === 'Internal Road'
                    ? null
                    : $this->lotArea,

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
                $this->lotType === 'Internal Road'
                    ? false
                    : $this->isUnderConstruction,
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

        $this->editLotNumber =
            $lot->lot_number;

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

            'editLotNumber' => [
                'exclude_if:editLotType,Internal Road',
                'required',
                'integer',
                'min:1',
                'max:999999',
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
                'exclude_if:editLotType,Internal Road',
                'required',
                'numeric',
                'gt:0',
                'max:99999999.99',
            ],

            'editLotArea' => [
                'exclude_if:editLotType,Internal Road',
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

        /*
        |--------------------------------------------------------------------------
        | INTERNAL ROAD MUST NOT BE INSIDE / OVERLAP ANY BLOCK
        |--------------------------------------------------------------------------
        */

        if (
            $this->editLotType === 'Internal Road'
        ) {
            foreach (
                $this->blocks as $existingBlock
            ) {
                if (
                    !is_array(
                        $existingBlock->geo_coords
                    )
                    ||
                    count(
                        $existingBlock->geo_coords
                    ) < 3
                ) {
                    continue;
                }

                if (
                    $this->geoPolygonsOverlap(
                        $this->editGeoCoords,
                        $existingBlock->geo_coords
                    )
                ) {
                    Notification::make()
                        ->title(
                            'Invalid Internal Road Location'
                        )
                        ->body(
                            "Internal Road cannot be inside, cross, touch, or overlap \"{$existingBlock->name}\"."
                        )
                        ->danger()
                        ->send();

                    return;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FIND BLOCK FOR NORMAL LOT TYPES
        |--------------------------------------------------------------------------
        */

        $block =
            $this->editLotType === 'Internal Road'
                ? null
                : $this->findContainingBlock(
                    $this->editGeoCoords
                );

        if ($block) {
            $duplicateLotNumber = Lot::query()
                ->where('map_id', $this->map->id)
                ->where('block_id', $block->id)
                ->where('lot_number', $this->editLotNumber)
                ->where('id', '!=', $this->editLotId)
                ->exists();

            if ($duplicateLotNumber) {
                Notification::make()
                    ->title('Lot Number Already Exists')
                    ->body(
                        "\"{$block->name}, Lot {$this->editLotNumber}\" already exists."
                    )
                    ->danger()
                    ->send();

                return;
            }
        }

        if (
            $this->editLotType !== 'Internal Road' &&
            !$block
        ) {
            Notification::make()
                ->title('Block Required')
                ->body('The property must be completely inside one block.')
                ->danger()
                ->send();

            return;
        }

        $hideCommercialFields = in_array(
            $this->editLotType,
            [
                'Model House',
                'Playground & Community Amenities',
                'Internal Road',
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
            'block_id' =>
                $this->editLotType === 'Internal Road'
                    ? null
                    : $block?->id,

            'lot_number' =>
                $this->editLotType === 'Internal Road'
                    ? null
                    : $this->editLotNumber,

            'name' =>
                $this->editLotType === 'Internal Road'
                    ? 'Internal Road'
                    : (
                        $block
                            ? $block->name . ', Lot ' . $this->editLotNumber
                            : 'Lot ' . $this->editLotNumber
                    ),

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
                $this->editLotType === 'Internal Road'
                    ? false
                    : $this->editIsUnderConstruction,
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

        session()->flash(
            'gis_success',
            "\"{$lotName}\" was successfully deleted."
        );

        $this->redirect(
            request()->header('Referer') ?? url()->current(),
            navigate: false
        );

        return;
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
        $this->lotNumber =
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

        $this->editLotNumber =
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

        $this->newBoundaryGeoCoords = [];
        $this->editBoundaryGeoCoords = [];
        $this->isEditingBoundary = false;

        $this->blockName = null;
        $this->newBlockGeoCoords = [];
        $this->editBlockId = null;
        $this->editBlockName = null;
        $this->editBlockGeoCoords = [];
        $this->isEditingBlock = false;
    }

    private function subdivisionBoundary(): array
    {
        return is_array($this->map?->boundary_geo_coords)
            ? $this->map->boundary_geo_coords
            : [];
    }

    private function polygonInsidePolygon(
        array $polygon,
        array $boundary
    ): bool {
        if (
            count($polygon) < 3 ||
            count($boundary) < 3
        ) {
            return false;
        }

        foreach ($polygon as $point) {
            if (
                !is_array($point) ||
                count($point) < 2 ||
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

    private function findContainingBlock(
        array $polygon
    ): ?Block {
        foreach ($this->blocks as $block) {
            if (
                is_array($block->geo_coords) &&
                count($block->geo_coords) >= 3 &&
                $this->polygonInsidePolygon(
                    $polygon,
                    $block->geo_coords
                )
            ) {
                return $block;
            }
        }

        return null;
    }

    private function polygonInsideSubdivisionBoundary(
        array $polygon
    ): bool {
        return $this->polygonInsidePolygon(
            $polygon,
            $this->subdivisionBoundary()
        );
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