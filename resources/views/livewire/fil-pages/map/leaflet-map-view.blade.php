@push('styles')

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/pannellum@2.5.7/build/pannellum.css"
    >

    <style>
        #estate-leaflet-map {
            width: 100%;
            height: 700px;
        }

        .estate-lot-tooltip {
            background: rgba(17, 24, 39, .92);
            color: white;
            border: none;
            border-radius: 6px;
            box-shadow: none;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 7px;
        }

        .estate-lot-tooltip::before {
            display: none;
        }

        .estate-sold-label {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .estate-sold-label::before {
            display: none !important;
        }

        .estate-sold-label-inner {
            width: 60px;
            height: 24px;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #111827;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;

            text-align: center;
            white-space: nowrap;

            pointer-events: none;
        }

        .estate-construction-icon {
            background: transparent !important;
            border: 0 !important;
        }

        .estate-construction-badge {
            width: 26px;
            height: 26px;
            border-radius: 9999px;
            background: rgba(245, 158, 11, .95);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .18);
            box-sizing: border-box;
            flex-shrink: 0;
        }

        .estate-construction-badge svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }

        .estate-leaflet-popup .leaflet-popup-content-wrapper {
            padding: 0;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.22);
        }

        .estate-leaflet-popup .leaflet-popup-content {
            margin: 0 !important;
            width: auto !important;
        }

        .estate-leaflet-popup .leaflet-popup-tip {
            background: white;
        }

        .estate-leaflet-popup .leaflet-popup-close-button {
            display: none;
        }

        .estate-popup {
            width: 320px;
            max-width: 80vw;
            font-family: inherit;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .estate-popup-panorama {
            width: 100%;
            height: 160px;
            background: #f3f4f6;
            overflow: hidden;
        }

        .estate-popup-body {
            padding: 12px 14px 14px;
        }

        .estate-popup-close {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 9999px;
            background: rgba(255, 255, 255, .92);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .14);
            cursor: pointer;
            z-index: 20;
        }

        .estate-popup-close:hover {
            background: white;
        }

        .estate-popup-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .estate-popup-title {
            font-size: 16px;
            line-height: 1.25;
            font-weight: 700;
            color: #111827;
            word-break: break-word;
        }

        .estate-popup-construction {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 4px;
            margin-bottom: 3px;
            color: #ea580c;
            font-size: 11px;
            line-height: 1.2;
            font-weight: 600;
        }

        .estate-popup-construction svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .estate-popup-meta {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-top: 3px;
            color: #6b7280;
            font-size: 12px;
            font-style: italic;
        }

        .estate-popup-meta-dot {
            width: 6px;
            height: 6px;
            flex-shrink: 0;
            border-radius: 9999px;
            background: #6b7280;
        }

        .estate-popup-area {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-style: normal;
        }

        .estate-popup-area svg {
            width: 15px;
            height: 15px;
        }

        .estate-popup-status {
            flex-shrink: 0;
            padding: 6px 12px;
            border-radius: 9999px;
            background: #bbf7d0;
            color: #166534;
            font-size: 11px;
            line-height: 1;
            text-transform: capitalize;
            font-weight: 500;
        }

        .estate-popup-extra {
            margin-top: 14px;
            padding: 12px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
        }

        .estate-popup-extra-section + .estate-popup-extra-section {
            margin-top: 14px;
        }

        .estate-popup-section-label {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .estate-popup-section-label span {
            flex-shrink: 0;
            padding: 2px 8px;
            border-radius: 9999px;
            background: #bfdbfe;
            color: #1e40af;
            font-size: 10px;
            line-height: 1.4;
            text-transform: capitalize;
        }

        .estate-popup-section-label hr {
            width: 100%;
            border: 0;
            border-top: 1px solid #3b82f6;
        }

        .estate-popup-person {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
            padding: 8px;
            border-radius: 8px;
            background: #f3f4f6;
        }

        .estate-popup-person img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 1px solid #d1d5db;
            flex-shrink: 0;
        }

        .estate-popup-person.user img {
            border-radius: 9999px;
        }

        .estate-popup-person.model img {
            border-radius: 6px;
        }

        .estate-popup-person strong {
            color: #1f2937;
            font-size: 12px;
            font-weight: 600;
            word-break: break-word;
        }

        .estate-popup-price {
            margin-top: 12px;
            color: #1f2937;
            font-size: 17px;
            font-weight: 600;
            text-align: right;
        }

        .estate-popup-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 14px;
        }

        .estate-popup-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .estate-popup-button svg {
            width: 16px;
            height: 16px;
        }

        .estate-popup-button-edit {
            color: #374151;
            background: white;
            border: 1px solid #d1d5db;
        }

        .estate-popup-button-delete {
            color: white;
            background: #dc2626;
            border: 1px solid #dc2626;
        }

        .estate-custom-tooltip {
            position: absolute;
            z-index: 1000;
            width: 320px;
            max-width: calc(100% - 20px);
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.22);
            overflow: visible;
            display: none;
        }

        .estate-custom-tooltip.is-visible {
            display: block;
        }

        .estate-custom-tooltip-arrow {
            position: absolute;
            width: 0;
            height: 0;
            pointer-events: none;
            z-index: 1001;
            transform: translateX(-50%);
        }

        .estate-custom-tooltip .estate-popup {
            width: 100%;
            max-width: none;
        }

        .estate-custom-tooltip .estate-popup-panorama {
            width: 100%;
            height: 160px;
            background: #f3f4f6;
            overflow: hidden;
        }

        .estate-custom-tooltip .estate-popup-panorama .pnlm-container,
        .estate-custom-tooltip .estate-popup-panorama canvas {
            width: 100% !important;
            height: 100% !important;
        }

    </style>
@endpush


<div>

    {{-- SUCCESS TOAST AFTER PAGE RELOAD --}}
    @if (session('gis_success'))
        <div
            x-data
            x-init="
                setTimeout(() => {
                    $wire.showMapNotification(
                        'Success',
                        @js(session('gis_success')),
                        'success'
                    );
                }, 150);
            "
        ></div>
    @endif

    @php
        $leafletLots = collect($lots)
            ->map(function ($lot) {
                return [
                    'id' => $lot->id,
                    'name' => $lot->name,
                    'geo_coords' => $lot->geo_coords,
                    'type' => $lot->type,
                    'status' => $lot->status,
                    'price' => $lot->price,
                    'lot_area' => $lot->lot_area,
                    'image' => $lot->image
                        ? asset('storage/' . $lot->image)
                        : null,
                    'is_under_construction' => (bool) $lot->is_under_construction,

                    'user' => $lot->user
                        ? [
                            'id' => $lot->user->id,
                            'name' => $lot->user->name,
                            'picture' => $lot->user->profile_picture
                                ? asset($lot->user->profile_picture)
                                : null,
                        ]
                        : null,

                    'house_model' => $lot->houseModel
                        ? [
                            'id' => $lot->houseModel->id,
                            'name' => $lot->houseModel->model_name,
                            'image' => $lot->houseModel->image
                                ? asset('storage/' . $lot->houseModel->image)
                                : null,
                        ]
                        : null,
                ];
            })
            ->values()
            ->toArray();
    @endphp

    <div
        class="bg-white rounded-2xl shadow-md p-5 border border-gray-100 mt-24"
    >

        {{-- HEADER --}}
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5"
        >
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Subdivision Lot Map
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Click on any lot to view details or assign to a client
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    onclick="startEstateLotMapping()"
                    class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800"
                >
                    New Lot Area
                </button>

                {{-- <button
                    type="button"
                    onclick="resetEstateGISView()"
                    class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium"
                >
                    Reset View
                </button> --}}
            </div>
        </div>

        {{-- MAP --}}
        <div class="relative w-full">

            <div
                id="estate-leaflet-map"
                wire:ignore
                class="w-full rounded-xl border border-gray-200 overflow-hidden"
                style="height: 700px; z-index: 0;"
            ></div>


            {{-- RESET VIEW --}}
            <button
                type="button"
                onclick="resetEstateGISView()"
                class="
                    absolute
                    top-4
                    right-4
                    z-[10]
                    flex
                    items-center
                    gap-2
                    px-3
                    py-2
                    rounded-lg
                    border
                    border-gray-200
                    bg-white
                    text-gray-700
                    text-sm
                    font-medium
                    shadow-md
                    hover:bg-gray-50
                    transition
                "
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M3 12a9 9 0 1 0 3-6.7"/>
                    <path d="M3 3v6h6"/>
                </svg>

                <span>
                    Reset View
                </span>
            </button>


            {{-- CUSTOM LOT TOOLTIP --}}
            <div
                id="estate-custom-tooltip"
                class="estate-custom-tooltip"
            >
                <div
                    id="estate-custom-tooltip-arrow"
                    class="estate-custom-tooltip-arrow"
                ></div>

                <div
                    id="estate-custom-tooltip-content"
                ></div>
            </div>

        </div>

        {{-- CREATE FORM --}}
        <x-modal
            name="create-gis-lot-modal"
            max-width="2xl"
            blur="md"
            align="center"
            x-on:gis-create-success.window="
                destroyEstateModalMiniMap('estate-create-mini-map');
                close();

                setTimeout(() => {
                    window.location.reload();
                }, 200);
            "
        >
            <div class="!max-w-4xl w-full mx-auto">
                <form wire:submit.prevent="createLotArea">
                    <x-card title="Create New Lot Area" class="!max-w-4xl w-full">

                        <p class="text-sm text-gray-500 -mt-2 mb-4">
                            Enter the information for the lot you mapped.
                        </p>

                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Draw Lot Boundary
                                </label>

                                <button
                                    type="button"
                                    onclick="resetEstateCreateDrawingMap('estate-create-mini-map')"
                                    class="text-xs font-medium text-blue-600 hover:text-blue-700"
                                >
                                    Clear Drawing
                                </button>
                            </div>

                            <div
                                wire:ignore
                                class="relative w-full h-[280px] rounded-xl border border-gray-200 overflow-hidden bg-gray-100"
                            >
                                <div
                                    id="estate-create-mini-map"
                                    class="absolute inset-0 w-full h-full"
                                ></div>


                                {{-- RESET VIEW --}}
                                <button
                                    type="button"
                                    onclick="resetEstateModalMapView('estate-create-mini-map')"
                                    class="
                                        absolute
                                        top-3
                                        right-3
                                        z-[500]
                                        flex
                                        items-center
                                        gap-2
                                        px-3
                                        py-2
                                        rounded-lg
                                        border
                                        border-gray-200
                                        bg-white
                                        text-gray-700
                                        text-xs
                                        font-medium
                                        shadow-md
                                        hover:bg-gray-50
                                        transition
                                    "
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                                        <path d="M3 3v6h6"/>
                                    </svg>

                                    <span>
                                        Reset View
                                    </span>
                                </button>
                            </div>

                            <p class="text-xs text-gray-400 mt-2">
                                Click points directly on the map to draw the new lot boundary. Click the first point to finish.
                            </p>
                        </div>
                        <div class="mt-3">
                            <x-input
                                label="Lot Name"
                                placeholder="Ex: Block 1, Lot 43"
                                wire:model.defer="lotName"
                            />
                        </div>

                        <div class="mt-3">
                            <x-input
                                type="number"
                                step="0.1"
                                min="0"
                                label="Lot Area"
                                suffix="sqm"
                                wire:model.defer="lotArea"
                            />
                        </div>

                        <div class="mt-3">
                            <x-native-select
                                label="Property Type"
                                wire:model.live="lotType"
                            >
                                <option value="">
                                    Select Type
                                </option>

                                <option value="Playground & Community Amenities">
                                    Playground & Community Amenities
                                </option>

                                <option value="Model House">
                                    Model House
                                </option>

                                <option value="Lot Only">
                                    Lot Only
                                </option>

                                <option value="House & Lot">
                                    House & Lot
                                </option>
                            </x-native-select>
                        </div>

                        @if (
                            $lotType &&
                            !in_array($lotType, [
                                'Model House',
                                'Playground & Community Amenities',
                            ])
                        )
                            <div class="mt-3">
                                <x-inputs.currency
                                    label="Price"
                                    placeholder="Enter price"
                                    icon="banknotes"
                                    currency="PHP"
                                    thousands=","
                                    decimal="."
                                    precision="2"
                                    wire:model.defer="lotPrice"
                                />
                            </div>
                        @endif

                        @if (
                            $lotType &&
                            !in_array($lotType, [
                                'Model House',
                                'Playground & Community Amenities',
                            ])
                        )
                            <div class="mt-3">
                                <h2 class="text-[#15233C] font-tertiary font-medium text-sm mb-1">
                                    Status
                                </h2>

                                <div class="grid w-full gap-2 grid-cols-3">
                                    @php
                                        $createOptions = [
                                            'available' => 'Available',
                                            'sold' => 'Sold',
                                            'reserved' => 'Reserved',
                                        ];
                                    @endphp

                                    @foreach($createOptions as $value => $label)
                                        <div>
                                            <input
                                                wire:model.live="lotStatus"
                                                type="radio"
                                                id="gisCreateLotStatus{{ $value }}"
                                                name="gisCreateLotStatus"
                                                value="{{ $value }}"
                                                class="hidden peer"
                                            >

                                            <label
                                                for="gisCreateLotStatus{{ $value }}"
                                                class="inline-flex items-center justify-center w-full p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer
                                                    peer-checked:border-2 peer-checked:border-blue-600 peer-checked:text-blue-600
                                                    hover:text-gray-600 hover:bg-gray-100 transition text-sm font-medium"
                                            >
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                @error('lotStatus')
                                    <span class="text-red-500 text-[10px] italic">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        @endif

                        @if (
                            $lotStatus &&
                            $lotStatus !== 'available' &&
                            !in_array($lotType, [
                                'Model House',
                                'Playground & Community Amenities',
                            ])
                        )
                            <div class="mt-3">
                                <x-select
                                    label="Client Name"
                                    wire:model.defer="userId"
                                    placeholder="Select some client"
                                    :async-data="route('api.users.index')"
                                    :template="[
                                        'name' => 'user-option',
                                        'config' => ['src' => 'profile_picture']
                                    ]"
                                    option-label="name"
                                    option-value="id"
                                    option-description="email"
                                />
                            </div>
                        @endif

                        @if ($lotType === 'Model House')
                            <div class="mt-3">
                                <x-select
                                    label="House Model"
                                    wire:model.defer="houseModelId"
                                    placeholder="Select some house model"
                                    :async-data="route('api.house-models.index')"
                                    :template="[
                                        'name' => 'user-option',
                                        'config' => ['src' => 'image']
                                    ]"
                                    option-label="name"
                                    option-value="id"
                                    option-description="description"
                                />
                            </div>
                        @endif

                        <div
                            class="mt-3 flex items-center justify-between bg-gray-50 p-3 rounded-lg border"
                        >
                            <div>
                                <h3 class="text-sm font-medium text-gray-700">
                                    Under Construction
                                </h3>

                                <p class="text-xs text-gray-400">
                                    Mark this lot as under construction
                                </p>
                            </div>

                            <x-toggle
                                wire:model.defer="isUnderConstruction"
                            />
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium mb-2">
                                Lot Image
                            </label>

                            <x-filepond::upload
                                wire:model="lotImage"
                                :accepted-file-types="[
                                    'image/png',
                                    'image/jpeg',
                                    'image/webp'
                                ]"
                                label="Upload your 360 View"
                            />
                        </div>    

                        <div class="mt-3">
                            <x-input
                                label="GIS Coordinates"
                                :value="json_encode($newGeoCoords)"
                                readonly
                            />
                        </div>

                        <x-slot name="footer" class="flex justify-end gap-x-3">
                            <x-button
                                flat
                                label="Cancel"
                                type="button"
                                wire:click="cancelMapping"
                                x-on:click="
                                    destroyEstateModalMiniMap('estate-create-mini-map');
                                    close();
                                "
                            />

                            <x-button
                                primary
                                label="Save Lot"
                                type="submit"
                            />
                        </x-slot>

                    </x-card>
                </form>
            </div>
        </x-modal>


        {{-- EDIT FORM --}}
        <x-modal
            name="edit-gis-lot-modal"
            persistent
            blur="md"
            align="center"
            max-width="2xl"
            x-on:gis-edit-success.window="
                destroyEstateModalMiniMap('estate-edit-mini-map');
                close();

                setTimeout(() => {
                    window.location.reload();
                }, 200);
            "
        >
            <div class="w-full">
                <form wire:submit.prevent="updateLot">

                    <x-card title="Edit Lot Area">

                        <p class="text-sm text-gray-500 -mt-2 mb-4">
                            Update the lot information and adjust its GIS boundary directly on the map.
                        </p>

                        {{-- MAP - ADJUST BOUNDARY --}}
                        <div class="mt-4">

                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Adjust Lot Boundary
                                </label>

                                <button
                                    type="button"
                                    onclick="resetEstateModalMiniMap('estate-edit-mini-map')"
                                    class="text-xs font-medium text-blue-600 hover:text-blue-700"
                                >
                                    Reset Points
                                </button>
                            </div>

                            <div
                                wire:ignore
                                class="relative w-full h-[280px] rounded-xl border border-gray-200 overflow-hidden bg-gray-100"
                            >
                                <div
                                    id="estate-edit-mini-map"
                                    class="absolute inset-0 w-full h-full"
                                ></div>


                                {{-- RESET VIEW --}}
                                <button
                                    type="button"
                                    onclick="resetEstateModalMapView('estate-edit-mini-map')"
                                    class="
                                        absolute
                                        top-3
                                        right-3
                                        z-[500]
                                        flex
                                        items-center
                                        gap-2
                                        px-3
                                        py-2
                                        rounded-lg
                                        border
                                        border-gray-200
                                        bg-white
                                        text-gray-700
                                        text-xs
                                        font-medium
                                        shadow-md
                                        hover:bg-gray-50
                                        transition
                                    "
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                                        <path d="M3 3v6h6"/>
                                    </svg>

                                    <span>
                                        Reset View
                                    </span>
                                </button>
                            </div>

                            <p class="text-xs text-gray-400 mt-2">
                                Drag the markers on the map to adjust the boundary.
                            </p>

                        </div>

                        {{-- LOT NAME --}}
                        <div class="mt-3">
                            <x-input
                                label="Lot Name"
                                placeholder="Ex: Block 1, Lot 43"
                                wire:model.defer="editLotName"
                            />
                        </div>

                        {{-- LOT AREA --}}
                        <div class="mt-3">
                            <x-input
                                type="number"
                                step="0.1"
                                min="0"
                                class="pr-28"
                                label="Lot Area"
                                placeholder="100"
                                suffix="sqm"
                                wire:model.defer="editLotArea"
                            />
                        </div>

                        {{-- LOT TYPE --}}
                        <div class="mt-3">
                            <x-native-select
                                label="Lot Type"
                                wire:model.live="editLotType"
                            >
                                <option value="">
                                    Select Type
                                </option>

                                <option value="Playground & Community Amenities">
                                    Playground & Community Amenities
                                </option>

                                <option value="Model House">
                                    Model House
                                </option>

                                <option value="Lot Only">
                                    Lot Only
                                </option>

                                <option value="House & Lot">
                                    House & Lot
                                </option>
                            </x-native-select>
                        </div>

                        {{-- PRICE --}}
                        @if (
                            $editLotType &&
                            !in_array($editLotType, [
                                'Model House',
                                'Playground & Community Amenities',
                            ])
                        )
                            <div class="mt-3">
                                <x-inputs.currency
                                    label="Price"
                                    placeholder="Enter price"
                                    icon="banknotes"
                                    currency="PHP"
                                    thousands=","
                                    decimal="."
                                    precision="2"
                                    wire:model.defer="editLotPrice"
                                />
                            </div>
                        @endif

                        {{-- STATUS --}}
                        @if (
                            $editLotType &&
                            !in_array($editLotType, [
                                'Model House',
                                'Playground & Community Amenities',
                            ])
                        )
                            <div class="mt-3">

                                <h2
                                    class="text-[#15233C] font-tertiary font-medium text-sm mb-1"
                                >
                                    Status
                                </h2>

                                <div class="grid w-full gap-2 grid-cols-3">

                                    @php
                                        $editOptions = [
                                            'available' => 'Available',
                                            'sold' => 'Sold',
                                            'reserved' => 'Reserved',
                                        ];
                                    @endphp

                                    @foreach($editOptions as $value => $label)

                                        <div>

                                            <input
                                                wire:model.live="editLotStatus"
                                                type="radio"
                                                id="gisEditLotStatus{{ $value }}"
                                                name="gisEditLotStatus"
                                                value="{{ $value }}"
                                                class="hidden peer"
                                            >

                                            <label
                                                for="gisEditLotStatus{{ $value }}"
                                                class="
                                                    inline-flex
                                                    items-center
                                                    justify-center
                                                    w-full
                                                    p-3
                                                    text-gray-500
                                                    bg-white
                                                    border
                                                    border-gray-200
                                                    rounded-lg
                                                    cursor-pointer
                                                    peer-checked:border-2
                                                    peer-checked:border-blue-600
                                                    peer-checked:text-blue-600
                                                    hover:text-gray-600
                                                    hover:bg-gray-100
                                                    transition
                                                    text-sm
                                                    font-medium
                                                "
                                            >
                                                {{ $label }}
                                            </label>

                                        </div>

                                    @endforeach

                                </div>

                                @error('editLotStatus')
                                    <span class="text-red-500 text-[10px] italic">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        @endif

                        {{-- CLIENT --}}
                        @if (
                            $editLotStatus &&
                            $editLotStatus !== 'available' &&
                            !in_array($editLotType, [
                                'Model House',
                                'Playground & Community Amenities',
                            ])
                        )
                            <div class="mt-3">
                                <x-select
                                    label="Client Name"
                                    wire:model.defer="editUserId"
                                    placeholder="Select some client"
                                    :async-data="route('api.users.index')"
                                    :template="[
                                        'name' => 'user-option',
                                        'config' => ['src' => 'profile_picture']
                                    ]"
                                    option-label="name"
                                    option-value="id"
                                    option-description="email"
                                />
                            </div>
                        @endif

                        {{-- HOUSE MODEL --}}
                        @if (
                            $editLotType &&
                            in_array($editLotType, [
                                'Model House',
                                'House & Lot',
                            ])
                        )
                            <div class="mt-3">
                                <x-select
                                    label="House Model"
                                    wire:model.defer="editHouseModelId"
                                    placeholder="Select some house model"
                                    :async-data="route('api.house-models.index')"
                                    :template="[
                                        'name' => 'user-option',
                                        'config' => ['src' => 'image']
                                    ]"
                                    option-label="name"
                                    option-value="id"
                                    option-description="description"
                                />
                            </div>
                        @endif

                        {{-- UNDER CONSTRUCTION --}}
                        <div
                            class="
                                mt-3
                                flex
                                items-center
                                justify-between
                                bg-gray-50
                                p-3
                                rounded-lg
                                border
                            "
                        >
                            <div>
                                <h3 class="text-sm font-medium text-gray-700">
                                    Under Construction
                                </h3>

                                <p class="text-xs text-gray-400">
                                    Mark this lot as under construction
                                </p>
                            </div>

                            <x-toggle
                                wire:model.defer="editIsUnderConstruction"
                            />
                        </div>


                        {{-- CURRENT IMAGE --}}
                        @if($editLotImagePreview)
                            <div class="mt-4">

                                <p class="text-sm font-medium text-gray-700 mb-2">
                                    Current Image
                                </p>

                                <img
                                    src="{{ asset('storage/' . $editLotImagePreview) }}"
                                    class="
                                        w-full
                                        max-h-60
                                        object-cover
                                        rounded-xl
                                        border
                                        border-gray-200
                                        shadow-sm
                                    "
                                >

                            </div>
                        @endif


                        {{-- REPLACE IMAGE --}}
                        <div class="mt-4">

                            <label class="block text-sm font-medium mb-2">
                                Lot Image
                            </label>

                            <x-filepond::upload
                                wire:model="editLotImage"
                                :accepted-file-types="[
                                    'image/png',
                                    'image/jpeg',
                                    'image/webp'
                                ]"
                                label="Upload your 360 View"
                            />

                        </div>


                     


                        {{-- GIS COORDINATES --}}
                        <div class="mt-3">

                            <x-input
                                label="GIS Coordinates"
                                :value="json_encode($editGeoCoords)"
                                readonly
                            />

                        </div>


                        <x-slot
                            name="footer"
                            class="flex justify-end gap-x-3"
                        >

                            <x-button
                                flat
                                label="Cancel"
                                type="button"
                                wire:click="cancelMapping"
                                x-on:click="
    destroyEstateModalMiniMap('estate-edit-mini-map');
    close();
"
                            />

                            <x-button
                                primary
                                label="Save Changes"
                                type="submit"
                            />

                        </x-slot>

                    </x-card>

                </form>
            </div>
        </x-modal>

    </div>

    <script>
        window.estateGISLots = @json($leafletLots);
    </script>
</div>


@push('scripts')

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.7/build/pannellum.js"></script>


    <script>
        window.estateLeafletMap = null;
        window.estateLotLayer = null;
        window.estateDrawingLayer = null;
        window.estateBoundaryLayer = null;
        window.estateConstructionMarkers = [];
        window.estateSoldMarkers = [];
        window.estateModalMiniMaps = {};
        window.estatePopupPanoramaViewer = null;
        window.estateCustomTooltipLotId = null;
        window.estateCustomTooltipLatLng = null;


        const manhattanResidences = [
            13.919700,
            121.421307
        ];


        const estateSubdivisionBoundary = [
            [13.920650, 121.420350],
            [13.920720, 121.421820],
            [13.920300, 121.422300],
            [13.919150, 121.422250],
            [13.918720, 121.421700],
            [13.918800, 121.420500],
            [13.919350, 121.420100]
        ];


        const estateLotColors = {
            "Playground & Community Amenities": "#f2b879",
            "Model House": "#c8c9c3",
            "Lot Only": "#c4e0b7",
            "House & Lot": "#f8e89c",
            "Sold": "#e9b4ae",
        };


        function initEstateLeafletMap()
        {
            const container =
                document.getElementById(
                    'estate-leaflet-map'
                );

            if (
                !container ||
                typeof L === 'undefined'
            ) {
                return;
            }


            hideEstateCustomTooltip();

            if (window.estateLeafletMap) {
                window.estateLeafletMap.remove();
                window.estateLeafletMap = null;
            }


            const map = L.map(
                container,
                {
                    zoomControl: true,
                    minZoom: 5,
                    maxZoom: 22,
                }
            );

            window.estateLeafletMap = map;

            L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                maxZoom: 22,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '© Google',
            }).addTo(map);

            const boundary = L.polygon(
                estateSubdivisionBoundary,
                {
                    color: '#2563eb',
                    weight: 4,
                    dashArray: '10 6',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.05,
                    interactive: false,
                }
            ).addTo(map);

            window.estateBoundaryLayer =
                boundary;


            const lotLayer =
                L.layerGroup()
                    .addTo(map);

            window.estateLotLayer =
                lotLayer;


            const drawingLayer =
                new L.FeatureGroup();

            map.addLayer(
                drawingLayer
            );

            window.estateDrawingLayer =
                drawingLayer;


            map.on(
                'zoomend',
                function()
                {
                    updateEstateLotOverlaySizes();
                }
            );


            map.on(
                'move zoom resize',
                function()
                {
                    refreshEstateCustomTooltipPosition();
                }
            );


            map.on(
                L.Draw.Event.CREATED,
                function(event)
                {
                    const polygon =
                        event.layer;


                    if (
                        !isPolygonInsideBoundary(
                            polygon
                        )
                    ) {
                        @this.call(
                            'showMapNotification',
                            'Outside Subdivision Boundary',
                            'Lot must be fully inside the Manhattan Residences boundary.',
                            'danger'
                        );

                        return;
                    }


                    if (
                        doesPolygonOverlapExistingLot(
                            polygon
                        )
                    ) {
                        @this.call(
                            'showMapNotification',
                            'Lot Area Already Mapped',
                            'This area overlaps an already mapped lot.',
                            'danger'
                        );

                        return;
                    }


                    drawingLayer.clearLayers();

                    polygon.setStyle({
                        color: '#2563eb',
                        weight: 3,
                        fillColor: '#3b82f6',
                        fillOpacity: 0.30,
                    });

                    drawingLayer.addLayer(
                        polygon
                    );

                    const coordinates =
                        getPolygonCoordinates(
                            polygon
                        );

                    @this.set(
                        'newGeoCoords',
                        coordinates
                    ).then(() => {

                        $openModal(
                            'create-gis-lot-modal'
                        );


                        initEstateModalMiniMap(
                            'estate-create-mini-map',
                            coordinates,
                            {
                                onChange:
                                    function(updatedCoordinates)
                                    {
                                        @this.set(
                                            'newGeoCoords',
                                            updatedCoordinates
                                        );
                                    },
                            }
                        );

                    });
                }
            );


            map.fitBounds(
                boundary.getBounds(),
                {
                    padding: [
                        30,
                        30
                    ],
                }
            );


            renderExistingEstateLots();


            setTimeout(
                () => {
                    map.invalidateSize();

                    updateEstateLotOverlaySizes();
                },
                250
            );
        }


        function renderExistingEstateLots()
        {
            const lots =
                window.estateGISLots || [];


            window.estateConstructionMarkers = [];
            window.estateSoldMarkers = [];


            lots.forEach(
                lot => {

                    if (
                        !Array.isArray(
                            lot.geo_coords
                        )
                        ||
                        lot.geo_coords.length < 3
                    ) {
                        return;
                    }


                    const color =
                        getEstateLotColor(
                            lot
                        );


                    const polygon =
                        L.polygon(
                            lot.geo_coords,
                            {
                                color: color,
                                fillColor: color,
                                weight: 2,
                                fillOpacity: 0.50,
                            }
                        );


                    polygon.estateLotId =
                        lot.id;
                    polygon.estateLot =
                        lot;


                    polygon.bindTooltip(
                        lot.name ?? 'Lot',
                        {
                            direction: 'center',
                            className:
                                'estate-lot-tooltip',
                        }
                    );


                    polygon.on(
                        'click',
                        function(event)
                        {
                            showEstateCustomTooltip(
                                lot,
                                event.latlng
                            );
                        }
                    );


                    polygon.addTo(
                        window.estateLotLayer
                    );

                    if (
                        (
                            lot.status || ''
                        )
                            .toLowerCase()
                            .trim()
                        === 'sold'
                    ) {
                        const center =
                            getEstatePolygonCenter(
                                polygon
                            );

                        const soldScale =
                            getEstateLotOverlayScale(
                                polygon
                            );

                        const soldMarker =
                            L.marker(
                                center,
                                {
                                    interactive: false,

                                    opacity:
                                        soldScale > 0
                                            ? 1
                                            : 0,

                                    icon:
                                        buildEstateSoldIcon(
                                            soldScale
                                        ),
                                }
                            );

                        soldMarker.estatePolygon =
                            polygon;

                        soldMarker.addTo(
                            window.estateLotLayer
                        );

                        window.estateSoldMarkers.push(
                            soldMarker
                        );
                    }

                    if (
                        lot.is_under_construction
                    ) {
                        const center =
                            getEstatePolygonCenter(
                                polygon
                            );

                        const constructionScale =
                            getEstateLotOverlayScale(
                                polygon
                            );

                        const constructionMarker =
                            L.marker(
                                center,
                                {
                                    interactive: false,

                                    opacity:
                                        constructionScale > 0
                                            ? 1
                                            : 0,

                                    icon:
                                        buildEstateConstructionIcon(
                                            constructionScale
                                        ),
                                }
                            );

                        constructionMarker.estatePolygon =
                            polygon;

                        constructionMarker.addTo(
                            window.estateLotLayer
                        );

                        window.estateConstructionMarkers.push(
                            constructionMarker
                        );
                    }

                }
            );
        }

        function getEstatePolygonCenter(
            polygon
        )
        {
            const points =
                polygon.getLatLngs()[0];


            if (
                !Array.isArray(points)
                ||
                points.length < 3
            ) {
                return polygon
                    .getBounds()
                    .getCenter();
            }


            let area = 0;
            let centerLat = 0;
            let centerLng = 0;


            for (
                let i = 0;
                i < points.length;
                i++
            ) {
                const current =
                    points[i];

                const next =
                    points[
                        (i + 1) %
                        points.length
                    ];


                const cross =
                    current.lng * next.lat
                    -
                    next.lng * current.lat;


                area += cross;

                centerLng +=
                    (
                        current.lng +
                        next.lng
                    )
                    *
                    cross;

                centerLat +=
                    (
                        current.lat +
                        next.lat
                    )
                    *
                    cross;
            }


            area *= 0.5;


            if (
                Math.abs(area) <
                0.000000000001
            ) {
                return polygon
                    .getBounds()
                    .getCenter();
            }


            centerLng /=
                6 * area;

            centerLat /=
                6 * area;


            return L.latLng(
                centerLat,
                centerLng
            );
        }

        function getEstatePolygonPixelSize(
            polygon
        )
        {
            const map =
                window.estateLeafletMap;


            if (
                !map
                ||
                !polygon
                ||
                !map._loaded
            ) {
                return {
                    width: 0,
                    height: 0,
                };
            }


            const bounds =
                polygon.getBounds();


            if (
                !bounds
                ||
                !bounds.isValid()
            ) {
                return {
                    width: 0,
                    height: 0,
                };
            }


            const northWest =
                map.latLngToContainerPoint(
                    bounds.getNorthWest()
                );


            const southEast =
                map.latLngToContainerPoint(
                    bounds.getSouthEast()
                );


            return {
                width:
                    Math.abs(
                        southEast.x -
                        northWest.x
                    ),

                height:
                    Math.abs(
                        southEast.y -
                        northWest.y
                    ),
            };
        }


        function getEstateLotOverlayScale(
            polygon
        )
        {
            const pixelSize =
                getEstatePolygonPixelSize(
                    polygon
                );

            const smallestSide =
                Math.min(
                    pixelSize.width,
                    pixelSize.height
                );

            if (
                !Number.isFinite(
                    smallestSide
                )
                ||
                smallestSide < 6
            ) {
                return 0;
            }

            return Math.min(
                1,
                smallestSide / 45
            );
        }


        function buildEstateSoldIcon(
            scale
        )
        {
            const safeScale =
                Math.max(
                    0,
                    scale || 0
                );

            const width =
                Math.max(
                    1,
                    60 * safeScale
                );

            const height =
                Math.max(
                    1,
                    24 * safeScale
                );

            const fontSize =
                Math.max(
                    1,
                    11 * safeScale
                );

            return L.divIcon({
                className:
                    'estate-sold-label',

                html: `
                    <div
                        class="estate-sold-label-inner"
                        style="
                            width:${width}px;
                            height:${height}px;
                            font-size:${fontSize}px;
                        "
                    >
                        SOLD
                    </div>
                `,

                iconSize: [
                    width,
                    height,
                ],

                iconAnchor: [
                    width / 2,
                    height / 2,
                ],
            });
        }


        function buildEstateConstructionIcon(
            scale
        )
        {
            const safeScale =
                Math.max(
                    0,
                    scale || 0
                );

            const size =
                Math.max(
                    1,
                    26 * safeScale
                );

            const svgSize =
                Math.max(
                    1,
                    15 * safeScale
                );

            return L.divIcon({
                className:
                    'estate-construction-icon',

                html: `
                    <div
                        class="estate-construction-badge"
                        style="
                            width:${size}px;
                            height:${size}px;
                        "
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="white"
                            style="
                                width:${svgSize}px;
                                height:${svgSize}px;
                            "
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75"
                            />
                        </svg>

                    </div>
                `,

                iconSize: [
                    size,
                    size,
                ],

                iconAnchor: [
                    size / 2,
                    size / 2,
                ],
            });
        }


        function updateEstateLotOverlaySizes()
        {
            (window.estateSoldMarkers || [])
                .forEach(
                    function(marker)
                    {
                        const polygon =
                            marker.estatePolygon;

                        if (!polygon) {
                            return;
                        }

                        const scale =
                            getEstateLotOverlayScale(
                                polygon
                            );

                        marker.setOpacity(
                            scale > 0
                                ? 1
                                : 0
                        );

                        marker.setIcon(
                            buildEstateSoldIcon(
                                scale
                            )
                        );
                    }
                );


            (window.estateConstructionMarkers || [])
                .forEach(
                    function(marker)
                    {
                        const polygon =
                            marker.estatePolygon;

                        if (!polygon) {
                            return;
                        }

                        const scale =
                            getEstateLotOverlayScale(
                                polygon
                            );

                        marker.setOpacity(
                            scale > 0
                                ? 1
                                : 0
                        );

                        marker.setIcon(
                            buildEstateConstructionIcon(
                                scale
                            )
                        );
                    }
                );
        }


        function getEstateLotColor(lot)
        {
            if (
                (
                    lot.status || ''
                )
                    .toLowerCase()
                    .trim()
                === 'sold'
            ) {
                return estateLotColors.Sold;
            }


            return estateLotColors[
                lot.type
            ] ?? '#0096ff';
        }


        function buildEstatePopup(lot)
        {
            const propertyType =
                (
                    lot.type || ''
                ).trim();


            const status =
                (
                    lot.status || ''
                )
                    .toLowerCase()
                    .trim();


            const hidePriceAndStatus =
                [
                    'Model House',
                    'Playground & Community Amenities',
                ].includes(
                    propertyType
                );


            const supportsModel =
                [
                    'Model House',
                    'House & Lot',
                ].includes(
                    propertyType
                );


            const showClient =
                status !== 'available'
                &&
                !!lot.user;


            const showModel =
                supportsModel
                &&
                !!lot.house_model;


            const showExtraSection =
                showClient
                ||
                showModel;


            const price =
                lot.price
                    ? '₱' +
                        Number(
                            lot.price
                        ).toLocaleString(
                            'en-PH'
                        )
                    : '₱0';


            const assignmentLabel =
                status
                    ? `${status} to`
                    : 'assigned to';


            return `
                <div
                    class="estate-popup"
                    style="position:relative;"
                >

                    <button
                        type="button"
                        class="estate-popup-close"
                        onclick="hideEstateCustomTooltip()"
                        aria-label="Close"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            style="
                                width:20px;
                                height:20px;
                                color:#4b5563;
                            "
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 6l12 12M18 6L6 18"
                            />
                        </svg>
                    </button>


                    ${
                        lot.image
                            ? `
                                <div
                                    class="estate-popup-panorama"
                                    id="estate-pano-${lot.id}"
                                ></div>
                            `
                            : `
                                <div
                                    class="estate-popup-panorama"
                                    style="
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        color:#9ca3af;
                                        font-size:12px;
                                    "
                                >
                                    No lot image
                                </div>
                            `
                    }


                    <div class="estate-popup-body">

                        <div class="estate-popup-header">

                            <div
                                style="
                                    min-width:0;
                                    flex:1;
                                "
                            >

                                <div class="estate-popup-title">
                                    ${escapeEstateHTML(
                                        lot.name ?? 'Lot'
                                    )}
                                </div>


                                ${
                                    lot.is_under_construction
                                        ? `
                                            <div class="estate-popup-construction">

                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"
                                                    />
                                                </svg>

                                                <span>
                                                    Under Construction
                                                </span>

                                            </div>
                                        `
                                        : ''
                                }


                                <div class="estate-popup-meta">

                                    <span>
                                        ${escapeEstateHTML(
                                            propertyType || '-'
                                        )}
                                    </span>


                                    <span class="estate-popup-meta-dot"></span>


                                    <span class="estate-popup-area">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                                            />
                                        </svg>

                                        <span>
                                            ${escapeEstateHTML(
                                                lot.lot_area ?? '-'
                                            )}
                                            sqm
                                        </span>

                                    </span>

                                </div>

                            </div>


                            ${
                                !hidePriceAndStatus
                                    ? `
                                        <div class="estate-popup-status">
                                            ${escapeEstateHTML(
                                                status || '-'
                                            )}
                                        </div>
                                    `
                                    : ''
                            }

                        </div>


                        ${
                            showExtraSection
                                ? `
                                    <div class="estate-popup-extra">

                                        ${
                                            showClient
                                                ? `
                                                    <div class="estate-popup-extra-section">

                                                        <div class="estate-popup-section-label">

                                                            <span>
                                                                ${escapeEstateHTML(
                                                                    assignmentLabel
                                                                )}
                                                            </span>

                                                            <hr>

                                                        </div>


                                                        <div class="estate-popup-person user">

                                                            ${
                                                                lot.user.picture
                                                                    ? `
                                                                        <img
                                                                            src="${escapeEstateHTML(
                                                                                lot.user.picture
                                                                            )}"
                                                                            alt="User"
                                                                        >
                                                                    `
                                                                    : `
                                                                        <div
                                                                            style="
                                                                                width:40px;
                                                                                height:40px;
                                                                                border-radius:9999px;
                                                                                background:#e5e7eb;
                                                                                flex-shrink:0;
                                                                            "
                                                                        ></div>
                                                                    `
                                                            }

                                                            <strong>
                                                                ${escapeEstateHTML(
                                                                    lot.user.name ?? 'No User'
                                                                )}
                                                            </strong>

                                                        </div>

                                                    </div>
                                                `
                                                : ''
                                        }


                                        ${
                                            showModel
                                                ? `
                                                    <div class="estate-popup-extra-section">

                                                        <div class="estate-popup-section-label">

                                                            <span>
                                                                Model Name
                                                            </span>

                                                            <hr>

                                                        </div>


                                                        <div class="estate-popup-person model">

                                                            ${
                                                                lot.house_model.image
                                                                    ? `
                                                                        <img
                                                                            src="${escapeEstateHTML(
                                                                                lot.house_model.image
                                                                            )}"
                                                                            alt="Model"
                                                                        >
                                                                    `
                                                                    : `
                                                                        <div
                                                                            style="
                                                                                width:40px;
                                                                                height:40px;
                                                                                border-radius:6px;
                                                                                background:#e5e7eb;
                                                                                flex-shrink:0;
                                                                            "
                                                                        ></div>
                                                                    `
                                                            }

                                                            <strong>
                                                                ${escapeEstateHTML(
                                                                    lot.house_model.name ?? 'No Model'
                                                                )}
                                                            </strong>

                                                        </div>

                                                    </div>
                                                `
                                                : ''
                                        }

                                    </div>
                                `
                                : ''
                        }


                        ${
                            !hidePriceAndStatus
                                ? `
                                    <div class="estate-popup-price">
                                        ${price}
                                    </div>
                                `
                                : ''
                        }


                        <div class="estate-popup-actions">

                            <button
                                type="button"
                                class="
                                    estate-popup-button
                                    estate-popup-button-edit
                                "
                                onclick="editEstateGISLot(${lot.id})"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16.862 4.487 19.5 7.125m-1.318-3.957a1.875 1.875 0 1 1 2.652 2.652L8.25 18.404 4.5 19.5l1.096-3.75L18.182 3.168Z"
                                    />
                                </svg>

                                <span>
                                    Edit
                                </span>
                            </button>


                            <button
                                type="button"
                                class="
                                    estate-popup-button
                                    estate-popup-button-delete
                                "
                                onclick="deleteEstateGISLot(
                                    ${lot.id},
                                    '${escapeEstateJS(
                                        lot.name ?? 'Lot'
                                    )}'
                                )"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673A2.25 2.25 0 0 1 15.916 21H8.084a2.25 2.25 0 0 1-2.244-2.327L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0V4.477c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                                    />
                                </svg>

                                <span>
                                    Delete
                                </span>
                            </button>

                        </div>

                    </div>

                </div>
            `;
        }


        function showEstateCustomTooltip(
            lot,
            latlng
        )
        {
            const tooltip =
                document.getElementById(
                    'estate-custom-tooltip'
                );

            const content =
                document.getElementById(
                    'estate-custom-tooltip-content'
                );


            if (
                !tooltip ||
                !content ||
                !window.estateLeafletMap
            ) {
                return;
            }


            hideEstateCustomTooltip(
                false
            );


            window.estateCustomTooltipLotId =
                lot.id;

            window.estateCustomTooltipLatLng =
                latlng;


            content.innerHTML =
                buildEstatePopup(
                    lot
                );


            tooltip.classList.add(
                'is-visible'
            );


            positionEstateCustomTooltip(
                latlng
            );


            if (
                lot.image
            ) {
                requestAnimationFrame(
                    function()
                    {
                        initEstateCustomTooltipPanorama(
                            lot
                        );
                    }
                );
            }
        }


        function hideEstateCustomTooltip(
            clearContent = true
        )
        {
            const tooltip =
                document.getElementById(
                    'estate-custom-tooltip'
                );

            const content =
                document.getElementById(
                    'estate-custom-tooltip-content'
                );


            if (
                window.estatePopupPanoramaViewer
            ) {
                try {
                    window
                        .estatePopupPanoramaViewer
                        .destroy();
                } catch (error) {
                    console.log(
                        error
                    );
                }

                window.estatePopupPanoramaViewer =
                    null;
            }


            if (tooltip) {
                tooltip.classList.remove(
                    'is-visible'
                );
            }


            if (
                clearContent &&
                content
            ) {
                content.innerHTML = '';
            }


            window.estateCustomTooltipLotId =
                null;

            window.estateCustomTooltipLatLng =
                null;
        }


        function positionEstateCustomTooltip(
            latlng
        )
        {
            const map =
                window.estateLeafletMap;

            const mapContainer =
                document.getElementById(
                    'estate-leaflet-map'
                );

            const tooltip =
                document.getElementById(
                    'estate-custom-tooltip'
                );

            const arrow =
                document.getElementById(
                    'estate-custom-tooltip-arrow'
                );


            if (
                !map ||
                !mapContainer ||
                !tooltip ||
                !arrow ||
                !latlng
            ) {
                return;
            }


            const point =
                map.latLngToContainerPoint(
                    latlng
                );


            const mapRect =
                mapContainer.getBoundingClientRect();

            const tooltipWidth =
                tooltip.offsetWidth || 320;

            const tooltipHeight =
                tooltip.offsetHeight || 300;


            const gap = 14;
            const padding = 10;


            let x =
                point.x -
                tooltipWidth / 2;

            let y =
                point.y -
                tooltipHeight -
                gap;

            let placedAbove =
                true;


            if (
                y < padding
            ) {
                y =
                    point.y +
                    gap;

                placedAbove =
                    false;
            }


            x =
                Math.max(
                    padding,
                    Math.min(
                        x,
                        mapRect.width -
                        tooltipWidth -
                        padding
                    )
                );


            y =
                Math.max(
                    padding,
                    Math.min(
                        y,
                        mapRect.height -
                        tooltipHeight -
                        padding
                    )
                );


            tooltip.style.left =
                `${x}px`;

            tooltip.style.top =
                `${y}px`;


            let arrowX =
                point.x -
                x;


            arrowX =
                Math.max(
                    20,
                    Math.min(
                        arrowX,
                        tooltipWidth - 20
                    )
                );


            arrow.style.left =
                `${arrowX}px`;


            if (
                placedAbove
            ) {
                arrow.style.top =
                    'auto';

                arrow.style.bottom =
                    '-10px';

                arrow.style.borderLeft =
                    '10px solid transparent';

                arrow.style.borderRight =
                    '10px solid transparent';

                arrow.style.borderTop =
                    '10px solid white';

                arrow.style.borderBottom =
                    '0';
            } else {
                arrow.style.top =
                    '-10px';

                arrow.style.bottom =
                    'auto';

                arrow.style.borderLeft =
                    '10px solid transparent';

                arrow.style.borderRight =
                    '10px solid transparent';

                arrow.style.borderBottom =
                    '10px solid white';

                arrow.style.borderTop =
                    '0';
            }
        }


        function initEstateCustomTooltipPanorama(
            lot
        )
        {
            const container =
                document.getElementById(
                    `estate-pano-${lot.id}`
                );


            if (
                !container ||
                !lot.image ||
                typeof pannellum ===
                'undefined'
            ) {
                return;
            }


            if (
                window.estatePopupPanoramaViewer
            ) {
                try {
                    window
                        .estatePopupPanoramaViewer
                        .destroy();
                } catch (error) {
                    console.log(
                        error
                    );
                }

                window.estatePopupPanoramaViewer =
                    null;
            }


            requestAnimationFrame(
                function()
                {
                    if (
                        !document.body.contains(
                            container
                        )
                    ) {
                        return;
                    }


                    window
                        .estatePopupPanoramaViewer =
                        pannellum.viewer(
                            container,
                            {
                                type:
                                    'equirectangular',

                                panorama:
                                    lot.image,

                                autoLoad:
                                    true,

                                showControls:
                                    false,
                            }
                        );
                }
            );
        }


        function refreshEstateCustomTooltipPosition()
        {
            if (
                window.estateCustomTooltipLatLng
            ) {
                positionEstateCustomTooltip(
                    window.estateCustomTooltipLatLng
                );
            }
        }


        function startEstateLotMapping()
        {
            if (
                !window.estateLeafletMap
            ) {
                return;
            }


            hideEstateCustomTooltip();


            window
                .estateDrawingLayer
                ?.clearLayers();


            @this.set(
                'newGeoCoords',
                []
            ).then(() => {

                $openModal(
                    'create-gis-lot-modal'
                );


                initEstateCreateDrawingMap(
                    'estate-create-mini-map',
                    {
                        onChange:
                            function(updatedCoordinates)
                            {
                                @this.set(
                                    'newGeoCoords',
                                    updatedCoordinates
                                );
                            },
                    }
                );

            });
        }


        function editEstateGISLot(
            lotId
        )
        {
            hideEstateCustomTooltip();


            @this.call(
                'startEditGeoMapping',
                lotId
            );
        }


        function deleteEstateGISLot(
            lotId,
            lotName
        )
        {
            @this.call(
                'deleteLotConfirmation',
                lotId,
                lotName
            );
        }


        function waitForEstateMiniMap(
            containerId,
            callback,
            attempt = 0
        )
        {
            const container =
                document.getElementById(
                    containerId
                );

            if (!container) {
                if (attempt < 30) {
                    setTimeout(
                        function()
                        {
                            waitForEstateMiniMap(
                                containerId,
                                callback,
                                attempt + 1
                            );
                        },
                        100
                    );
                }

                return;
            }

            const rect =
                container.getBoundingClientRect();

            if (
                rect.width < 50
                ||
                rect.height < 50
                ||
                container.offsetParent === null
            ) {
                if (attempt < 30) {
                    setTimeout(
                        function()
                        {
                            waitForEstateMiniMap(
                                containerId,
                                callback,
                                attempt + 1
                            );
                        },
                        100
                    );
                }

                return;
            }

            callback();
        }



        function initEstateCreateDrawingMap(
            containerId,
            opts
        )
        {
            opts = opts || {};


            waitForEstateMiniMap(
                containerId,
                function()
                {
                    destroyEstateModalMiniMap(
                        containerId
                    );


                    const container =
                        document.getElementById(
                            containerId
                        );


                    if (!container) {
                        return;
                    }


                    container.innerHTML = '';


                    if (container._leaflet_id) {
                        delete container._leaflet_id;
                    }


                    const map =
                        L.map(
                            container,
                            {
                                zoomControl: true,
                                minZoom: 5,
                                maxZoom: 22,
                                attributionControl: false,
                            }
                        );


                    L.tileLayer(
                        'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
                        {
                            maxZoom: 22,
                            subdomains: [
                                'mt0',
                                'mt1',
                                'mt2',
                                'mt3',
                            ],
                        }
                    ).addTo(
                        map
                    );

                    const modalBoundary =
                        L.polygon(
                            estateSubdivisionBoundary,
                            {
                                color: '#2563eb',
                                weight: 3,
                                dashArray: '10 6',
                                fillColor: '#3b82f6',
                                fillOpacity: 0.03,
                                interactive: false,
                            }
                        ).addTo(
                            map
                        );

                    const existingLots =
                        window.estateGISLots || [];


                    existingLots.forEach(
                        function(lot)
                        {
                            if (
                                !Array.isArray(
                                    lot.geo_coords
                                )
                                ||
                                lot.geo_coords.length < 3
                            ) {
                                return;
                            }


                            const color =
                                getEstateLotColor(
                                    lot
                                );


                            L.polygon(
                                lot.geo_coords,
                                {
                                    color: color,
                                    fillColor: color,
                                    weight: 2,
                                    fillOpacity: 0.45,
                                    interactive: false,
                                }
                            ).addTo(
                                map
                            );
                        }
                    );

                    const drawingLayer =
                        new L.FeatureGroup();


                    map.addLayer(
                        drawingLayer
                    );


                    const entry = {
                        map: map,
                        polygon: null,
                        drawingLayer: drawingLayer,
                        drawer: null,
                        initialCoords: [],
                        onChange: opts.onChange,
                        ignoreLotId: null,
                        isCreateDrawingMap: true,
                    };


                    window.estateModalMiniMaps[
                        containerId
                    ] = entry;


                    const enableDrawer =
                        function()
                        {
                            if (
                                entry.drawer
                            ) {
                                try {
                                    entry.drawer.disable();
                                } catch (error) {
                                    //
                                }
                            }


                            entry.drawer =
                                new L.Draw.Polygon(
                                    map,
                                    {
                                        allowIntersection: false,
                                        showArea: true,

                                        shapeOptions: {
                                            color: '#f59e0b',
                                            weight: 4,
                                            fillColor: '#fbbf24',
                                            fillOpacity: 0.30,
                                        },
                                    }
                                );


                            entry.drawer.enable();
                        };


                    map.on(
                        L.Draw.Event.CREATED,
                        function(event)
                        {
                            const polygon =
                                event.layer;


                            if (
                                !isPolygonInsideEstateBoundaryCoordinates(
                                    polygon
                                )
                            ) {
                                @this.call(
                                    'showMapNotification',
                                    'Outside Subdivision Boundary',
                                    'Lot must be fully inside the Manhattan Residences boundary.',
                                    'danger'
                                );


                                enableDrawer();

                                return;
                            }


                            if (
                                doesPolygonOverlapExistingLot(
                                    polygon
                                )
                            ) {
                                @this.call(
                                    'showMapNotification',
                                    'Lot Area Already Mapped',
                                    'This area overlaps an already mapped lot.',
                                    'danger'
                                );


                                enableDrawer();

                                return;
                            }


                            drawingLayer.clearLayers();


                            polygon.setStyle({
                                color: '#f59e0b',
                                weight: 4,
                                fillColor: '#fbbf24',
                                fillOpacity: 0.30,
                            });


                            drawingLayer.addLayer(
                                polygon
                            );


                            polygon.editing.enable();


                            entry.polygon =
                                polygon;


                            const syncCoordinates =
                                function()
                                {
                                    if (
                                        !isPolygonInsideEstateBoundaryCoordinates(
                                            polygon
                                        )
                                    ) {
                                        @this.call(
                                            'showMapNotification',
                                            'Outside Subdivision Boundary',
                                            'Lot must be fully inside the Manhattan Residences boundary.',
                                            'danger'
                                        );
                                    } else if (
                                        doesPolygonOverlapExistingLot(
                                            polygon
                                        )
                                    ) {
                                        @this.call(
                                            'showMapNotification',
                                            'Lot Area Already Mapped',
                                            'This area overlaps an already mapped lot.',
                                            'danger'
                                        );
                                    }


                                    const coordinates =
                                        getPolygonCoordinates(
                                            polygon
                                        );


                                    if (
                                        typeof entry.onChange ===
                                        'function'
                                    ) {
                                        entry.onChange(
                                            coordinates
                                        );
                                    }
                                };


                            polygon.on(
                                'edit',
                                syncCoordinates
                            );


                            syncCoordinates();
                        }
                    );


                    map.fitBounds(
                        modalBoundary.getBounds(),
                        {
                            padding: [
                                20,
                                20
                            ],
                        }
                    );


                    requestAnimationFrame(
                        function()
                        {
                            map.invalidateSize(
                                true
                            );


                            map.fitBounds(
                                modalBoundary.getBounds(),
                                {
                                    padding: [
                                        20,
                                        20
                                    ],
                                }
                            );


                            enableDrawer();
                        }
                    );


                    setTimeout(
                        function()
                        {
                            const currentEntry =
                                window.estateModalMiniMaps[
                                    containerId
                                ];


                            if (
                                !currentEntry
                                ||
                                currentEntry.map !== map
                            ) {
                                return;
                            }


                            map.invalidateSize(
                                true
                            );
                        },
                        350
                    );
                }
            );
        }


        function resetEstateCreateDrawingMap(
            containerId
        )
        {
            const entry =
                window.estateModalMiniMaps[
                    containerId
                ];


            if (
                !entry ||
                !entry.isCreateDrawingMap
            ) {
                return;
            }


            if (
                entry.polygon
            ) {
                entry.polygon.off();


                if (
                    entry.polygon.editing
                ) {
                    entry.polygon.editing.disable();
                }
            }


            entry.drawingLayer
                ?.clearLayers();


            entry.polygon =
                null;


            if (
                typeof entry.onChange ===
                'function'
            ) {
                entry.onChange(
                    []
                );
            }


            if (
                entry.drawer
            ) {
                try {
                    entry.drawer.disable();
                } catch (error) {
                    //
                }
            }


            entry.drawer =
                new L.Draw.Polygon(
                    entry.map,
                    {
                        allowIntersection: false,
                        showArea: true,

                        shapeOptions: {
                            color: '#f59e0b',
                            weight: 4,
                            fillColor: '#fbbf24',
                            fillOpacity: 0.30,
                        },
                    }
                );


            entry.drawer.enable();
        }


        function isPolygonInsideEstateBoundaryCoordinates(
            polygon
        )
        {
            const lotPoints =
                polygon
                    .getLatLngs()[0];


            const boundaryPoints =
                estateSubdivisionBoundary.map(
                    point =>
                        L.latLng(
                            point[0],
                            point[1]
                        )
                );


            return lotPoints.every(
                point =>
                    pointInsidePolygon(
                        point,
                        boundaryPoints
                    )
            );
        }


        function initEstateModalMiniMap(
            containerId,
            coords,
            opts
        )
        {
            opts = opts || {};

            if (
                !Array.isArray(
                    coords
                )
                ||
                coords.length < 3
                ||
                typeof L === 'undefined'
            ) {
                return;
            }

            waitForEstateMiniMap(
                containerId,
                function()
                {
                    destroyEstateModalMiniMap(
                        containerId
                    );

                    const container =
                        document.getElementById(
                            containerId
                        );

                    if (!container) {
                        return;
                    }

                    container.innerHTML = '';

                    if (container._leaflet_id) {
                        delete container._leaflet_id;
                    }

                    const map =
                        L.map(
                            container,
                            {
                                zoomControl: true,
                                minZoom: 5,
                                maxZoom: 22,
                                attributionControl: false,
                            }
                        );

                    L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                        maxZoom: 22,
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    }).addTo(map);

                    const modalBoundary =
                        L.polygon(
                            estateSubdivisionBoundary,
                            {
                                color: '#2563eb',
                                weight: 3,
                                dashArray: '10 6',
                                fillColor: '#3b82f6',
                                fillOpacity: 0.03,
                                interactive: false,
                            }
                        ).addTo(map);

                    const existingLots =
                        window.estateGISLots || [];


                    existingLots.forEach(
                        function(lot)
                        {
                            if (
                                !Array.isArray(
                                    lot.geo_coords
                                )
                                ||
                                lot.geo_coords.length < 3
                            ) {
                                return;
                            }

                            if (
                                opts.ignoreLotId
                                &&
                                Number(lot.id) ===
                                Number(opts.ignoreLotId)
                            ) {
                                return;
                            }


                            const color =
                                getEstateLotColor(
                                    lot
                                );


                            L.polygon(
                                lot.geo_coords,
                                {
                                    color: color,
                                    fillColor: color,
                                    weight: 2,
                                    fillOpacity: 0.45,
                                    interactive: false,
                                }
                            ).addTo(map);
                        }
                    );

                    const polygon =
                        L.polygon(
                            coords,
                            {
                                color: '#f59e0b',
                                weight: 4,
                                fillColor: '#fbbf24',
                                fillOpacity: 0.30,
                            }
                        ).addTo(map);

                    polygon.editing.enable();

                    map.fitBounds(
                        modalBoundary.getBounds(),
                        {
                            padding: [
                                20,
                                20
                            ],
                        }
                    );

                    const syncCoordinates =
                        function()
                        {
                            if (
                                !isPolygonInsideBoundary(
                                    polygon
                                )
                            ) {
                                @this.call(
                                    'showMapNotification',
                                    'Outside Subdivision Boundary',
                                    'Lot must be fully inside the Manhattan Residences boundary.',
                                    'danger'
                                );
                            } else if (
                                doesPolygonOverlapExistingLot(
                                    polygon,
                                    opts.ignoreLotId
                                )
                            ) {
                                @this.call(
                                    'showMapNotification',
                                    'Lot Area Already Mapped',
                                    'This area overlaps an already mapped lot.',
                                    'danger'
                                );
                            }

                            const coordinates =
                                getPolygonCoordinates(
                                    polygon
                                );

                            if (
                                typeof opts.onChange ===
                                'function'
                            ) {
                                opts.onChange(
                                    coordinates
                                );
                            }
                        };

                    polygon.on(
                        'edit',
                        syncCoordinates
                    );

                    window.estateModalMiniMaps[
                        containerId
                    ] = {
                        map: map,
                        polygon: polygon,

                        initialCoords:
                            coords.map(
                                point => [
                                    point[0],
                                    point[1],
                                ]
                            ),

                        onChange: opts.onChange,
                        ignoreLotId: opts.ignoreLotId,
                    };

                    requestAnimationFrame(
                        function()
                        {
                            map.invalidateSize(
                                true
                            );

                            map.fitBounds(
                                modalBoundary.getBounds(),
                                {
                                    padding: [
                                        20,
                                        20
                                    ],
                                }
                            );
                        }
                    );

                    setTimeout(
                        function()
                        {
                            const entry =
                                window.estateModalMiniMaps[
                                    containerId
                                ];

                            if (
                                !entry
                                ||
                                entry.map !== map
                            ) {
                                return;
                            }

                            map.invalidateSize(
                                true
                            );

                            map.fitBounds(
                                modalBoundary.getBounds(),
                                {
                                    padding: [
                                        20,
                                        20
                                    ],
                                }
                            );
                        },
                        350
                    );

                    setTimeout(
                        function()
                        {
                            const entry =
                                window.estateModalMiniMaps[
                                    containerId
                                ];

                            if (
                                !entry
                                ||
                                entry.map !== map
                            ) {
                                return;
                            }

                            map.invalidateSize(
                                true
                            );
                        },
                        700
                    );

                    syncCoordinates();
                }
            );
        }


        function destroyEstateModalMiniMap(
            containerId
        )
        {
            const entry =
                window.estateModalMiniMaps[
                    containerId
                ];

            if (entry?.drawer) {
                try {
                    entry.drawer.disable();
                } catch (error) {
                    //
                }
            }

            if (entry?.polygon) {
                entry.polygon.off();
            }

            if (entry?.map) {
                entry.map.off();
                entry.map.remove();
            }

            delete window.estateModalMiniMaps[
                containerId
            ];

            const container =
                document.getElementById(
                    containerId
                );

            if (container) {
                container.innerHTML = '';

                if (container._leaflet_id) {
                    delete container._leaflet_id;
                }
            }
        }


        function resetEstateModalMiniMap(
            containerId
        )
        {
            const entry =
                window.estateModalMiniMaps[
                    containerId
                ];

            if (!entry) {
                return;
            }


            entry.polygon.editing.disable();

            entry.polygon.setLatLngs([
                entry.initialCoords.map(
                    point =>
                        L.latLng(
                            point[0],
                            point[1]
                        )
                ),
            ]);

            entry.polygon.editing.enable();

            entry.map.fitBounds(
                entry.polygon.getBounds(),
                {
                    padding: [
                        30,
                        30
                    ],
                }
            );

            if (
                typeof entry.onChange ===
                'function'
            ) {
                entry.onChange(
                    getPolygonCoordinates(
                        entry.polygon
                    )
                );
            }
        }

        function resetEstateModalMapView(
            containerId
        )
        {
            const entry =
                window.estateModalMiniMaps[
                    containerId
                ];


            if (
                !entry ||
                !entry.map
            ) {
                return;
            }


            const map =
                entry.map;


            const boundaryBounds =
                L.latLngBounds(
                    estateSubdivisionBoundary
                );


            map.invalidateSize(
                true
            );


            map.fitBounds(
                boundaryBounds,
                {
                    padding: [
                        20,
                        20
                    ],
                }
            );
        }

        window.addEventListener(
            'gis-edit-lot',
            function(event)
            {
                const coords =
                    event.detail.coords;

                const lotId =
                    event.detail.lotId;


                window
                    .estateDrawingLayer
                    ?.clearLayers();


                window
                    .estateLotLayer
                    ?.eachLayer(
                        function(layer)
                        {
                            if (
                                layer.estateLotId ==
                                lotId
                            ) {
                                layer.setStyle?.({
                                    opacity: 0,
                                    fillOpacity: 0,
                                });
                            }
                        }
                    );


                const contextPolygon =
                    L.polygon(
                        coords,
                        {
                            color: '#f59e0b',
                            weight: 4,
                            fillColor: '#fbbf24',
                            fillOpacity: 0.30,
                            interactive: false,
                        }
                    );


                window
                    .estateDrawingLayer
                    .addLayer(
                        contextPolygon
                    );


                window
                    .estateLeafletMap
                    ?.fitBounds(
                        contextPolygon.getBounds(),
                        {
                            padding: [
                                100,
                                100
                            ],
                        }
                    );


                $openModal(
                    'edit-gis-lot-modal'
                );


                initEstateModalMiniMap(
                    'estate-edit-mini-map',
                    coords,
                    {
                        ignoreLotId: lotId,

                        onChange:
                            function(coordinates)
                            {
                                @this.set(
                                    'editGeoCoords',
                                    coordinates
                                );
                            },
                    }
                );
            }
        );


        function getPolygonCoordinates(
            polygon
        )
        {
            return polygon
                .getLatLngs()[0]
                .map(
                    point => [
                        Number(
                            point.lat.toFixed(7)
                        ),
                        Number(
                            point.lng.toFixed(7)
                        ),
                    ]
                );
        }


        function isPolygonInsideBoundary(
            polygon
        )
        {
            const lotPoints =
                polygon
                    .getLatLngs()[0];


            const boundaryPoints =
                window
                    .estateBoundaryLayer
                    .getLatLngs()[0];


            return lotPoints.every(
                point =>
                    pointInsidePolygon(
                        point,
                        boundaryPoints
                    )
            );
        }


        function pointInsidePolygon(
            point,
            polygonPoints
        )
        {
            const x =
                point.lng;

            const y =
                point.lat;

            let inside =
                false;


            for (
                let i = 0,
                    j = polygonPoints.length - 1;

                i < polygonPoints.length;

                j = i++
            ) {
                const xi =
                    polygonPoints[i].lng;

                const yi =
                    polygonPoints[i].lat;

                const xj =
                    polygonPoints[j].lng;

                const yj =
                    polygonPoints[j].lat;


                const intersect =
                    (
                        (yi > y)
                        !==
                        (yj > y)
                    )
                    &&
                    (
                        x <
                        (
                            (xj - xi)
                            *
                            (y - yi)
                            /
                            (
                                yj - yi
                                || Number.EPSILON
                            )
                            +
                            xi
                        )
                    );


                if (intersect) {
                    inside =
                        !inside;
                }
            }


            return inside;
        }


        function doesPolygonOverlapExistingLot(
            polygon,
            ignoreLotId = null
        )
        {
            const newPoints =
                polygon
                    .getLatLngs()[0];


            const lots =
                window.estateGISLots || [];


            for (
                const lot of lots
            ) {
                if (
                    ignoreLotId &&
                    Number(lot.id) ===
                    Number(ignoreLotId)
                ) {
                    continue;
                }


                if (
                    !Array.isArray(
                        lot.geo_coords
                    )
                    ||
                    lot.geo_coords.length < 3
                ) {
                    continue;
                }


                const existing =
                    lot.geo_coords.map(
                        point =>
                            L.latLng(
                                point[0],
                                point[1]
                            )
                    );


                if (
                    polygonsOverlap(
                        newPoints,
                        existing
                    )
                ) {
                    return true;
                }
            }


            return false;
        }


        function polygonsOverlap(
            a,
            b
        )
        {
            for (
                let i = 0;
                i < a.length;
                i++
            ) {
                const a1 =
                    a[i];

                const a2 =
                    a[
                        (i + 1)
                        %
                        a.length
                    ];


                for (
                    let j = 0;
                    j < b.length;
                    j++
                ) {
                    const b1 =
                        b[j];

                    const b2 =
                        b[
                            (j + 1)
                            %
                            b.length
                        ];


                    if (
                        segmentsIntersect(
                            a1,
                            a2,
                            b1,
                            b2
                        )
                    ) {
                        return true;
                    }
                }
            }


            if (
                pointInsidePolygon(
                    a[0],
                    b
                )
            ) {
                return true;
            }


            if (
                pointInsidePolygon(
                    b[0],
                    a
                )
            ) {
                return true;
            }


            return false;
        }


        function segmentsIntersect(
            p1,
            p2,
            q1,
            q2
        )
        {
            const orientation =
                function(a, b, c)
                {
                    return (
                        (b.lng - a.lng)
                        *
                        (c.lat - a.lat)
                    )
                    -
                    (
                        (b.lat - a.lat)
                        *
                        (c.lng - a.lng)
                    );
                };


            const o1 =
                orientation(
                    p1,
                    p2,
                    q1
                );

            const o2 =
                orientation(
                    p1,
                    p2,
                    q2
                );

            const o3 =
                orientation(
                    q1,
                    q2,
                    p1
                );

            const o4 =
                orientation(
                    q1,
                    q2,
                    p2
                );


            return (
                (
                    o1 > 0 &&
                    o2 < 0
                )
                ||
                (
                    o1 < 0 &&
                    o2 > 0
                )
            )
            &&
            (
                (
                    o3 > 0 &&
                    o4 < 0
                )
                ||
                (
                    o3 < 0 &&
                    o4 > 0
                )
            );
        }


        function resetEstateGISView()
        {
            if (
                !window.estateLeafletMap
                ||
                !window.estateBoundaryLayer
            ) {
                return;
            }


            window
                .estateLeafletMap
                .fitBounds(
                    window
                        .estateBoundaryLayer
                        .getBounds(),
                    {
                        padding: [
                            30,
                            30
                        ],
                    }
                );
        }


        function escapeEstateHTML(
            value
        )
        {
            return String(
                value ?? ''
            )
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }


        function escapeEstateJS(
            value
        )
        {
            return String(
                value ?? ''
            )
                .replaceAll('\\', '\\\\')
                .replaceAll("'", "\\'")
                .replaceAll('"', '\\"');
        }

        // document.addEventListener(
        //     'click',
        //     function()
        //     {
        //         setTimeout(
        //             function()
        //             {
        //                 const lots =
        //                     window.estateGISLots || [];


        //                 lots.forEach(
        //                     lot => {

        //                         if (!lot.image) {
        //                             return;
        //                         }


        //                         const container =
        //                             document.getElementById(
        //                                 `estate-pano-${lot.id}`
        //                             );


        //                         if (
        //                             !container ||
        //                             container.dataset.ready === '1'
        //                         ) {
        //                             return;
        //                         }


        //                         if (
        //                             typeof pannellum === 'undefined'
        //                         ) {
        //                             return;
        //                         }


        //                         container.dataset.ready =
        //                             '1';


        //                         pannellum.viewer(
        //                             container,
        //                             {
        //                                 type:
        //                                     'equirectangular',

        //                                 panorama:
        //                                     lot.image,

        //                                 autoLoad:
        //                                     true,

        //                                 showControls:
        //                                     false,
        //                             }
        //                         );
        //                     }
        //                 );
        //             },
        //             150
        //         );
        //     }
        // );


        window.addEventListener(
            'gis-mapping-cancelled',
            function()
            {
                window
                    .estateDrawingLayer
                    ?.clearLayers();


                window
                    .estateLotLayer
                    ?.eachLayer(
                        function(layer)
                        {
                            if (
                                typeof layer.setStyle ===
                                'function'
                            ) {
                                layer.setStyle({
                                    opacity: 1,
                                    fillOpacity: 0.50,
                                });
                            }
                        }
                    );


                destroyEstateModalMiniMap(
                    'estate-edit-mini-map'
                );

                destroyEstateModalMiniMap(
                    'estate-create-mini-map'
                );

                setTimeout(
                    function()
                    {
                        resetEstateGISView();
                    },
                    150
                );


                if (
                    typeof $closeModal ===
                    'function'
                ) {
                    $closeModal(
                        'edit-gis-lot-modal'
                    );

                    $closeModal(
                        'create-gis-lot-modal'
                    );
                }
            }
        );


        window.addEventListener(
            'gis-lot-saved',
            function()
            {
                destroyEstateModalMiniMap(
                    'estate-edit-mini-map'
                );

                destroyEstateModalMiniMap(
                    'estate-create-mini-map'
                );


                if (
                    typeof $closeModal ===
                    'function'
                ) {
                    $closeModal(
                        'edit-gis-lot-modal'
                    );

                    $closeModal(
                        'create-gis-lot-modal'
                    );
                }


                setTimeout(
                    initEstateLeafletMap,
                    150
                );
            }
        );


        document.addEventListener(
            'livewire:initialized',
            function()
            {
                setTimeout(
                    initEstateLeafletMap,
                    200
                );
            }
        );


        document.addEventListener(
            'livewire:navigated',
            function()
            {
                setTimeout(
                    initEstateLeafletMap,
                    200
                );
            }
        );


        if (
            document.readyState ===
            'interactive'
            ||
            document.readyState ===
            'complete'
        ) {
            setTimeout(
                initEstateLeafletMap,
                200
            );
        }

    </script>

@endpush
