<div class="pb-80">

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
        @if ($houseModels->count())
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-4">
                @foreach($houseModels as $model)

                    <div class="flex flex-col bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-2xs rounded-xl">

                        <img 
                            class="w-full h-48 object-cover rounded-t-xl" 
                            src="{{ $model->image 
                                ? asset('storage/' . $model->image) 
                                : 'https://images.unsplash.com/photo-1680868543815-b8666dba60f7' }}" 
                            alt="{{ $model->model_name }}"
                        >

                        <div class="p-4">

                            <div class="flex justify-between items-center">

                                <h3 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $model->model_name }}
                                </h3>

                                <div>
                                    <x-button.circle
                                        xs
                                        onclick="$openModal('editModelHouse')"
                                        wire:click="getSelectedModelHouse({{$model->id}})"
                                        icon="pencil"
                                    />

                                    <x-button.circle 
                                        xs 
                                        wire:click="deleteModelHouseConfirmation({{$model->id}}, '{{$model->model_name}}')" 
                                        negative
                                        icon="trash" 
                                    />
                                </div>

                            </div>

                            <p class="my-2 text-sm text-gray-600 dark:text-gray-300">
                                {{ $model->floor_area }} sqm
                            </p>

                            <div class="flex justify-start items-center gap-3">

                                {{-- Bedrooms --}}
                                <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-300">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg" 
                                        viewBox="0 0 512 512" 
                                        class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                    >
                                        <path
                                            d="M384 240H96V136a40.12 40.12 0 0140-40h240a40.12 40.12 0 0140 40v104zM48 416V304a64.19 64.19 0 0164-64h288a64.19 64.19 0 0164 64v112" 
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-linecap="round" 
                                            stroke-linejoin="round"
                                            stroke-width="32"
                                        />

                                        <path
                                            d="M48 416v-8a24.07 24.07 0 0124-24h368a24.07 24.07 0 0124 24v8M112 240v-16a32.09 32.09 0 0132-32h80a32.09 32.09 0 0132 32v16M256 240v-16a32.09 32.09 0 0132-32h80a32.09 32.09 0 0132 32v16" 
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-linecap="round" 
                                            stroke-linejoin="round"
                                            stroke-width="32"
                                        />
                                    </svg>

                                    <span class="text-xs">
                                        {{ $model->bedrooms }} Bedrooms
                                    </span>

                                </div>

                                <span class="text-gray-500 dark:text-gray-400">•</span>

                                {{-- Bathrooms --}}
                                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg" 
                                        viewBox="0 0 512 512" 
                                        class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                    >
                                        <path
                                            d="M96 192V96a32 32 0 0132-32h256a32 32 0 0132 32v96" 
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-linecap="round" 
                                            stroke-linejoin="round"
                                            stroke-width="32"
                                        />

                                        <path
                                            d="M64 224h384v64a96 96 0 01-96 96H160a96 96 0 01-96-96v-64z" 
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-linejoin="round" 
                                            stroke-width="32"
                                        />

                                        <path
                                            d="M112 384v48M400 384v48M160 224v-32M352 224v-32" 
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-linecap="round" 
                                            stroke-linejoin="round"
                                            stroke-width="32"
                                        />
                                    </svg>

                                    <span class="text-xs">
                                        {{ $model->bathrooms }} Bathrooms
                                    </span>

                                </div>

                            </div>

                            <hr class="my-5 border-gray-200 dark:border-gray-700">

                            <p class="text-gray-900 dark:text-white font-semibold text-lg">
                                ₱{{ number_format($model->price, 2) }}
                            </p>

                        </div>

                        @if($model->virtualTour)

                            <x-button
                                class="mb-3 mx-auto w-[90%]"
                                icon="pencil-square"
                                rounded
                                amber
                                label="Edit Virtual Tour"
                                x-on:click="
                                    $wire.editVirtualTour({{ $model->id }})
                                        .then(() => {
                                            $openModal('createVirtualTour');
                                        });
                                "
                            />

                        @else

                            <x-button
                                class="mb-3 mx-auto w-[90%]"
                                icon="viewfinder-circle"
                                rounded
                                positive
                                label="Create Virtual Tour"
                                x-on:click="
                                    $wire.selectHouseModel({{ $model->id }})
                                        .then(() => {
                                            $openModal('createVirtualTour');
                                        });
                                "
                            />

                        @endif

                        <x-button 
                            class="mb-3 mx-auto w-[90%]"
                            icon="eye" 
                            rounded  
                            label="Show Virtual Tour" 
                            wire:click="viewHouseTour({{ $model->id }})"
                            x-on:click="$openModal('viewTour')"
                        />

                    </div>

                @endforeach
            </div>
        @else
            <div class="col-span-3 text-center py-10 text-gray-500 border-2 border-dashed rounded-lg">
                <p class="italic text-gray-400">No house models found.</p>
            </div>
        @endif
        

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
                            wire:model="virtualTourUrl"
                        />
                    </div>

                    <div class="mt-3">
                        <x-input
                            label="Model Name"
                            placeholder="E.g, Naomi"
                            wire:model="modelName"
                        />
                    </div>

                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-inputs.number 
                            label="Bedrooms" 
                            min="0"
                            wire:model="bedroomsCount" 
                        />

                        <x-inputs.number 
                            label="Bathrooms" 
                            min="0"
                            wire:model="bathroomsCount" 
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
                            wire:model="floorArea"
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
                            wire:model="price"
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
                        wire:model="editVirtualTourUrl"
                    />
                </div>

                <div class="mt-3">
                    <x-input
                        label="Model Name"
                        placeholder="E.g, Naomi"
                        wire:model="editModelName"
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
                        wire:model="editFloorArea"
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
                        wire:model="editPrice"
                    />
                </div>

                <x-slot name="footer" class="flex justify-end gap-x-4">
                    <x-button flat label="Cancel" @click="closeModal()" x-on:click="close"/>
                    <x-button primary label="Update" wire:click="editModelHouseConfirmation('{{$editModelName}}')" />
                </x-slot>

            </x-card>
        </x-modal>

        {{-- CREATE VIRUAL TOUR MODAL --}}
        <x-modal blur name="createVirtualTour" max-width="4xl" persistent>
            <x-card
                title="{{ $isEditingTour
                    ? 'Edit Virtual Tour'
                    : 'Virtual Tour Builder'
                }}"
            >

                {{-- TITLE --}}
                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input label="Tour Title" wire:model="tourTitle" />
                    <x-input
                        label="Scene Name"
                        wire:model="newSceneName"
                    />
                </div>
                <hr class="my-4">

                {{-- ADD SCENE --}}
                <x-button sm icon="squares-plus" rounded label="Add Scene" wire:click="addScene" />
                
                <div class="border-2 border-dashed rounded-lg mt-6 px-6">
                    {{-- SCENE SWITCH --}}
                    <div class="mt-4">
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($scenes as $i => $scene)
                                <div
                                    wire:key="scene-tab-{{ $i }}"
                                    class="group inline-flex items-stretch overflow-hidden rounded-xl border shadow-sm transition
                                        {{ $activeScene == $i
                                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100'
                                            : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow'
                                        }}"
                                >
                                    {{-- Select scene --}}
                                    <button
                                        type="button"
                                        wire:click="setActiveScene({{ $i }})"
                                        class="flex items-center gap-2 px-4 py-1 text-sm font-medium transition
                                            {{ $activeScene == $i
                                                ? 'text-blue-700'
                                                : 'text-gray-700 hover:bg-gray-50'
                                            }}"
                                    >
                                        <span
                                            class="flex h-5 w-5 items-center justify-center rounded-full text-xs font-semibold
                                                {{ $activeScene == $i
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-gray-100 text-gray-600'
                                                }}"
                                        >
                                            {{ $i + 1 }}
                                        </span>

                                        <span class="max-w-[160px] text-xs truncate">
                                            {{ $scene['name'] }}
                                        </span>

                                        {{-- @if($activeScene == $i)
                                            <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                        @endif --}}
                                    </button>

                                    {{-- Edit scene name --}}
                                    <button
                                        type="button"
                                        wire:click="openEditScene({{ $i }})"
                                        {{-- x-on:click="$openModal('editSceneName')" --}}
                                        class="flex items-center justify-center border-l px-2 transition
                                            {{ $activeScene == $i
                                                ? 'border-blue-200 text-blue-600 hover:bg-blue-100'
                                                : 'border-gray-200 text-gray-400 hover:bg-gray-100 hover:text-gray-700'
                                            }}"
                                        title="Rename {{ $scene['name'] }}"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.8"
                                            stroke="currentColor"
                                            class="h-3 w-3"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM19.5 7.125 16.875 4.5"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M19.5 13.5v4.125A2.625 2.625 0 0 1 16.875 20.25H6.375A2.625 2.625 0 0 1 3.75 17.625V7.125A2.625 2.625 0 0 1 6.375 4.5H10.5"
                                            />
                                        </svg>
                                    </button>
                                    {{-- Delete scene --}}
                                    <button
                                        type="button"
                                        wire:click="deleteScene({{ $i }})"
                                        class="flex items-center justify-center border-l px-2 transition
                                            {{ $activeScene == $i
                                                ? 'border-blue-200 text-red-500 hover:bg-red-50 hover:text-red-600'
                                                : 'border-gray-200 text-gray-400 hover:bg-red-50 hover:text-red-600'
                                            }}"
                                        title="Delete {{ $scene['name'] }}"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.8"
                                            stroke="currentColor"
                                            class="h-3 w-3"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21
                                                c.342.052.682.107 1.022.166m-1.022-.165
                                                L18.16 19.673a2.25 2.25 0 0 1-2.244
                                                2.077H8.084a2.25 2.25 0 0 1-2.244-2.077
                                                L4.772 5.79m14.456 0a48.108 48.108
                                                0 0 0-3.478-.397m-12 .562c.34-.059.68-.114
                                                1.022-.165m0 0a48.11 48.11 0 0 1
                                                3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201
                                                a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09
                                                1.022-2.09 2.201v.916m7.5 0a48.667
                                                48.667 0 0 0-7.5 0"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- ACTIVE SCENE --}}
                    @if(isset($scenes[$activeScene]))
                        <div
                            wire:key="scene-upload-{{ $activeScene }}"
                            class="mt-4"
                        >
                            <div x-data="{ isDragging: false }" class="mt-4">
                                <label
                                    for="scene-upload-{{ $activeScene }}"
                                    class="relative flex flex-col items-center justify-center w-full h-36 border-2 border-dashed rounded-xl cursor-pointer transition-all duration-300"
                                    :class="isDragging
                                        ? 'border-blue-500 bg-blue-50'
                                        : 'border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-blue-400'"
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @drop.prevent="isDragging = false"
                                >

                                    <div class="flex flex-col items-center justify-center text-center px-4">

                                        {{-- ICON --}}
                                        <svg
                                            class="w-10 h-10 mb-2 text-blue-500"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 16V4m0 0l-4 4m4-4l4 4M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1"
                                            />
                                        </svg>

                                        <p class="text-sm font-semibold text-gray-700">
                                            Upload Panorama
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Drag & drop or click to browse
                                        </p>

                                        @if(!empty($scenes[$activeScene]['file']))
                                            <div class="mt-2 text-[11px] text-green-600 font-medium truncate max-w-[220px]">
                                                {{ $scenes[$activeScene]['file']->getClientOriginalName() }}
                                            </div>
                                        @endif

                                    </div>

                                    <input
                                        id="scene-upload-{{ $activeScene }}"
                                        type="file"
                                        wire:model="scenes.{{ $activeScene }}.file"
                                        class="hidden"
                                        accept="image/*"
                                    />
                                </label>

                                {{-- LOADING --}}
                                <div wire:loading wire:target="scenes.{{ $activeScene }}.file" class="mt-2">
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-blue-600 h-1.5 animate-pulse w-full"></div>
                                    </div>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Uploading...
                                    </p>
                                </div>
                            </div>

                            @if(!empty($scenes[$activeScene]['preview']))

                            <div class="relative mt-4">

                                <div
                                    wire:ignore
                                    x-data="panoramaEditor()"
                                    x-init="init()"
                                >
                                    {{-- PANNELLUM VIEWER --}}
                                    <div
                                        x-ref="viewer"
                                        wire:ignore
                                        class="w-full h-[500px] min-h-[500px] bg-black rounded-lg overflow-hidden"
                                        style="height:500px !important;"
                                    ></div>
                                </div>

                                {{-- REMOVE PANORAMA BUTTON --}}
                                <button
                                    type="button"
                                    wire:click="removeScenePanorama"
                                    class="absolute top-1 left-11 z-[10000] flex items-center justify-center w-8 h-8 rounded-full bg-white/90 text-red-600 shadow-md hover:bg-red-50 hover:text-red-700 transition"
                                    title="Remove Panorama"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                        @endif

                            {{-- HOTSPOT FORM --}}
                            {{-- @if($showHotspotForm)

                                <div class="mt-4 border rounded-lg p-4 bg-white">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                        <x-input
                                            label="Hotspot Label"
                                            wire:model="tempLabel"
                                        />

                                        <div>
                                            <label class="block text-sm mb-1">
                                                Target Scene
                                            </label>

                                            <select
                                                wire:model="tempTargetScene"
                                                class="w-full border rounded px-3 py-2"
                                            >
                                                <option value="">
                                                    Select Scene
                                                </option>

                                                @foreach($scenes as $index => $scene)

                                                    @if($index !== $activeScene)

                                                        <option value="{{ $index }}">
                                                            {{ $scene['name'] }}
                                                        </option>

                                                    @endif

                                                @endforeach
                                            </select>
                                        </div>

                                    </div>

                                    <div class="flex gap-2 mt-4">

                                        <x-button
                                            primary
                                            label="{{ $editingHotspot !== null ? 'Update' : 'Save' }}"
                                            wire:click="saveHotspot"
                                        />

                                        <x-button
                                            flat
                                            label="Cancel"
                                            wire:click="resetHotspotForm"
                                        />

                                    </div>

                                </div>

                            @endif --}}

                            <div
                                x-data="{ open: @entangle('showHotspotForm') }"
                                x-show="open"
                                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50"
                                x-cloak
                            >
                                <div class="bg-white w-[400px] rounded-lg p-4 shadow-lg">

                                    <h2 class="text-lg font-bold mb-3">
                                        {{ $editingHotspot !== null
                                            ? 'Edit Hotspot'
                                            : 'Create Hotspot'
                                        }}
                                    </h2>

                                    <div class="space-y-3">

                                        <input
                                            type="text"
                                            placeholder="Hotspot Label"
                                            class="w-full border rounded px-3 py-2"
                                            wire:model="tempLabel"
                                        />

                                        <select
                                            class="w-full border rounded px-3 py-2"
                                            wire:model="tempTargetScene"
                                        >
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

                                    <div class="flex justify-end gap-2 mt-4">

                                        <button
                                            class="px-3 py-1 border rounded"
                                            wire:click="resetHotspotForm"
                                        >
                                            Cancel
                                        </button>

                                        <button
                                            class="px-3 py-1 bg-blue-600 text-white rounded"
                                            wire:click="saveHotspot"
                                        >
                                            {{ $editingHotspot !== null
                                                ? 'Update'
                                                : 'Save'
                                            }}
                                        </button>

                                    </div>

                                </div>
                            </div>

                            {{-- HOTSPOTS --}}
                            <div class="my-4 border border-dashed rounded-lg px-5 py-3">

                                <h3 class="font-bold mb-2">
                                    Hotspots
                                </h3>

                                <div class="space-y-2">

                                    @foreach($scenes[$activeScene]['hotspots'] ?? [] as $i => $h)

                                        <div class="flex items-center justify-between bg-gray-100 rounded-lg p-3">

                                            <div>
                                                <div class="font-medium">
                                                    {{ $h['label'] }}
                                                </div>

                                                <div class="text-xs text-gray-500">
                                                    Pitch: {{ $h['pitch'] }},
                                                    Yaw: {{ $h['yaw'] }}
                                                </div>
                                            </div>

                                            <div class="flex gap-2">

                                                <x-button
                                                    xs
                                                    icon="pencil"
                                                    amber
                                                    wire:click="editHotspot({{ $i }})"
                                                />

                                                <x-button
                                                    xs
                                                    icon="trash"
                                                    red
                                                    wire:click="deleteHotspot({{ $i }})"
                                                />

                                            </div>
                                        </div>

                                    @endforeach

                                </div>
                            </div>

                        </div>
                    @endif
                </div>

                {{-- SAVE --}}
                <x-slot name="footer" class="flex justify-end gap-x-4">

                    <x-button
                        flat
                        label="Cancel"
                        x-on:click="close"
                    />

                    @if($isEditingTour)

                        <x-button
                            primary
                            label="Update Tour"
                            wire:click="updateTour"
                            spinner="updateTour"
                        />

                    @else

                        <x-button
                            primary
                            label="Save Tour"
                            wire:click="saveTour"
                            spinner="saveTour"
                        />

                    @endif

                </x-slot>
            </x-card>

            <script>
                document.addEventListener('alpine:init', () => {

                Alpine.data('panoramaEditor', () => ({

                    viewer: null,
                    hotspotMode: false,

                    init() {

                        this.renderViewer();
                        this.injectHotspotButton();

                        // 🔥 FIX: react to scene change
                        this.$watch(() => @this.activeScene, () => {
                            this.hotspotMode = false;
                            this.renderViewer();
                            this.injectHotspotButton();
                        });

                        // 🔥 FIX: react to scenes updates (uploads / edits)
                        this.$watch(() => @this.scenes, () => {
                            this.renderViewer();
                            this.injectHotspotButton();
                        });
                    },

                    renderViewer() {

                        const scenes = @this.scenes;
                        const active = @this.activeScene;
                        const scene = scenes?.[active];

                        if (!scene || !scene.preview) return;

                        const el = this.$refs.viewer;

                        // 🔥 HARD RESET OLD INSTANCE
                        if (this.viewer) {
                            try { this.viewer.destroy(); } catch (e) {}
                            this.viewer = null;
                        }

                        el.innerHTML = '';

                        this.viewer = pannellum.viewer(el, {
                            type: 'equirectangular',
                            panorama: scene.preview,
                            autoLoad: true,
                            showControls: true,
                            hotSpots: (scene.hotspots || []).map(h => ({
                                pitch: Number(h.pitch),
                                yaw: Number(h.yaw),
                                type: 'info',
                                text: h.label
                            }))
                        });

                        this.bindClick();
                    },

                    injectHotspotButton() {

                        const check = () => {

                            const container = this.$refs.viewer?.querySelector('.pnlm-render-container');
                            if (!container) return setTimeout(check, 200);

                            let overlay = container.querySelector('.hotspot-overlay');

                            if (!overlay) {
                                overlay = document.createElement('div');
                                overlay.className = 'hotspot-overlay';
                                container.style.position = 'relative';
                                container.appendChild(overlay);
                            }

                            if (overlay.querySelector('.hotspot-btn')) return;

                            const btn = document.createElement('button');
                            btn.innerHTML = '➕ Hotspot';
                            btn.className = 'hotspot-btn';

                            btn.addEventListener('click', (e) => {
                                e.preventDefault();
                                e.stopPropagation();

                                // toggle state
                                this.hotspotMode = !this.hotspotMode;

                                // update UI via class (NOT inline style)
                                btn.classList.toggle('active', this.hotspotMode);

                                console.log('HOTSPOT MODE:', this.hotspotMode);
                            });

                            overlay.appendChild(btn);
                        };

                        setTimeout(check, 500);
                    },

                    bindClick() {

                        const container = this.viewer?.getContainer();
                        if (!container) return;

                        // 🔥 REMOVE OLD HANDLER SAFELY
                        container.onclick = null;

                        container.addEventListener('click', (event) => {

                            if (!this.hotspotMode) return;

                            event.preventDefault();
                            event.stopPropagation();

                            const coords = this.viewer.mouseEventToCoords(event);
                            if (!coords) return;

                            this.hotspotMode = false;

                            const btn = this.$el.querySelector('.hotspot-btn');
                            if (btn) btn.classList.remove('active');

                            @this.call('prepareHotspot', coords[0], coords[1]);
                        }, true);
                    }

                }));

                });
            </script>
        </x-modal>
        

        {{-- VIEW TOUR --}}
        <x-modal blur name="viewTour" max-width="6xl" persistent>
            <x-card title="Virtual Tour Viewer">

                
                <div
                    x-data="tourViewer()"
                    x-init="setScenes(@js($viewScenes ?? [])); init()"
                    class="relative w-full h-[500px]"
                >
                    <div
                        x-show="scenes.length > 0"
                        x-ref="viewer"
                        wire:ignore
                        class="w-full h-full bg-black rounded"
                    >
                    </div>
                    <!-- No tour fallback -->
                    <template x-if="scenes.length === 0">
                        <div class="w-full h-[500px] flex items-center justify-center text-gray-500">
                            No Virtual Tour Yet
                        </div>
                    </template>    
                </div>
                <x-slot name="footer" class="flex justify-end gap-x-4">
                    <x-button flat label="Close" @click="closeModal()" x-on:click="close" wire:click="reloadWeb" />
                </x-slot>
            </x-card>
        </x-modal>

        <x-modal
            blur
            name="editSceneName"
            align="center"
            persistent
            max-width="md"
            wire:model="showEditSceneModal"
        >
            <form wire:submit.prevent="updateSceneName">
                <x-card title="Edit Scene Name">

                    <x-input
                        label="Scene Name"
                        wire:model="editingSceneName"
                    />

                    <x-slot name="footer">
                        <div class="flex justify-end gap-2">
                            <x-button
                                flat
                                type="button"
                                label="Cancel"
                                wire:click="$set('showEditSceneModal', false)"
                            />

                            <x-button
                                primary
                                type="submit"
                                label="Save"
                                spinner="updateSceneName"
                            />
                        </div>
                    </x-slot>

                </x-card>
            </form>
        </x-modal>
    </div>

    <livewire:fil-pages.map.map-view />

    {{-- VIEW TOUR SCRIPT  --}}
    <script>
        document.addEventListener('alpine:init', () => {

            Alpine.data('tourViewer', () => ({
                scenes: [],
                viewer: null,
                activeIndex: 0,

                setScenes(data) {
            this.scenes =
                Array.isArray(data)
                    ? data
                    : [];
        },

        preloadScenes() {
            this.scenes.forEach(scene => {
                if (!scene.image) return;

                const img = new Image();
                img.src = scene.image;
            });
        },

        init() {

            if (this.scenes.length === 0) {
                return;
            }

            // Preload every panorama
            this.preloadScenes();

            this.$nextTick(() => {
                this.createViewer();
            });
        },

                getSceneKey(index) {
                    return `scene-${index}`;
                },

                createViewer() {

                    const container =
                        this.$refs.viewer;

                    if (!container) {
                        return;
                    }

                    const pannellumScenes = {};

                    this.scenes.forEach((scene, index) => {

                        pannellumScenes[
                            this.getSceneKey(index)
                        ] = {

                            type: 'equirectangular',

                            panorama: scene.image,

                            hotSpots:
                                this.buildHotspots(
                                    scene
                                ),
                        };
                    });

                    this.viewer =
                        pannellum.viewer(
                            container,
                            {
                                default: {
                                    firstScene:
                                        this.getSceneKey(0),

                                    autoLoad:
                                        true,

                                    sceneFadeDuration:
                                        300,

                                    showControls:
                                        true,

                                    showZoomCtrl:
                                        true,

                                    showFullscreenCtrl:
                                        true,
                                },

                                scenes:
                                    pannellumScenes,
                            }
                        );
                },

                buildHotspots(scene) {

                    return (
                        scene.hotspots || []
                    ).map(h => ({

                        pitch:
                            Number(h.pitch),

                        yaw:
                            Number(h.yaw),

                        type:
                            'custom',

                        createTooltipFunc:
                            (hotSpotDiv) => {

                                const wrapper =
                                    document.createElement(
                                        'div'
                                    );

                                wrapper.style.position =
                                    'relative';

                                wrapper.style.display =
                                    'inline-flex';

                                wrapper.style.alignItems =
                                    'center';

                                wrapper.style.justifyContent =
                                    'center';


                                const ping =
                                    document.createElement(
                                        'div'
                                    );

                                Object.assign(
                                    ping.style,
                                    {
                                        position:
                                            'absolute',

                                        inset:
                                            '-10px',

                                        borderRadius:
                                            '9999px',

                                        background:
                                            'rgba(37, 99, 235, 0.25)',

                                        animation:
                                            'hotspot-ping 1.6s ease-out infinite',

                                        zIndex:
                                            '0'
                                    }
                                );


                                const button =
                                    document.createElement(
                                        'button'
                                    );

                                button.type =
                                    'button';

                                button.innerText =
                                    h.label;


                                Object.assign(
                                    button.style,
                                    {
                                        position:
                                            'relative',

                                        zIndex:
                                            '2',

                                        background:
                                            '#2563eb',

                                        color:
                                            'white',

                                        border:
                                            'none',

                                        borderRadius:
                                            '999px',

                                        padding:
                                            '10px 14px',

                                        fontSize:
                                            '13px',

                                        fontWeight:
                                            '600',

                                        cursor:
                                            'pointer',

                                        boxShadow:
                                            '0 4px 12px rgba(0,0,0,0.35)',

                                        whiteSpace:
                                            'nowrap',

                                        transition:
                                            'transform .2s ease, background .2s ease'
                                    }
                                );


                                button.onmouseover =
                                    () => {

                                        button.style.transform =
                                            'scale(1.08)';

                                        button.style.background =
                                            '#1d4ed8';
                                    };


                                button.onmouseout =
                                    () => {

                                        button.style.transform =
                                            'scale(1)';

                                        button.style.background =
                                            '#2563eb';
                                    };


                                button.onclick =
                                    (e) => {

                                        e.preventDefault();

                                        e.stopPropagation();

                                        const currentScene =
                                            scene;

                                        const targetIndex =
                                            this.scenes.findIndex(
                                                s =>
                                                    Number(s.id)
                                                    ===
                                                    Number(
                                                        h.target_scene_id
                                                    )
                                            );

                                        if (
                                            targetIndex === -1
                                        ) {
                                            return;
                                        }

                                        const targetScene =
                                            this.scenes[
                                                targetIndex
                                            ];

                                        /*
                                        |--------------------------------------------------------------------------
                                        | FACE RETURN HOTSPOT
                                        |--------------------------------------------------------------------------
                                        */

                                        const returnHotspot =
                                            (
                                                targetScene.hotspots
                                                || []
                                            ).find(
                                                targetHotspot =>
                                                    Number(
                                                        targetHotspot
                                                            .target_scene_id
                                                    )
                                                    ===
                                                    Number(
                                                        currentScene.id
                                                    )
                                            );

                                        const sceneKey =
                                            this.getSceneKey(
                                                targetIndex
                                            );

                                        if (returnHotspot) {

                                            const pitch =
                                                Number(
                                                    returnHotspot.pitch
                                                );

                                            const yaw =
                                                Number(
                                                    returnHotspot.yaw
                                                );

                                            if (
                                                Number.isFinite(
                                                    pitch
                                                )
                                                &&
                                                Number.isFinite(
                                                    yaw
                                                )
                                            ) {

                                                this.viewer.loadScene(
                                                    sceneKey,
                                                    pitch,
                                                    yaw
                                                );

                                            } else {

                                                this.viewer.loadScene(
                                                    sceneKey
                                                );
                                            }

                                        } else {

                                            this.viewer.loadScene(
                                                sceneKey
                                            );
                                        }

                                        this.activeIndex =
                                            targetIndex;
                                    };


                                wrapper.appendChild(
                                    ping
                                );

                                wrapper.appendChild(
                                    button
                                );

                                hotSpotDiv.appendChild(
                                    wrapper
                                );

                                hotSpotDiv.style.transform =
                                    'translate(-50%, -50%)';
                            }
                    }));
                }

            }));

        });
    </script>
</div>
