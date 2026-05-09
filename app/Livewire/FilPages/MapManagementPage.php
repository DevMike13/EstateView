<?php

namespace App\Livewire\FilPages;

use App\Models\HouseModel;
use App\Models\Lot;
use App\Models\Map;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;

class MapManagementPage extends Component
{
    use WithFilePond, Actions;
    
    // LOT MANAGEMENT
    public $map;
    public $lots;

    public $lotName;
    public $lotType;
    public $lotImage;

    public $lotCoordinates = '';
    public $points = [];

    public $lotCounts = [];
    // LOT MANEGEMENT END

    // MODEL HOUSE
    public $modelHouseImage;
    public $virtualTourUrl;
    public $modelName;
    public $bedroomsCount;
    public $bathroomsCount;
    public $floorArea;
    public $price;

    protected $rules = [
        'modelHouseImage' => 'nullable|image|max:5120', // 5MB
        'virtualTourUrl' => 'nullable|url',
        'modelName' => 'required|string|max:255',
        'bedroomsCount' => 'required|integer|min:0',
        'bathroomsCount' => 'required|integer|min:0',
        'floorArea' => 'required|numeric|min:0',
        'price' => 'required|numeric|min:0',
    ];

    public $houseModels = [];

    public $selectedHouseId;

    // public $editModelHouseImage;
    public $editModelHouseImage = [];
    public $editImagePreview;
    public $editVirtualTourUrl;
    public $editModelName;
    public $editBedroomsCount;
    public $editBathroomsCount;
    public $editFloorArea;
    public $editPrice;

    protected $editRules = [
        'editModelHouseImage.*' => 'nullable|image|max:5120',
        'editVirtualTourUrl' => 'nullable|url',
        'editModelName' => 'required|string|max:255',
        'editBedroomsCount' => 'required|integer|min:0',
        'editBathroomsCount' => 'required|integer|min:0',
        'editFloorArea' => 'required|numeric|min:0',
        'editPrice' => 'required|numeric|min:0',
    ];
    // MODEL HOUSE END

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

        $this->houseModels = HouseModel::all();

        $this->generateLotCounts();
    }

    public function createModelHouse()
    {
        $this->validate();

        $imagePath = null;

        if ($this->modelHouseImage) {

            $fileName = Str::slug($this->modelName) 
                . '-' 
                . Str::uuid() 
                . '.' 
                . $this->modelHouseImage->getClientOriginalExtension();

            $imagePath = $this->modelHouseImage->storeAs(
                'modelHouse',
                $fileName,
                'public'
            );
        }

        $cleanPrice = $this->price 
            ? str_replace(',', '', $this->price) 
            : null;

        HouseModel::create([
            'image' => $imagePath,
            'virtual_tour_url' => $this->virtualTourUrl,
            'model_name' => $this->modelName,
            'bedrooms' => $this->bedroomsCount,
            'bathrooms' => $this->bathroomsCount,
            'floor_area' => $this->floorArea,
            'price' => $cleanPrice,
        ]);

        $this->reset([
            'modelHouseImage',
            'virtualTourUrl',
            'modelName',
            'bedroomsCount',
            'bathroomsCount',
            'floorArea',
            'price',
        ]);

         Notification::make()
            ->title('Success!')
            ->body("New Model Created!")
            ->success()
            ->send();

        $this->dispatch('reload');
        return redirect()->back();
    }

    public function getSelectedModelHouse($id)
    {
        $model = HouseModel::findOrFail($id);

        $this->selectedHouseId = $model->id;

        // $this->editModelHouseImage = $model->image;
        $this->editImagePreview = Storage::url($model->image);
        
        $this->editModelHouseImage = $model->image
        ? [asset('storage/' . $model->image)]
        : [];

        $this->editModelName = $model->model_name;
        $this->editVirtualTourUrl = $model->virtual_tour_url;
        $this->editBedroomsCount = $model->bedrooms;
        $this->editBathroomsCount = $model->bathrooms;
        $this->editFloorArea = $model->floor_area;
        $this->editPrice = $model->price;
    }

    public function editSelectedModelHouse($name)
    {
        if($this->selectedHouseId){
            $this->validate($this->editRules);

            $model = HouseModel::findOrFail($this->selectedHouseId);

            $imagePath = $model->image;

            if (
                is_array($this->editModelHouseImage) &&
                isset($this->editModelHouseImage[0]) &&
                $this->editModelHouseImage[0] instanceof TemporaryUploadedFile
            ) {

                $file = $this->editModelHouseImage[0];

                $fileName = Str::slug($this->editModelName)
                    . '-' 
                    . Str::uuid() 
                    . '.' 
                    . $file->getClientOriginalExtension();

                $imagePath = $file->storeAs(
                    'modelHouse',
                    $fileName,
                    'public'
                );
            }
            
            $model->update([
                'image' => $imagePath,
                'virtual_tour_url' => $this->editVirtualTourUrl,
                'model_name' => $this->editModelName,
                'bedrooms' => $this->editBedroomsCount,
                'bathrooms' => $this->editBathroomsCount,
                'floor_area' => $this->editFloorArea,
                'price' => str_replace(',', '', $this->editPrice),
            ]);

            $this->reset([
                'selectedHouseId',
                'editModelHouseImage',
                'editVirtualTourUrl',
                'editModelName',
                'editBedroomsCount',
                'editBathroomsCount',
                'editFloorArea',
                'editPrice',
            ]);

            Notification::make()
                ->title('Updated!')
                ->body('Model House updated successfully.')
                ->success()
                ->send();

            $this->dispatch('reload');
            return redirect()->back();
        }
    }

    public function editModelHouseConfirmation($name){
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => "Do you want to edit this model house with name . ".  html_entity_decode('<span class="text-red-600 underline">' . $name . '</span>') . " ?",
            'acceptLabel' => 'Yes, update it',
            'method'      => 'editSelectedModelHouse',
            'icon'        => 'error',
            'params'      => $name
        ]);
    }

    public function resetForm()
    {
        $this->reset([
            'modelHouseImage',
            'virtualTourUrl',
            'modelName',
            'bedroomsCount',
            'bathroomsCount',
            'floorArea',
            'price',
            'selectedHouseId',
        ]);

        $this->isEditMode = false;
    }

    public function deleteModelHouse($id)
    {
        $model = HouseModel::findOrFail($id);

        if ($model->image) {
            Storage::disk('public')->delete($model->image);
        }

        $model->delete();

        $this->houseModels = HouseModel::latest()->get();

        Notification::make()
            ->title('Deleted!')
            ->body('Model house removed successfully.')
            ->success()
            ->send();

        $this->dispatch('reload');

        return redirect()->back();
    }

    public function deleteModelHouseConfirmation($id, $modelName){
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => "Do you want to remove this model Name: ".  html_entity_decode('<span class="text-red-600 underline">' . $modelName . '</span>') . " ?",
            'acceptLabel' => 'Yes, delete it',
            'method'      => 'deleteModelHouse',
            'icon'        => 'error',
            'params'      => $id
        ]);
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
