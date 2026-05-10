<div>

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
        @foreach($lotCounts as $type => $count)

            @php
                $color = $typeColors[$type] ?? '#ccc';
            @endphp

            <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-100">
                
                <div class="flex gap-5 items-center">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4" style="background-color: {{ $color }}20; color: {{ $color }}">

                        {{-- House & Lot --}}
                        @if($type === 'House & Lot')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>

                        {{-- Model House --}}
                        @elseif($type === 'Model House')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819" />
                            </svg>

                        {{-- Lot Only --}}
                        @elseif($type === 'Lot Only')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>

                        {{-- Default Icon --}}
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v18h18" />
                            </svg>
                        @endif
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900">
                        {{ $count }}
                    </h2>
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    {{ $type }}
                </p>
            </div>

        @endforeach
    </div>

    {{-- MODEL HOUSE --}}
    <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-100 mt-10">
        <div class="w-full h-auto flex justify-between items-center mb-5">
            <div>
                <h2 class="text-lg font-semibold">House Models</h2>
                <p class="text-sm text-gray-500">Available house designs for clients</p>
            </div>
            <div class="flex justify-end">
                <x-button icon="plus" primary label="Add Model" x-on:click="$openModal('newModelHouse')" />
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-4">
            @foreach($houseModels as $model)
                <div class="flex flex-col bg-card border border-card-line shadow-2xs rounded-xl">
                    <img 
                        class="w-full h-48 object-cover rounded-t-xl" 
                        src="{{ $model->image 
                        ? asset('storage/' . $model->image) 
                        : 'https://images.unsplash.com/photo-1680868543815-b8666dba60f7' }}" 
                        alt="{{ $model->model_name }}"
                    >
                    <div class="p-4">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold text-foreground">
                                {{ $model->model_name }}
                            </h3>

                            <div>
                                <x-button.circle xs onclick="$openModal('editModelHouse')" wire:click="getSelectedModelHouse({{$model->id}})" icon="pencil" />
                                <x-button.circle 
                                    xs 
                                    wire:click="deleteModelHouseConfirmation({{$model->id}}, '{{$model->model_name}}')" 
                                    negative icon="trash" 
                                />
                            </div>
                        </div>
                       
                        <p class="my-2 text-sm text-muted-foreground-1">
                            {{ $model->floor_area }} sqm
                        </p>

                        <div class="flex justify-start items-center gap-3">
                            <div class="flex items-center gap-1 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    viewBox="0 0 512 512" 
                                    class="w-4 h-4 text-gray-500">

                                    <path d="M384 240H96V136a40.12 40.12 0 0140-40h240a40.12 40.12 0 0140 40v104zM48 416V304a64.19 64.19 0 0164-64h288a64.19 64.19 0 0164 64v112" 
                                        fill="none" stroke="currentColor" stroke-linecap="round" 
                                        stroke-linejoin="round" stroke-width="32"/>

                                    <path d="M48 416v-8a24.07 24.07 0 0124-24h368a24.07 24.07 0 0124 24v8M112 240v-16a32.09 32.09 0 0132-32h80a32.09 32.09 0 0132 32v16M256 240v-16a32.09 32.09 0 0132-32h80a32.09 32.09 0 0132 32v16" 
                                        fill="none" stroke="currentColor" stroke-linecap="round" 
                                        stroke-linejoin="round" stroke-width="32"/>
                                </svg>

                                <span class="text-xs">{{ $model->bedrooms }} Bedrooms</span>
                            </div>
                            •
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" 
                                    viewBox="0 0 512 512" 
                                    class="w-4 h-4 text-gray-500">

                                    <path d="M96 192V96a32 32 0 0132-32h256a32 32 0 0132 32v96" 
                                        fill="none" stroke="currentColor" stroke-linecap="round" 
                                        stroke-linejoin="round" stroke-width="32"/>

                                    <path d="M64 224h384v64a96 96 0 01-96 96H160a96 96 0 01-96-96v-64z" 
                                        fill="none" stroke="currentColor" stroke-linejoin="round" 
                                        stroke-width="32"/>

                                    <path d="M112 384v48M400 384v48M160 224v-32M352 224v-32" 
                                        fill="none" stroke="currentColor" stroke-linecap="round" 
                                        stroke-linejoin="round" stroke-width="32"/>

                                </svg>

                                <span class="text-xs">{{ $model->bathrooms }} Bathrooms</span>
                            </div>
                        </div>
                        
                        <hr class="my-5">

                        <p class="text-muted-foreground-1 font-semibold text-lg">
                            ₱{{ number_format($model->price, 2) }}
                        </p>
                        
                    </div>

                    <x-button 
                        class="my-5 mx-auto w-[80%]"
                        icon="home" 
                        rounded 
                        positive 
                        label="Create Virtual Tour" 
                        wire:click="selectHouseModel({{ $model->id }})"
                        x-on:click="$openModal('createVirtualTour')"
                    />

                    <x-button 
                        class="my-5 mx-auto w-[80%]"
                        icon="home" 
                        rounded 
                        positive 
                        label="Show Tour" 
                        wire:click="viewHouseTour({{ $model->id }})"
                        x-on:click="$openModal('viewTour')"
                    />
                </div>
            @endforeach
        </div>

        {{-- CREATE MODEL HOUSE --}}
        <x-modal blur name="newModelHouse" persistent align="center" max-width="xl">
            <form wire:submit.prevent="createModelHouse" class="w-full">
                <x-card title="Create New Model House">
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-2">
                            360° Virtual Tour Image
                        </label>

                        <x-filepond::upload
                            wire:model="modelHouseImage"
                            :accepted-file-types="['image/png', 'image/jpeg', 'image/webp']"
                            label="Click to upload 360° image"
                        />
                    </div>

                    <div class="mt-3">
                        <x-input
                            label="Virtual Tour URL (Optional)"
                            placeholder="Ex: https://example.com/virtual-tour"
                            wire:model.defer="virtualTourUrl"
                        />
                    </div>

                    <div class="mt-3">
                        <x-input
                            label="Model Name"
                            placeholder="E.g, Naomi"
                            wire:model.defer="modelName"
                        />
                    </div>

                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-inputs.number 
                            label="Bedrooms" 
                            min="0"
                            wire:model.defer="bedroomsCount" 
                        />

                        <x-inputs.number 
                            label="Bathrooms" 
                            min="0"
                            wire:model.defer="bathroomsCount" 
                        />
                    </div>

                    <div class="mt-3">
                        <x-input
                            type="number"
                            step="0.1"
                            min="0"
                            class="pr-28"
                            label="Floor Area"
                            placeholder="100"
                            suffix="sqm"
                            wire:model.defer="floorArea"
                        />
                    </div>

                    <div class="mt-3">
                        <x-inputs.currency
                            label="Price"
                            placeholder="Enter price"
                            icon="banknotes"
                            currency="PHP"
                            thousands=","
                            decimal="."
                            precision="2"
                            wire:model.defer="price"
                        />
                    </div>

                    <x-slot name="footer" class="flex justify-end gap-x-4">
                        <div class="flex justify-end gap-x-4">
                            <x-button flat label="Cancel" @click="closeModal()" x-on:click="close"/>
                            <x-button primary label="Save" type="submit" />
                        </div>
                    </x-slot>

                </x-card>
            </form>
        </x-modal>


        {{-- EDIT MODEL HOUSE --}}
        <x-modal blur name="editModelHouse" persistent align="center" max-width="xl">
            <x-card title="Edit Model House">
                <div class="flex items-center gap-6">
                    @if($editImagePreview)
                        <div class="shrink-0">
                            <p class="text-sm font-medium text-gray-700 mb-2">
                                Current Image
                            </p>

                            <img
                                src="{{ $editImagePreview }}"
                                class="w-32 h-32 object-cover rounded-xl border border-gray-200 shadow-sm"
                            >
                        </div>
                    @endif
                    <div class="flex-1">
                        
                        <label class="block text-sm font-medium mb-2">
                            360° Virtual Tour Image
                        </label>

                        <x-filepond::upload
                            wire:model="editModelHouseImage"
                            :accepted-file-types="['image/png', 'image/jpeg', 'image/webp']"
                        />
                        
                    </div>
                </div>

                <div class="mt-3">
                    <x-input
                        label="Virtual Tour URL (Optional)"
                        placeholder="Ex: https://example.com/virtual-tour"
                        wire:model.defer="editVirtualTourUrl"
                    />
                </div>

                <div class="mt-3">
                    <x-input
                        label="Model Name"
                        placeholder="E.g, Naomi"
                        wire:model.defer="editModelName"
                    />
                </div>

                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-inputs.number 
                        label="Bedrooms" 
                        min="0"
                        wire:model="editBedroomsCount" 
                    />

                    <x-inputs.number 
                        label="Bathrooms" 
                        min="0"
                        wire:model="editBathroomsCount" 
                    />
                </div>

                <div class="mt-3">
                    <x-input
                        type="number"
                        step="0.1"
                        min="0"
                        class="pr-28"
                        label="Floor Area"
                        placeholder="100"
                        suffix="sqm"
                        wire:model.defer="editFloorArea"
                    />
                </div>

                <div class="mt-3">
                    <x-inputs.currency
                        label="Price"
                        placeholder="Enter price"
                        icon="banknotes"
                        currency="PHP"
                        thousands=","
                        decimal="."
                        precision="2"
                        wire:model.defer="editPrice"
                    />
                </div>

                <x-slot name="footer" class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" @click="closeModal()" x-on:click="close"/>
                    <x-button primary label="Update" wire:click="editModelHouseConfirmation('{{$editModelName}}')" />
                </x-slot>

            </x-card>
        </x-modal>

        {{-- CREATE VIRUAL TOUR MODAL --}}
        <x-modal blur name="createVirtualTour" max-width="6xl" persistent>
            <x-card title="Virtual Tour Builder">

                {{-- TITLE --}}
                <x-input label="Tour Title" wire:model="tourTitle" />
                <x-input
                    label="Scene Name"
                    wire:model="newSceneName"
                />
                {{-- ADD SCENE --}}
                <x-button class="mt-3" primary label="Add Scene" wire:click="addScene" />

                {{-- SCENE SWITCH --}}
                <div class="flex gap-2 mt-4">
                    @foreach($scenes as $i => $scene)
                        <button
                            class="px-3 py-1 rounded {{ $activeScene == $i ? 'bg-blue-600 text-white' : 'bg-gray-200' }}"
                            wire:click="setActiveScene({{ $i }})"
                        >
                            {{ $scene['name'] }}
                        </button>
                    @endforeach
                </div>

                {{-- ACTIVE SCENE --}}
                @if(isset($scenes[$activeScene]))
                    <div class="mt-4">

                        {{-- NATIVE UPLOAD (NO FILEPOND) --}}
                        <input
                            type="file"
                            wire:model="scenes.{{ $activeScene }}.file"
                            class="block w-full border p-2 rounded"
                        />

                        @if(!empty($scenes[$activeScene]['preview']))
                            <div class="mt-4 relative w-full h-[500px] bg-gray-100 rounded-lg overflow-hidden border">

                                <img
                                    src="{{ $scenes[$activeScene]['preview'] }}"
                                    class="w-full h-full object-contain cursor-crosshair"
                                >
                                
                                
                                <div
                                    class="absolute inset-0 cursor-crosshair"
                                    wire:click="startHotspot($event.offsetX, $event.offsetY)"
                                ></div>

                                @if($showHotspotForm)
                                    <div
                                        class="absolute bg-white shadow-lg p-2 rounded border"
                                        style="left: {{ $hotspotX }}px; top: {{ $hotspotY }}px;"
                                    >
                                        <input
                                            type="text"
                                            wire:model="hotspotLabel"
                                            class="border p-1 text-sm"
                                            placeholder="Hotspot name"
                                        />

                                        @php
                                            $currentSceneId = $scenes[$activeScene]['id'] ?? null;
                                        @endphp

                                        <div>
                                            <label class="block text-sm font-medium mb-1">
                                                Target Scene
                                            </label>

                                            <select wire:model.live="tempTargetScene" class="w-full border rounded px-3 py-2">
                                                <option value="">Select Scene</option>

                                                @foreach($scenes as $index => $scene)
                                                    @if($index !== $activeScene)
                                                        <option value="{{ $index }}">
                                                            {{ $scene['name'] }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="flex gap-2 mt-1">
                                            <button
                                                class="text-xs bg-blue-500 text-white px-2 py-1 rounded"
                                                wire:click="saveInlineHotspot"
                                            >
                                                Save
                                            </button>

                                            <button
                                                class="text-xs bg-gray-400 text-white px-2 py-1 rounded"
                                                wire:click="$set('showHotspotForm', false)"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @endif

                        {{-- HOTSPOTS --}}
                        <div class="mt-3">
                            <h3 class="font-bold">Hotspots</h3>

                            @foreach($scenes[$activeScene]['hotspots'] ?? [] as $h)
                                <div class="text-sm bg-gray-100 p-2 rounded mt-1">
                                    {{ $h['label'] }}
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endif

                {{-- SAVE --}}
                <x-button class="mt-4" primary label="Save Tour" wire:click="saveTour" />

            </x-card>
        </x-modal>

        {{-- VIEW TOUR --}}

        <x-modal blur name="viewTour" max-width="6xl" persistent>
            <x-card title="Virtual Tour Viewer">

                <div
                    x-data="tourViewer()"
                    x-init="setScenes(@js($viewScenes ?? [])); init()"
                    class="relative w-full h-[500px]"
                >
                    
                    <!-- Scene Buttons -->
                    {{-- <div class="flex gap-2 mb-3">
                        <template x-for="(scene, index) in scenes" :key="scene.id">
                            <button
                                class="px-3 py-1 rounded bg-gray-200"
                                @click="loadScene(index)"
                                x-text="'Scene ' + (index + 1)"
                            ></button>
                        </template>
                    </div> --}}

                    <!-- Viewer -->
                    <div x-ref="viewer" class="w-full h-[100%] bg-black rounded"></div>

                </div>
            
            </x-card>
        </x-modal>
    </div>

    {{-- SUBDIVISION LOT MAP --}}
    <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-100 mt-10">
        <div class="w-full h-auto flex justify-between items-center mb-5">
            <div>
                <h2 class="text-lg font-semibold">Subdivision Lot Map</h2>
                <p class="text-sm text-gray-500">Click on any lot to view details or assign to a client</p>
            </div>
            <div class="flex justify-end">
                <x-button label="New Lot Area" x-on:click="$openModal('newLot')" primary />
            </div>
        </div>
       
        <div class="relative">
            @if($map)

                <img 
                    id="map-image"
                    src="{{ asset($map->image_path) }}"
                    usemap="#estate-map"
                    class="w-full"
                />

                <canvas 
                    id="lot-overlay"
                    class="absolute top-0 left-0 pointer-events-none"
                    {{-- wire:ignore --}}
                ></canvas>
                <div
                    id="lot-tooltip"
                    class="absolute hidden z-50 bg-white shadow-2xl overflow-visible rounded-xl border w-80"
                >
                    <div class="relative overflow-visible">
                        <div id="tooltip-arrow"></div>
                        {{-- <img
                            id="tooltip-image"
                            class="w-full h-40 object-cover rounded-t-xl"
                        /> --}}
                        <div
                            id="tooltip-panorama"
                            class="w-full h-40 rounded-t-xl overflow-hidden"
                        ></div>

                        <div class="p-3">
                            <div class="text-lg font-bold" id="tooltip-name"></div>

                            <div class="text-sm text-gray-500 mt-1" id="tooltip-type"></div>

                            <div class="text-gray-400 mt-2" id="tooltip-coords" style="font-size: 8px;"></div>

                            <div class="mt-3 flex justify-end gap-2">
                                <button
                                    id="tooltip-edit-btn"
                                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700"
                                    type="button"
                                >
                                    Edit
                                </button>
                                <button
                                    id="tooltip-edit-btn"
                                    class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                                    type="button"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <map name="estate-map">
                    @foreach($lots as $lot)
                        <area
                            shape="poly"
                            coords="{{ $lot->coords }}"
                            href="#"
                            {{-- wire:click.prevent="openLot({{ $lot->id }})" --}}
                            title="Click to show info"
                            data-type="{{ $lot->type }}"
                            data-name="{{ $lot->name }}"
                            data-image="{{ $lot->image ? asset('storage/' . $lot->image) : '' }}"
                            data-coords="{{ $lot->coords }}"
                        />
                    @endforeach
                </map>

            @endif
        </div>

        <x-modal blur name="newLot" persistent align="center" max-width="6xl">
            <form wire:submit.prevent="createLotArea" class="w-full" x-data="lotDrawer()" x-init="init()">
                <x-card title="Create New Lot Area">
                    <div class="mt-3 relative">
                        @if($map)
                            <img 
                                id="modal-map-image"
                                src="{{ asset($map->image_path) }}"
                                class="w-full cursor-crosshair"
                                @click="addPoint($event)"
                                x-ref="img"
                            />
                            <canvas 
                                id="modal-lot-overlay"
                                class="absolute top-0 left-0 pointer-events-none"
                                x-ref="canvas"
                            ></canvas>
                        @endif
                    </div>

                    <!-- Reset Button -->
                    <div class="mt-3 flex gap-x-2">
                        <x-button primary label="Reset Points" @click="resetPoints()" />
                    </div>

                    <div class="mt-3">
                        <x-input
                            label="Lot Name"
                            placeholder="Ex: Block 1, Lot 43"
                            wire:model.defer="lotName"
                        />
                    </div>

                    <div class="mt-3">
                        <x-native-select
                            label="Lot Type"
                            wire:model="lotType"
                        >
                            <option value="">Select Type</option>
                            <option value="Playground & Community Amenities">Playground & Community Amenities</option>
                            <option value="Model House">Model House</option>
                            <option value="Lot Only">Lot Only</option>
                            <option value="House & Lot">House & Lot</option>
                            <option value="Sold">Sold</option>
                        </x-native-select>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-2">
                            Lot Image
                        </label>

                        <x-filepond::upload
                            wire:model="lotImage"
                            :accepted-file-types="['image/png', 'image/jpeg', 'image/webp']"
                            label="Upload your 360 View"
                        />
                    </div>

                    <div class="mt-3">
                        <x-input
                            label="Lot Coordinates"
                            placeholder="Ex: 74,238,239,38"
                            x-model="coordsString"
                            readonly
                        />
                    </div>

                    <x-slot name="footer" class="flex justify-end gap-x-4">
                        <div class="flex justify-end gap-x-4">
                            <x-button flat label="Cancel" @click="closeModal()" x-on:click="close"/>
                            <x-button primary label="Save" type="submit" @click="$wire.lotCoordinates = coordsString" />
                        </div>
                    </x-slot>

                </x-card>
            </form>

            {{-- <script>
                document.addEventListener("livewire:init", () => {
                    pannellum.viewer('panorama', {
                        type: "equirectangular",
                        panorama: "{{ asset('images/shot-panoramic-composition-living-room.jpg') }}",
                        autoLoad: true
                    });
                });
            </script> --}}

            <script>
                function lotDrawer() {
                    return {
                        points: [],
                        coordsString: '',
                        canvas: null,
                        ctx: null,
                        img: null,

                        init() {
                            this.canvas = this.$refs.canvas;
                            this.img = this.$refs.img;
                            this.ctx = this.canvas.getContext('2d');
                            window.addEventListener('resize', () => this.redraw());
                        },

                        onImageLoad() {
                            // ensure canvas matches image size after it loads
                            this.canvas.width = this.img.clientWidth;
                            this.canvas.height = this.img.clientHeight;
                            this.redraw();
                        },

                        addPoint(e) {
                            if (!this.img.naturalWidth || !this.img.naturalHeight) return;

                            // calculate natural coordinates
                            const x = Math.round((e.offsetX / this.img.clientWidth) * this.img.naturalWidth);
                            const y = Math.round((e.offsetY / this.img.clientHeight) * this.img.naturalHeight);

                            this.points.push({x, y});
                            this.updateCoordsString();
                            this.redraw();

                            // sync to Livewire
                            $wire.addPoint(e.offsetX / this.img.clientWidth, e.offsetY / this.img.clientHeight);
                        },

                        resetPoints() {
                            this.points = [];
                            this.coordsString = '';
                            this.redraw();
                            $wire.resetPoints();
                        },

                        updateCoordsString() {
                            this.coordsString = this.points.map(p => `${p.x},${p.y}`).join(',');
                        },

                        redraw() {
                            if (!this.canvas || !this.img) return;

                            this.canvas.width = this.img.clientWidth;
                            this.canvas.height = this.img.clientHeight;
                            const ctx = this.ctx;

                            ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

                            if (this.points.length === 0) return;

                            // Draw polygon
                            ctx.beginPath();
                            this.points.forEach((p, i) => {
                                const x = p.x * this.img.clientWidth / this.img.naturalWidth;
                                const y = p.y * this.img.clientHeight / this.img.naturalHeight;
                                if (i === 0) ctx.moveTo(x, y);
                                else ctx.lineTo(x, y);
                            });
                            ctx.closePath();
                            ctx.fillStyle = "rgba(0,150,255,0.3)";
                            ctx.fill();
                            ctx.strokeStyle = "#0096ff";
                            ctx.lineWidth = 2;
                            ctx.stroke();

                            // Draw black dots
                            this.points.forEach(p => {
                                const x = p.x * this.img.clientWidth / this.img.naturalWidth;
                                const y = p.y * this.img.clientHeight / this.img.naturalHeight;
                                ctx.beginPath();
                                ctx.arc(x, y, 5, 0, Math.PI * 2);
                                ctx.fillStyle = "#000";
                                ctx.fill();
                                ctx.strokeStyle = "#fff";
                                ctx.lineWidth = 1;
                                ctx.stroke();
                            });
                        },

                        closeModal() {
                            this.resetPoints();
                            $dispatch('close');
                        }
                    }
                }
            </script>
        </x-modal>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-rwdImageMaps/1.6/jquery.rwdImageMaps.min.js"></script>

    <script>
        $(document).ready(function () {
            $('img[usemap]').rwdImageMaps();
            drawLots();
        });
        $(window).on('resize', function(){
            drawLots();
        });
    </script>

    {{-- <script>
        function drawLots() {

            const img = document.getElementById('map-image');
            const canvas = document.getElementById('lot-overlay');

            if (!img || !canvas) return;

            const rect = img.getBoundingClientRect();

            canvas.width = rect.width;
            canvas.height = rect.height;

            canvas.style.width = rect.width + "px";
            canvas.style.height = rect.height + "px";

            const ctx = canvas.getContext("2d");
            ctx.clearRect(0,0,canvas.width,canvas.height);

            document.querySelectorAll("area").forEach(area => {

                // IMPORTANT: use resized coords from rwdImageMaps
                const coords = area.coords.split(',').map(Number);

                ctx.beginPath();

                for(let i=0;i<coords.length;i+=2){

                    const x = coords[i];
                    const y = coords[i+1];

                    if(i === 0) ctx.moveTo(x,y);
                    else ctx.lineTo(x,y);

                }

                ctx.closePath();

                ctx.fillStyle = "rgba(0,150,255,0.35)";
                ctx.fill();

                ctx.strokeStyle = "#0096ff";
                ctx.lineWidth = 2;
                ctx.stroke();

            });

        }

        window.addEventListener("load", drawLots);
        window.addEventListener("resize", drawLots);
    </script> --}}
    <script>

        function hexToRGBA(hex, opacity) {

            const r = parseInt(hex.substring(1, 3), 16);
            const g = parseInt(hex.substring(3, 5), 16);
            const b = parseInt(hex.substring(5, 7), 16);

            return `rgba(${r}, ${g}, ${b}, ${opacity})`;

        }

        function drawLots() {

            const img = document.getElementById('map-image');
            const canvas = document.getElementById('lot-overlay');

            if (!img || !canvas) return;

            const rect = img.getBoundingClientRect();

            canvas.width = rect.width;
            canvas.height = rect.height;

            canvas.style.width = rect.width + "px";
            canvas.style.height = rect.height + "px";

            const ctx = canvas.getContext("2d");

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            const colors = {

                "Playground & Community Amenities": "#f2b879",
                "Model House": "#c8c9c3",
                "Lot Only": "#c4e0b7",
                "House & Lot": "#f8e89c",
                "Sold": "#e9b4ae",

            };

            document.querySelectorAll("area").forEach(area => {

                const coords = area.coords
                    .split(',')
                    .map(Number);

                const type = area.dataset.type;

                const color = colors[type] || "#0096ff";

                ctx.beginPath();

                for (let i = 0; i < coords.length; i += 2) {

                    const x = coords[i];
                    const y = coords[i + 1];

                    if (i === 0) {
                        ctx.moveTo(x, y);
                    } else {
                        ctx.lineTo(x, y);
                    }

                }

                ctx.closePath();

                // Fill
                ctx.fillStyle = hexToRGBA(color, 0.45);
                ctx.fill();

                // Border
                ctx.strokeStyle = color;
                ctx.lineWidth = 2;
                ctx.stroke();

            });

        }

        window.addEventListener("load", drawLots);
        window.addEventListener("resize", drawLots);

    </script>

    <script>

        function initLotTooltip() {

            const tooltip = document.getElementById('lot-tooltip');
            const tName = document.getElementById('tooltip-name');
            const tType = document.getElementById('tooltip-type');
            const tImage = document.getElementById('tooltip-image');
            const tCoords = document.getElementById('tooltip-coords');

            const img = document.getElementById('map-image');

            if (!img || !tooltip) return;

            // function show(area, e) {

            //     const panoContainer = document.getElementById('tooltip-panorama');
            //     panoContainer.innerHTML = "";

            //     tName.textContent = area.dataset.name ?? 'No Name';
            //     tType.textContent = area.dataset.type ?? 'No Type';
            //     tCoords.textContent = area.dataset.coords ?? '';
                
            //     // if (area.dataset.image) {

            //     //     tImage.onload = () => {
            //     //         tImage.style.display = "block";
            //     //     };

            //     //     tImage.onerror = () => {
            //     //         tImage.style.display = "none";
            //     //     };

            //     //     tImage.src = area.dataset.image;

            //     // } else {
            //     //     tImage.removeAttribute('src');
            //     //     tImage.style.display = "none";
            //     // }

            //     tooltip.classList.remove('hidden');

            //     requestAnimationFrame(() => {

            //         const panoContainer = document.getElementById('tooltip-panorama');
            //         panoContainer.innerHTML = "";

            //         pannellum.viewer(panoContainer, {
            //             type: "equirectangular",
            //             panorama: area.dataset.image,
            //             autoLoad: true,
            //             showControls: false
            //         });

            //     });
            //     move(e);
            // }

            function show(area, e) {

                const panoContainer = document.getElementById('tooltip-panorama');
                panoContainer.innerHTML = "";

                tName.textContent = area.dataset.name ?? 'No Name';
                tType.textContent = area.dataset.type ?? 'No Type';
                tCoords.textContent = area.dataset.coords ?? '';

                tooltip.classList.remove('hidden');

                // 👇 IMPORTANT: wait for layout BEFORE initializing pannellum
                requestAnimationFrame(() => {

                    pannellum.viewer(panoContainer, {
                        type: "equirectangular",
                        panorama: area.dataset.image,
                        autoLoad: true,
                        showControls: false
                    });

                });

                move(e);
            }

            function move(e) {

                const container = img.getBoundingClientRect();

                const tooltipWidth = tooltip.offsetWidth;
                const tooltipHeight = tooltip.offsetHeight;

                let x = (e.clientX - container.left + 20);
                let y = (e.clientY - container.top - tooltipHeight - 20);

                let placedAbove = true;

                // if not enough space above → place below cursor
                if (y < 0) {
                    y = (e.clientY - container.top + 20);
                    placedAbove = false;
                }

                // clamp horizontal
                if (x + tooltipWidth > container.width) {
                    x = container.width - tooltipWidth - 10;
                }

                tooltip.style.left = x + "px";
                tooltip.style.top = y + "px";

                // 🔥 HANDLE ARROW
                const arrow = document.getElementById('tooltip-arrow');

                if (placedAbove) {
                    // tooltip above cursor → arrow points DOWN
                    arrow.style.bottom = "-10px";
                    arrow.style.top = "auto";

                    arrow.style.borderLeft = "10px solid transparent";
                    arrow.style.borderRight = "10px solid transparent";
                    arrow.style.borderTop = "10px solid white";
                    arrow.style.borderBottom = "0";
                } else {
                    // tooltip below cursor → arrow points UP
                    arrow.style.top = "-10px";
                    arrow.style.bottom = "auto";

                    arrow.style.borderLeft = "10px solid transparent";
                    arrow.style.borderRight = "10px solid transparent";
                    arrow.style.borderBottom = "10px solid white";
                    arrow.style.borderTop = "0";
                }

                // center arrow under cursor
                let arrowX = (e.clientX - container.left) - x;
                arrowX = Math.max(20, Math.min(tooltipWidth - 20, arrowX));

                arrow.style.left = arrowX + "px";
            }

            function hide() {
                tooltip.classList.add('hidden');
            }

            // function bind() {

            //     document.querySelectorAll('area').forEach(area => {

            //         area.addEventListener('mouseenter', (e) => show(area, e));
            //         area.addEventListener('mousemove', (e) => move(e));
            //         area.addEventListener('mouseleave', hide);

            //     });

            // }
            function bind() {
                let activeArea = null;

                document.querySelectorAll('area').forEach(area => {

                    area.addEventListener('click', (e) => {
                        e.preventDefault();

                        // toggle off if same area clicked again
                        if (activeArea === area) {
                            hide();
                            activeArea = null;
                            return;
                        }

                        activeArea = area;

                        show(area, e); // show once
                    });

                });
            }

            // IMPORTANT: wait for image map plugin
            function waitForMap() {

                if (window.jQuery && $('img[usemap]').length) {

                    $('img[usemap]').rwdImageMaps();

                    setTimeout(bind, 300);

                } else {
                    setTimeout(waitForMap, 200);
                }

            }

            waitForMap();

            // Livewire safety hook (VERY IMPORTANT)
            document.addEventListener('livewire:navigated', () => {
                setTimeout(bind, 300);
            });

            document.addEventListener('livewire:init', () => {
                setTimeout(bind, 300);
            });

        }

        document.addEventListener('DOMContentLoaded', initLotTooltip);

    </script>

    <script>
    document.addEventListener('livewire:init', () => {

        let viewer = null;

        function load(image) {

            setTimeout(() => {

                const container = document.getElementById('panorama');
                if (!container || !image) return;

                if (viewer) {
                    viewer.destroy();
                    viewer = null;
                }

                container.innerHTML = "";

                viewer = pannellum.viewer(container, {
                    type: "equirectangular",
                    panorama: image,
                    autoLoad: true,
                    showControls: true
                });

                viewer.on('mousedown', function (e) {
                    Livewire.dispatch('open-hotspot', {
                        pitch: e.pitch,
                        yaw: e.yaw
                    });
                });

            }, 50); // 🔥 ensures modal + DOM is ready
        }

        Livewire.on('load-panorama', (data) => {
            if (!data.image) return;
            load(data.image);
        });

    });
    </script>

    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('tourViewer', () => ({
                scenes: [],
                viewer: null,
                activeIndex: 0,

                setScenes(data) {
                    this.scenes = Array.isArray(data) ? data : [];
                    console.log('SCENES LOADED:', this.scenes);
                },

                init() {
                    if (this.scenes.length > 0) {
                        this.$nextTick(() => this.loadScene(0));
                    }
                },

                // buildHotspots(scene) {

                //     return (scene.hotspots || []).map(h => ({

                //         pitch: Number(h.pitch),
                //         yaw: Number(h.yaw),

                //         type: "custom",

                //         createTooltipArgs: {
                //             text: h.label
                //         },

                //         createTooltipFunc: (hotSpotDiv, args) => {

                //             hotSpotDiv.innerHTML = `
                //                 <button
                //                     style="
                //                         background:#2563eb;
                //                         color:white;
                //                         border:none;
                //                         border-radius:999px;
                //                         padding:10px 14px;
                //                         font-size:13px;
                //                         font-weight:600;
                //                         cursor:pointer;
                //                         box-shadow:0 4px 12px rgba(0,0,0,0.35);
                //                         transition:all .2s ease;
                //                         white-space:nowrap;
                //                     "
                //                     onmouseover="
                //                         this.style.transform='scale(1.08)';
                //                         this.style.background='#1d4ed8';
                //                     "
                //                     onmouseout="
                //                         this.style.transform='scale(1)';
                //                         this.style.background='#2563eb';
                //                     "
                //                 >
                //                     ${args.text}
                //                 </button>
                //             `;

                //             hotSpotDiv.style.transform = "translate(-50%, -50%)";
                //         },

                //         clickHandlerFunc: () => {

                //             const idx = this.scenes.findIndex(
                //                 s => s.id === h.target_scene_id
                //             );

                //             if (idx !== -1) {
                //                 this.loadScene(idx);
                //             }
                //         }

                //     }));
                // },

                buildHotspots(scene) {

                    return (scene.hotspots || []).map(h => ({

                        pitch: Number(h.pitch),
                        yaw: Number(h.yaw),

                        type: "custom",

                        createTooltipFunc: (hotSpotDiv) => {

                            const button = document.createElement("button");

                            button.innerText = h.label;

                            button.style.background = "#2563eb";
                            button.style.color = "white";
                            button.style.border = "none";
                            button.style.borderRadius = "999px";
                            button.style.padding = "10px 14px";
                            button.style.fontSize = "13px";
                            button.style.fontWeight = "600";
                            button.style.cursor = "pointer";
                            button.style.boxShadow = "0 4px 12px rgba(0,0,0,0.35)";
                            button.style.whiteSpace = "nowrap";

                            // hover
                            button.onmouseover = () => {
                                button.style.transform = "scale(1.08)";
                                button.style.background = "#1d4ed8";
                            };

                            button.onmouseout = () => {
                                button.style.transform = "scale(1)";
                                button.style.background = "#2563eb";
                            };

                            // 🔥 ACTUAL SCENE SWITCH
                            button.onclick = (e) => {

                                e.stopPropagation();

                                const idx = this.scenes.findIndex(
                                    s => s.id === h.target_scene_id
                                );

                                console.log("TARGET:", h.target_scene_id);
                                console.log("FOUND INDEX:", idx);

                                if (idx !== -1) {
                                    this.loadScene(idx);
                                }
                            };

                            hotSpotDiv.appendChild(button);

                            hotSpotDiv.style.transform =
                                "translate(-50%, -50%)";
                        }

                    }));
                },

                loadScene(index) {

                    const scene = this.scenes[index];
                    if (!scene) return;

                    this.activeIndex = index;

                    const container = this.$refs.viewer;

                    if (this.viewer) {
                        try { this.viewer.destroy(); } catch (e) {}
                        this.viewer = null;
                    }

                    container.innerHTML = "";

                    this.$nextTick(() => {
                        requestAnimationFrame(() => {

                            this.viewer = pannellum.viewer(container, {
                                type: "equirectangular",
                                panorama: scene.image,
                                autoLoad: true,
                                showControls: true,
                                hotSpots: this.buildHotspots(scene)
                            });

                        });
                    });
                }
            }));

        });
    </script>
</div>
