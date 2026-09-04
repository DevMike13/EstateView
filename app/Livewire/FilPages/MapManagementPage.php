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
        'floorArea' => 'required|numeric|min:0|max:999999.99',
        'price' => 'required|numeric|min:0|max:9999999999.99',
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
        'editFloorArea' => 'required|numeric|min:0|max:999999.99',
        'editPrice' => 'required|numeric|min:0|max:9999999999.99',
    ];
    // MODEL HOUSE END

    protected $messages = [
        // Create
        'floorArea.max' =>
            'The floor area cannot exceed 999,999.99 sqm.',
        'price.max' =>
            'The price cannot exceed ₱9,999,999,999.99.',
        // Edit
        'editFloorArea.max' =>
            'The floor area cannot exceed 999,999.99 sqm.',
        'editPrice.max' =>
            'The price cannot exceed ₱9,999,999,999.99.',
    ];

    // VIRTUAL TOUR CREATION
    public $editingSceneIndex = null;
    public $editingSceneName = '';
    public bool $showEditSceneModal = false;

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
    public bool $isEditingTour = false;

    /*
    |--------------------------------------------------------------------------
    | DEFERRED SCENE DELETION
    |--------------------------------------------------------------------------
    |
    | When a user deletes a scene while editing a tour, we only remove it
    | from the local $scenes array (UI). The actual database scene,
    | its hotspots, and its stored image are only permanently deleted
    | when the user clicks "Update Tour".
    |
    */
    public $deletedSceneIds = [];
    // VIRTUAL TOUR CREATION END

    public $typeColors = [
        'Playground & Community Amenities' => '#f2b879',
        'Model House' => '#c8c9c3',
        'Lot Only' => '#c4e0b7',
        'House & Lot' => '#f8e89c',
        'Internal Road' => '#ebebeb',
        'Sold' => '#e9b4ae',
    ];

    public function mount()
    {
        $this->map = Map::with('lots')->first();
        $this->lots = $this->map?->lots ?? [];

        // $this->houseModels = HouseModel::all();
        $this->refreshHouseModels();

        $this->generateLotCounts();
    }

    public function createModelHouse()
    {
        // Remove currency formatting before validation
        if ($this->price !== null && $this->price !== '') {
            $this->price = str_replace(',', '', $this->price);
        }

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

        HouseModel::create([
            'image' => $imagePath,
            'virtual_tour_url' => $this->virtualTourUrl,
            'model_name' => $this->modelName,
            'bedrooms' => $this->bedroomsCount,
            'bathrooms' => $this->bathroomsCount,
            'floor_area' => $this->floorArea,
            'price' => $this->price,
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
        $model = HouseModel::find($id);

        /*
        |--------------------------------------------------------------------------
        | ALREADY DELETED BY ANOTHER USER
        |--------------------------------------------------------------------------
        */

        if (! $model) {

            $this->refreshHouseModels();

            Notification::make()
                ->title('Already Removed')
                ->body(
                    'This model house was already removed by another admin or staff member.'
                )
                ->warning()
                ->send();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE IMAGE
        |--------------------------------------------------------------------------
        */

        if (
            $model->image &&
            Storage::disk('public')->exists($model->image)
        ) {
            Storage::disk('public')->delete(
                $model->image
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE MODEL
        |--------------------------------------------------------------------------
        */

        $model->delete();

        /*
        |--------------------------------------------------------------------------
        | REFRESH LIST
        |--------------------------------------------------------------------------
        */

        $this->refreshHouseModels();

        Notification::make()
            ->title('Deleted!')
            ->body('Model house removed successfully.')
            ->success()
            ->send();
    }

    public function deleteModelHouseConfirmation($id, $modelName){
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => "Do you want to remove this model Name: ".  html_entity_decode('<span class="text-red-600 underline">' . $modelName . '</span>') . " ?",
            'acceptLabel' => 'Yes',
            'method'      => 'deleteModelHouse',
            'icon'        => 'error',
            'params'      => $id
        ]);
    }

    // CREATE VIRTUAL TOUR FUNCTIONS
    public function selectHouseModel($id)
    {
        $this->selectedHouseModel = HouseModel::findOrFail($id);

        $this->selectedTour = null;
        $this->isEditingTour = false;

        $this->tourTitle =
            $this->selectedHouseModel->model_name . ' Tour';

        $this->scenes = [];
        $this->newSceneName = '';
        $this->activeScene = 0;
        $this->deletedSceneIds = [];

        $this->resetHotspotForm();
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT EXISTING VIRTUAL TOUR
    |--------------------------------------------------------------------------
    */

    public function editVirtualTour($id)
    {
        $house = HouseModel::with([
            'virtualTour.scenes.hotspots',
        ])->findOrFail($id);

        $tour = $house->virtualTour;

        /*
        * Safety fallback.
        */
        if (! $tour) {
            $this->selectHouseModel($id);
            return;
        }

        $this->selectedHouseModel = $house;

        /*
        * Store the ID rather than the entire Eloquent model.
        */
        $this->selectedTour = $tour->id;

        $this->isEditingTour = true;

        $this->tourTitle = $tour->title;

        $this->deletedSceneIds = [];

        /*
        |--------------------------------------------------------------------------
        | Scene Database ID -> Editor Index
        |--------------------------------------------------------------------------
        |
        | The database hotspot stores target_scene_id.
        | Your editor uses target_index.
        |
        */

        $sceneIdToIndex = $tour->scenes
            ->values()
            ->mapWithKeys(function ($scene, $index) {

                return [
                    $scene->id => $index,
                ];

            })
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Load Existing Scenes
        |--------------------------------------------------------------------------
        */

        $this->scenes = $tour->scenes
            ->values()
            ->map(function ($scene) use ($sceneIdToIndex) {

                return [
                    /*
                    * Keep existing database scene ID.
                    */
                    'id' => $scene->id,

                    'name' => $scene->name,

                    /*
                    * No replacement upload yet.
                    */
                    'file' => null,

                    /*
                    * Existing image can immediately be displayed
                    * in the Pannellum editor.
                    */
                    'preview' => asset(
                        'storage/' . $scene->image
                    ),

                    /*
                    * Keep original storage path.
                    */
                    'existing_image' => $scene->image,

                    /*
                    * Existing hotspot records.
                    */
                    'hotspots' => $scene->hotspots
                        ->map(function ($hotspot) use ($sceneIdToIndex) {

                            return [
                                'id' =>
                                    $hotspot->id,

                                'label' =>
                                    $hotspot->label,

                                'pitch' =>
                                    (float) $hotspot->pitch,

                                'yaw' =>
                                    (float) $hotspot->yaw,

                                /*
                                * Convert database scene ID
                                * into editor array index.
                                */
                                'target_index' =>
                                    $hotspot->target_scene_id !== null
                                        ? (
                                            $sceneIdToIndex[
                                                $hotspot->target_scene_id
                                            ] ?? null
                                        )
                                        : null,
                            ];

                        })
                        ->values()
                        ->toArray(),
                ];

            })
            ->toArray();

        $this->activeScene = 0;

        $this->newSceneName = '';

        $this->resetHotspotForm();
    }


    /*
    |--------------------------------------------------------------------------
    | ADD SCENE
    |--------------------------------------------------------------------------
    */

    public function addScene()
    {
        $this->validate([
            'newSceneName' => 'required|string|max:255',
        ]);

        $this->scenes[] = [
            'name' => $this->newSceneName,
            'file' => null,
            'preview' => null,
            'hotspots' => [],
        ];

        $this->newSceneName = '';

        $this->activeScene =
            count($this->scenes) - 1;
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT SCENE NAME
    |--------------------------------------------------------------------------
    */

    public function openEditScene($index)
    {
        if (! isset($this->scenes[$index])) {
            return;
        }

        $this->editingSceneIndex = $index;

        $this->editingSceneName =
            $this->scenes[$index]['name'];

        $this->showEditSceneModal = true;
    }


    public function updateSceneName()
    {
        $this->validate([
            'editingSceneName' =>
                'required|string|max:255',
        ]);

        if (
            $this->editingSceneIndex === null
            || ! isset(
                $this->scenes[
                    $this->editingSceneIndex
                ]
            )
        ) {
            return;
        }

        $this->scenes[
            $this->editingSceneIndex
        ]['name'] =
            trim($this->editingSceneName);

        $this->showEditSceneModal = false;

        $this->reset([
            'editingSceneIndex',
            'editingSceneName',
        ]);

        Notification::make()
            ->title('Scene Updated')
            ->body(
                'Scene name updated successfully.'
            )
            ->success()
            ->send();
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE SCENE (UI ONLY — DEFERRED PERMANENT DELETE)
    |--------------------------------------------------------------------------
    |
    | This no longer touches the database. It only removes the scene from
    | the local $scenes array so the change is reflected in the UI.
    |
    | If the scene already exists in the database (has an 'id'), its ID is
    | stored in $deletedSceneIds. The actual database row, its hotspots,
    | and its stored image are only removed once the user clicks
    | "Update Tour" (see updateTour()).
    |
    */

    public function deleteScene($index)
    {
        if (! isset($this->scenes[$index])) {
            return;
        }

        $scene = $this->scenes[$index];

        /*
        |--------------------------------------------------------------------------
        | MARK EXISTING DATABASE SCENE FOR DELETION
        |--------------------------------------------------------------------------
        */

        if (! empty($scene['id'])) {
            $this->deletedSceneIds[] = $scene['id'];
        }

        /*
        |--------------------------------------------------------------------------
        | REMOVE FROM LIVEWIRE SCENES ARRAY
        |--------------------------------------------------------------------------
        */

        unset($this->scenes[$index]);

        $this->scenes = array_values($this->scenes);

        /*
        |--------------------------------------------------------------------------
        | FIX HOTSPOT TARGET INDEXES
        |--------------------------------------------------------------------------
        |
        | Because target_index uses the array position:
        |
        | 0 = Entrance
        | 1 = Living Area
        | 2 = Kitchen
        |
        | deleting scene 1 means scene 2 becomes scene 1.
        |
        */

        foreach ($this->scenes as $sceneIndex => $currentScene) {

            $hotspots = [];

            foreach ($currentScene['hotspots'] ?? [] as $hotspot) {

                $targetIndex = $hotspot['target_index'] ?? null;

                /*
                * Remove hotspot pointing to deleted scene.
                */
                if (
                    $targetIndex !== null &&
                    (int) $targetIndex === (int) $index
                ) {
                    continue;
                }

                /*
                * Shift indexes after deleted scene.
                */
                if (
                    $targetIndex !== null &&
                    (int) $targetIndex > (int) $index
                ) {
                    $hotspot['target_index'] =
                        (int) $targetIndex - 1;
                }

                $hotspots[] = $hotspot;
            }

            $this->scenes[$sceneIndex]['hotspots'] =
                $hotspots;
        }

        /*
        |--------------------------------------------------------------------------
        | FIX ACTIVE SCENE
        |--------------------------------------------------------------------------
        */

        if (count($this->scenes) === 0) {

            $this->activeScene = 0;

        } elseif ($this->activeScene >= count($this->scenes)) {

            $this->activeScene =
                count($this->scenes) - 1;

        } elseif ($this->activeScene > $index) {

            $this->activeScene--;
        }

        $this->resetHotspotForm();

        /*
        |--------------------------------------------------------------------------
        | REFRESH PANORAMA EDITOR
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'init-editor-scenes',
            [
                'scenes' => $this->scenes,
                'activeScene' => $this->activeScene,
            ]
        );

        // Notification::make()
        //     ->title('Scene Removed')
        //     ->body('The scene has been removed from this tour. Click "Update Tour" to save this change permanently.')
        //     ->success()
        //     ->send();
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE / UPDATE HOTSPOT
    |--------------------------------------------------------------------------
    */

    public function saveHotspot()
    {
        $this->validate([
            'tempLabel' =>
                'required|string|max:255',

            'tempTargetScene' =>
                'required|integer',
        ]);

        $data = [
            'label' =>
                $this->tempLabel,

            'pitch' =>
                $this->tempPitch,

            'yaw' =>
                $this->tempYaw,

            'target_index' =>
                (int) $this->tempTargetScene,
        ];

        /*
        |--------------------------------------------------------------------------
        | Editing Existing Hotspot
        |--------------------------------------------------------------------------
        */

        if ($this->editingHotspot !== null) {

            /*
            * Keep original DB ID when editing
            * an existing hotspot.
            */
            $existingId =
                $this->scenes[
                    $this->activeScene
                ]['hotspots'][
                    $this->editingHotspot
                ]['id'] ?? null;

            if ($existingId) {
                $data['id'] = $existingId;
            }

            $this->scenes[
                $this->activeScene
            ]['hotspots'][
                $this->editingHotspot
            ] = $data;

        } else {

            /*
            |--------------------------------------------------------------------------
            | New Hotspot
            |--------------------------------------------------------------------------
            */

            $this->scenes[
                $this->activeScene
            ]['hotspots'][] = $data;
        }

        $this->resetHotspotForm();
    }


    public function resetHotspotForm()
    {
        $this->reset([
            'tempLabel',
            'tempPitch',
            'tempYaw',
            'tempTargetScene',
            'editingHotspot',
        ]);

        $this->showHotspotForm = false;
    }


    public function editHotspot($index)
    {
        $hotspot =
            $this->scenes[
                $this->activeScene
            ]['hotspots'][$index];

        $this->editingHotspot =
            $index;

        $this->tempPitch =
            $hotspot['pitch'];

        $this->tempYaw =
            $hotspot['yaw'];

        $this->tempLabel =
            $hotspot['label'];

        $this->tempTargetScene =
            $hotspot['target_index'];

        $this->showHotspotForm = true;
    }


    public function deleteHotspot($index)
    {
        unset(
            $this->scenes[
                $this->activeScene
            ]['hotspots'][$index]
        );

        $this->scenes[
            $this->activeScene
        ]['hotspots'] =
            array_values(
                $this->scenes[
                    $this->activeScene
                ]['hotspots']
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVE SCENE
    |--------------------------------------------------------------------------
    */

    public function setActiveScene($index)
    {
        $this->activeScene = $index;

        if (
            ! empty(
                $this->scenes[
                    $index
                ]['preview']
            )
        ) {

            $this->dispatch(
                'load-panorama',
                [
                    'image' =>
                        $this->scenes[
                            $index
                        ]['preview']
                ]
            );

        } else {

            $this->dispatch(
                'load-panorama',
                [
                    'image' => null
                ]
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PANORAMA UPLOAD
    |--------------------------------------------------------------------------
    */

    public function updatedScenes($value, $key)
{
    [$index, $field] = explode('.', $key);

    if (
        $field === 'file'
        && isset($this->scenes[$index]['file'])
    ) {

        $this->validate([
            "scenes.$index.file" => 'image|max:51200',
        ]);

        $this->scenes[$index]['preview'] =
            $this->scenes[$index]['file']->temporaryUrl();

        $this->dispatch('init-editor-scenes', [
            'scenes' => $this->scenes,
            'activeScene' => $this->activeScene,
        ]);
    }
}

    public function removeScenePanorama()
{
    if (! isset($this->scenes[$this->activeScene])) {
        return;
    }

    $this->scenes[$this->activeScene]['file'] = null;
    $this->scenes[$this->activeScene]['preview'] = null;

    $this->dispatch('init-editor-scenes', [
        'scenes' => $this->scenes,
        'activeScene' => $this->activeScene,
    ]);
}


    /*
    |--------------------------------------------------------------------------
    | PREPARE HOTSPOT
    |--------------------------------------------------------------------------
    */

    public function prepareHotspot(
        $pitch,
        $yaw
    ) {
        $this->tempPitch = $pitch;
        $this->tempYaw = $yaw;

        $this->tempLabel = '';
        $this->tempTargetScene = null;

        $this->editingHotspot = null;

        $this->showHotspotForm = true;
    }


    #[On('open-hotspot')]
    public function openHotspot(
        $pitch,
        $yaw,
        $scene
    ): void {

        Log::info(
            '🔥 openHotspot TRIGGERED',
            [
                'pitch' => $pitch,
                'yaw' => $yaw,
                'scene' => $scene,
            ]
        );

        $this->tempPitch =
            (float) $pitch;

        $this->tempYaw =
            (float) $yaw;

        $this->activeScene =
            (int) $scene;

        $this->editingHotspot = null;

        $this->showHotspotForm = true;

        Log::info(
            '🔥 hotspot form state updated',
            [
                'showHotspotForm' =>
                    $this->showHotspotForm,

                'activeScene' =>
                    $this->activeScene,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE NEW TOUR
    |--------------------------------------------------------------------------
    */

    public function saveTour()
    {
        $this->validate([
            'tourTitle' =>
                'required|string',

            'selectedHouseModel.id' =>
                'required',
        ]);

        /*
    |--------------------------------------------------------------------------
    | VALIDATE: EVERY SCENE MUST HAVE A PANORAMA IMAGE
    |--------------------------------------------------------------------------
    */

    foreach ($this->scenes as $scene) {

        $hasFile =
            isset($scene['file'])
            && $scene['file'] instanceof TemporaryUploadedFile;

        if (! $hasFile) {

            Notification::make()
                ->title('Missing Panorama')
                ->body(
                    'Scene "' . ($scene['name'] ?: 'Untitled') . '" has no panorama image uploaded. Please upload one before saving the tour.'
                )
                ->danger()
                ->send();

            return;
        }
    }
        /*
        * Prevent accidental duplicate tours.
        */
        $existingTour =
            VirtualTour::where(
                'house_model_id',
                $this->selectedHouseModel->id
            )->first();

        if ($existingTour) {

            $this->selectedTour =
                $existingTour->id;

            $this->isEditingTour =
                true;

            return;
        }

        $tour = VirtualTour::create([
            'title' =>
                $this->tourTitle,

            'house_model_id' =>
                $this->selectedHouseModel->id,
        ]);

        $createdScenes = [];

        /*
        |--------------------------------------------------------------------------
        | STEP 1: CREATE ALL SCENES
        |--------------------------------------------------------------------------
        */

        foreach (
            $this->scenes
            as $i => $scene
        ) {

            if (! $scene['file']) {
                continue;
            }

            $path =
                $scene['file']
                    ->store(
                        'virtual-tour',
                        'public'
                    );

            $createdScenes[$i] =
                TourScene::create([
                    'virtual_tour_id' =>
                        $tour->id,

                    'image' =>
                        $path,

                    'name' =>
                        $scene['name'],
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2: CREATE HOTSPOTS
        |--------------------------------------------------------------------------
        */

        foreach (
            $this->scenes
            as $i => $scene
        ) {

            if (
                ! isset(
                    $createdScenes[$i]
                )
            ) {
                continue;
            }

            $dbScene =
                $createdScenes[$i];

            foreach (
                $scene['hotspots'] ?? []
                as $hotspot
            ) {

                $targetIndex =
                    $hotspot[
                        'target_index'
                    ] ?? null;

                $targetSceneId =
                    null;

                if (
                    $targetIndex !== null
                    && isset(
                        $createdScenes[
                            $targetIndex
                        ]
                    )
                ) {

                    $targetSceneId =
                        $createdScenes[
                            $targetIndex
                        ]->id;
                }

                TourHotSpot::create([
                    'scene_id' =>
                        $dbScene->id,

                    'label' =>
                        $hotspot['label'],

                    'pitch' =>
                        $hotspot['pitch'],

                    'yaw' =>
                        $hotspot['yaw'],

                    'target_scene_id' =>
                        $targetSceneId,
                ]);
            }
        }

        Notification::make()
            ->title(
                'Virtual Tour Created!'
            )
            ->body(
                'Virtual tour created successfully.'
            )
            ->success()
            ->send();

        $this->reset([
            'tourTitle',
            'scenes',
            'activeScene',
            'selectedHouseModel',
            'selectedTour',
            'isEditingTour',
            'newSceneName',
            'deletedSceneIds',
        ]);

        /*
        * Refresh cards so Create immediately
        * changes to Edit.
        */
        $this->houseModels =
            HouseModel::with([
                'virtualTour.scenes.hotspots'
            ])->get();
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE EXISTING TOUR
    |--------------------------------------------------------------------------
    */

    public function updateTour()
    {
        $this->validate([
            'tourTitle' =>
                'required|string',

            'selectedHouseModel.id' =>
                'required',
        ]);

        /*
        |--------------------------------------------------------------------------
        | VALIDATE: EVERY SCENE MUST HAVE A PANORAMA IMAGE
        |--------------------------------------------------------------------------
        |
        | Existing scenes may use their already-saved panorama.
        | New / replaced scenes may contain a TemporaryUploadedFile.
        |
        */

        if (empty($this->scenes)) {

            Notification::make()
                ->title('Missing Scene')
                ->body(
                    'The virtual tour must contain at least one scene.'
                )
                ->danger()
                ->send();

            return;
        }

        foreach ($this->scenes as $scene) {

            $hasNewFile =
                isset($scene['file'])
                && $scene['file']
                    instanceof TemporaryUploadedFile;

            $hasExistingPanorama =
                ! empty($scene['preview']);

            if (
                ! $hasNewFile
                && ! $hasExistingPanorama
            ) {

                Notification::make()
                    ->title('Missing Panorama')
                    ->body(
                        'Scene "'
                        . ($scene['name'] ?: 'Untitled')
                        . '" has no panorama image. Please upload one before updating the tour.'
                    )
                    ->danger()
                    ->send();

                return;
            }
        }

        if (! $this->selectedTour) {

            Notification::make()
                ->title('Unable to Update')
                ->body(
                    'The virtual tour could not be found.'
                )
                ->danger()
                ->send();

            return;
        }

        $tour =
            VirtualTour::with([
                'scenes.hotspots',
            ])->findOrFail(
                $this->selectedTour
            );

        /*
        |--------------------------------------------------------------------------
        | PERMANENTLY DELETE SCENES MARKED FOR DELETION
        |--------------------------------------------------------------------------
        |
        | These scenes were only removed from the UI when the user clicked
        | the delete button. Now that "Update Tour" was clicked, we remove
        | them (and their hotspots + stored image) from the database.
        |
        */

        foreach ($this->deletedSceneIds as $deletedId) {

            $dbSceneToDelete = TourScene::find($deletedId);

            if ($dbSceneToDelete) {

                if (
                    $dbSceneToDelete->image &&
                    Storage::disk('public')->exists($dbSceneToDelete->image)
                ) {
                    Storage::disk('public')->delete($dbSceneToDelete->image);
                }

                /*
                * Delete hotspots that belong to this scene.
                */
                TourHotSpot::where(
                    'scene_id',
                    $dbSceneToDelete->id
                )->delete();

                /*
                * Delete hotspots from other scenes
                * that point TO this scene.
                */
                TourHotSpot::where(
                    'target_scene_id',
                    $dbSceneToDelete->id
                )->delete();

                $dbSceneToDelete->delete();
            }
        }

        $this->deletedSceneIds = [];

        /*
        |--------------------------------------------------------------------------
        | UPDATE TOUR
        |--------------------------------------------------------------------------
        */

        $tour->update([
            'title' =>
                $this->tourTitle,
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE / CREATE SCENES
        |--------------------------------------------------------------------------
        */

        $savedScenes = [];

        foreach (
            $this->scenes
            as $index => $scene
        ) {

            /*
            |--------------------------------------------------------------------------
            | Existing Scene
            |--------------------------------------------------------------------------
            */

            if (
                ! empty(
                    $scene['id']
                )
            ) {

                $dbScene =
                    TourScene::query()
                        ->where(
                            'virtual_tour_id',
                            $tour->id
                        )
                        ->findOrFail(
                            $scene['id']
                        );

                $imagePath =
                    $dbScene->image;

                /*
                * Replace panorama only if
                * a new file was uploaded.
                */
                if (
                    isset($scene['file'])
                    && $scene['file']
                        instanceof TemporaryUploadedFile
                ) {

                    if (
                        $dbScene->image
                        && Storage::disk(
                            'public'
                        )->exists(
                            $dbScene->image
                        )
                    ) {

                        Storage::disk(
                            'public'
                        )->delete(
                            $dbScene->image
                        );
                    }

                    $imagePath =
                        $scene['file']
                            ->store(
                                'virtual-tour',
                                'public'
                            );
                }

                $dbScene->update([
                    'name' =>
                        $scene['name'],

                    'image' =>
                        $imagePath,
                ]);

                $savedScenes[
                    $index
                ] = $dbScene;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | New Scene Added During Edit
            |--------------------------------------------------------------------------
            */

            if (
                empty($scene['file'])
                || ! (
                    $scene['file']
                    instanceof
                        TemporaryUploadedFile
                )
            ) {
                continue;
            }

            $path =
                $scene['file']
                    ->store(
                        'virtual-tour',
                        'public'
                    );

            $savedScenes[
                $index
            ] =
                TourScene::create([
                    'virtual_tour_id' =>
                        $tour->id,

                    'image' =>
                        $path,

                    'name' =>
                        $scene['name'],
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | REBUILD HOTSPOTS
        |--------------------------------------------------------------------------
        |
        | This keeps target_scene_id synchronized with
        | the current editor target_index values.
        |
        */

        foreach (
            $savedScenes
            as $dbScene
        ) {

            TourHotSpot::where(
                'scene_id',
                $dbScene->id
            )->delete();
        }

        foreach (
            $this->scenes
            as $index => $scene
        ) {

            if (
                ! isset(
                    $savedScenes[
                        $index
                    ]
                )
            ) {
                continue;
            }

            $dbScene =
                $savedScenes[
                    $index
                ];

            foreach (
                $scene['hotspots'] ?? []
                as $hotspot
            ) {

                $targetIndex =
                    $hotspot[
                        'target_index'
                    ] ?? null;

                $targetSceneId =
                    null;

                if (
                    $targetIndex !== null
                    && isset(
                        $savedScenes[
                            $targetIndex
                        ]
                    )
                ) {

                    $targetSceneId =
                        $savedScenes[
                            $targetIndex
                        ]->id;
                }

                TourHotSpot::create([
                    'scene_id' =>
                        $dbScene->id,

                    'label' =>
                        $hotspot['label'],

                    'pitch' =>
                        $hotspot['pitch'],

                    'yaw' =>
                        $hotspot['yaw'],

                    'target_scene_id' =>
                        $targetSceneId,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        Notification::make()
        ->title('Virtual Tour Updated!')
        ->body('Virtual tour updated successfully.')
        ->success()
        ->send();

    $this->houseModels = HouseModel::with([
        'virtualTour.scenes.hotspots'
    ])->get();

    /*
    * Reload the updated tour into the editor.
    */
    $this->editVirtualTour(
        $this->selectedHouseModel->id
    );
    }


    /*
    |--------------------------------------------------------------------------
    | VIEW TOUR
    |--------------------------------------------------------------------------
    */

    public function viewHouseTour($id)
    {
        $house =
            HouseModel::with(
                'virtualTour.scenes.hotspots'
            )->findOrFail($id);

        $tour =
            $house->virtualTour;

        if (
            ! $tour
            || ! $tour->scenes
            || $tour->scenes->isEmpty()
        ) {

            $this->viewScenes = [];

            $this->dispatch(
                'open-viewer-modal'
            );

            return;
        }

        $this->viewScenes =
            $tour->scenes
                ->map(
                    fn ($scene) => [
                        'id' =>
                            $scene->id,

                        'name' =>
                            $scene->name,

                        'image' =>
                            asset(
                                'storage/'
                                . $scene->image
                            ),

                        'hotspots' =>
                            $scene->hotspots
                                ->map(
                                    fn ($h) => [
                                        'pitch' =>
                                            $h->pitch,

                                        'yaw' =>
                                            $h->yaw,

                                        'label' =>
                                            $h->label,

                                        'target_scene_id' =>
                                            $h->target_scene_id,
                                    ]
                                )
                                ->toArray(),
                    ]
                )
                ->values()
                ->toArray();

        $this->dispatch(
            'open-viewer-modal'
        );
    }


    public function setViewScene($sceneId)
    {
        $this->dispatch(
            'switch-view-scene',
            sceneId: $sceneId
        );
    }


    #[On('go-to-scene')]
    public function goToScene($scene_id)
    {
        $index =
            collect(
                $this->viewScenes
            )->search(
                fn ($s) =>
                    $s['id'] == $scene_id
            );

        if ($index !== false) {
            $this->setViewScene(
                $index
            );
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

    public function refreshHouseModels(): void
    {
        $this->houseModels = HouseModel::with([
            'virtualTour.scenes.hotspots'
        ])
            ->latest()
            ->get();
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