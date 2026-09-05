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

        .estate-block-label {
            background: transparent !important;
            color: #d9dde7 !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            padding: 0 !important;
            font-weight: 700;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .estate-block-label::before {
            display: none !important;
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
            justify-content: flex-start;

            color: #111827;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;

            text-align: left;
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

        .estate-lot-number-label,
        .estate-lot-area-label {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .estate-lot-number-label::before,
        .estate-lot-area-label::before {
            display: none !important;
        }

        .estate-lot-number-inner {
            color: #000000;
            font-weight: 800;
            line-height: 1;
            white-space: nowrap;
            text-align: right;
            pointer-events: none;

            text-shadow: none;
        }

        .estate-lot-area-inner {
            display: flex;
            align-items: center;
            justify-content: center;

            color: #000000;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
            text-align: center;
            pointer-events: none;

            text-shadow: none;
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

        .estate-map-filter-panel {
            position: absolute;
            top: 14px;
            left: 47%;
            transform: translateX(-50%);
            width: max-content;
            max-width: calc(100% - 320px);
            padding: 10px 12px;
            border: 1px solid rgba(209, 213, 219, .95);
            border-radius: 10px;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 4px 14px rgba(15, 23, 42, .14);
            backdrop-filter: blur(4px);
        }

        .estate-map-filter-items {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px 12px;
            flex-wrap: wrap;
        }

        .estate-map-filter-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #374151;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
        }

        .estate-map-filter-item input {
            width: 14px;
            height: 14px;
            margin: 0;
            cursor: pointer;
        }

        .estate-map-filter-color {
            width: 10px;
            height: 10px;
            flex-shrink: 0;
            border-radius: 3px;
            border: 1px solid rgba(17, 24, 39, .18);
        }

        .estate-block-label-modal {
            font-size: 9px !important;
        }

        @media (max-width: 1100px) {
            .estate-map-filter-panel {
                left: 45%;
                max-width: calc(100% - 300px);
            }
        }

        @media (max-width: 900px) {
            .estate-map-filter-panel {
                top: 95px;
                left: 50%;
                max-width: calc(100% - 30px);
            }
        }

        .leaflet-editing-icon,
        .leaflet-touch .leaflet-editing-icon {
            width: 10px !important;
            height: 10px !important;

            margin-left: -5px !important;
            margin-top: -5px !important;

            border-radius: 50% !important;

            background: #ffffff !important;
            border: 2px solid #2563eb !important;

            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) !important;
        }

        /* The transparent/middle points Leaflet creates between vertices */
        .leaflet-editing-icon[style*="opacity: 0.6"],
        .leaflet-editing-icon[style*="opacity:0.6"] {
            width: 8px !important;
            height: 8px !important;

            margin-left: -4px !important;
            margin-top: -4px !important;

            background: #ffffff !important;
            border: 2px solid #6366f1 !important;

            opacity: 0.75 !important;
        }

        #estate-leaflet-map .leaflet-interactive:focus {
            outline: none !important;
        }

        .estate-map-not-allowed-area {
            cursor: not-allowed !important;
        }

        .estate-map-cursor-not-allowed,
        .estate-map-cursor-not-allowed .leaflet-map-pane,
        .estate-map-cursor-not-allowed .leaflet-map-pane * {
            cursor: not-allowed !important;
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
                    'lot_number' => $lot->lot_number,
                    'geo_coords' => $lot->geo_coords,
                    'type' => $lot->type,
                    'status' => $lot->status,
                    'price' => $lot->price,
                    'lot_area' => $lot->lot_area,
                    'block_id' => $lot->block_id,
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

        $leafletBlocks = collect($blocks ?? [])
            ->map(function ($block) use ($lots) {
                return [
                    'id' => $block->id,
                    'name' => $block->name,
                    'geo_coords' => $block->geo_coords,

                    // Delete is only allowed when this block has no lots.
                    'can_delete' => !collect($lots ?? [])
                        ->contains(
                            fn ($lot) =>
                                (int) $lot->block_id === (int) $block->id
                        ),
                ];
            })
            ->values()
            ->toArray();

        $canDeleteBoundary =
            collect($blocks ?? [])->isEmpty()
            &&
            collect($lots ?? [])->isEmpty();

        $hasBoundary =
            is_array($map?->boundary_geo_coords)
            &&
            count($map->boundary_geo_coords) >= 3;
    @endphp

    <div
        class="bg-white rounded-2xl shadow-md p-5 border border-gray-100 mt-16"
    >

        {{-- HEADER --}}
        <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5"
        >
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Subdivision GIS Map
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Click on any lot to view details or assign to a client
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @if (!$hasBoundary)
                    <button
                        type="button"
                        onclick="startEstateBoundaryMapping()"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700"
                    >
                        Create Boundary
                    </button>
                @else
                    <button
                        type="button"
                        onclick="editEstateSubdivisionBoundary()"
                        class="px-4 py-2 rounded-lg border border-blue-200 bg-blue-50 text-blue-700 text-sm font-medium hover:bg-blue-100"
                    >
                        Edit Boundary
                    </button>

                    <button
                        type="button"
                        onclick="startEstateBlockMapping()"
                        class="px-4 py-2 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 text-sm font-medium hover:bg-indigo-100"
                    >
                        Create Block
                    </button>

                    @if (collect($blocks ?? [])->isNotEmpty())
                        <button
                            type="button"
                            onclick="startEstateLotMapping()"
                            class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800"
                        >
                            New Lot Area
                        </button>
                    @endif
                @endif
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


            <div
                id="estate-map-filter-panel"
                class="estate-map-filter-panel"
                wire:ignore
            >
                <div class="estate-map-filter-items">

                    <label class="estate-map-filter-item">
                        <input
                            type="checkbox"
                            class="estate-map-filter-checkbox"
                            data-filter-kind="boundary"
                            checked
                        >
                        <span
                            class="estate-map-filter-color"
                            style="background:#2563eb;"
                        ></span>
                        <span>Subdivision Boundary</span>
                    </label>

                    <label class="estate-map-filter-item">
                        <input
                            type="checkbox"
                            class="estate-map-filter-checkbox"
                            data-filter-kind="blocks"
                            checked
                        >
                        <span
                            class="estate-map-filter-color"
                            style="background:#9333ea;"
                        ></span>
                        <span>Blocks</span>
                    </label>

                    <label class="estate-map-filter-item">
                        <input
                            type="checkbox"
                            class="estate-map-filter-checkbox"
                            data-filter-kind="lot-type"
                            data-lot-type="Playground & Community Amenities"
                            checked
                        >
                        <span
                            class="estate-map-filter-color"
                            style="background:#f2b879;"
                        ></span>
                        <span>Playground & Community Amenities</span>
                    </label>

                    <label class="estate-map-filter-item">
                        <input
                            type="checkbox"
                            class="estate-map-filter-checkbox"
                            data-filter-kind="lot-type"
                            data-lot-type="Model House"
                            checked
                        >
                        <span
                            class="estate-map-filter-color"
                            style="background:#c8c9c3;"
                        ></span>
                        <span>Model House</span>
                    </label>

                    <label class="estate-map-filter-item">
                        <input
                            type="checkbox"
                            class="estate-map-filter-checkbox"
                            data-filter-kind="lot-type"
                            data-lot-type="Lot Only"
                            checked
                        >
                        <span
                            class="estate-map-filter-color"
                            style="background:#c4e0b7;"
                        ></span>
                        <span>Lot Only</span>
                    </label>

                    <label class="estate-map-filter-item">
                        <input
                            type="checkbox"
                            class="estate-map-filter-checkbox"
                            data-filter-kind="lot-type"
                            data-lot-type="House & Lot"
                            checked
                        >
                        <span
                            class="estate-map-filter-color"
                            style="background:#f8e89c;"
                        ></span>
                        <span>House & Lot</span>
                    </label>

                    <label class="estate-map-filter-item">
                        <input
                            type="checkbox"
                            class="estate-map-filter-checkbox"
                            data-filter-kind="lot-type"
                            data-lot-type="Internal Road"
                            checked
                        >
                        <span
                            class="estate-map-filter-color"
                            style="background:#ffffff;"
                        ></span>
                        <span>Internal Road</span>
                    </label>

                </div>
            </div>


            {{-- RESET VIEW --}}
            @if ($hasBoundary)
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
            @endif


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

        {{-- CREATE SUBDIVISION BOUNDARY --}}
        <x-modal
            name="create-gis-boundary-modal"
            max-width="6xl"
            blur="md"
            align="center"
            x-on:gis-boundary-create-success.window="
                destroyEstateModalMiniMap('estate-create-boundary-mini-map');
                close();

                setTimeout(() => {
                    window.location.reload();
                }, 200);
            "
        >
            <div class="w-[900px] max-w-[90vw] mx-auto">
                <form wire:submit.prevent="createBoundary">
                    <x-card title="Create Subdivision Boundary">
                    <p class="text-sm text-gray-500 -mt-2 mb-4">
                        Draw the outer subdivision boundary.
                    </p>

                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Subdivision Boundary
                        </label>

                        <button
                            type="button"
                            onclick="resetEstateCreateDrawingMap('estate-create-boundary-mini-map')"
                            class="text-xs font-medium text-blue-600 hover:text-blue-700"
                        >
                            Clear Drawing
                        </button>
                    </div>

                    <div
                        id="estate-create-boundary-map-container"
                        wire:ignore
                        class="relative w-full h-[360px] rounded-xl border border-gray-200 overflow-hidden bg-gray-100"
                    >
                        <div
                            id="estate-create-boundary-mini-map"
                            class="absolute inset-0 w-full h-full"
                        ></div>

                        {{-- Map Controls --}}
                        <div class="absolute top-3 right-3 z-[500] flex gap-2">

                            {{-- Fullscreen --}}
                            <button
                                type="button"
                                onclick="toggleEstateMapFullscreen(
                                    'estate-create-boundary-map-container',
                                    this
                                )"
                                class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                            >
                                ⛶
                            </button>

                            {{-- Reset View --}}
                            <button
                                type="button"
                                onclick="resetEstateModalMapView('estate-create-boundary-mini-map')"
                                class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                            >
                                Reset View
                            </button>

                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mt-2">
                        Click points on the map to draw the subdivision boundary. Click the first point to finish.
                    </p>

                    <div class="mt-3">
                        <x-input
                            label="GIS Coordinates"
                            id="estate-create-boundary-coords"
                            :value="json_encode($newBoundaryGeoCoords)"
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
                                destroyEstateModalMiniMap('estate-create-boundary-mini-map');
                                close();
                            "
                        />

                        <x-button
                            primary
                            label="Save Boundary"
                            type="submit"
                        />
                    </x-slot>
                    </x-card>
                </form>
            </div>
        </x-modal>


        {{-- EDIT SUBDIVISION BOUNDARY --}}
        <x-modal
            name="edit-gis-boundary-modal"
            max-width="7xl"
            blur="md"
            align="center"
            x-on:gis-boundary-edit-success.window="
                destroyEstateModalMiniMap('estate-edit-boundary-mini-map');
                close();

                setTimeout(() => {
                    window.location.reload();
                }, 200);
            "
        >
            <form wire:submit.prevent="updateBoundary">
                <x-card title="Edit Subdivision Boundary">
                    <p class="text-sm text-gray-500 -mt-2 mb-4">
                        Drag the points to adjust the subdivision boundary. Existing blocks and properties must remain inside it.
                    </p>

                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Subdivision Boundary
                        </label>

                        <button
                            type="button"
                            onclick="resetEstateModalMiniMap('estate-edit-boundary-mini-map')"
                            class="text-xs font-medium text-blue-600 hover:text-blue-700"
                        >
                            Reset Points
                        </button>
                    </div>

                    <div
                        id="estate-edit-boundary-map-container"
                        wire:ignore
                        class="relative w-full h-[360px] rounded-xl border border-gray-200 overflow-hidden bg-gray-100"
                    >
                        <div
                            id="estate-edit-boundary-mini-map"
                            class="absolute inset-0 w-full h-full"
                        ></div>

                        <div class="absolute top-3 right-3 z-[500] flex gap-2">

                            <button
                                type="button"
                                onclick="toggleEstateMapFullscreen(
                                    'estate-edit-boundary-map-container',
                                    this
                                )"
                                class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                            >
                                ⛶
                            </button>

                            <button
                                type="button"
                                onclick="resetEstateModalMapView('estate-edit-boundary-mini-map')"
                                class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                            >
                                Reset View
                            </button>

                        </div>
                    </div>

                    <div class="mt-3">
                        <x-input
                            label="GIS Coordinates"
                            id="estate-edit-boundary-coords"
                            :value="json_encode($editBoundaryGeoCoords)"
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
                                destroyEstateModalMiniMap('estate-edit-boundary-mini-map');
                                close();
                            "
                        />

                        <x-button
                            primary
                            label="Save Boundary"
                            type="submit"
                        />
                    </x-slot>
                </x-card>
            </form>
        </x-modal>


        {{-- CREATE BLOCK --}}
        <x-modal
            name="create-gis-block-modal"
            max-width="6xl"
            blur="md"
            align="center"
            x-on:gis-block-create-success.window="
                destroyEstateModalMiniMap('estate-create-block-mini-map');
                close();

                setTimeout(() => {
                    window.location.reload();
                }, 200);
            "
        >
            <div class="w-[900px] max-w-[90vw] mx-auto">
                <form wire:submit.prevent="createBlock">
                    <x-card title="Create Block">
                        <p class="text-sm text-gray-500 -mt-2 mb-4">
                            Draw the block inside the subdivision boundary.
                        </p>

                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Block Boundary
                            </label>

                            <button
                                type="button"
                                onclick="resetEstateCreateDrawingMap('estate-create-block-mini-map')"
                                class="text-xs font-medium text-blue-600 hover:text-blue-700"
                            >
                                Clear Drawing
                            </button>
                        </div>

                        <div
                            id="estate-create-block-map-container"
                            wire:ignore
                            class="relative w-full h-[320px] rounded-xl border border-gray-200 overflow-hidden bg-gray-100"
                        >
                            <div
                                id="estate-create-block-mini-map"
                                class="absolute inset-0 w-full h-full"
                            ></div>

                            <div class="absolute top-3 right-3 z-[500] flex gap-2">

                                <button
                                    type="button"
                                    onclick="toggleEstateMapFullscreen(
                                        'estate-create-block-map-container',
                                        this
                                    )"
                                    class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                                >
                                    ⛶
                                </button>

                                <button
                                    type="button"
                                    onclick="resetEstateModalMapView('estate-create-block-mini-map')"
                                    class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                                >
                                    Reset View
                                </button>

                            </div>
                        </div>

                        <div class="mt-3">
                            <x-input
                                label="Block Name"
                                placeholder="Ex: Block 1"
                                wire:model.defer="blockName"
                            />
                        </div>

                        <div class="mt-3">
                            <x-input
                                label="GIS Coordinates"
                                id="estate-create-block-coords"
                                :value="json_encode($newBlockGeoCoords)"
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
                                    destroyEstateModalMiniMap('estate-create-block-mini-map');
                                    close();
                                "
                            />

                            <x-button
                                primary
                                label="Save Block"
                                type="submit"
                            />
                        </x-slot>
                    </x-card>
                </form>
            </div>
        </x-modal>


        {{-- EDIT BLOCK --}}
        <x-modal
            name="edit-gis-block-modal"
            max-width="7xl"
            blur="md"
            align="center"
            x-on:gis-block-edit-success.window="
                destroyEstateModalMiniMap('estate-edit-block-mini-map');
                close();

                setTimeout(() => {
                    window.location.reload();
                }, 200);
            "
        >
        <div class="w-[900px] max-w-[90vw] mx-auto">
            <form wire:submit.prevent="updateBlock">
                <x-card title="Edit Block">
                    <p class="text-sm text-gray-500 -mt-2 mb-4">
                        Update the block name or boundary. Existing lots in this block must remain inside it.
                    </p>

                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Block Boundary
                        </label>

                        <button
                            type="button"
                            onclick="resetEstateModalMiniMap('estate-edit-block-mini-map')"
                            class="text-xs font-medium text-blue-600 hover:text-blue-700"
                        >
                            Reset Points
                        </button>
                    </div>

                    <div
                        id="estate-edit-block-map-container"
                        wire:ignore
                        class="relative w-full h-[320px] rounded-xl border border-gray-200 overflow-hidden bg-gray-100"
                    >
                        <div
                            id="estate-edit-block-mini-map"
                            class="absolute inset-0 w-full h-full"
                        ></div>

                        <div class="absolute top-3 right-3 z-[500] flex gap-2">

                            {{-- Fullscreen --}}
                            <button
                                type="button"
                                onclick="toggleEstateMapFullscreen(
                                    'estate-edit-block-map-container',
                                    this
                                )"
                                class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                            >
                                ⛶
                            </button>

                            {{-- Reset View --}}
                            <button
                                type="button"
                                onclick="resetEstateModalMapView('estate-edit-block-mini-map')"
                                class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                            >
                                Reset View
                            </button>

                        </div>
                    </div>

                    <div class="mt-3">
                        <x-input
                            label="Block Name"
                            placeholder="Ex: Block 1"
                            wire:model.defer="editBlockName"
                        />
                    </div>

                    <div class="mt-3">
                        <x-input
                            label="GIS Coordinates"
                            id="estate-edit-block-coords"
                            :value="json_encode($editBlockGeoCoords)"
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
                                destroyEstateModalMiniMap('estate-edit-block-mini-map');
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


        {{-- CREATE FORM --}}
        <x-modal
            name="create-gis-lot-modal"
            max-width="7xl"
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
                                id="estate-create-lot-map-container"
                                wire:ignore
                                class="relative w-full h-[280px] rounded-xl border border-gray-200 overflow-hidden bg-gray-100"
                            >
                                <div
                                    id="estate-create-mini-map"
                                    class="absolute inset-0 w-full h-full"
                                ></div>


                                <div class="absolute top-3 right-3 z-[500] flex gap-2">

                                    {{-- Fullscreen --}}
                                    <button
                                        type="button"
                                        onclick="toggleEstateMapFullscreen(
                                            'estate-create-lot-map-container',
                                            this
                                        )"
                                        class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                                    >
                                        ⛶
                                    </button>

                                    {{-- Reset View --}}
                                    <button
                                        type="button"
                                        onclick="resetEstateModalMapView('estate-create-mini-map')"
                                    class="
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
                            </div>

                            <p class="text-xs text-gray-400 mt-2">
                                Click points directly on the map to draw the new lot boundary. Click the first point to finish.
                            </p>
                        </div>
                        @if ($lotType !== 'Internal Road')
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Block
                                </label>

                                <input
                                    id="estate-create-detected-block"
                                    type="text"
                                    value="Draw the lot boundary first"
                                    readonly
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                                >

                                <p class="text-xs text-gray-400 mt-1">
                                    Automatically detected from the property boundary.
                                </p>
                            </div>

                            <div class="mt-3">
                                <x-input
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    min="1"
                                    label="Lot Number"
                                    placeholder="Ex: 43"
                                    wire:model.defer="lotNumber"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                />
                            </div>

                            <div class="mt-3">
                                <x-input
                                    type="text"
                                    inputmode="decimal"
                                    label="Lot Area"
                                    placeholder="Ex: 150.50"
                                    wire:model.defer="lotArea"
                                    oninput="
                                        this.value = this.value
                                            .replace(/[^0-9.]/g, '')
                                            .replace(/(\..*)\./g, '$1');
                                    "
                                />
                            </div>
                        @endif

                        <div class="mt-3">
                            <x-native-select
                                label="Property Type"
                                id="estate-create-lot-type"
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

                                <option value="Internal Road">
                                    Internal Road
                                </option>
                            </x-native-select>
                        </div>

                        @if (
                            $lotType &&
                            !in_array($lotType, [
                                'Model House',
                                'Playground & Community Amenities',
                                'Internal Road',
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
                                'Internal Road',
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
                                'Internal Road',
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
                                id="estate-create-lot-coords"
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
            max-width="7xl"
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
                                id="estate-edit-lot-map-container"
                                wire:ignore
                                class="relative w-full h-[280px] rounded-xl border border-gray-200 overflow-hidden bg-gray-100"
                            >
                                <div
                                    id="estate-edit-mini-map"
                                    class="absolute inset-0 w-full h-full"
                                ></div>


                                <div class="absolute top-3 right-3 z-[500] flex gap-2">

                                    {{-- Fullscreen --}}
                                    <button
                                        type="button"
                                        onclick="toggleEstateMapFullscreen(
                                            'estate-edit-lot-map-container',
                                            this
                                        )"
                                        class="px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs font-medium shadow-md hover:bg-gray-50"
                                    >
                                        ⛶
                                    </button>

                                    {{-- Reset View --}}
                                    <button
                                        type="button"
                                        onclick="resetEstateModalMapView('estate-edit-mini-map')"
                                    class="
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
                            </div>

                            <p class="text-xs text-gray-400 mt-2">
                                Drag the markers on the map to adjust the boundary.
                            </p>

                        </div>

                        @if ($editLotType !== 'Internal Road')
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Block
                                </label>

                                <input
                                    id="estate-edit-detected-block"
                                    type="text"
                                    value="Automatically detected from the property boundary"
                                    readonly
                                    class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                                >
                            </div>

                            {{-- LOT NAME --}}
                            <div class="mt-3">
                                <x-input
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    label="Lot Number"
                                    placeholder="Ex: 43"
                                    wire:model.defer="editLotNumber"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                />
                            </div>

                            {{-- LOT AREA --}}
                            <div class="mt-3">
                                <x-input
                                    type="text"
                                    inputmode="decimal"
                                    label="Lot Area"
                                    placeholder="Ex: 150.50"
                                    wire:model.defer="editLotArea"
                                    oninput="
                                        this.value = this.value
                                            .replace(/[^0-9.]/g, '')
                                            .replace(/(\..*)\./g, '$1');
                                    "
                                />
                            </div>
                        @endif

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

                                <option value="Internal Road">
                                    Internal Road
                                </option>
                            </x-native-select>
                        </div>

                        {{-- PRICE --}}
                        @if (
                            $editLotType &&
                            !in_array($editLotType, [
                                'Model House',
                                'Playground & Community Amenities',
                                'Internal Road',
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
                                'Internal Road',
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
                                'Internal Road',
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
                        @if ($editLotImage)

                            <div class="mt-4" wire:key="edit-lot-new-image-preview">
                                <p class="text-sm font-medium text-gray-700 mb-2">
                                    New Image Preview
                                </p>

                                <div class="relative">
                                    <img
                                        src="{{ $editLotImage->temporaryUrl() }}"
                                        class="w-full max-h-60 object-cover rounded-xl border border-gray-200 shadow-sm"
                                    >

                                    <button
                                        type="button"
                                        wire:click="removeNewLotImagePreview"
                                        class="absolute top-2 right-2 w-8 h-8 flex items-center justify-center rounded-full bg-white/90 shadow-md hover:bg-white"
                                        aria-label="Remove new image preview"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        @elseif ($editLotImagePreview)

                            <div x-data="{ hideCurrentImage: false }" class="mt-4" x-show="!hideCurrentImage" wire:key="edit-lot-current-image-preview">

                                <p class="text-sm font-medium text-gray-700 mb-2">
                                    Current Image
                                </p>

                                <div class="relative">
                                    <img
                                        src="{{ asset('storage/' . $editLotImagePreview) }}"
                                        class="w-full max-h-60 object-cover rounded-xl border border-gray-200 shadow-sm"
                                    >

                                    <button
                                        type="button"
                                        @click="hideCurrentImage = true"
                                        class="absolute top-2 right-2 w-8 h-8 flex items-center justify-center rounded-full bg-white/90 shadow-md hover:bg-white"
                                        aria-label="Remove current image preview"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4 text-gray-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                                        </svg>
                                    </button>
                                </div>

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
                                id="estate-edit-lot-coords"
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
        window.estateGISBlocks = @json($leafletBlocks);
        window.estateCanDeleteBoundary = @json($canDeleteBoundary);
    </script>
</div>


@push('scripts')

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.7/build/pannellum.js"></script>


    <script>
        window.estateLeafletMap = null;
        window.estateLotLayer = null;
        window.estateBlockLayer = null;
        window.estateDrawingLayer = null;
        window.estateBoundaryLayer = null;
        window.estateBoundaryClickMask = null;
        window.estateConstructionMarkers = [];
        window.estateSoldMarkers = [];
        window.estateLotNumberMarkers = [];
        window.estateLotAreaMarkers = [];
        window.estateModalMiniMaps = {};
        window.estatePopupPanoramaViewer = null;
        window.estateCustomTooltipLotId = null;
        window.estateCustomTooltipLatLng = null;

        window.estateMapFilters = window.estateMapFilters || {
            boundary: true,
            blocks: true,
            lotTypes: {
                "Playground & Community Amenities": true,
                "Model House": true,
                "Lot Only": true,
                "House & Lot": true,
                "Internal Road": true,
            },
        };


        const manhattanResidences = [
            13.919700,
            121.421307
        ];


        const estateSubdivisionBoundary =
            @json($map?->boundary_geo_coords ?? []);


        const estateLotColors = {
            "Playground & Community Amenities": "#f2b879",
            "Model House": "#c8c9c3",
            "Lot Only": "#c4e0b7",
            "House & Lot": "#f8e89c",
            "Internal Road": "#ffffff",
            "Sold": "#e9b4ae",
        };


        function getEstateSubdivisionBoundaryPoints()
        {
            if (
                !Array.isArray(
                    estateSubdivisionBoundary
                )
                ||
                estateSubdivisionBoundary.length < 3
            ) {
                return [];
            }

            return estateSubdivisionBoundary.map(
                point =>
                    L.latLng(
                        point[0],
                        point[1]
                    )
            );
        }


        function isEstatePointInsideSubdivisionBoundary(
            latlng
        )
        {
            const boundaryPoints =
                getEstateSubdivisionBoundaryPoints();

            if (
                !latlng ||
                boundaryPoints.length < 3
            ) {
                return false;
            }

            return pointInsidePolygon(
                latlng,
                boundaryPoints
            );
        }


        function addEstateBoundaryInteractionMask(
            map
        )
        {
            if (
                !map
                ||
                !Array.isArray(
                    estateSubdivisionBoundary
                )
                ||
                estateSubdivisionBoundary.length < 3
            ) {
                return null;
            }

            const worldRing = [
                [
                    85.05112878,
                    -180,
                ],
                [
                    85.05112878,
                    180,
                ],
                [
                    -85.05112878,
                    180,
                ],
                [
                    -85.05112878,
                    -180,
                ],
            ];

            const mask =
                L.polygon(
                    [
                        worldRing,
                        estateSubdivisionBoundary,
                    ],
                    {
                        stroke: false,
                        fill: true,
                        fillColor: '#000000',
                        fillOpacity: 0.001,
                        interactive: true,
                        bubblingMouseEvents: false,
                        className: 'estate-map-not-allowed-area',
                    }
                ).addTo(
                    map
                );

            [
                'click',
                'dblclick',
                // 'mousedown',
                // 'mouseup',
                'contextmenu',
            ].forEach(
                function(eventName)
                {
                    mask.on(
                        eventName,
                        function(event)
                        {
                            if (
                                event?.originalEvent
                            ) {
                                L.DomEvent.stop(
                                    event.originalEvent
                                );
                            }
                        }
                    );
                }
            );

            return mask;
        }


        function stopEstateMappedAreaClicks(
            layer
        )
        {
            if (!layer) {
                return layer;
            }

            [
                'click',
                'dblclick',
                'contextmenu',
            ].forEach(
                function(eventName)
                {
                    layer.on(
                        eventName,
                        function(event)
                        {
                            L.DomEvent.stopPropagation(
                                event
                            );

                            if (
                                event?.originalEvent
                            ) {
                                L.DomEvent.stop(
                                    event.originalEvent
                                );
                            }
                        }
                    );
                }
            );

            return layer;
        }


        function isEstatePointInsideExistingBlock(
            latlng,
            ignoreBlockId = null
        )
        {
            const blocks =
                window.estateGISBlocks || [];

            for (
                const block of blocks
            ) {
                if (
                    ignoreBlockId
                    &&
                    Number(block.id) ===
                    Number(ignoreBlockId)
                ) {
                    continue;
                }

                if (
                    !Array.isArray(
                        block.geo_coords
                    )
                    ||
                    block.geo_coords.length < 3
                ) {
                    continue;
                }

                const blockPoints =
                    block.geo_coords.map(
                        point =>
                            L.latLng(
                                point[0],
                                point[1]
                            )
                    );

                if (
                    pointInsidePolygon(
                        latlng,
                        blockPoints
                    )
                ) {
                    return true;
                }
            }

            return false;
        }


        function isEstatePointInsideAnyBlock(
            latlng
        )
        {
            return isEstatePointInsideExistingBlock(
                latlng
            );
        }


        function isEstatePointInsideExistingLot(
            latlng,
            ignoreLotId = null
        )
        {
            const lots =
                window.estateGISLots || [];

            for (
                const lot of lots
            ) {
                if (
                    ignoreLotId
                    &&
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

                const lotPoints =
                    lot.geo_coords.map(
                        point =>
                            L.latLng(
                                point[0],
                                point[1]
                            )
                    );

                if (
                    pointInsidePolygon(
                        latlng,
                        lotPoints
                    )
                ) {
                    return true;
                }
            }

            return false;
        }


        function addEstateOutsideBlocksInteractionMask(
            map
        )
        {
            if (!map) {
                return null;
            }

            const blockRings =
                (window.estateGISBlocks || [])
                    .filter(
                        block =>
                            Array.isArray(
                                block.geo_coords
                            )
                            &&
                            block.geo_coords.length >= 3
                    )
                    .map(
                        block =>
                            block.geo_coords
                    );

            if (
                blockRings.length === 0
            ) {
                return null;
            }

            const worldRing = [
                [
                    85.05112878,
                    -180,
                ],
                [
                    85.05112878,
                    180,
                ],
                [
                    -85.05112878,
                    180,
                ],
                [
                    -85.05112878,
                    -180,
                ],
            ];

            const mask =
                L.polygon(
                    [
                        worldRing,
                        ...blockRings,
                    ],
                    {
                        stroke: false,
                        fill: true,
                        fillColor: '#000000',
                        fillOpacity: 0.001,
                        interactive: true,
                        bubblingMouseEvents: false,
                    }
                ).addTo(
                    map
                );

            stopEstateMappedAreaClicks(
                mask
            );

            return mask;
        }


        function doesEstateSegmentCrossCoordinates(
            startPoint,
            endPoint,
            coordinates
        )
        {
            if (
                !startPoint
                ||
                !endPoint
                ||
                !Array.isArray(
                    coordinates
                )
                ||
                coordinates.length < 3
            ) {
                return false;
            }

            const polygonPoints =
                coordinates.map(
                    point =>
                        L.latLng(
                            point[0],
                            point[1]
                        )
                );

            for (
                let index = 0;
                index < polygonPoints.length;
                index++
            ) {
                const edgeStart =
                    polygonPoints[
                        index
                    ];

                const edgeEnd =
                    polygonPoints[
                        (
                            index + 1
                        )
                        %
                        polygonPoints.length
                    ];

                if (
                    segmentsIntersect(
                        startPoint,
                        endPoint,
                        edgeStart,
                        edgeEnd
                    )
                ) {
                    return true;
                }
            }

            return false;
        }


        function doesEstateSegmentCrossExistingBlock(
            startPoint,
            endPoint,
            ignoreBlockId = null
        )
        {
            const blocks =
                window.estateGISBlocks || [];

            for (
                const block of blocks
            ) {
                if (
                    ignoreBlockId
                    &&
                    Number(block.id) ===
                    Number(ignoreBlockId)
                ) {
                    continue;
                }

                if (
                    !Array.isArray(
                        block.geo_coords
                    )
                    ||
                    block.geo_coords.length < 3
                ) {
                    continue;
                }

                if (
                    doesEstateSegmentCrossCoordinates(
                        startPoint,
                        endPoint,
                        block.geo_coords
                    )
                ) {
                    return true;
                }
            }

            return false;
        }


        function doesEstateSegmentCrossExistingLot(
            startPoint,
            endPoint,
            ignoreLotId = null
        )
        {
            const lots =
                window.estateGISLots || [];

            for (
                const lot of lots
            ) {
                if (
                    ignoreLotId
                    &&
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

                if (
                    doesEstateSegmentCrossCoordinates(
                        startPoint,
                        endPoint,
                        lot.geo_coords
                    )
                ) {
                    return true;
                }
            }

            return false;
        }


        function getEstateBlockContainingPoint(
            latlng
        )
        {
            if (!latlng) {
                return null;
            }

            const blocks =
                window.estateGISBlocks || [];

            for (
                const block of blocks
            ) {
                if (
                    !Array.isArray(
                        block.geo_coords
                    )
                    ||
                    block.geo_coords.length < 3
                ) {
                    continue;
                }

                const blockPoints =
                    block.geo_coords.map(
                        point =>
                            L.latLng(
                                point[0],
                                point[1]
                            )
                    );

                if (
                    pointInsidePolygon(
                        latlng,
                        blockPoints
                    )
                ) {
                    return block;
                }
            }

            return null;
        }


        function isEstateInternalRoadDrawing()
        {
            const inputId = window.estateActiveLotTypeInputId;
            return !!inputId && document.getElementById(inputId)?.value === 'Internal Road';
        }


        function isEstateDrawingSegmentAllowed(
            startPoint,
            endPoint,
            mode,
            ignoreLotId = null,
            ignoreBlockId = null
        )
        {
            if (
                !startPoint
                ||
                !endPoint
            ) {
                return true;
            }

            if (
                mode === 'block'
            ) {
                return !doesEstateSegmentCrossExistingBlock(
                    startPoint,
                    endPoint,
                    ignoreBlockId
                );
            }

            if (
                mode === 'lot'
            ) {
                if (isEstateInternalRoadDrawing()) {
                    if (
                        isEstatePointInsideAnyBlock(startPoint)
                        || isEstatePointInsideAnyBlock(endPoint)
                        || doesEstateSegmentCrossExistingBlock(startPoint, endPoint)
                    ) {
                        return false;
                    }

                    return !doesEstateSegmentCrossExistingLot(
                        startPoint,
                        endPoint,
                        ignoreLotId
                    );
                }

                const startBlock =
                    getEstateBlockContainingPoint(
                        startPoint
                    );

                const endBlock =
                    getEstateBlockContainingPoint(
                        endPoint
                    );

                if (
                    !startBlock
                    ||
                    !endBlock
                    ||
                    Number(startBlock.id) !==
                    Number(endBlock.id)
                ) {
                    return false;
                }

                if (
                    doesEstateSegmentCrossCoordinates(
                        startPoint,
                        endPoint,
                        startBlock.geo_coords
                    )
                ) {
                    return false;
                }

                if (
                    doesEstateSegmentCrossExistingLot(
                        startPoint,
                        endPoint,
                        ignoreLotId
                    )
                ) {
                    return false;
                }
            }

            return true;
        }


        function restrictEstateDrawerToMappedAreas(
            drawer,
            mode,
            ignoreLotId = null,
            ignoreBlockId = null
        )
        {
            if (
                !drawer
                ||
                mode === 'boundary'
                ||
                typeof drawer.addVertex !==
                    'function'
            ) {
                return;
            }

            const originalAddVertex =
                drawer.addVertex.bind(
                    drawer
                );

            drawer.addVertex =
                function(latlng)
                {
                    if (
                        !isEstatePointInsideSubdivisionBoundary(
                            latlng
                        )
                    ) {
                        return;
                    }

                    if (
                        mode === 'block'
                        &&
                        isEstatePointInsideExistingBlock(
                            latlng,
                            ignoreBlockId
                        )
                    ) {
                        return;
                    }

                    if (
                        mode === 'lot'
                    ) {
                        if (
                            isEstateInternalRoadDrawing()
                                ? isEstatePointInsideAnyBlock(latlng)
                                : !isEstatePointInsideAnyBlock(latlng)
                        ) {
                            return;
                        }

                        if (
                            isEstatePointInsideExistingLot(
                                latlng,
                                ignoreLotId
                            )
                        ) {
                            return;
                        }
                    }

                    const markers =
                        Array.isArray(
                            drawer._markers
                        )
                            ? drawer._markers
                            : [];

                    const lastMarker =
                        markers.length > 0
                            ? markers[
                                markers.length - 1
                            ]
                            : null;

                    const lastPoint =
                        lastMarker
                            &&
                            typeof lastMarker.getLatLng ===
                                'function'
                                ? lastMarker.getLatLng()
                                : null;

                    if (
                        lastPoint
                        &&
                        !isEstateDrawingSegmentAllowed(
                            lastPoint,
                            latlng,
                            mode,
                            ignoreLotId,
                            ignoreBlockId
                        )
                    ) {
                        @this.call(
                            'showMapNotification',
                            mode === 'block'
                                ? 'Block Edge Not Allowed'
                                : 'Lot Edge Not Allowed',
                            mode === 'block'
                                ? 'That point would make the new block edge pass through an existing mapped block. Choose another point.'
                                : 'That point would make the new lot edge leave its block or pass through an existing mapped lot. Choose another point.',
                            'danger'
                        );

                        return;
                    }

                    return originalAddVertex(
                        latlng
                    );
                };


            if (
                typeof drawer._finishShape ===
                    'function'
            ) {
                const originalFinishShape =
                    drawer._finishShape.bind(
                        drawer
                    );

                drawer._finishShape =
                    function()
                    {
                        const markers =
                            Array.isArray(
                                drawer._markers
                            )
                                ? drawer._markers
                                : [];

                        if (
                            markers.length >= 3
                        ) {
                            const firstMarker =
                                markers[0];

                            const lastMarker =
                                markers[
                                    markers.length - 1
                                ];

                            const firstPoint =
                                firstMarker
                                &&
                                typeof firstMarker.getLatLng ===
                                    'function'
                                    ? firstMarker.getLatLng()
                                    : null;

                            const lastPoint =
                                lastMarker
                                &&
                                typeof lastMarker.getLatLng ===
                                    'function'
                                    ? lastMarker.getLatLng()
                                    : null;

                            if (
                                firstPoint
                                &&
                                lastPoint
                                &&
                                !isEstateDrawingSegmentAllowed(
                                    lastPoint,
                                    firstPoint,
                                    mode,
                                    ignoreLotId,
                                    ignoreBlockId
                                )
                            ) {
                                @this.call(
                                    'showMapNotification',
                                    mode === 'block'
                                        ? 'Cannot Close Block'
                                        : 'Cannot Close Lot',
                                    mode === 'block'
                                        ? 'The closing edge would pass through an existing mapped block. Add or move a point before closing the shape.'
                                        : 'The closing edge would leave the selected block or pass through an existing mapped lot. Add or move a point before closing the shape.',
                                    'danger'
                                );

                                return;
                            }
                        }

                        return originalFinishShape();
                    };
            }
        }


        function keepEstateEditedPolygonInAllowedArea(
            polygon,
            mode,
            ignoreLotId = null,
            ignoreBlockId = null,
            containerId = null
        )
        {
            if (
                !polygon
                ||
                mode === 'boundary'
            ) {
                return;
            }

            let resetting =
                false;

            polygon.on(
                'edit',
                function()
                {
                    if (resetting) {
                        return;
                    }

                    let valid =
                        isPolygonInsideEstateBoundaryCoordinates(
                            polygon
                        );

                    if (
                        valid
                        &&
                        mode === 'block'
                    ) {
                        valid =
                            !doesPolygonOverlapExistingBlock(
                                polygon,
                                ignoreBlockId
                            );
                    }

                    if (
                        valid
                        &&
                        mode === 'lot'
                    ) {
                        const coordinates =
                            getPolygonCoordinates(
                                polygon
                            );

                        valid =
                            (
                                isEstateInternalRoadDrawing()
                                    ? !doesPolygonOverlapExistingBlock(polygon)
                                    : !!findContainingEstateBlock(coordinates)
                            )
                            &&
                            !doesPolygonOverlapExistingLot(
                                polygon,
                                ignoreLotId
                            );
                    }

                    if (valid) {
                        return;
                    }

                    if (
                        !containerId
                    ) {
                        return;
                    }

                    resetting =
                        true;

                    /*
                    |--------------------------------------------------------------------------
                    | USE THE SAME BEHAVIOR AS "RESET POINTS"
                    |--------------------------------------------------------------------------
                    |
                    | Instead of manually moving the polygon back and trying to
                    | refresh Leaflet Draw's existing handles, rebuild the polygon
                    | exactly the same way as pressing Reset Points.
                    |
                    */

                    setTimeout(
                        function()
                        {
                            restoreEstateModalMiniMapOriginalPolygon(
                                containerId
                            );
                        },
                        0
                    );
                }
            );
        }

        function restrictEstateDrawerToBoundary(
            drawer,
            mode
        )
        {
            if (
                !drawer
                ||
                mode === 'boundary'
                ||
                typeof drawer.addVertex !==
                    'function'
            ) {
                return;
            }

            const getMappedAreas =
                function()
                {
                    if (
                        mode === 'block'
                    ) {
                        return (
                            window.estateGISBlocks
                            || []
                        );
                    }

                    if (
                        mode === 'lot'
                    ) {
                        return (
                            window.estateGISLots
                            || []
                        );
                    }

                    return [];
                };

            const getMappedPolygonPoints =
                function(item)
                {
                    if (
                        !Array.isArray(
                            item?.geo_coords
                        )
                        ||
                        item.geo_coords.length < 3
                    ) {
                        return [];
                    }

                    return item.geo_coords.map(
                        point =>
                            L.latLng(
                                point[0],
                                point[1]
                            )
                    );
                };

            const segmentCrossesMappedArea =
                function(start, end)
                {
                    if (
                        !start
                        ||
                        !end
                    ) {
                        return false;
                    }

                    const mappedAreas =
                        getMappedAreas();

                    for (
                        const item of mappedAreas
                    ) {
                        const points =
                            getMappedPolygonPoints(
                                item
                            );

                        if (
                            points.length < 3
                        ) {
                            continue;
                        }

                        if (
                            pointInsidePolygon(
                                end,
                                points
                            )
                        ) {
                            return true;
                        }

                        for (
                            let i = 0;
                            i < points.length;
                            i++
                        ) {
                            const edgeStart =
                                points[i];

                            const edgeEnd =
                                points[
                                    (i + 1)
                                    %
                                    points.length
                                ];

                            if (
                                segmentsIntersect(
                                    start,
                                    end,
                                    edgeStart,
                                    edgeEnd
                                )
                            ) {
                                return true;
                            }
                        }
                    }

                    return false;
                };

            const showRejectedPointError =
                function(isClosing = false)
                {
                    @this.call(
                        'showMapNotification',
                        isClosing
                            ? 'Cannot Close Area'
                            : 'Point Not Allowed',
                        isClosing
                            ? (
                                mode === 'block'
                                    ? 'The closing edge crosses an existing mapped block. Add another point or choose a different path.'
                                    : 'The closing edge crosses an existing mapped lot. Add another point or choose a different path.'
                            )
                            : (
                                mode === 'block'
                                    ? 'This edge would cross an existing mapped block. Choose another point.'
                                    : 'This edge would cross an existing mapped lot. Choose another point.'
                            ),
                        'danger'
                    );
                };

            const originalAddVertex =
                drawer.addVertex.bind(
                    drawer
                );

            drawer.addVertex =
                function(latlng)
                {
                    if (
                        !isEstatePointInsideSubdivisionBoundary(
                            latlng
                        )
                    ) {
                        @this.call(
                            'showMapNotification',
                            'Point Not Allowed',
                            'Choose a point inside the subdivision boundary.',
                            'danger'
                        );

                        return;
                    }

                    const markers =
                        drawer._markers || [];

                    const previousPoint =
                        markers.length
                            ? markers[
                                markers.length - 1
                            ].getLatLng()
                            : null;

                    if (
                        previousPoint
                        &&
                        segmentCrossesMappedArea(
                            previousPoint,
                            latlng
                        )
                    ) {
                        showRejectedPointError();

                        return;
                    }

                    return originalAddVertex(
                        latlng
                    );
                };

            if (
                typeof drawer.completeShape ===
                    'function'
            ) {
                const originalCompleteShape =
                    drawer.completeShape.bind(
                        drawer
                    );

                drawer.completeShape =
                    function()
                    {
                        const markers =
                            drawer._markers || [];

                        if (
                            markers.length >= 3
                        ) {
                            const lastPoint =
                                markers[
                                    markers.length - 1
                                ].getLatLng();

                            const firstPoint =
                                markers[0]
                                    .getLatLng();

                            if (
                                segmentCrossesMappedArea(
                                    lastPoint,
                                    firstPoint
                                )
                            ) {
                                showRejectedPointError(
                                    true
                                );

                                return;
                            }
                        }

                        return originalCompleteShape();
                    };
            }
        }


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
                    minZoom: 18,
                    maxZoom: 22,
                }
            );

            window.estateLeafletMap = map;

            const baseLayers =
                buildEstateBaseLayers();

            baseLayers.default.addTo(
                map
            );

            window.estateSelectedBaseLayer =
                window.estateSelectedBaseLayer
                || 'Satellite';

            L.control.layers(
                baseLayers.layers,
                null,
                {
                    position: 'bottomleft'
                }
            ).addTo(
                map
            );

            map.on(
                'baselayerchange',
                function(event)
                {
                    window.estateSelectedBaseLayer =
                        event.name;
                }
            );

            let boundary = null;

            if (
                Array.isArray(
                    estateSubdivisionBoundary
                )
                &&
                estateSubdivisionBoundary.length >= 3
            ) {
                boundary = L.polygon(
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
            }

            window.estateBoundaryLayer =
                boundary;

            window.estateBoundaryClickMask =
                boundary
                    ? addEstateBoundaryInteractionMask(
                        map
                    )
                    : null;


            const blockLayer =
                L.layerGroup()
                    .addTo(map);

            window.estateBlockLayer =
                blockLayer;


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
                'zoom zoomend',
                function()
                {
                    updateEstateLotOverlaySizes();
                    updateEstateBlockLabelSizes();

                    setTimeout(
                        function()
                        {
                            updateEstateBlockLabelSizes();
                        },
                        50
                    );
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
                                        updateEstateCoordinateField(
                                            'estate-create-lot-coords',
                                            updatedCoordinates
                                        );

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


            if (boundary) {
                map.fitBounds(
                    boundary.getBounds(),
                    {
                        padding: [
                            30,
                            30
                        ],
                    }
                );
            } else {
                map.setView(
                    manhattanResidences,
                    18
                );
            }


            renderExistingEstateBlocks();
            renderExistingEstateLots();

            initEstateMapFilters();
            applyEstateMapFilters();


            setTimeout(
                () => {
                    map.invalidateSize();

                    updateEstateLotOverlaySizes();
                    updateEstateBlockLabelSizes();
                },
                250
            );
        }


        function renderExistingEstateBlocks()
        {
            const blocks =
                window.estateGISBlocks || [];

            window
                .estateBlockLayer
                ?.clearLayers();

            blocks.forEach(
                function(block)
                {
                    if (
                        !Array.isArray(
                            block.geo_coords
                        )
                        ||
                        block.geo_coords.length < 3
                    ) {
                        return;
                    }

                    const polygon =
                        L.polygon(
                            block.geo_coords,
                            {
                                color: '#9333ea',
                                weight: 2,
                                dashArray: '7 5',
                                fillColor: '#a855f7',
                                fillOpacity: 0.04,
                            }
                        );

                    polygon.estateBlockId =
                        block.id;

                    polygon.estateFilterKind =
                        'blocks';

                    polygon.bindTooltip(
                        block.name ?? 'Block',
                        {
                            direction: 'center',
                            permanent: true,
                            className: 'estate-block-label',
                            opacity: 1,
                        }
                    );

                    polygon.on(
                        'add',
                        function()
                        {
                            const bottomCenter =
                                getEstateBlockBottomCenter(
                                    polygon
                                );

                            if (!bottomCenter) {
                                return;
                            }

                            polygon
                                .getTooltip()
                                ?.setLatLng(
                                    bottomCenter
                                );
                        }
                    );

                    polygon.on(
                        'click',
                        function(event)
                        {
                            L.DomEvent.stopPropagation(
                                event
                            );

                            showEstateBlockTooltip(
                                block,
                                event.latlng
                            );
                        }
                    );

                    polygon.addTo(
                        window.estateBlockLayer
                    );

                    polygon.on(
                    'tooltipopen',
                    function()
                    {
                        const tooltip =
                            polygon.getTooltip();

                        const element =
                            tooltip?.getElement();

                        if (!element) {
                            return;
                        }


                        const fontSize =
                            getEstateBlockLabelSize(
                                polygon
                            );


                        element.style.fontSize =
                            `${fontSize}px`;
                    }
                );
                }
            );
        }


        function renderExistingEstateLots()
        {
            const lots =
                window.estateGISLots || [];


            window.estateConstructionMarkers = [];
            window.estateSoldMarkers = [];
            window.estateLotNumberMarkers = [];
            window.estateLotAreaMarkers = [];


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


                    const isInternalRoad =
                        (
                            lot.type || ''
                        )
                            .trim()
                            .toLowerCase()
                        === 'internal road';


                    const polygon =
                        L.polygon(
                            lot.geo_coords,
                            {
                                color:
                                    isInternalRoad
                                        ? '#ffffff'
                                        : color,

                                fillColor:
                                    isInternalRoad
                                        ? '#ffffff'
                                        : color,

                                weight: 2,

                                fillOpacity:
                                    isInternalRoad
                                        ? 0.04
                                        : 0.50,

                                dashArray:
                                    isInternalRoad
                                        ? '10 5 2 5'
                                        : null,
                            }
                        );


                    polygon.estateLotId =
                        lot.id;
                    polygon.estateLot =
                        lot;
                    polygon.estateLotType =
                        lot.type;


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
                        lot.lot_number !== null &&
                        lot.lot_number !== undefined
                    ) {
                        const lotNumberScale =
                            getEstateLotOverlayScale(
                                polygon
                            );

                        const lotNumberMarker =
                            L.marker(
                                getEstatePolygonTopRight(
                                    polygon
                                ),
                                {
                                    interactive: false,

                                    opacity:
                                        lotNumberScale > 0
                                            ? 1
                                            : 0,

                                    icon:
                                        buildEstateLotNumberIcon(
                                            lot.lot_number,
                                            lotNumberScale
                                        ),
                                }
                            );

                        lotNumberMarker.estatePolygon =
                            polygon;

                        lotNumberMarker.estateLotNumber =
                            lot.lot_number;

                        lotNumberMarker.estateLotType =
                            lot.type;

                        lotNumberMarker.addTo(
                            window.estateLotLayer
                        );

                        window.estateLotNumberMarkers.push(
                            lotNumberMarker
                        );
                    }

                    if (
                        lot.lot_area !== null &&
                        lot.lot_area !== undefined
                    ) {
                        const lotAreaScale =
                            getEstateLotOverlayScale(
                                polygon
                            );

                        const lotAreaMarker =
                            L.marker(
                                getEstatePolygonCenter(
                                    polygon
                                ),
                                {
                                    interactive: false,

                                    opacity:
                                        lotAreaScale > 0
                                            ? 1
                                            : 0,

                                    icon:
                                        buildEstateLotAreaIcon(
                                            lot.lot_area,
                                            lotAreaScale
                                        ),
                                }
                            );

                        lotAreaMarker.estatePolygon =
                            polygon;

                        lotAreaMarker.estateLotArea =
                            lot.lot_area;

                        lotAreaMarker.estateLotType =
                            lot.type;

                        lotAreaMarker.addTo(
                            window.estateLotLayer
                        );

                        window.estateLotAreaMarkers.push(
                            lotAreaMarker
                        );
                    }

                    if (
                        (
                            lot.status || ''
                        )
                            .toLowerCase()
                            .trim()
                        === 'sold'
                    ) {
                        const topLeft =
                            getEstatePolygonTopLeft(
                                polygon
                            );

                        const soldScale =
                            getEstateLotOverlayScale(
                                polygon
                            );

                        const soldMarker =
                            L.marker(
                                topLeft,
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

                        soldMarker.estateLotType =
                            lot.type;

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
                        const topLeft =
                            getEstatePolygonTopLeft(
                                polygon
                            );

                        const constructionScale =
                            getEstateLotOverlayScale(
                                polygon
                            );

                        const constructionMarker =
                            L.marker(
                                topLeft,
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

                        constructionMarker.estateLotType =
                            lot.type;

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

        function initEstateMapFilters()
        {
            const checkboxes =
                document.querySelectorAll(
                    '#estate-map-filter-panel .estate-map-filter-checkbox'
                );

            checkboxes.forEach(
                function(checkbox)
                {
                    const kind =
                        checkbox.dataset.filterKind;

                    const lotType =
                        checkbox.dataset.lotType;

                    if (
                        kind === 'boundary'
                    ) {
                        checkbox.checked =
                            window.estateMapFilters.boundary;
                    } else if (
                        kind === 'blocks'
                    ) {
                        checkbox.checked =
                            window.estateMapFilters.blocks;
                    } else if (
                        kind === 'lot-type'
                        &&
                        lotType
                    ) {
                        checkbox.checked =
                            window.estateMapFilters.lotTypes[
                                lotType
                            ] !== false;
                    }

                    if (
                        checkbox.dataset.estateFilterBound ===
                        '1'
                    ) {
                        return;
                    }

                    checkbox.dataset.estateFilterBound =
                        '1';

                    checkbox.addEventListener(
                        'change',
                        function()
                        {
                            const currentKind =
                                checkbox.dataset.filterKind;

                            const currentLotType =
                                checkbox.dataset.lotType;

                            if (
                                currentKind === 'boundary'
                            ) {
                                window.estateMapFilters.boundary =
                                    checkbox.checked;
                            } else if (
                                currentKind === 'blocks'
                            ) {
                                window.estateMapFilters.blocks =
                                    checkbox.checked;
                            } else if (
                                currentKind === 'lot-type'
                                &&
                                currentLotType
                            ) {
                                window.estateMapFilters.lotTypes[
                                    currentLotType
                                ] =
                                    checkbox.checked;
                            }

                            hideEstateCustomTooltip();

                            applyEstateMapFilters();
                        }
                    );
                }
            );
        }


        function setEstateMapLayerVisibility(
            layer,
            visible
        )
        {
            const map =
                window.estateLeafletMap;

            if (
                !map ||
                !layer
            ) {
                return;
            }

            const isVisible =
                map.hasLayer(
                    layer
                );

            if (
                visible &&
                !isVisible
            ) {
                layer.addTo(
                    map
                );
            }

            if (
                !visible &&
                isVisible
            ) {
                map.removeLayer(
                    layer
                );
            }
        }


        function applyEstateMapFilters()
        {
            const map =
                window.estateLeafletMap;

            if (!map) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | SUBDIVISION BOUNDARY
            |--------------------------------------------------------------------------
            */

            setEstateMapLayerVisibility(
                window.estateBoundaryLayer,
                window.estateMapFilters.boundary
            );

            /*
            |--------------------------------------------------------------------------
            | BLOCKS
            |--------------------------------------------------------------------------
            */

            if (
                window.estateBlockLayer
            ) {
                window
                    .estateBlockLayer
                    .eachLayer(
                        function(layer)
                        {
                            setEstateMapLayerVisibility(
                                layer,
                                window.estateMapFilters.blocks
                            );
                        }
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | LOT TYPES + THEIR LABELS / MARKERS
            |--------------------------------------------------------------------------
            */

            if (
                window.estateLotLayer
            ) {
                window
                    .estateLotLayer
                    .eachLayer(
                        function(layer)
                        {
                            const lotType =
                                layer.estateLotType
                                ??
                                layer.estateLot?.type
                                ??
                                null;

                            if (!lotType) {
                                return;
                            }

                            const visible =
                                window.estateMapFilters.lotTypes[
                                    lotType
                                ] !== false;

                            setEstateMapLayerVisibility(
                                layer,
                                visible
                            );
                        }
                    );
            }

            if (
                window.estateBoundaryLayer &&
                window.estateLeafletMap.hasLayer(
                    window.estateBoundaryLayer
                ) &&
                typeof window.estateBoundaryLayer.bringToBack ===
                    'function'
            ) {
                window
                    .estateBoundaryLayer
                    .bringToBack();
            }


            if (
                window.estateBlockLayer
            ) {
                window
                    .estateBlockLayer
                    .eachLayer(
                        function(layer)
                        {
                            if (
                                window.estateLeafletMap.hasLayer(
                                    layer
                                ) &&
                                typeof layer.bringToFront ===
                                    'function'
                            ) {
                                layer.bringToFront();
                            }
                        }
                    );
            }


            if (
                window.estateLotLayer
            ) {
                window
                    .estateLotLayer
                    .eachLayer(
                        function(layer)
                        {
                            if (
                                window.estateLeafletMap.hasLayer(
                                    layer
                                ) &&
                                typeof layer.bringToFront ===
                                    'function'
                            ) {
                                layer.bringToFront();
                            }
                        }
                    );
            }

            updateEstateLotOverlaySizes();
            updateEstateBlockLabelSizes();
        }


        function getEstatePolygonCenter(
            polygon
        )
        {
            if (!polygon) {
                return null;
            }

            const bounds =
                polygon.getBounds();

            if (
                !bounds ||
                !bounds.isValid()
            ) {
                return null;
            }

            return bounds.getCenter();
        }

        function getEstateBlockBottomCenter(
            polygon
        )
        {
            if (!polygon) {
                return null;
            }

            const points =
                polygon.getLatLngs()[0];

            if (
                !Array.isArray(points)
                ||
                points.length < 3
            ) {
                return null;
            }


            const bounds =
                polygon.getBounds();

            if (
                !bounds
                ||
                !bounds.isValid()
            ) {
                return null;
            }


            /*
            |--------------------------------------------------------------------------
            | POSITION SLIGHTLY ABOVE THE BOTTOM
            |--------------------------------------------------------------------------
            |
            | 0.15 = 15% above the bottom edge.
            |
            | This keeps the name visually at the bottom,
            | but prevents it from sitting directly on the polygon border.
            |
            */

            const south =
                bounds.getSouth();

            const north =
                bounds.getNorth();

            const targetLat =
                south +
                (
                    north - south
                ) * 0.15;


            /*
            |--------------------------------------------------------------------------
            | FIND ALL HORIZONTAL INTERSECTIONS
            |--------------------------------------------------------------------------
            */

            const intersections = [];


            for (
                let i = 0;
                i < points.length;
                i++
            ) {
                const a =
                    points[i];

                const b =
                    points[
                        (i + 1)
                        %
                        points.length
                    ];


                if (
                    (
                        a.lat <= targetLat
                        &&
                        b.lat > targetLat
                    )
                    ||
                    (
                        b.lat <= targetLat
                        &&
                        a.lat > targetLat
                    )
                ) {
                    const ratio =
                        (
                            targetLat -
                            a.lat
                        )
                        /
                        (
                            b.lat -
                            a.lat
                        );


                    const lng =
                        a.lng +
                        (
                            b.lng -
                            a.lng
                        ) * ratio;


                    intersections.push(
                        lng
                    );
                }
            }


            intersections.sort(
                function(a, b)
                {
                    return a - b;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | FIND THE WIDEST INTERIOR SECTION
            |--------------------------------------------------------------------------
            |
            | Intersections appear in pairs:
            |
            | outside | inside | outside | inside
            |
            */

            let bestLeft =
                null;

            let bestRight =
                null;

            let bestWidth =
                -Infinity;


            for (
                let i = 0;
                i + 1 < intersections.length;
                i += 2
            ) {
                const left =
                    intersections[i];

                const right =
                    intersections[i + 1];

                const width =
                    right - left;


                if (
                    width >
                    bestWidth
                ) {
                    bestWidth =
                        width;

                    bestLeft =
                        left;

                    bestRight =
                        right;
                }
            }


            if (
                bestLeft !== null
                &&
                bestRight !== null
            ) {
                return L.latLng(
                    targetLat,
                    (
                        bestLeft +
                        bestRight
                    ) / 2
                );
            }


            /*
            |--------------------------------------------------------------------------
            | FALLBACK
            |--------------------------------------------------------------------------
            */

            return bounds.getCenter();
        }

        function getEstateLotLabelCenter(
            polygon
        )
        {
            if (!polygon) {
                return null;
            }

            const bounds =
                polygon.getBounds();

            if (
                !bounds ||
                !bounds.isValid()
            ) {
                return null;
            }

            return bounds.getCenter();
        }

        function getEstatePolygonTopLeft(
            polygon
        )
        {
            if (!polygon) {
                return null;
            }

            const bounds =
                polygon.getBounds();

            if (
                !bounds ||
                !bounds.isValid()
            ) {
                return null;
            }

            const points =
                polygon.getLatLngs()[0];

            if (
                !Array.isArray(points) ||
                points.length === 0
            ) {
                return bounds.getNorthWest();
            }

            /*
            |--------------------------------------------------------------------------
            | GET THE ACTUAL LOT VERTEX CLOSEST TO TOP-LEFT
            |--------------------------------------------------------------------------
            |
            | bounds.getNorthWest() may be outside an angled polygon.
            |
            | We use it only as a reference, then find the nearest REAL polygon
            | vertex. This keeps the SOLD text / construction icon attached to
            | an actual corner of the lot, just like the top-right lot number.
            |
            */

            const northWest =
                bounds.getNorthWest();

            let closestPoint =
                points[0];

            let closestDistance =
                Infinity;

            points.forEach(
                function(point)
                {
                    const distance =
                        northWest.distanceTo(
                            point
                        );

                    if (
                        distance <
                        closestDistance
                    ) {
                        closestDistance =
                            distance;

                        closestPoint =
                            point;
                    }
                }
            );

            return closestPoint;
        }

        function getEstatePolygonTopRight(
            polygon
        )
        {
            if (!polygon) {
                return null;
            }

            const bounds =
                polygon.getBounds();

            if (
                !bounds ||
                !bounds.isValid()
            ) {
                return null;
            }

            const points =
                polygon.getLatLngs()[0];

            if (
                !Array.isArray(points) ||
                points.length === 0
            ) {
                return bounds.getNorthEast();
            }

            /*
            |--------------------------------------------------------------------------
            | GET THE ACTUAL LOT VERTEX CLOSEST TO TOP-RIGHT
            |--------------------------------------------------------------------------
            |
            | bounds.getNorthEast() may be outside an angled polygon.
            |
            | We use it only as a reference, then find the nearest REAL polygon
            | vertex. This keeps the lot number attached to the actual lot.
            |
            */

            const northEast =
                bounds.getNorthEast();

            let closestPoint =
                points[0];

            let closestDistance =
                Infinity;

            points.forEach(
                function(point)
                {
                    const distance =
                        northEast.distanceTo(
                            point
                        );

                    if (
                        distance <
                        closestDistance
                    ) {
                        closestDistance =
                            distance;

                        closestPoint =
                            point;
                    }
                }
            );

            return closestPoint;
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

        function getEstateBlockLabelSize(
            polygon
        )
        {
            return 9;
        }

        function updateEstateBlockLabelSizes()
        {
            if (
                !window.estateBlockLayer
            ) {
                return;
            }


            window
                .estateBlockLayer
                .eachLayer(
                    function(layer)
                    {
                        if (
                            !layer.estateBlockId ||
                            typeof layer.getTooltip !==
                                'function'
                        ) {
                            return;
                        }


                        const tooltip =
                            layer.getTooltip();


                        if (!tooltip) {
                            return;
                        }


                        const element =
                            tooltip.getElement();


                        if (!element) {
                            return;
                        }


                        const fontSize =
                            getEstateBlockLabelSize(
                                layer
                            );


                        if (fontSize <= 0) {
                            element.style.display =
                                'none';

                            return;
                        }


                        element.style.display =
                            '';


                        element.style.fontSize =
                            `${fontSize}px`;


                        /*
                        |--------------------------------------------------------------------------
                        | Scale padding too
                        |--------------------------------------------------------------------------
                        */

                        const verticalPadding =
                            Math.max(
                                2,
                                fontSize * 0.32
                            );

                        const horizontalPadding =
                            Math.max(
                                4,
                                fontSize * 0.55
                            );


                        element.style.padding =
                            `${verticalPadding}px ${horizontalPadding}px`;
                    }
                );
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
                smallestSide < 8
            ) {
                return 0;
            }


            return Math.min(
                1,
                smallestSide / 100
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

                /*
                |--------------------------------------------------------------------------
                | TOP-LEFT ANCHOR
                |--------------------------------------------------------------------------
                |
                | The marker LatLng is the polygon's fixed north-west point.
                | Anchor the icon's own top-left corner to that point so the
                | SOLD text grows to the right/down and stays inside the lot.
                |
                */
                iconAnchor: [
                    0,
                    0,
                ],
            });
        }

        function buildEstateLotNumberIcon(
            lotNumber,
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
                    55 * safeScale
                );

            const height =
                Math.max(
                    1,
                    18 * safeScale
                );

            const fontSize =
                Math.max(
                    1,
                    10 * safeScale
                );

            return L.divIcon({
                className:
                    'estate-lot-number-label',

                html: `
                    <div
                        class="estate-lot-number-inner"
                        style="
                            width:${width}px;
                            height:${height}px;
                            font-size:${fontSize}px;
                        "
                    >
                        ${lotNumber}
                    </div>
                `,

                iconSize: [
                    width,
                    height,
                ],

                iconAnchor: [
                    width,
                    0,
                ],
            });
        }

        function buildEstateLotAreaIcon(
            lotArea,
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
                    70 * safeScale
                );

            const height =
                Math.max(
                    1,
                    18 * safeScale
                );

            const fontSize =
                Math.max(
                    1,
                    11 * safeScale
                );

            return L.divIcon({
                className:
                    'estate-lot-area-label',

                html: `
                    <div
                        class="estate-lot-area-inner"
                        style="
                            width:${width}px;
                            height:${height}px;
                            font-size:${fontSize}px;
                        "
                    >
                        ${Math.round(Number(lotArea))}
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

                /*
                |--------------------------------------------------------------------------
                | TOP-LEFT ANCHOR
                |--------------------------------------------------------------------------
                |
                | The marker LatLng is the polygon's fixed north-west point.
                |
                */
                iconAnchor: [
                    0,
                    0,
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

                        marker.setLatLng(
                            getEstatePolygonTopLeft(
                                polygon
                            )
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
                        
                        marker.setLatLng(
                            getEstatePolygonTopLeft(
                                polygon
                            )
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
                (window.estateLotNumberMarkers || [])
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

                            marker.setLatLng(
                                getEstatePolygonTopRight(
                                    polygon
                                )
                            );

                            marker.setOpacity(
                                scale > 0
                                    ? 1
                                    : 0
                            );

                            marker.setIcon(
                                buildEstateLotNumberIcon(
                                    marker.estateLotNumber,
                                    scale
                                )
                            );
                        }
                    );


                (window.estateLotAreaMarkers || [])
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

                            marker.setLatLng(
                                getEstatePolygonCenter(
                                    polygon
                                )
                            );

                            marker.setOpacity(
                                scale > 0
                                    ? 1
                                    : 0
                            );

                            marker.setIcon(
                                buildEstateLotAreaIcon(
                                    marker.estateLotArea,
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


        function buildEstateBlockPopup(block)
        {
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
                            style="width:20px;height:20px;color:#4b5563;"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>

                    <div
                        style="
                            height:110px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            background:#eef2ff;
                            color:#4f46e5;
                        "
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            style="width:42px;height:42px;"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25m-4.5-13.5h16.5m0 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 4.5m8.5-4.5 1 4.5m-9.5 0h8.5" />
                        </svg>
                    </div>

                    <div class="estate-popup-body">
                        <div class="estate-popup-header">
                            <div style="min-width:0;flex:1;">
                                <div class="estate-popup-title">
                                    ${escapeEstateHTML(block.name ?? 'Block')}
                                </div>

                                <div class="estate-popup-meta">
                                    <span>Subdivision Block</span>
                                </div>
                            </div>
                        </div>

                        <div class="estate-popup-actions">
                            <button
                                type="button"
                                class="estate-popup-button estate-popup-button-edit"
                                onclick="editEstateGISBlock(${block.id})"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487 19.5 7.125m-1.318-3.957a1.875 1.875 0 1 1 2.652 2.652L8.25 18.404 4.5 19.5l1.096-3.75L18.182 3.168Z" />
                                </svg>
                                <span>Edit</span>
                            </button>

                            ${
                                block.can_delete
                                    ? `
                                        <button
                                            type="button"
                                            class="estate-popup-button estate-popup-button-delete"
                                            onclick="deleteEstateGISBlock(
                                                ${block.id},
                                                '${escapeEstateJS(block.name ?? 'Block')}'
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

                                            <span>Delete</span>
                                        </button>
                                    `
                                    : ''
                            }
                        </div>
                    </div>
                </div>
            `;
        }


        function buildEstatePopup(lot)
        {
            const propertyType =
                (
                    lot.type || ''
                ).trim();


            const rawStatus =
                (
                    lot.status || ''
                )
                    .toLowerCase()
                    .trim();

            const isSold =
                rawStatus === 'sold';

            const status =
                isSold
                    ? 'sold'
                    : rawStatus;


            const hidePriceAndStatus =
                [
                    'Model House',
                    'Playground & Community Amenities',
                    'Internal Road',
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


        function showEstateBlockTooltip(
            block,
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
                `block-${block.id}`;

            window.estateCustomTooltipLatLng =
                latlng;

            content.innerHTML =
                buildEstateBlockPopup(
                    block
                );

            tooltip.classList.add(
                'is-visible'
            );

            positionEstateCustomTooltip(
                latlng
            );
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


        function updateEstateCoordinateField(
            inputId,
            coordinates
        )
        {
            const input =
                document.getElementById(
                    inputId
                );

            if (!input) {
                return;
            }

            input.value =
                JSON.stringify(
                    coordinates || []
                );

            input.dispatchEvent(
                new Event(
                    'input',
                    {
                        bubbles: true,
                    }
                )
            );

            input.dispatchEvent(
                new Event(
                    'change',
                    {
                        bubbles: true,
                    }
                )
            );
        }


        function startEstateBoundaryMapping()
        {
            @this.set(
                'newBoundaryGeoCoords',
                []
            ).then(() => {

                $openModal(
                    'create-gis-boundary-modal'
                );

                initEstateCreateDrawingMap(
                    'estate-create-boundary-mini-map',
                    {
                        mode: 'boundary',

                        onChange:
                            function(updatedCoordinates)
                            {
                                updateEstateCoordinateField(
                                    'estate-create-boundary-coords',
                                    updatedCoordinates
                                );

                                @this.set(
                                    'newBoundaryGeoCoords',
                                    updatedCoordinates
                                );
                            },
                    }
                );
            });
        }


        function editEstateSubdivisionBoundary()
        {
            hideEstateCustomTooltip();

            @this.call(
                'startEditBoundary'
            );
        }


        function startEstateBlockMapping()
        {
            @this.set(
                'newBlockGeoCoords',
                []
            ).then(() => {

                $openModal(
                    'create-gis-block-modal'
                );

                initEstateCreateDrawingMap(
                    'estate-create-block-mini-map',
                    {
                        mode: 'block',

                        onChange:
                            function(updatedCoordinates)
                            {
                                updateEstateCoordinateField(
                                    'estate-create-block-coords',
                                    updatedCoordinates
                                );

                                @this.set(
                                    'newBlockGeoCoords',
                                    updatedCoordinates
                                );
                            },
                    }
                );
            });
        }


        function editEstateGISBlock(
            blockId
        )
        {
            hideEstateCustomTooltip();

            @this.call(
                'startEditBlock',
                blockId
            );
        }


        function deleteEstateGISBlock(
            blockId,
            blockName
        )
        {
            hideEstateCustomTooltip();

            @this.call(
                'deleteBlockConfirmation',
                blockId,
                blockName
            );
        }


        function deleteEstateGISBoundary()
        {
            hideEstateCustomTooltip();

            @this.call(
                'deleteBoundaryConfirmation'
            );
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


                window.estateActiveLotTypeInputId =
                    'estate-create-lot-type';

                const mainMapState =
                    getEstateMainMapState();

                initEstateCreateDrawingMap(
                    'estate-create-mini-map',
                    {
                        mode: 'lot',

                        initialView:
                            mainMapState,

                        onChange:
                            function(updatedCoordinates)
                            {
                                updateEstateCoordinateField(
                                    'estate-create-lot-coords',
                                    updatedCoordinates
                                );

                                @this.set(
                                    'newGeoCoords',
                                    updatedCoordinates
                                );

                                updateDetectedBlockField(
                                    'estate-create-detected-block',
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



        function getEstateModalLotOverlayScale(
            map,
            polygon
        )
        {
            if (
                !map
                ||
                !polygon
                ||
                !map._loaded
            ) {
                return 0;
            }


            const bounds =
                polygon.getBounds();


            if (
                !bounds
                ||
                !bounds.isValid()
            ) {
                return 0;
            }


            const northWest =
                map.latLngToContainerPoint(
                    bounds.getNorthWest()
                );


            const southEast =
                map.latLngToContainerPoint(
                    bounds.getSouthEast()
                );


            const width =
                Math.abs(
                    southEast.x -
                    northWest.x
                );


            const height =
                Math.abs(
                    southEast.y -
                    northWest.y
                );


            const smallestSide =
                Math.min(
                    width,
                    height
                );


            if (
                !Number.isFinite(
                    smallestSide
                )
                ||
                smallestSide < 8
            ) {
                return 0;
            }


            return Math.min(
                1,
                smallestSide / 100
            );
        }


        function buildEstateModalLotNumberIcon(
            lotNumber,
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
                    42 * safeScale
                );


            const height =
                Math.max(
                    1,
                    16 * safeScale
                );


            const fontSize =
                Math.max(
                    1,
                    10 * safeScale
                );


            return L.divIcon({
                className:
                    'estate-lot-number-label',

                html: `
                    <div
                        class="estate-lot-number-inner"
                        style="
                            width:${width}px;
                            height:${height}px;
                            font-size:${fontSize}px;
                        "
                    >
                        ${escapeEstateHTML(
                            lotNumber
                        )}
                    </div>
                `,

                iconSize: [
                    width,
                    height,
                ],

                iconAnchor: [
                    width,
                    0,
                ],
            });
        }


        function buildEstateModalLotAreaIcon(
            lotArea,
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
                    52 * safeScale
                );


            const height =
                Math.max(
                    1,
                    16 * safeScale
                );


            const fontSize =
                Math.max(
                    1,
                    10 * safeScale
                );


            return L.divIcon({
                className:
                    'estate-lot-area-label',

                html: `
                    <div
                        class="estate-lot-area-inner"
                        style="
                            width:${width}px;
                            height:${height}px;
                            font-size:${fontSize}px;
                        "
                    >
                        ${escapeEstateHTML(
                            Math.round(
                                Number(
                                    lotArea
                                )
                            )
                        )}
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


        function renderModalEstateLots(
            map,
            ignoreLotId = null,
            lotMappedAreaClicks = false
        )
        {
            const existingLots =
                window.estateGISLots || [];


            const overlayItems = [];


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
                        ignoreLotId
                        &&
                        Number(lot.id) ===
                        Number(ignoreLotId)
                    ) {
                        return;
                    }


                    const color =
                        getEstateLotColor(
                            lot
                        );


                    const isInternalRoad =
                        (
                            lot.type || ''
                        )
                            .trim()
                            .toLowerCase()
                        === 'internal road';


                    const mappedLot =
                        L.polygon(
                            lot.geo_coords,
                            {
                                color:
                                    isInternalRoad
                                        ? '#ffffff'
                                        : color,

                                fillColor:
                                    isInternalRoad
                                        ? '#ffffff'
                                        : color,

                                weight: 2,

                                fillOpacity:
                                    isInternalRoad
                                        ? 0.04
                                        : 0.45,

                                dashArray:
                                    isInternalRoad
                                        ? '10 5 2 5'
                                        : null,

                                interactive:
                                    lotMappedAreaClicks,
                                bubblingMouseEvents:
                                    !lotMappedAreaClicks,
                                className:
                                    lotMappedAreaClicks
                                        ? 'estate-map-not-allowed-area'
                                        : '',
                            }
                        ).addTo(
                            map
                        );

                    if (
                        lotMappedAreaClicks
                    ) {
                        stopEstateMappedAreaClicks(
                            mappedLot
                        );
                    }


                    const scale =
                        getEstateModalLotOverlayScale(
                            map,
                            mappedLot
                        );


                    let lotNumberMarker = null;
                    let lotAreaMarker = null;


                    if (
                        lot.lot_number !== null
                        &&
                        lot.lot_number !== undefined
                    ) {
                        lotNumberMarker =
                            L.marker(
                                getEstatePolygonTopRight(
                                    mappedLot
                                ),
                                {
                                    interactive: false,

                                    opacity:
                                        scale > 0
                                            ? 1
                                            : 0,

                                    icon:
                                        buildEstateModalLotNumberIcon(
                                            lot.lot_number,
                                            scale
                                        ),
                                }
                            ).addTo(
                                map
                            );
                    }


                    if (
                        lot.lot_area !== null
                        &&
                        lot.lot_area !== undefined
                    ) {
                        lotAreaMarker =
                            L.marker(
                                getEstatePolygonCenter(
                                    mappedLot
                                ),
                                {
                                    interactive: false,

                                    opacity:
                                        scale > 0
                                            ? 1
                                            : 0,

                                    icon:
                                        buildEstateModalLotAreaIcon(
                                            lot.lot_area,
                                            scale
                                        ),
                                }
                            ).addTo(
                                map
                            );
                    }


                    overlayItems.push({
                        polygon: mappedLot,
                        lotNumberMarker:
                            lotNumberMarker,
                        lotAreaMarker:
                            lotAreaMarker,
                        lotNumber:
                            lot.lot_number,
                        lotArea:
                            lot.lot_area,
                    });
                }
            );


            const updateModalLotDetails =
                function()
                {
                    overlayItems.forEach(
                        function(item)
                        {
                            const scale =
                                getEstateModalLotOverlayScale(
                                    map,
                                    item.polygon
                                );


                            if (
                                item.lotNumberMarker
                            ) {
                                item
                                    .lotNumberMarker
                                    .setLatLng(
                                        getEstatePolygonTopRight(
                                            item.polygon
                                        )
                                    );


                                item
                                    .lotNumberMarker
                                    .setOpacity(
                                        scale > 0
                                            ? 1
                                            : 0
                                    );


                                item
                                    .lotNumberMarker
                                    .setIcon(
                                        buildEstateModalLotNumberIcon(
                                            item.lotNumber,
                                            scale
                                        )
                                    );
                            }


                            if (
                                item.lotAreaMarker
                            ) {
                                item
                                    .lotAreaMarker
                                    .setLatLng(
                                        getEstatePolygonCenter(
                                            item.polygon
                                        )
                                    );


                                item
                                    .lotAreaMarker
                                    .setOpacity(
                                        scale > 0
                                            ? 1
                                            : 0
                                    );


                                item
                                    .lotAreaMarker
                                    .setIcon(
                                        buildEstateModalLotAreaIcon(
                                            item.lotArea,
                                            scale
                                        )
                                    );
                            }
                        }
                    );
                };


            map.on(
                'zoom zoomend resize',
                updateModalLotDetails
            );


            setTimeout(
                updateModalLotDetails,
                50
            );
        }


        function isEstateModalCursorPointAllowed(
            latlng,
            mode,
            ignoreLotId = null,
            ignoreBlockId = null
        )
        {
            if (
                !latlng
                ||
                mode === 'boundary'
            ) {
                return true;
            }

            if (
                !isEstatePointInsideSubdivisionBoundary(
                    latlng
                )
            ) {
                return false;
            }

            if (
                mode === 'block'
            ) {
                return !isEstatePointInsideExistingBlock(
                    latlng,
                    ignoreBlockId
                );
            }

            if (
                mode === 'lot'
            ) {
                if (
                    isEstateInternalRoadDrawing()
                ) {
                    return (
                        !isEstatePointInsideAnyBlock(
                            latlng
                        )
                        &&
                        !isEstatePointInsideExistingLot(
                            latlng,
                            ignoreLotId
                        )
                    );
                }

                return (
                    isEstatePointInsideAnyBlock(
                        latlng
                    )
                    &&
                    !isEstatePointInsideExistingLot(
                        latlng,
                        ignoreLotId
                    )
                );
            }

            return true;
        }


        function enableEstateModalAllowedAreaCursor(
            map,
            mode,
            ignoreLotId = null,
            ignoreBlockId = null
        )
        {
            if (
                !map
                ||
                mode === 'boundary'
            ) {
                return;
            }

            const container =
                map.getContainer();

            if (!container) {
                return;
            }

            const updateCursor =
                function(event)
                {
                    const target =
                        event.target;

                    if (
                        target?.closest?.(
                            '.leaflet-control'
                        )
                    ) {
                        container.classList.remove(
                            'estate-map-cursor-not-allowed'
                        );

                        return;
                    }

                    const latlng =
                        map.mouseEventToLatLng(
                            event
                        );

                    const allowed =
                        isEstateModalCursorPointAllowed(
                            latlng,
                            mode,
                            ignoreLotId,
                            ignoreBlockId
                        );

                    container.classList.toggle(
                        'estate-map-cursor-not-allowed',
                        !allowed
                    );
                };

            container.addEventListener(
                'mousemove',
                updateCursor
            );

            container.addEventListener(
                'mouseleave',
                function()
                {
                    container.classList.remove(
                        'estate-map-cursor-not-allowed'
                    );
                }
            );
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
                                minZoom: 18,
                                maxZoom: 22,
                                attributionControl: false,
                            }
                        );


                    addEstateSelectedBaseLayerToMap(
                        map
                    );

                    const mode =
                        opts.mode || 'lot';

                    enableEstateModalAllowedAreaCursor(
                        map,
                        mode,
                        null,
                        null
                    );

                    let modalBoundary = null;

                    if (
                        Array.isArray(
                            estateSubdivisionBoundary
                        )
                        &&
                        estateSubdivisionBoundary.length >= 3
                    ) {
                        modalBoundary =
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
                    }

                    if (
                        mode !== 'boundary'
                        &&
                        modalBoundary
                    ) {
                        addEstateBoundaryInteractionMask(
                            map
                        );
                    }

                    if (
                        mode !== 'boundary'
                    ) {
                        renderModalEstateBlocks(
                            map,
                            null,
                            mode === 'block'
                        );
                    }

                    if (
                        mode !== 'boundary'
                    ) {
                        renderModalEstateLots(
                            map,
                            null,
                            mode === 'lot'
                        );
                    }

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
                        ignoreBlockId: null,
                        isCreateDrawingMap: true,
                        mode: mode,
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
                                            color:
                                                mode === 'boundary'
                                                    ? '#2563eb'
                                                    : (
                                                        mode === 'block'
                                                            ? '#9333ea'
                                                            : '#f59e0b'
                                                    ),
                                            weight: 4,
                                            fillColor:
                                                mode === 'boundary'
                                                    ? '#3b82f6'
                                                    : (
                                                        mode === 'block'
                                                            ? '#a855f7'
                                                            : '#fbbf24'
                                                    ),
                                            fillOpacity: 0.30,
                                        },
                                    }
                                );

                            restrictEstateDrawerToMappedAreas(
                                entry.drawer,
                                mode,
                                entry.ignoreLotId,
                                entry.ignoreBlockId
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
                                mode !== 'boundary'
                                &&
                                !isPolygonInsideEstateBoundaryCoordinates(
                                    polygon
                                )
                            ) {
                                @this.call(
                                    'showMapNotification',
                                    'Outside Subdivision Boundary',
                                    mode === 'block'
                                        ? 'Block must be fully inside the subdivision boundary.'
                                        : 'Lot must be fully inside the subdivision boundary.',
                                    'danger'
                                );


                                enableDrawer();

                                return;
                            }


                            if (
                                mode === 'block'
                                &&
                                doesPolygonOverlapExistingBlock(
                                    polygon
                                )
                            ) {
                                @this.call(
                                    'showMapNotification',
                                    'Block Area Already Mapped',
                                    'This area overlaps an already mapped block.',
                                    'danger'
                                );


                                enableDrawer();

                                return;
                            }


                            if (mode === 'lot') {
                                const coordinates = getPolygonCoordinates(polygon);
                                const internalRoad = isEstateInternalRoadDrawing();
                                const validBlockPlacement = internalRoad
                                    ? !doesPolygonOverlapExistingBlock(polygon)
                                    : !!findContainingEstateBlock(coordinates);

                                if (!validBlockPlacement) {
                                    @this.call(
                                        'showMapNotification',
                                        internalRoad ? 'Internal Road Inside Block' : 'Lot Outside Block',
                                        internalRoad
                                            ? 'Internal Road must be completely outside every mapped block.'
                                            : 'Lot must be completely inside one mapped block.',
                                        'danger'
                                    );
                                    enableDrawer();
                                    return;
                                }
                            }


                            if (
                                mode === 'lot'
                                &&
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
                                color:
                                    mode === 'boundary'
                                        ? '#2563eb'
                                        : (
                                            mode === 'block'
                                                ? '#9333ea'
                                                : '#f59e0b'
                                        ),
                                weight: 4,
                                fillColor:
                                    mode === 'boundary'
                                        ? '#3b82f6'
                                        : (
                                            mode === 'block'
                                                ? '#a855f7'
                                                : '#fbbf24'
                                        ),
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
                                        mode !== 'boundary'
                                        &&
                                        !isPolygonInsideEstateBoundaryCoordinates(
                                            polygon
                                        )
                                    ) {
                                        @this.call(
                                            'showMapNotification',
                                            'Outside Subdivision Boundary',
                                            mode === 'block'
                                                ? 'Block must be fully inside the subdivision boundary.'
                                                : 'Lot must be fully inside the subdivision boundary.',
                                            'danger'
                                        );
                                    } else if (
                                        mode === 'block'
                                        &&
                                        doesPolygonOverlapExistingBlock(
                                            polygon
                                        )
                                    ) {
                                        @this.call(
                                            'showMapNotification',
                                            'Block Area Already Mapped',
                                            'This area overlaps an already mapped block.',
                                            'danger'
                                        );
                                    } else if (
                                        mode === 'lot'
                                        &&
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


                    if (
                        opts.initialView?.center
                        &&
                        Number.isFinite(
                            opts.initialView?.zoom
                        )
                    ) {
                        map.setView(
                            opts.initialView.center,
                            opts.initialView.zoom,
                            {
                                animate: false,
                            }
                        );
                    } else if (
                        modalBoundary
                    ) {
                        map.fitBounds(
                            modalBoundary.getBounds(),
                            {
                                padding: [
                                    20,
                                    20
                                ],
                            }
                        );
                    } else {
                        map.setView(
                            manhattanResidences,
                            18
                        );
                    }


                    requestAnimationFrame(
                        function()
                        {
                            map.invalidateSize(
                                true
                            );

                            if (
                                opts.initialView?.center
                                &&
                                Number.isFinite(
                                    opts.initialView?.zoom
                                )
                            ) {
                                map.setView(
                                    opts.initialView.center,
                                    opts.initialView.zoom,
                                    {
                                        animate: false,
                                    }
                                );
                            } else if (
                                modalBoundary
                            ) {
                                map.fitBounds(
                                    modalBoundary.getBounds(),
                                    {
                                        padding: [
                                            20,
                                            20
                                        ],
                                    }
                                );
                            } else {
                                map.setView(
                                    manhattanResidences,
                                    18
                                );
                            }

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
                            color:
                                entry.mode === 'boundary'
                                    ? '#2563eb'
                                    : (
                                        entry.mode === 'block'
                                            ? '#9333ea'
                                            : '#f59e0b'
                                    ),
                            weight: 4,
                            fillColor:
                                entry.mode === 'boundary'
                                    ? '#3b82f6'
                                    : (
                                        entry.mode === 'block'
                                            ? '#a855f7'
                                            : '#fbbf24'
                                    ),
                            fillOpacity: 0.30,
                        },
                    }
                );

            restrictEstateDrawerToMappedAreas(
                entry.drawer,
                entry.mode,
                entry.ignoreLotId,
                entry.ignoreBlockId
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


            if (
                !Array.isArray(
                    estateSubdivisionBoundary
                )
                ||
                estateSubdivisionBoundary.length < 3
            ) {
                return false;
            }

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

            const mode =
                opts.mode || 'lot';

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
                                minZoom: 18,
                                maxZoom: 22,
                                attributionControl: false,
                            }
                        );

                    addEstateSelectedBaseLayerToMap(
                        map
                    );

                    enableEstateModalAllowedAreaCursor(
                        map,
                        mode,
                        opts.ignoreLotId,
                        opts.ignoreBlockId
                    );

                    let modalBoundary = null;

                    if (
                        mode !== 'boundary'
                        &&
                        Array.isArray(
                            estateSubdivisionBoundary
                        )
                        &&
                        estateSubdivisionBoundary.length >= 3
                    ) {
                        modalBoundary =
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
                    }

                    if (
                        mode !== 'boundary'
                        &&
                        modalBoundary
                    ) {
                        addEstateBoundaryInteractionMask(
                            map
                        );
                    }

                    renderModalEstateBlocks(
                        map,
                        opts.ignoreBlockId,
                        mode === 'block'
                    );

                    renderModalEstateLots(
                        map,
                        opts.ignoreLotId,
                        mode === 'lot'
                    );

                    const polygon =
                        L.polygon(
                            coords,
                            {
                                color:
                                    mode === 'boundary'
                                        ? '#2563eb'
                                        : (
                                            mode === 'block'
                                                ? '#9333ea'
                                                : '#f59e0b'
                                        ),
                                weight: 4,
                                fillColor:
                                    mode === 'boundary'
                                        ? '#3b82f6'
                                        : (
                                            mode === 'block'
                                                ? '#a855f7'
                                                : '#fbbf24'
                                        ),
                                fillOpacity: 0.30,
                            }
                        ).addTo(map);

                    polygon.editing.enable();

                    keepEstateEditedPolygonInAllowedArea(
                        polygon,
                        mode,
                        opts.ignoreLotId,
                        opts.ignoreBlockId,
                        containerId
                    );

                    if (
                        opts.initialView?.center
                        &&
                        Number.isFinite(
                            opts.initialView?.zoom
                        )
                    ) {
                        map.setView(
                            opts.initialView.center,
                            opts.initialView.zoom,
                            {
                                animate: false,
                            }
                        );
                    } else {
                        map.fitBounds(
                            (
                                modalBoundary
                                    ? modalBoundary
                                    : polygon
                            ).getBounds(),
                            {
                                padding: [
                                    20,
                                    20
                                ],
                            }
                        );
                    }

                    const syncCoordinates =
                        function()
                        {
                            if (
                                mode !== 'boundary'
                                &&
                                !isPolygonInsideEstateBoundaryCoordinates(
                                    polygon
                                )
                            ) {
                                @this.call(
                                    'showMapNotification',
                                    'Outside Subdivision Boundary',
                                    mode === 'block'
                                        ? 'Block must remain inside the subdivision boundary.'
                                        : 'Lot must remain inside the subdivision boundary.',
                                    'danger'
                                );
                            } else if (
                                mode === 'block'
                                &&
                                doesPolygonOverlapExistingBlock(
                                    polygon,
                                    opts.ignoreBlockId
                                )
                            ) {
                                @this.call(
                                    'showMapNotification',
                                    'Block Area Already Mapped',
                                    'This area overlaps an already mapped block.',
                                    'danger'
                                );
                            } else if (
                                mode === 'lot'
                                &&
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
                            JSON.parse(
                                JSON.stringify(
                                    coords
                                )
                            ),

                        onChange: opts.onChange,
                        ignoreLotId: opts.ignoreLotId,
                        ignoreBlockId: opts.ignoreBlockId,
                        mode: mode,
                        drawer: null,
                        redrawCreatedHandler: null,
                    };

                    requestAnimationFrame(
                        function()
                        {
                            map.invalidateSize(
                                true
                            );

                            map.fitBounds(
                                (
                                    modalBoundary
                                        ? modalBoundary
                                        : polygon
                                ).getBounds(),
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
                                (
                                    modalBoundary
                                        ? modalBoundary
                                        : polygon
                                ).getBounds(),
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
                if (
                    entry.redrawCreatedHandler
                ) {
                    entry.map.off(
                        L.Draw.Event.CREATED,
                        entry.redrawCreatedHandler
                    );
                }

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


        function restoreEstateModalMiniMapOriginalPolygon(
            containerId
        )
        {
            const entry =
                window.estateModalMiniMaps[
                    containerId
                ];

            if (
                !entry ||
                !entry.map ||
                !Array.isArray(entry.initialCoords) ||
                entry.initialCoords.length < 3
            ) {
                return;
            }


            const map =
                entry.map;


            /*
            |--------------------------------------------------------------------------
            | COPY ORIGINAL COORDINATES
            |--------------------------------------------------------------------------
            */

            const originalCoordinates =
                JSON.parse(
                    JSON.stringify(
                        entry.initialCoords
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | REMOVE OLD EDIT POLYGON + ITS EDIT HANDLES
            |--------------------------------------------------------------------------
            */

            if (entry.polygon) {

                entry.polygon.off();

                if (
                    entry.polygon.editing &&
                    entry.polygon.editing.enabled()
                ) {
                    entry.polygon.editing.disable();
                }


                /*
                |--------------------------------------------------------------------------
                | Explicitly remove Leaflet Draw marker group
                |--------------------------------------------------------------------------
                */

                if (
                    entry.polygon.editing &&
                    entry.polygon.editing._markerGroup
                ) {
                    try {
                        entry.polygon.editing
                            ._markerGroup
                            .clearLayers();

                        map.removeLayer(
                            entry.polygon.editing
                                ._markerGroup
                        );
                    } catch (error) {
                        console.log(error);
                    }
                }


                map.removeLayer(
                    entry.polygon
                );
            }


            /*
            |--------------------------------------------------------------------------
            | DETERMINE CORRECT STYLE
            |--------------------------------------------------------------------------
            */

            const mode =
                entry.mode || 'lot';


            const polygonStyle = {
                color:
                    mode === 'boundary'
                        ? '#2563eb'
                        : (
                            mode === 'block'
                                ? '#9333ea'
                                : '#f59e0b'
                        ),

                weight: 4,

                fillColor:
                    mode === 'boundary'
                        ? '#3b82f6'
                        : (
                            mode === 'block'
                                ? '#a855f7'
                                : '#fbbf24'
                        ),

                fillOpacity: 0.30,
            };


            /*
            |--------------------------------------------------------------------------
            | CREATE A BRAND NEW POLYGON
            |--------------------------------------------------------------------------
            */

            const polygon =
                L.polygon(
                    originalCoordinates,
                    polygonStyle
                ).addTo(
                    map
                );


            /*
            |--------------------------------------------------------------------------
            | ENABLE EDITING
            |--------------------------------------------------------------------------
            */

            polygon.editing.enable();

            keepEstateEditedPolygonInAllowedArea(
                polygon,
                mode,
                entry.ignoreLotId,
                entry.ignoreBlockId,
                containerId
            );


            /*
            |--------------------------------------------------------------------------
            | RECREATE EDIT HANDLER
            |--------------------------------------------------------------------------
            */

            const syncCoordinates =
                function()
                {
                    if (
                        mode !== 'boundary' &&
                        !isPolygonInsideEstateBoundaryCoordinates(
                            polygon
                        )
                    ) {
                        @this.call(
                            'showMapNotification',
                            'Outside Subdivision Boundary',
                            mode === 'block'
                                ? 'Block must remain inside the subdivision boundary.'
                                : 'Lot must remain inside the subdivision boundary.',
                            'danger'
                        );
                    }

                    else if (
                        mode === 'block' &&
                        doesPolygonOverlapExistingBlock(
                            polygon,
                            entry.ignoreBlockId
                        )
                    ) {
                        @this.call(
                            'showMapNotification',
                            'Block Area Already Mapped',
                            'This area overlaps an already mapped block.',
                            'danger'
                        );
                    }

                    else if (
                        mode === 'lot' &&
                        doesPolygonOverlapExistingLot(
                            polygon,
                            entry.ignoreLotId
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


            /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | Replace old polygon reference with new polygon
            |--------------------------------------------------------------------------
            */

            entry.polygon =
                polygon;


            /*
            |--------------------------------------------------------------------------
            | FIT MAP TO RESTORED POLYGON
            |--------------------------------------------------------------------------
            */

            map.invalidateSize(
                true
            );


            map.fitBounds(
                polygon.getBounds(),
                {
                    padding: [
                        30,
                        30
                    ],
                }
            );


            /*
            |--------------------------------------------------------------------------
            | SYNC ORIGINAL COORDINATES BACK TO LIVEWIRE
            |--------------------------------------------------------------------------
            */

            if (
                typeof entry.onChange ===
                'function'
            ) {
                entry.onChange(
                    originalCoordinates
                );
            }
        }

        function enableEstateEditRedrawDrawer(
            containerId
        )
        {
            const entry =
                window.estateModalMiniMaps[
                    containerId
                ];

            if (
                !entry
                ||
                !entry.map
            ) {
                return;
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

            const mode =
                entry.mode || 'lot';

            entry.drawer =
                new L.Draw.Polygon(
                    entry.map,
                    {
                        allowIntersection: false,
                        showArea: true,

                        shapeOptions: {
                            color:
                                mode === 'boundary'
                                    ? '#2563eb'
                                    : (
                                        mode === 'block'
                                            ? '#9333ea'
                                            : '#f59e0b'
                                    ),

                            weight: 4,

                            fillColor:
                                mode === 'boundary'
                                    ? '#3b82f6'
                                    : (
                                        mode === 'block'
                                            ? '#a855f7'
                                            : '#fbbf24'
                                    ),

                            fillOpacity: 0.30,
                        },
                    }
                );

            restrictEstateDrawerToMappedAreas(
                entry.drawer,
                mode,
                entry.ignoreLotId,
                entry.ignoreBlockId
            );

            entry.drawer.enable();
        }


        function attachEstateRedrawnEditPolygon(
            containerId,
            polygon
        )
        {
            const entry =
                window.estateModalMiniMaps[
                    containerId
                ];

            if (
                !entry
                ||
                !entry.map
                ||
                !polygon
            ) {
                return false;
            }

            const mode =
                entry.mode || 'lot';

            if (
                mode !== 'boundary'
                &&
                !isPolygonInsideEstateBoundaryCoordinates(
                    polygon
                )
            ) {
                @this.call(
                    'showMapNotification',
                    'Outside Subdivision Boundary',
                    mode === 'block'
                        ? 'Block must be fully inside the subdivision boundary.'
                        : 'Lot must be fully inside the subdivision boundary.',
                    'danger'
                );

                return false;
            }

            if (
                mode === 'block'
                &&
                doesPolygonOverlapExistingBlock(
                    polygon,
                    entry.ignoreBlockId
                )
            ) {
                @this.call(
                    'showMapNotification',
                    'Block Area Already Mapped',
                    'This area overlaps an already mapped block.',
                    'danger'
                );

                return false;
            }

            if (
                mode === 'lot'
            ) {
                const coordinates =
                    getPolygonCoordinates(
                        polygon
                    );

                const internalRoad =
                    isEstateInternalRoadDrawing();

                const validBlockPlacement =
                    internalRoad
                        ? !doesPolygonOverlapExistingBlock(
                            polygon
                        )
                        : !!findContainingEstateBlock(
                            coordinates
                        );

                if (
                    !validBlockPlacement
                ) {
                    @this.call(
                        'showMapNotification',
                        internalRoad
                            ? 'Internal Road Inside Block'
                            : 'Lot Outside Block',
                        internalRoad
                            ? 'Internal Road must be completely outside every mapped block.'
                            : 'Lot must be completely inside one mapped block.',
                        'danger'
                    );

                    return false;
                }

                if (
                    doesPolygonOverlapExistingLot(
                        polygon,
                        entry.ignoreLotId
                    )
                ) {
                    @this.call(
                        'showMapNotification',
                        'Lot Area Already Mapped',
                        'This area overlaps an already mapped lot.',
                        'danger'
                    );

                    return false;
                }
            }

            polygon.setStyle({
                color:
                    mode === 'boundary'
                        ? '#2563eb'
                        : (
                            mode === 'block'
                                ? '#9333ea'
                                : '#f59e0b'
                        ),

                weight: 4,

                fillColor:
                    mode === 'boundary'
                        ? '#3b82f6'
                        : (
                            mode === 'block'
                                ? '#a855f7'
                                : '#fbbf24'
                        ),

                fillOpacity: 0.30,
            });

            polygon.addTo(
                entry.map
            );

            polygon.editing.enable();

            keepEstateEditedPolygonInAllowedArea(
                polygon,
                mode,
                entry.ignoreLotId,
                entry.ignoreBlockId,
                containerId
            );

            const syncCoordinates =
                function()
                {
                    if (
                        mode !== 'boundary'
                        &&
                        !isPolygonInsideEstateBoundaryCoordinates(
                            polygon
                        )
                    ) {
                        @this.call(
                            'showMapNotification',
                            'Outside Subdivision Boundary',
                            mode === 'block'
                                ? 'Block must remain inside the subdivision boundary.'
                                : 'Lot must remain inside the subdivision boundary.',
                            'danger'
                        );
                    } else if (
                        mode === 'block'
                        &&
                        doesPolygonOverlapExistingBlock(
                            polygon,
                            entry.ignoreBlockId
                        )
                    ) {
                        @this.call(
                            'showMapNotification',
                            'Block Area Already Mapped',
                            'This area overlaps an already mapped block.',
                            'danger'
                        );
                    } else if (
                        mode === 'lot'
                        &&
                        doesPolygonOverlapExistingLot(
                            polygon,
                            entry.ignoreLotId
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

            entry.polygon =
                polygon;

            entry.drawer =
                null;

            syncCoordinates();

            return true;
        }


        function resetEstateModalMiniMap(
            containerId
        )
        {
            const entry =
                window.estateModalMiniMaps[
                    containerId
                ];

            if (
                !entry
                ||
                !entry.map
            ) {
                return;
            }

            const map =
                entry.map;

            if (
                entry.drawer
            ) {
                try {
                    entry.drawer.disable();
                } catch (error) {
                    //
                }

                entry.drawer =
                    null;
            }

            if (
                entry.polygon
            ) {
                entry.polygon.off();

                if (
                    entry.polygon.editing
                    &&
                    entry.polygon.editing.enabled()
                ) {
                    entry.polygon.editing.disable();
                }

                if (
                    entry.polygon.editing
                    &&
                    entry.polygon.editing._markerGroup
                ) {
                    try {
                        entry.polygon.editing
                            ._markerGroup
                            .clearLayers();

                        map.removeLayer(
                            entry.polygon.editing
                                ._markerGroup
                        );
                    } catch (error) {
                        //
                    }
                }

                if (
                    map.hasLayer(
                        entry.polygon
                    )
                ) {
                    map.removeLayer(
                        entry.polygon
                    );
                }
            }

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
                entry.redrawCreatedHandler
            ) {
                map.off(
                    L.Draw.Event.CREATED,
                    entry.redrawCreatedHandler
                );
            }

            const createdHandler =
                function(event)
                {
                    const currentEntry =
                        window.estateModalMiniMaps[
                            containerId
                        ];

                    if (
                        !currentEntry
                        ||
                        !currentEntry.drawer
                    ) {
                        return;
                    }

                    const polygon =
                        event.layer;

                    const accepted =
                        attachEstateRedrawnEditPolygon(
                            containerId,
                            polygon
                        );

                    if (
                        accepted
                    ) {
                        map.off(
                            L.Draw.Event.CREATED,
                            createdHandler
                        );

                        currentEntry.redrawCreatedHandler =
                            null;

                        return;
                    }

                    setTimeout(
                        function()
                        {
                            enableEstateEditRedrawDrawer(
                                containerId
                            );
                        },
                        0
                    );
                };

            entry.redrawCreatedHandler =
                createdHandler;

            map.on(
                L.Draw.Event.CREATED,
                createdHandler
            );

            enableEstateEditRedrawDrawer(
                containerId
            );
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


            map.invalidateSize(
                true
            );

            if (
                Array.isArray(
                    estateSubdivisionBoundary
                )
                &&
                estateSubdivisionBoundary.length >= 3
            ) {
                const boundaryBounds =
                    L.latLngBounds(
                        estateSubdivisionBoundary
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
            } else {
                map.setView(
                    manhattanResidences,
                    18
                );
            }
        }

        window.addEventListener(
            'gis-edit-boundary',
            function(event)
            {
                const coords =
                    event.detail.coords;

                $openModal(
                    'edit-gis-boundary-modal'
                );

                initEstateModalMiniMap(
                    'estate-edit-boundary-mini-map',
                    coords,
                    {
                        mode: 'boundary',

                        onChange:
                            function(coordinates)
                            {
                                updateEstateCoordinateField(
                                    'estate-edit-boundary-coords',
                                    coordinates
                                );

                                @this.set(
                                    'editBoundaryGeoCoords',
                                    coordinates
                                );
                            },
                    }
                );
            }
        );


        window.addEventListener(
            'gis-edit-block',
            function(event)
            {
                const coords =
                    event.detail.coords;

                const blockId =
                    event.detail.blockId;

                $openModal(
                    'edit-gis-block-modal'
                );

                initEstateModalMiniMap(
                    'estate-edit-block-mini-map',
                    coords,
                    {
                        mode: 'block',
                        ignoreBlockId: blockId,

                        onChange:
                            function(coordinates)
                            {
                                updateEstateCoordinateField(
                                    'estate-edit-block-coords',
                                    coordinates
                                );

                                @this.set(
                                    'editBlockGeoCoords',
                                    coordinates
                                );
                            },
                    }
                );
            }
        );


        window.addEventListener(
            'gis-edit-lot',
            function(event)
            {
                const coords =
                    event.detail.coords;

                const lotId =
                    event.detail.lotId;

                const editedLot =
                    (window.estateGISLots || []).find(
                        lot =>
                            Number(lot.id) ===
                            Number(lotId)
                    );

                const editedLotType =
                    editedLot?.type ?? null;


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


                window.estateActiveLotTypeInputId =
                    'estate-edit-lot-type';

                const mainMapState =
                    getEstateMainMapState();

                initEstateModalMiniMap(
                    'estate-edit-mini-map',
                    coords,
                    {
                        mode: 'lot',

                        ignoreLotId:
                            lotId,

                        initialView:
                            mainMapState,

                        onChange:
                            function(coordinates)
                            {
                                updateEstateCoordinateField(
                                    'estate-edit-lot-coords',
                                    coordinates
                                );

                                @this.set(
                                    'editGeoCoords',
                                    coordinates
                                );

                                updateDetectedBlockField(
                                    'estate-edit-detected-block',
                                    coordinates,
                                    editedLotType
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


            if (
                !window.estateBoundaryLayer
            ) {
                return false;
            }

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


        function renderModalEstateBlocks(
            map,
            ignoreBlockId = null,
            blockMappedAreaClicks = false
        )
        {
            const blocks =
                window.estateGISBlocks || [];

            blocks.forEach(
                function(block)
                {
                    if (
                        ignoreBlockId
                        &&
                        Number(block.id) ===
                        Number(ignoreBlockId)
                    ) {
                        return;
                    }

                    if (
                        !Array.isArray(
                            block.geo_coords
                        )
                        ||
                        block.geo_coords.length < 3
                    ) {
                        return;
                    }

                    const mappedBlockPolygon =
                        L.polygon(
                            block.geo_coords,
                            {
                                color: '#9333ea',
                                weight: 2,
                                dashArray: '7 5',
                                fillColor: '#a855f7',
                                fillOpacity: 0.03,
                                interactive:
                                    blockMappedAreaClicks,
                                bubblingMouseEvents:
                                    !blockMappedAreaClicks,
                                className:
                                    blockMappedAreaClicks
                                        ? 'estate-map-not-allowed-area'
                                        : '',
                            }
                        );
                    
                    mappedBlockPolygon.bindTooltip(
                        block.name ?? 'Block',
                        {
                            direction: 'center',
                            permanent: true,
                            className: 'estate-block-label estate-block-label-modal',
                            opacity: 1,
                        }
                    );

                    mappedBlockPolygon.on(
                        'add',
                        function()
                        {
                            const bottomCenter =
                                getEstateBlockBottomCenter(
                                    mappedBlockPolygon
                                );

                            if (!bottomCenter) {
                                return;
                            }

                            mappedBlockPolygon
                                .getTooltip()
                                ?.setLatLng(
                                    bottomCenter
                                );
                        }
                    );

                    if (
                        blockMappedAreaClicks
                    ) {
                        stopEstateMappedAreaClicks(
                            mappedBlockPolygon
                        );
                    }

                    mappedBlockPolygon.addTo(
                        map
                    );
                }
            );
        }


        function doesPolygonOverlapExistingBlock(
            polygon,
            ignoreBlockId = null
        )
        {
            const newPoints =
                polygon
                    .getLatLngs()[0];

            const blocks =
                window.estateGISBlocks || [];

            for (
                const block of blocks
            ) {
                if (
                    ignoreBlockId
                    &&
                    Number(block.id) ===
                    Number(ignoreBlockId)
                ) {
                    continue;
                }

                if (
                    !Array.isArray(
                        block.geo_coords
                    )
                    ||
                    block.geo_coords.length < 3
                ) {
                    continue;
                }

                const existing =
                    block.geo_coords.map(
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


        function findContainingEstateBlock(
            coordinates
        )
        {
            if (
                !Array.isArray(coordinates)
                ||
                coordinates.length < 3
            ) {
                return null;
            }

            const polygonPoints =
                coordinates.map(
                    point =>
                        L.latLng(
                            point[0],
                            point[1]
                        )
                );

            const blocks =
                window.estateGISBlocks || [];

            for (
                const block of blocks
            ) {
                if (
                    !Array.isArray(
                        block.geo_coords
                    )
                    ||
                    block.geo_coords.length < 3
                ) {
                    continue;
                }

                const blockPoints =
                    block.geo_coords.map(
                        point =>
                            L.latLng(
                                point[0],
                                point[1]
                            )
                    );

                const inside =
                    polygonPoints.every(
                        point =>
                            pointInsidePolygon(
                                point,
                                blockPoints
                            )
                    );

                if (inside) {
                    return block;
                }
            }

            return null;
        }


        function updateDetectedBlockField(
            inputId,
            coordinates,
            lotType = null
        )
        {
            const input =
                document.getElementById(
                    inputId
                );

            if (lotType === 'Internal Road') {
                if (input) {
                    input.value = '';
                }

                return;
            }

            if (!input) {
                return;
            }

            // Nothing has been drawn yet.
            if (
                !Array.isArray(coordinates) ||
                coordinates.length < 3
            ) {
                input.value =
                    'Draw the lot boundary first';

                return;
            }

            const block =
                findContainingEstateBlock(
                    coordinates
                );

            if (block) {
                input.value =
                    block.name;

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | LOT IS NOT FULLY INSIDE A BLOCK
            |--------------------------------------------------------------------------
            */

            input.value =
                'Adjust lot boundary';

            // @this.call(
            //     'showMapNotification',
            //     'Lot Outside Block',
            //     'Adjust the lot boundary points so the entire lot is inside a block.',
            //     'danger'
            // );
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

        function toggleEstateMapFullscreen(
            containerId,
            button
        ) {
            const container =
                document.getElementById(
                    containerId
                );

            if (!container) {
                return;
            }

            const mapElement =
                container.querySelector(
                    '.leaflet-container'
                );

            let map = null;

            if (mapElement) {
                const entries =
                    window.estateModalMiniMaps || {};

                for (
                    const entry of Object.values(entries)
                ) {
                    if (
                        entry?.map
                        &&
                        entry.map.getContainer() ===
                            mapElement
                    ) {
                        map = entry.map;

                        break;
                    }
                }
            }

            if (!document.fullscreenElement) {

                container
                    .requestFullscreen()
                    .then(
                        function()
                        {
                            if (button) {
                                button.innerHTML = '⛶';
                            }

                            setTimeout(
                                function()
                                {
                                    if (!map) {
                                        return;
                                    }

                                    map.invalidateSize(
                                        true
                                    );

                                    map.setView(
                                        map.getCenter(),
                                        map.getZoom(),
                                        {
                                            animate: false,
                                        }
                                    );
                                },
                                150
                            );

                            setTimeout(
                                function()
                                {
                                    map?.invalidateSize(
                                        true
                                    );
                                },
                                400
                            );
                        }
                    )
                    .catch(
                        function(error)
                        {
                            console.error(
                                'Fullscreen error:',
                                error
                            );
                        }
                    );

                return;
            }

            document
                .exitFullscreen()
                .then(
                    function()
                    {
                        if (button) {
                            button.innerHTML = '⛶';
                        }

                        setTimeout(
                            function()
                            {
                                map?.invalidateSize(
                                    true
                                );
                            },
                            150
                        );
                    }
                );
        }

        document.addEventListener(
            'fullscreenchange',
            function () {

                const fullscreenElement =
                    document.fullscreenElement;

                if (!fullscreenElement) {
                    return;
                }

                const mapContainer =
                    fullscreenElement.querySelector(
                        '.leaflet-container'
                    );

                if (!mapContainer) {
                    return;
                }

                const entries = window.estateModalMiniMaps || {};

                Object.values(entries).forEach(
                    function (entry) {

                        if (entry?.map && entry.map.getContainer() === mapContainer) {

                            setTimeout(() => {

                                entry.map.invalidateSize(
                                    true
                                );

                            }, 100);

                            setTimeout(() => {

                                entry.map.invalidateSize(
                                    true
                                );

                            }, 500);
                        }
                    }
                );
            }
        );

        function buildEstateBaseLayers()
        {
            const satellite = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
                {
                    maxZoom: 22,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google',
                }
            );

            const hybrid = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
                {
                    maxZoom: 22,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google',
                }
            );

            const roadmap = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
                {
                    maxZoom: 22,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google',
                }
            );

            const terrain = L.tileLayer(
                'https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}',
                {
                    maxZoom: 22,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google',
                }
            );

            return {
                layers: {
                    'Satellite': satellite,
                    'Hybrid': hybrid,
                    'Roadmap': roadmap,
                    'Terrain': terrain,
                },
                default: satellite,
            };
        }

        function getEstateMainMapState()
        {
            const map =
                window.estateLeafletMap;

            if (!map) {
                return null;
            }

            return {
                center: map.getCenter(),
                zoom: map.getZoom(),
                baseLayer: window.estateSelectedBaseLayer || 'Satellite',
            };
        }

        function addEstateSelectedBaseLayerToMap(
            map
        )
        {
            const baseLayers =
                buildEstateBaseLayers();

            const selected =
                window.estateSelectedBaseLayer
                || 'Satellite';

            const layer =
                baseLayers.layers[selected]
                || baseLayers.default;

            layer.addTo(
                map
            );

            return baseLayers;
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
                                const isInternalRoad =
                                    (
                                        layer.estateLotType
                                        ??
                                        layer.estateLot?.type
                                        ??
                                        ''
                                    )
                                        .trim()
                                        .toLowerCase()
                                    === 'internal road';

                                layer.setStyle({
                                    opacity: 1,
                                    fillOpacity:
                                        isInternalRoad
                                            ? 0.04
                                            : 0.50,
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

                destroyEstateModalMiniMap(
                    'estate-create-boundary-mini-map'
                );

                destroyEstateModalMiniMap(
                    'estate-edit-boundary-mini-map'
                );

                destroyEstateModalMiniMap(
                    'estate-create-block-mini-map'
                );

                destroyEstateModalMiniMap(
                    'estate-edit-block-mini-map'
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
            'gis-block-deleted',
            function()
            {
                hideEstateCustomTooltip();

                setTimeout(
                    function()
                    {
                        window.location.reload();
                    },
                    150
                );
            }
        );


        window.addEventListener(
            'gis-boundary-deleted',
            function()
            {
                hideEstateCustomTooltip();

                setTimeout(
                    function()
                    {
                        window.location.reload();
                    },
                    150
                );
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
