<?php

namespace App\Livewire\FilPages;

use App\Models\HouseModel;
use App\Models\Lot;
use App\Models\Map;
use App\Models\TourHotSpot;
use App\Models\TourScene;
use App\Models\VirtualTour;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;

class MapManagementPage extends Component
{
    use WithFilePond, Actions, WithFileUploads;
    
    // LOT MANAGEMENT
    public $map;
    public $lots;
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

    public $editModelHouseImage;
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

    // VIRTUAL TOUR CREATION
    public $selectedHouseModel;

    public $tourTitle;
    public $scenes = [];
    public $newSceneName = '';
    public $activeScene = 0;

    public $editingHotspot = null;

    // hotspot temp
    public $tempLabel = '';
    public $tempPitch = null;
    public $tempYaw = null;
    public $tempTargetScene = null;
    
    public $showHotspotForm = false;
    // public $hotspotX;
    // public $hotspotY;
    public $hotspotLabel;

    public $viewScenes = [];
    public $activeViewScene = 0;
    public $selectedTour = null;
    // VIRTUAL TOUR CREATION END

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

        // $this->houseModels = HouseModel::all();
        $this->houseModels = HouseModel::with([
            'virtualTour.scenes.hotspots'
        ])->get();

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
       $this->editImagePreview = $model->image
        ? asset('storage/' . $model->image)
        : null;
        
        $this->editModelHouseImage = null;

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

            if ($this->editModelHouseImage instanceof TemporaryUploadedFile) {
                
            // delete old image
                if (
                    $model->image &&
                    Storage::disk('public')->exists($model->image)
                ) {
                    Storage::disk('public')->delete($model->image);
                }

                $fileName = Str::slug($this->editModelName)
                    . '-'
                    . Str::uuid()
                    . '.'
                    . $this->editModelHouseImage->getClientOriginalExtension();

                $imagePath = $this->editModelHouseImage->storeAs(
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

    // CREATE VIRTUAL TOUR FUNCTIONS
    public function selectHouseModel($id)
    {
        $this->selectedHouseModel = HouseModel::findOrFail($id);
        $this->tourTitle = $this->selectedHouseModel->model_name . ' Tour';

        // auto initialize first scene
        $this->scenes = [];
        $this->activeScene = 0;
    }

    public function addScene()
    {
        $this->validate([
            'newSceneName' => 'required|string|max:255',
        ]);

        $this->scenes[] = [
            'name' => $this->newSceneName,
            'file' => null,
            'preview' => null,
            'hotspots' => []
        ];

        $this->newSceneName = '';
        $this->activeScene = count($this->scenes) - 1;
    }

    // public function saveHotspot()
    // {
    //     $this->validate([
    //         'tempLabel' => 'required|string|max:255',
    //         'tempTargetScene' => 'required|integer',
    //     ]);

    //     $data = [
    //         'pitch' => $this->tempPitch,
    //         'yaw' => $this->tempYaw,
    //         'label' => $this->tempLabel,
    //         'target_index' => (int) $this->tempTargetScene,
    //     ];

    //     // EDIT
    //     if ($this->editingHotspot !== null) {

    //         $this->scenes[$this->activeScene]['hotspots'][$this->editingHotspot] = $data;

    //     } else {

    //         $this->scenes[$this->activeScene]['hotspots'][] = $data;
    //     }

    //     $this->resetHotspotForm();
    // }

    public function saveHotspot()
    {
        $this->validate([
            'tempLabel' => 'required|string|max:255',
        ]);

        $this->scenes[$this->activeScene]['hotspots'][] = [
            'label' => $this->tempLabel,
            'pitch' => $this->tempPitch,
            'yaw' => $this->tempYaw,
            'target_index' => $this->tempTargetScene,
        ];

        $this->resetHotspotForm();
    }

    public function resetHotspotForm()
    {
        $this->reset([
            'tempLabel',
            'tempPitch',
            'tempYaw',
            'tempTargetScene',
        ]);

        $this->showHotspotForm = false;
    }

    public function editHotspot($index)
    {
        $hotspot = $this->scenes[$this->activeScene]['hotspots'][$index];

        $this->editingHotspot = $index;

        $this->tempPitch = $hotspot['pitch'];
        $this->tempYaw = $hotspot['yaw'];
        $this->tempLabel = $hotspot['label'];
        $this->tempTargetScene = $hotspot['target_index'];

        $this->showHotspotForm = true;
    }

    public function deleteHotspot($index)
    {
        unset($this->scenes[$this->activeScene]['hotspots'][$index]);

        $this->scenes[$this->activeScene]['hotspots'] =
            array_values($this->scenes[$this->activeScene]['hotspots']);
    }

    public function setActiveScene($index)
    {
        $this->activeScene = $index;

        if (!empty($this->scenes[$index]['preview'])) {
            $this->dispatch('load-panorama', [
                'image' => $this->scenes[$index]['preview']
            ]);
        } else {
            $this->dispatch('load-panorama', [
                'image' => null
            ]);
        }
    }

    public function updatedScenes($value, $key)
    {
        [$index, $field] = explode('.', $key);

        if ($field === 'file' && isset($this->scenes[$index]['file'])) {

            $this->validate([
                'scenes.*.file' => 'image|max:51200',
            ]);
            $this->scenes[$index]['preview'] =
                $this->scenes[$index]['file']->temporaryUrl();

             $this->dispatch('init-editor-scenes', [
                'scenes' => $this->scenes,
                'activeScene' => $this->activeScene
            ]);
        }
    }

    public function prepareHotspot($pitch, $yaw)
    {
        $this->tempPitch = $pitch;
        $this->tempYaw = $yaw;

        $this->tempLabel = '';
        $this->tempTargetScene = null;

        $this->showHotspotForm = true;
    }

    #[On('open-hotspot')]
    public function openHotspot($pitch, $yaw, $scene): void
    {
        Log::info('🔥 openHotspot TRIGGERED', [
            'pitch' => $pitch,
            'yaw' => $yaw,
            'scene' => $scene,
        ]);

        $this->tempPitch = (float) $pitch;
        $this->tempYaw = (float) $yaw;
        $this->activeScene = (int) $scene;

        $this->showHotspotForm = true;

        Log::info('🔥 hotspot form state updated', [
            'showHotspotForm' => $this->showHotspotForm,
            'activeScene' => $this->activeScene,
        ]);
    }

    // =====================
    // SAVE TOUR
    // =====================
    public function saveTour()
    {
        
        $this->validate([
            'tourTitle' => 'required|string',
            'selectedHouseModel.id' => 'required',
        ]);

        $tour = VirtualTour::create([
            'title' => $this->tourTitle,
            'house_model_id' => $this->selectedHouseModel->id,
        ]);

        // 🔥 STORE CREATED SCENES
        $createdScenes = [];

        /*
        |--------------------------------------------------------------------------
        | STEP 1: CREATE ALL SCENES FIRST
        |--------------------------------------------------------------------------
        */

        foreach ($this->scenes as $i => $scene) {

            if (!$scene['file']) continue;

            $path = $scene['file']->store('virtual-tour', 'public');

            $createdScenes[$i] = TourScene::create([
                'virtual_tour_id' => $tour->id,
                'image' => $path,
                'name' => $scene['name'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2: CREATE HOTSPOTS
        |--------------------------------------------------------------------------
        */

        foreach ($this->scenes as $i => $scene) {

            if (!isset($createdScenes[$i])) continue;

            $dbScene = $createdScenes[$i];

            foreach ($scene['hotspots'] as $hotspot) {

                $targetIndex = $hotspot['target_index'] ?? null;

                $targetSceneId = null;

                if ($targetIndex !== null && isset($createdScenes[$targetIndex])) {
                    $targetSceneId = $createdScenes[$targetIndex]->id;
                }

                TourHotSpot::create([
                    'scene_id' => $dbScene->id,
                    'label' => $hotspot['label'],
                    'pitch' => $hotspot['pitch'],
                    'yaw' => $hotspot['yaw'],
                    'target_scene_id' => $targetSceneId,
                ]);
            }
        }

        $this->reset([
            'tourTitle',
            'scenes',
            'activeScene',
            'selectedHouseModel'
        ]);

        $this->dispatch('reload');

        return redirect()->back();
    }

    public function viewHouseTour($id)
    {
        $house = HouseModel::with('virtualTour.scenes.hotspots')->findOrFail($id);

        $tour = $house->virtualTour;

        if (!$tour || !$tour->scenes || $tour->scenes->isEmpty()) {
            $this->viewScenes = []; // important
            $this->dispatch('open-viewer-modal');
            return;
        }

        $this->viewScenes = $tour->scenes->map(fn ($scene) => [
            'id' => $scene->id,
            'name' => $scene->name,
            'image' => asset('storage/' . $scene->image),
            'hotspots' => $scene->hotspots->map(fn ($h) => [
                'pitch' => $h->pitch,
                'yaw' => $h->yaw,
                'label' => $h->label,
                'target_scene_id' => $h->target_scene_id,
            ])->toArray(),
        ])->values()->toArray();

        $this->dispatch('open-viewer-modal');
    }
    
    public function setViewScene($sceneId)
    {
        $this->dispatch('switch-view-scene', sceneId: $sceneId);
    }

    #[On('go-to-scene')]
    public function goToScene($scene_id)
    {
        $index = collect($this->viewScenes)
            ->search(fn ($s) => $s['id'] == $scene_id);

        if ($index !== false) {
            $this->setViewScene($index);
        }
    }

    // CREATE VIRTUAL TOUR FUNCTIONS END

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

    public function reloadWeb(){

        $this->dispatch('reload');
        return redirect()->back();

    }

    public function render()
    {
        return view('livewire.fil-pages.map-management-page', [
            'typeColors' => $this->typeColors,
        ]);
    }
}
