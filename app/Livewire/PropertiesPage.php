<?php

namespace App\Livewire;

use App\Models\HouseModel;
use Livewire\Attributes\On;
use Livewire\Component;

class PropertiesPage extends Component
{
    public $houseModels = [];

    public $viewScenes = [];

    public function mount()
    {
        $this->houseModels = HouseModel::with([
            'virtualTour.scenes.hotspots'
        ])->get();
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

    public function reloadWeb(){

        $this->dispatch('reload');
        return redirect()->back();

    }

    public function render()
    {
        return view('livewire.properties-page');
    }
}
