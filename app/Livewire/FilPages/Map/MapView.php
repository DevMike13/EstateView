<?php

namespace App\Livewire\FilPages\Map;

use App\Models\Lot;
use App\Models\Map;
use Filament\Notifications\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;
use Illuminate\Support\Str;

class MapView extends Component
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
    public $isUnderConstruction = false;
    public $lotStatus = 'available';
    public $lotCoordinates = '';
    public $points = [];

    public $lotCounts = [];
    // LOT MANEGEMENT END

    // EDIT
    public $activeLotId;

    public $editLotId;
    public $editLotName;
    public $editLotType;
    public $editLotImagePreview;
    public $editLotImage;
    public $editLotPrice;
    public $editLotArea;
    public $editUserId;
    public $editHouseModelId;
    public $editLotStatus;
    public $editIsUnderConstruction;
    public $editLotCoordinates = '';
    public $editPoints = [];


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

        $this->generateLotCounts();
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
        $this->lotStatus = null; 
        $this->lotPrice = null;
        $this->lotArea = null;
        $this->userId = null;
        $this->isUnderConstruction = false;
        $this->houseModelId = null;

        $this->dispatch('redraw-modal-points');
    }

    public function createLotArea()
    {
        /*
        |--------------------------------------------------------------------------
        | Clean price before validation
        |--------------------------------------------------------------------------
        */
        if ($this->lotPrice !== null && $this->lotPrice !== '') {
            $this->lotPrice = str_replace(',', '', $this->lotPrice);
        }

        $this->validate([
            'lotName' => ['required', 'string', 'max:255'],
            'lotType' => ['required', 'string'],

            'lotCoordinates' => ['required', 'string'],

            'lotStatus' => ['required', 'in:available,sold,reserved'],

            'lotPrice' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'lotArea' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'userId' => ['nullable', 'exists:users,id'],

            'isUnderConstruction' => ['nullable', 'boolean'],

            'houseModelId' => [
                'nullable',
                'required_if:lotType,Model House',
                'required_if:lotType,House & Lot',
                'exists:house_models,id',
            ],

            'lotImage' => ['nullable', 'image', 'max:20480'],
        ], [
            'lotPrice.numeric' => 'The lot price must be a valid number.',
            'lotPrice.min' => 'The lot price cannot be less than 0.',
            'lotPrice.max' => 'The lot price is out of range. The maximum allowed value is ₱99,999,999.99.',

            'lotArea.numeric' => 'The lot area must be a valid number.',
            'lotArea.min' => 'The lot area cannot be less than 0.',
            'lotArea.max' => 'The lot area is out of range. The maximum allowed value is 99,999,999.99.',
        ]);

        $imagePath = null;

        if ($this->lotImage) {

            $imagePath = $this->lotImage->storeAs(
                'modelImages',
                Str::uuid() . '.' . $this->lotImage->getClientOriginalExtension(),
                'public'
            );
        }

        $cleanPrice = $this->lotPrice 
            ? str_replace(',', '', $this->lotPrice) 
            : null;

        Lot::create([
            'map_id' => $this->map->id,
            'name' => $this->lotName,
            'coords' => $this->lotCoordinates,
            'type' => $this->lotType,
            'image' => $imagePath,

            'status' => $this->lotStatus,

            'price' => $cleanPrice,
            'lot_area' => $this->lotArea,
            'user_id' => $this->userId,
            'is_under_construction' => $this->isUnderConstruction ?? false,
            'house_model_id' => in_array(
                $this->lotType,
                ['Model House', 'House & Lot']
            )
                ? $this->houseModelId
                : null,
        ]);

        $this->resetPoints();

        $this->map = Map::with('lots')->first();
        $this->lots = $this->map->lots;

        $this->dispatch('refreshMap');
        $this->dispatch('reload');
    }

    public function setActiveLot($id)
    {
        $this->activeLotId = $id;
    }

    public function loadEditLot()
    {
        // $lot = Lot::findOrFail($this->activeLotId);
        $lot = Lot::with(['user', 'houseModel'])->findOrFail($this->activeLotId);

        $this->editLotId = $lot->id;
        $this->editLotName = $lot->name;
        $this->editLotType = $lot->type;
        $this->editLotImage = null;
        $this->editLotImagePreview = $lot->image;
        $this->editLotCoordinates = $lot->coords;
        $this->editLotPrice = $lot->price;
        $this->editLotStatus = $lot->status;
        $this->editLotArea = $lot->lot_area;
        // $this->editUserId = $lot->user_id;
        // $this->editHouseModelId = $lot->house_model_id;
        $this->editUserId = $lot->user?->id;
        $this->editHouseModelId = $lot->houseModel?->id;
        $this->editIsUnderConstruction = (bool) $lot->is_under_construction;

        $coords = explode(',', $lot->coords);
        $this->editPoints = [];

        for ($i = 0; $i < count($coords); $i += 2) {
            $this->editPoints[] = [
                'x' => (int) $coords[$i],
                'y' => (int) $coords[$i + 1],
            ];
        }
    }

    /* =========================
        UPDATE LOT
    ==========================*/
    public function updateLot()
    {
        /*
        |--------------------------------------------------------------------------
        | Clean price before validation
        |--------------------------------------------------------------------------
        */
        if ($this->editLotPrice !== null && $this->editLotPrice !== '') {
            $this->editLotPrice = str_replace(',', '', $this->editLotPrice);
        }

        $this->validate([
            'editLotName' => ['required', 'string', 'max:255'],
            'editLotType' => ['required', 'string'],

            'editLotCoordinates' => ['required', 'string'],

            'editLotStatus' => ['required', 'in:available,sold,reserved'],

            'editLotPrice' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'editLotArea' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],

            'editUserId' => ['nullable', 'exists:users,id'],

            'editHouseModelId' => [
                'nullable',
                'required_if:editLotType,Model House',
                'required_if:editLotType,House & Lot',
                'exists:house_models,id'
            ],

            'editIsUnderConstruction' => ['nullable', 'boolean'],

            'editLotImage' => ['nullable', 'image', 'max:20480'],
        ], [
            'editLotPrice.numeric' => 'The lot price must be a valid number.',
            'editLotPrice.min' => 'The lot price cannot be less than 0.',
            'editLotPrice.max' => 'The lot price is out of range. The maximum allowed value is ₱99,999,999.99.',

            'editLotArea.numeric' => 'The lot area must be a valid number.',
            'editLotArea.min' => 'The lot area cannot be less than 0.',
            'editLotArea.max' => 'The lot area is out of range. The maximum allowed value is 99,999,999.99.',
        ]);

        $cleanPrice = $this->editLotPrice 
            ? str_replace(',', '', $this->editLotPrice) 
            : null;

        $data = [
            'name' => $this->editLotName,
            'type' => $this->editLotType,
            'coords' => $this->editLotCoordinates,

            'price' => $cleanPrice,
            'lot_area' => $this->editLotArea,
            'status' => $this->editLotStatus,
            'user_id' => $this->editLotStatus != 'available' ? $this->editUserId : null,
            'house_model_id' => in_array($this->editLotType, ['Model House', 'House & Lot'])
                ? $this->editHouseModelId
                : null,

            'is_under_construction' => $this->editIsUnderConstruction ?? false,
        ];

        if ($this->editLotImage) {
            $data['image'] = $this->editLotImage->storeAs(
                'modelImages',
                Str::uuid() . '.' . $this->editLotImage->getClientOriginalExtension(),
                'public'
            );
        }

        Lot::where('id', $this->editLotId)->update($data);

        $this->resetEdit();
        $this->reloadMap();
        $this->reloadWeb();
    }

    public function deleteLot($id)
    {
        $lot = Lot::find($id);

        if (!$lot) return;

        $lotName = $lot->name;

        $lot->delete();

        $this->activeLotId = null;

        Notification::make()
            ->title('Lot Deleted')
            ->body("\"{$lotName}\" was successfully removed from the map.")
            ->danger()
            ->send();

        $this->reloadWeb();
    }

    public function deleteLotConfirmation($id, $lotName)
    {
        $this->dialog()->confirm([
            'title' => 'Are you sure?',
            'description' => "Do you want to delete this lot: " .
                html_entity_decode('<span class="text-red-600 underline">' . $lotName . '</span>') .
                " ?",
            'icon' => 'error',
            'acceptLabel' => 'Yes, delete it',
            'rejectLabel' => 'Cancel',
            'method' => 'deleteLot',
            'params' => $id,
        ]);
    }

    /* =========================
        HELPERS
    ==========================*/
    private function reloadMap()
    {
        $this->map = Map::with('lots')->first();
        $this->lots = $this->map->lots;
    }

    public function resetCreate()
    {
        $this->lotName = null;
        $this->lotType = null;
        $this->lotImage = null;
        $this->lotCoordinates = '';
        $this->points = [];
    }

    public function resetEdit()
    {
        $this->editLotId = null;
        $this->editLotName = null;
        $this->editLotType = null;
        $this->editLotImage = null;
        $this->editLotCoordinates = '';
        $this->editIsUnderConstruction = false;
        $this->editPoints = [];
    }

    public function reloadWeb()
    {
        $this->dispatch('reload');
        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.fil-pages.map.map-view', [
            'typeColors' => $this->typeColors,
        ]);
    }
}