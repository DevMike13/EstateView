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
        $this->lotStatus = null; // ✅ ADD THIS
        $this->lotPrice = null;
        $this->lotArea = null;
        $this->userId = null;
        $this->houseModelId = null;

        $this->dispatch('redraw-modal-points');
    }

    public function createLotArea()
    {
        $this->validate([
            'lotName' => ['required', 'string', 'max:255'],
            'lotType' => ['required', 'string'],

            'lotCoordinates' => ['required', 'string'],

            'lotStatus' => ['required', 'in:available,sold,reserved'],

            'lotPrice' => ['nullable', 'numeric'],
            'lotArea' => ['nullable', 'numeric', 'min:0'],

            'userId' => ['nullable', 'exists:users,id'],

            'houseModelId' => [
                'nullable',
                'required_if:lotType,Model House',
                'exists:house_models,id'
            ],

            'lotImage' => ['nullable', 'image', 'max:20480'],
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
            'house_model_id' => $this->lotType === 'Model House'
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
        $this->editLotImage =null;
        $this->editLotImagePreview = $lot->image;
        $this->editLotCoordinates = $lot->coords;
        $this->editLotPrice = $lot->price;
        $this->editLotStatus = $lot->status;
        $this->editLotArea = $lot->lot_area;
        // $this->editUserId = $lot->user_id;
        // $this->editHouseModelId = $lot->house_model_id;
        $this->editUserId = $lot->user?->id;
        $this->editHouseModelId = $lot->houseModel?->id;

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
        $this->validate([
            'editLotName' => ['required', 'string', 'max:255'],
            'editLotType' => ['required', 'string'],

            'editLotCoordinates' => ['required', 'string'],

            'editLotStatus' => ['required', 'in:available,sold,reserved'],

            'editLotPrice' => ['nullable', 'numeric'],
            'editLotArea' => ['nullable', 'numeric', 'min:0'],

            'editUserId' => ['nullable', 'exists:users,id'],

            'editHouseModelId' => [
                'nullable',
                'required_if:editLotType,Model House',
                'exists:house_models,id'
            ],

            'editLotImage' => ['nullable', 'image', 'max:20480'],
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
            'house_model_id' => $this->editLotType === 'Model House'
                ? $this->editHouseModelId
                : null,
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

        $lot->delete();

        $this->activeLotId = null;

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
            'accept'      => [
                'label'  => 'Yes, delete it',
                'method' => 'deleteLot',
                'params' => '$id',
            ],
            'reject' => [
                'label'  => 'No, cancel',
                'method' => 'reloadWeb',
            ],
            
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
        $this->editPoints = [];
    }

    public function reloadWeb(){

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
