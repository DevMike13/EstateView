@push('styles')

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/pannellum@2.5.7/build/pannellum.css"
    >

    <style>
        #client-estate-leaflet-map {
            width: 100%;
            height: 700px;
        }

        .client-estate-lot-tooltip {
            background: rgba(17, 24, 39, .92);
            color: white;
            border: none;
            border-radius: 6px;
            box-shadow: none;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 7px;
        }

        .client-estate-lot-tooltip::before {
            display: none;
        }

        .client-estate-sold-label {
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            color: #111827 !important;
            font-weight: 800 !important;
            font-size: 11px !important;
        }

        .client-estate-sold-label::before {
            display: none !important;
        }

        .client-estate-construction-icon {
            background: transparent !important;
            border: 0 !important;
        }

        .client-estate-construction-badge {
            border-radius: 9999px;
            background: rgba(245, 158, 11, .95);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .18);
            box-sizing: border-box;
            flex-shrink: 0;
        }

        .client-estate-custom-tooltip {
            position: absolute;
            z-index: 1000;
            width: 320px;
            max-width: calc(100% - 20px);
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, .22);
            overflow: visible;
            display: none;
        }

        .client-estate-custom-tooltip.is-visible {
            display: block;
        }

        .client-estate-custom-tooltip-arrow {
            position: absolute;
            width: 0;
            height: 0;
            pointer-events: none;
            z-index: 1001;
            transform: translateX(-50%);
        }

        .client-estate-popup {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            font-family: inherit;
        }

        .client-estate-popup-panorama {
            width: 100%;
            height: 160px;
            background: #f3f4f6;
            overflow: hidden;
        }

        .client-estate-popup-panorama .pnlm-container,
        .client-estate-popup-panorama canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .client-estate-popup-close {
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

        .client-estate-popup-close:hover {
            background: white;
        }

        .client-estate-popup-body {
            padding: 12px 14px 14px;
        }

        .client-estate-popup-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }

        .client-estate-popup-title {
            font-size: 16px;
            line-height: 1.25;
            font-weight: 700;
            color: #111827;
            word-break: break-word;
        }

        .client-estate-popup-construction {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 5px;
            color: #ea580c;
            font-size: 11px;
            font-weight: 600;
        }

        .client-estate-popup-construction svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .client-estate-popup-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px;
            margin-top: 5px;
            color: #6b7280;
            font-size: 12px;
            font-style: italic;
        }

        .client-estate-popup-meta-dot {
            width: 5px;
            height: 5px;
            border-radius: 9999px;
            background: #6b7280;
            flex-shrink: 0;
        }

        .client-estate-popup-area {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-style: normal;
        }

        .client-estate-popup-area svg {
            width: 15px;
            height: 15px;
        }

        .client-estate-popup-status {
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

        .client-estate-popup-status.sold {
            background: #fecaca;
            color: #991b1b;
        }

        .client-estate-popup-status.reserved {
            background: #bbf7d0;
            color: #166534;
        }

        .client-estate-popup-extra {
            margin-top: 14px;
            padding: 12px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
        }

        .client-estate-popup-extra-section + .client-estate-popup-extra-section {
            margin-top: 14px;
        }

        .client-estate-popup-section-label {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .client-estate-popup-section-label span {
            flex-shrink: 0;
            padding: 2px 8px;
            border-radius: 9999px;
            background: #bfdbfe;
            color: #1e40af;
            font-size: 10px;
            line-height: 1.4;
            text-transform: capitalize;
        }

        .client-estate-popup-section-label hr {
            width: 100%;
            border: 0;
            border-top: 1px solid #3b82f6;
        }

        .client-estate-popup-person {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
            padding: 8px;
            border-radius: 8px;
            background: #f3f4f6;
        }

        .client-estate-popup-person img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 1px solid #d1d5db;
            flex-shrink: 0;
        }

        .client-estate-popup-person.user img {
            border-radius: 9999px;
        }

        .client-estate-popup-person.model img {
            border-radius: 6px;
        }

        .client-estate-popup-person strong {
            color: #1f2937;
            font-size: 12px;
            font-weight: 600;
            word-break: break-word;
        }

        .client-estate-popup-price {
            margin-top: 12px;
            color: #1f2937;
            font-size: 17px;
            font-weight: 600;
            text-align: right;
        }

        .client-estate-popup-reserve {
            margin-top: 14px;
        }

        .client-estate-popup-reserve button {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: background .2s ease;
        }

        .client-estate-popup-reserve button:hover {
            background: #1d4ed8;
        }

        .client-estate-popup-reserve button svg {
            width: 17px;
            height: 17px;
        }

        /* =========================================================
           CLIENT CARD - REFERENCE UI
           ========================================================= */

        .client-estate-custom-tooltip {
            width: 380px;
            max-width: calc(100% - 20px);
            border-radius: 16px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, .18);
            border: 1px solid #e5e7eb;
        }

        .client-estate-popup {
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
        }

        .client-estate-popup-panorama {
            height: 180px;
            background: #e5e7eb;
        }

        .client-estate-popup-close {
            top: 10px;
            right: 10px;
            width: 34px;
            height: 34px;
            background: rgba(255, 255, 255, .96);
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .12);
        }

        .client-estate-popup-body {
            padding: 18px 16px 16px;
        }

        .client-estate-popup-top-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .client-estate-popup-title-wrap {
            min-width: 0;
            flex: 1;
        }

        .client-estate-popup-title {
            font-size: 18px;
            line-height: 1.2;
            font-weight: 800;
            color: #111827;
            margin: 0;
        }

        .client-estate-popup-meta {
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
            color: #8b95a7;
            font-size: 13px;
            line-height: 1.3;
        }

        .client-estate-popup-meta-type {
            font-style: italic;
        }

        .client-estate-popup-meta-dot {
            width: 5px;
            height: 5px;
            border-radius: 9999px;
            background: #9ca3af;
            flex-shrink: 0;
        }

        .client-estate-popup-area {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-style: normal;
        }

        .client-estate-popup-area svg {
            width: 15px;
            height: 15px;
        }

        .client-estate-popup-status {
            flex-shrink: 0;
            padding: 9px 18px;
            border-radius: 9999px;
            font-size: 13px;
            line-height: 1;
            font-weight: 500;
            text-transform: capitalize;
            background: #bbf7d0;
            color: #166534;
        }

        /* .client-estate-popup-status.reserved {
            background: #fef3c7;
            color: #92400e;
        } */

        .client-estate-popup-status.sold {
            background: #fecaca;
            color: #991b1b;
        }

        .client-estate-popup-construction {
            margin-top: 7px;
            margin-bottom: 0;
            font-size: 11px;
        }

        .client-estate-popup-extra {
            margin-top: 18px;
            padding: 12px;
            border: 2px dashed #e5e7eb;
            border-radius: 10px;
            min-height: 28px;
        }

        .client-estate-popup-extra.empty {
            min-height: 28px;
            padding: 0;
        }

        .client-estate-popup-price {
            margin-top: 16px;
            font-size: 20px;
            line-height: 1.2;
            font-weight: 800;
            color: #1f2937;
            text-align: right;
        }

        .client-estate-popup-bottom {
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .client-estate-popup-reserve {
            margin-top: 0;
        }

        .client-estate-popup-reserve button {
            width: auto;
            min-width: 138px;
            padding: 10px 16px;
            border-radius: 8px;
            background: #2563eb;
            box-shadow: 0 4px 10px rgba(37, 99, 235, .25);
            font-size: 13px;
            font-weight: 600;
        }

        .client-estate-popup-reserve button:hover {
            background: #1d4ed8;
        }

        .client-estate-popup-section-label span {
            font-size: 10px;
        }

        .client-estate-popup-person {
            margin-top: 8px;
        }

        @media (max-width: 640px) {
            .client-estate-custom-tooltip {
                width: min(92vw, 380px);
            }

            .client-estate-popup-panorama {
                height: 150px;
            }

            .client-estate-popup-body {
                padding: 15px 14px 14px;
            }

            .client-estate-popup-title {
                font-size: 16px;
            }

            .client-estate-popup-status {
                padding: 8px 14px;
                font-size: 12px;
            }

            .client-estate-popup-price {
                color: #1f2937;
                font-size: 17px;
                font-weight: 600;
            }
        }

    </style>

@endpush


<div wire:poll.5s="refreshLots">

    @php
        $lots = $lots ?? [];

        $typeColors = $typeColors ?? [];

        $lotCounts = $lotCounts ?? [];


        $currentUserId = auth()->id();

        $leafletLots = collect($lots)
            ->map(function ($lot) use ($currentUserId) {

                $belongsToCurrentUser =
                    $currentUserId &&
                    $lot->user_id &&
                    (int) $lot->user_id ===
                    (int) $currentUserId;

                return [
                    'id' => $lot->id,

                    'name' => $lot->name,

                    'geo_coords' => $lot->geo_coords,

                    'type' => $lot->type,

                    'status' => $lot->status,

                    'price' => $lot->price,

                    'lot_area' => $lot->lot_area,

                    'image' => $lot->image
                        ? asset(
                            'storage/' .
                            $lot->image
                        )
                        : null,

                    'is_under_construction' =>
                        (bool) $lot->is_under_construction,

                    'user' =>
                        $belongsToCurrentUser &&
                        $lot->user
                            ? [
                                'id' =>
                                    $lot->user->id,

                                'name' =>
                                    $lot->user->name,

                                'picture' =>
                                    $lot->user->profile_picture
                                        ? asset(
                                            $lot->user->profile_picture
                                        )
                                        : null,
                            ]
                            : null,

                    'belongs_to_current_user' =>
                        (bool) $belongsToCurrentUser,

                    'house_model' =>
                        $lot->houseModel
                            ? [
                                'id' =>
                                    $lot->houseModel->id,

                                'name' =>
                                    $lot->houseModel->model_name,

                                'image' =>
                                    $lot->houseModel->image
                                        ? asset(
                                            'storage/' .
                                            $lot->houseModel->image
                                        )
                                        : null,
                            ]
                            : null,
                ];
            })
            ->values()
            ->toArray();
    @endphp


    <div
        class="bg-white rounded-2xl shadow-md p-5 border border-gray-100 mt-10"
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
                    Explore available properties.
                </p>
            </div>         
        </div>

        {{-- MAP LEGEND --}}
        @if(!empty($typeColors))
            <div class="mb-6">

                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-gray-800">
                        Map Legend
                    </h3>

                    <p class="text-xs text-gray-500">
                        Colors indicate the type and status of each mapped lot.
                    </p>
                </div>


                <div
                    class="
                        grid
                        grid-cols-2
                        sm:grid-cols-3
                        lg:grid-cols-5
                        gap-3
                    "
                >

                    @foreach($typeColors as $type => $color)

                        <div
                            class="
                                flex
                                items-center
                                gap-3
                                rounded-xl
                                border
                                border-gray-100
                                bg-white
                                px-3
                                py-3
                                shadow-sm
                            "
                        >

                            <div
                                class="
                                    w-10
                                    h-10
                                    rounded-lg
                                    border
                                    flex-shrink-0
                                "
                                style="
                                    background-color: {{ $color }}73;
                                    border-color: {{ $color }};
                                "
                            ></div>


                            <div class="min-w-0">

                                <div
                                    class="
                                        text-xs
                                        sm:text-sm
                                        font-medium
                                        text-gray-700
                                        leading-tight
                                    "
                                >
                                    {{ $type }}
                                </div>

                                {{-- @if(isset($lotCounts[$type]))
                                    <div class="text-[11px] text-gray-400 mt-1">
                                        {{ $lotCounts[$type] }}
                                        {{ $lotCounts[$type] == 1 ? 'lot' : 'lots' }}
                                    </div>
                                @endif --}}

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>
        @endif


        {{-- LEAFLET MAP --}}
        <div
            class="relative w-full"
        >

            <div
                id="client-estate-leaflet-map"
                wire:ignore
                class="
                    w-full
                    rounded-xl
                    border
                    border-gray-200
                    overflow-hidden
                "
                style="
                    height: 700px;
                    z-index: 0;
                "
            >
            </div>

            <button
                type="button"
                onclick="resetClientEstateGISView()"
                class="
                    absolute
                    top-3
                    right-3
                    z-[30]
                    flex
                    items-center
                    gap-2
                    px-3
                    py-2
                    rounded-lg
                    border
                    border-gray-300
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
                id="client-estate-custom-tooltip"
                class="client-estate-custom-tooltip"
            >

                <div
                    id="client-estate-custom-tooltip-arrow"
                    class="client-estate-custom-tooltip-arrow"
                >
                </div>

                <div
                    id="client-estate-custom-tooltip-content"
                >
                </div>

            </div>

        </div>

    </div>


    <script>
        window.clientEstateGISLots =
            @json($leafletLots);

        if (
            window.clientEstateLeafletMap &&
            window.clientEstateLotLayer &&
            typeof renderClientEstateLots === 'function'
        ) {
            renderClientEstateLots();
        }
    </script>

</div>


@push('scripts')

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    >
    </script>

    <script
        src="https://cdn.jsdelivr.net/npm/pannellum@2.5.7/build/pannellum.js"
    >
    </script>


    <script>

        window.clientEstateLeafletMap = null;
        window.clientEstateLotLayer = null;
        window.clientEstateBoundaryLayer = null;

        window.clientEstateConstructionMarkers = [];
        window.clientEstateSoldMarkers = [];

        window.clientEstatePanoramaViewer = null;

        window.clientEstateTooltipLotId = null;
        window.clientEstateTooltipLatLng = null;


        /*
        |--------------------------------------------------------------------------
        | SUBDIVISION BOUNDARY
        |--------------------------------------------------------------------------
        */

        const clientEstateSubdivisionBoundary = [
            [13.920650, 121.420350],
            [13.920720, 121.421820],
            [13.920300, 121.422300],
            [13.919150, 121.422250],
            [13.918720, 121.421700],
            [13.918800, 121.420500],
            [13.919350, 121.420100]
        ];


        /*
        |--------------------------------------------------------------------------
        | LOT COLORS
        |--------------------------------------------------------------------------
        */

        const clientEstateLotColors = {
            "Playground & Community Amenities":
                "#f2b879",

            "Model House":
                "#c8c9c3",

            "Lot Only":
                "#c4e0b7",

            "House & Lot":
                "#f8e89c",

            "Sold":
                "#e9b4ae",
        };

        function initClientEstateLeafletMap()
        {
            const container =
                document.getElementById(
                    'client-estate-leaflet-map'
                );


            if (
                !container ||
                typeof L === 'undefined'
            ) {
                return;
            }


            hideClientEstateTooltip();

            if (
                window.clientEstateLeafletMap
            ) {
                try {
                    window
                        .clientEstateLeafletMap
                        .off();

                    window
                        .clientEstateLeafletMap
                        .remove();
                } catch (error) {
                    console.log(error);
                }

                window.clientEstateLeafletMap =
                    null;
            }


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
                    }
                );


            window.clientEstateLeafletMap =
                map;


            /*
            |--------------------------------------------------------------------------
            | SATELLITE TILE
            |--------------------------------------------------------------------------
            */

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

                    attribution:
                        '© Google',
                }
            ).addTo(
                map
            );

            const boundary =
                L.polygon(
                    clientEstateSubdivisionBoundary,
                    {
                        color:
                            '#2563eb',

                        weight:
                            4,

                        dashArray:
                            '10 6',

                        fillColor:
                            '#3b82f6',

                        fillOpacity:
                            0.05,

                        interactive:
                            false,
                    }
                ).addTo(
                    map
                );


            window.clientEstateBoundaryLayer =
                boundary;

            const lotLayer =
                L.layerGroup()
                    .addTo(
                        map
                    );


            window.clientEstateLotLayer =
                lotLayer;

            map.fitBounds(
                boundary.getBounds(),
                {
                    padding: [
                        30,
                        30
                    ],
                }
            );


            renderClientEstateLots();

            map.on(
                'zoomend',
                function()
                {
                    updateClientEstateLotOverlaySizes();
                }
            );


            map.on(
                'move zoom resize',
                function()
                {
                    refreshClientEstateTooltipPosition();
                }
            );


            map.on(
                'click',
                function()
                {
                    /*
                    | Do not automatically close here.
                    | Polygon click controls the tooltip.
                    */
                }
            );

            setTimeout(
                function()
                {
                    if (
                        window.clientEstateLeafletMap ===
                        map
                    ) {
                        map.invalidateSize(
                            true
                        );
                    }
                },
                250
            );
        }

        function renderClientEstateLots()
        {
            const map =
                window.clientEstateLeafletMap;


            const lotLayer =
                window.clientEstateLotLayer;


            if (
                !map ||
                !lotLayer
            ) {
                return;
            }


            lotLayer.clearLayers();


            window.clientEstateConstructionMarkers =
                [];

            window.clientEstateSoldMarkers =
                [];


            const lots =
                window.clientEstateGISLots || [];


            lots.forEach(
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
                        getClientEstateLotColor(
                            lot
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | LOT POLYGON
                    |--------------------------------------------------------------------------
                    */

                    const polygon =
                        L.polygon(
                            lot.geo_coords,
                            {
                                color:
                                    color,

                                fillColor:
                                    color,

                                weight:
                                    2,

                                fillOpacity:
                                    0.50,
                            }
                        );


                    polygon.clientEstateLotId =
                        lot.id;


                    polygon.clientEstateLot =
                        lot;


                    polygon.bindTooltip(
                        lot.name ?? 'Lot',
                        {
                            direction:
                                'center',

                            className:
                                'client-estate-lot-tooltip',
                        }
                    );


                    polygon.on(
                        'click',
                        function(event)
                        {
                            if (
                                event.originalEvent
                            ) {
                                L.DomEvent.stopPropagation(
                                    event.originalEvent
                                );
                            }


                            showClientEstateTooltip(
                                lot,
                                event.latlng
                            );
                        }
                    );


                    polygon.addTo(
                        lotLayer
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | SOLD LABEL
                    |--------------------------------------------------------------------------
                    */

                    const status =
                        (
                            lot.status || ''
                        )
                            .toLowerCase()
                            .trim();


                    if (
                        status === 'sold'
                    ) {
                        const center =
                            polygon
                                .getBounds()
                                .getCenter();


                        const scale =
                            getClientEstateLotOverlayScale(
                                polygon
                            );


                        const soldMarker =
                            L.marker(
                                center,
                                {
                                    interactive:
                                        false,

                                    opacity:
                                        scale > 0
                                            ? 1
                                            : 0,

                                    icon:
                                        buildClientEstateSoldIcon(
                                            scale
                                        ),
                                }
                            );


                        soldMarker.clientEstatePolygon =
                            polygon;


                        soldMarker.addTo(
                            lotLayer
                        );


                        window
                            .clientEstateSoldMarkers
                            .push(
                                soldMarker
                            );
                    }

                    if (
                        lot.is_under_construction
                    ) {
                        const center =
                            polygon
                                .getBounds()
                                .getCenter();


                        const scale =
                            getClientEstateLotOverlayScale(
                                polygon
                            );


                        const constructionMarker =
                            L.marker(
                                center,
                                {
                                    interactive:
                                        false,

                                    opacity:
                                        scale > 0
                                            ? 1
                                            : 0,

                                    icon:
                                        buildClientEstateConstructionIconByScale(
                                            scale
                                        ),
                                }
                            );


                        constructionMarker.clientEstatePolygon =
                            polygon;


                        constructionMarker.addTo(
                            lotLayer
                        );


                        window
                            .clientEstateConstructionMarkers
                            .push(
                                constructionMarker
                            );
                    }
                }
            );
        }

        function getClientEstateLotColor(
            lot
        )
        {
            const status =
                (
                    lot.status || ''
                )
                    .toLowerCase()
                    .trim();


            if (
                status === 'sold'
            ) {
                return clientEstateLotColors[
                    'Sold'
                ];
            }


            return clientEstateLotColors[
                lot.type
            ] ?? '#0096ff';
        }

        function getClientEstatePolygonPixelSize(
            polygon
        )
        {
            const map =
                window.clientEstateLeafletMap;


            if (
                !map ||
                !polygon ||
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
                !bounds ||
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


        function getClientEstateLotOverlayScale(
            polygon
        )
        {
            const pixelSize =
                getClientEstatePolygonPixelSize(
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


        function buildClientEstateSoldIcon(
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
                    'client-estate-sold-label',

                html: `
                    <div
                        style="
                            width:${width}px;
                            height:${height}px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:${fontSize}px;
                            font-weight:800;
                            line-height:1;
                            white-space:nowrap;
                            pointer-events:none;
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


        function buildClientEstateConstructionIconByScale(
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
                    'client-estate-construction-icon',

                html:
                    `
                        <div
                            class="client-estate-construction-badge"
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


        function updateClientEstateLotOverlaySizes()
        {
            (
                window.clientEstateSoldMarkers
                || []
            ).forEach(
                function(marker)
                {
                    const polygon =
                        marker.clientEstatePolygon;


                    if (!polygon) {
                        return;
                    }


                    const scale =
                        getClientEstateLotOverlayScale(
                            polygon
                        );


                    marker.setOpacity(
                        scale > 0
                            ? 1
                            : 0
                    );


                    marker.setIcon(
                        buildClientEstateSoldIcon(
                            scale
                        )
                    );
                }
            );


            (
                window.clientEstateConstructionMarkers
                || []
            ).forEach(
                function(marker)
                {
                    const polygon =
                        marker.clientEstatePolygon;


                    if (!polygon) {
                        return;
                    }


                    const scale =
                        getClientEstateLotOverlayScale(
                            polygon
                        );


                    marker.setOpacity(
                        scale > 0
                            ? 1
                            : 0
                    );


                    marker.setIcon(
                        buildClientEstateConstructionIconByScale(
                            scale
                        )
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BUILD CLIENT LOT TOOLTIP
        |--------------------------------------------------------------------------
        */

        function buildClientEstatePopup(
            lot
        )
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


            const isNonReservable =
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


            const showOwner =
                status !== 'available'
                &&
                lot.belongs_to_current_user
                &&
                !!lot.user;


            const showModel =
                supportsModel
                &&
                !!lot.house_model;


            const showExtra =
                showOwner
                ||
                showModel;


            const canReserve =
                status === 'available'
                &&
                !isNonReservable;


            const price =
                '₱' +
                Number(
                    lot.price || 0
                ).toLocaleString(
                    'en-PH'
                );


            let assignmentLabel =
                'Assigned To';


            if (
                status === 'sold'
            ) {
                assignmentLabel =
                    'Sold To';
            } else if (
                status === 'reserved'
            ) {
                assignmentLabel =
                    'Reserved To';
            }


            let statusClass =
                '';


            if (
                status === 'sold'
            ) {
                statusClass =
                    'sold';
            } else if (
                status === 'reserved'
            ) {
                statusClass =
                    'reserved';
            }


            return `
                <div
                    class="client-estate-popup"
                >

                    <button
                        type="button"
                        class="client-estate-popup-close"
                        onclick="hideClientEstateTooltip()"
                        aria-label="Close"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            style="
                                width:19px;
                                height:19px;
                                color:#64748b;
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
                                    id="client-estate-pano-${lot.id}"
                                    class="client-estate-popup-panorama"
                                ></div>
                            `
                            : `
                                <div
                                    class="client-estate-popup-panorama"
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


                    <div
                        class="client-estate-popup-body"
                    >

                        <div
                            class="client-estate-popup-top-row"
                        >

                            <div
                                class="client-estate-popup-title-wrap"
                            >

                                <div
                                    class="client-estate-popup-title"
                                >
                                    ${escapeClientEstateHTML(
                                        lot.name ?? 'Lot'
                                    )}
                                </div>


                                <div
                                    class="client-estate-popup-meta"
                                >
                                    <span
                                        class="client-estate-popup-meta-type"
                                    >
                                        ${escapeClientEstateHTML(
                                            propertyType || '-'
                                        )}
                                    </span>


                                    <span
                                        class="client-estate-popup-meta-dot"
                                    ></span>


                                    <span
                                        class="client-estate-popup-area"
                                    >
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
                                            ${escapeClientEstateHTML(
                                                lot.lot_area ?? '-'
                                            )}
                                            sqm
                                        </span>
                                    </span>
                                </div>


                                ${
                                    lot.is_under_construction
                                        ? `
                                            <div class="client-estate-popup-construction">

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

                            </div>


                            ${
                                !isNonReservable
                                    ? `
                                        <div
                                            class="
                                                client-estate-popup-status
                                                ${statusClass}
                                            "
                                        >
                                            ${escapeClientEstateHTML(
                                                status || '-'
                                            )}
                                        </div>
                                    `
                                    : ''
                            }

                        </div>


                        ${
                            showExtra
                                ? `
                                    <div
                                        class="client-estate-popup-extra"
                                    >

                                        ${
                                            showOwner
                                                ? `
                                                    <div
                                                        class="client-estate-popup-extra-section"
                                                    >
                                                        <div
                                                            class="client-estate-popup-section-label"
                                                        >
                                                            <span>
                                                                ${assignmentLabel}
                                                            </span>

                                                            <hr>
                                                        </div>

                                                        <div
                                                            class="
                                                                client-estate-popup-person
                                                                user
                                                            "
                                                        >
                                                            ${
                                                                lot.user.picture
                                                                    ? `
                                                                        <img
                                                                            src="${escapeClientEstateHTML(
                                                                                lot.user.picture
                                                                            )}"
                                                                            alt="Client"
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
                                                                ${escapeClientEstateHTML(
                                                                    lot.user.name
                                                                    ?? 'Client'
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
                                                    <div
                                                        class="client-estate-popup-extra-section"
                                                    >
                                                        <div
                                                            class="client-estate-popup-section-label"
                                                        >
                                                            <span>
                                                                Model Name
                                                            </span>

                                                            <hr>
                                                        </div>

                                                        <div
                                                            class="
                                                                client-estate-popup-person
                                                                model
                                                            "
                                                        >
                                                            ${
                                                                lot.house_model.image
                                                                    ? `
                                                                        <img
                                                                            src="${escapeClientEstateHTML(
                                                                                lot.house_model.image
                                                                            )}"
                                                                            alt="House Model"
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
                                                                ${escapeClientEstateHTML(
                                                                    lot.house_model.name
                                                                    ?? 'House Model'
                                                                )}
                                                            </strong>
                                                        </div>
                                                    </div>
                                                `
                                                : ''
                                        }

                                    </div>
                                `
                                : `
                                    <div
                                        class="client-estate-popup-extra empty"
                                    ></div>
                                `
                        }


                        ${
                            !isNonReservable
                                ? `
                                    <div
                                        class="client-estate-popup-price"
                                    >
                                        ${price}
                                    </div>
                                `
                                : ''
                        }


                        ${
                            canReserve
                                ? `
                                    <div
                                        class="client-estate-popup-bottom"
                                    >
                                        <div
                                            class="client-estate-popup-reserve"
                                        >
                                            <button
                                                type="button"
                                                onclick="reserveClientEstateLot(${lot.id})"
                                            >
                                                <span>
                                                    Reserve this lot
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                `
                                : ''
                        }

                    </div>

                </div>
            `;
        }

        function showClientEstateTooltip(
            lot,
            latlng
        )
        {
            const tooltip =
                document.getElementById(
                    'client-estate-custom-tooltip'
                );


            const content =
                document.getElementById(
                    'client-estate-custom-tooltip-content'
                );


            if (
                !tooltip ||
                !content ||
                !window.clientEstateLeafletMap
            ) {
                return;
            }


            hideClientEstateTooltip(
                false
            );


            window.clientEstateTooltipLotId =
                lot.id;


            window.clientEstateTooltipLatLng =
                latlng;


            content.innerHTML =
                buildClientEstatePopup(
                    lot
                );


            tooltip.classList.add(
                'is-visible'
            );


            positionClientEstateTooltip(
                latlng
            );


            if (
                lot.image
            ) {
                requestAnimationFrame(
                    function()
                    {
                        initClientEstatePanorama(
                            lot
                        );
                    }
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | HIDE TOOLTIP
        |--------------------------------------------------------------------------
        */

        function hideClientEstateTooltip(
            clearContent = true
        )
        {
            const tooltip =
                document.getElementById(
                    'client-estate-custom-tooltip'
                );


            const content =
                document.getElementById(
                    'client-estate-custom-tooltip-content'
                );


            if (
                window.clientEstatePanoramaViewer
            ) {
                try {
                    window
                        .clientEstatePanoramaViewer
                        .destroy();
                } catch (error) {
                    console.log(
                        error
                    );
                }


                window.clientEstatePanoramaViewer =
                    null;
            }


            if (
                tooltip
            ) {
                tooltip.classList.remove(
                    'is-visible'
                );
            }


            if (
                clearContent &&
                content
            ) {
                content.innerHTML =
                    '';
            }


            window.clientEstateTooltipLotId =
                null;


            window.clientEstateTooltipLatLng =
                null;
        }


        /*
        |--------------------------------------------------------------------------
        | TOOLTIP POSITION
        |--------------------------------------------------------------------------
        */

        function positionClientEstateTooltip(
            latlng
        )
        {
            const map =
                window.clientEstateLeafletMap;


            const mapContainer =
                document.getElementById(
                    'client-estate-leaflet-map'
                );


            const tooltip =
                document.getElementById(
                    'client-estate-custom-tooltip'
                );


            const arrow =
                document.getElementById(
                    'client-estate-custom-tooltip-arrow'
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
                mapContainer
                    .getBoundingClientRect();


            const tooltipWidth =
                tooltip.offsetWidth || 320;


            const tooltipHeight =
                tooltip.offsetHeight || 300;


            const gap =
                14;


            const padding =
                10;


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
                point.x - x;


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


        function refreshClientEstateTooltipPosition()
        {
            if (
                window.clientEstateTooltipLatLng
            ) {
                positionClientEstateTooltip(
                    window.clientEstateTooltipLatLng
                );
            }
        }

        function initClientEstatePanorama(
            lot
        )
        {
            const container =
                document.getElementById(
                    `client-estate-pano-${lot.id}`
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
                window.clientEstatePanoramaViewer
            ) {
                try {
                    window
                        .clientEstatePanoramaViewer
                        .destroy();
                } catch (error) {
                    console.log(
                        error
                    );
                }


                window.clientEstatePanoramaViewer =
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
                        .clientEstatePanoramaViewer =
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


        /*
        |--------------------------------------------------------------------------
        | RESERVE LOT
        |--------------------------------------------------------------------------
        */

        function reserveClientEstateLot(
            lotId
        )
        {
            const lots =
                window.clientEstateGISLots
                || [];


            const lot =
                lots.find(
                    function(item)
                    {
                        return Number(
                            item.id
                        ) === Number(
                            lotId
                        );
                    }
                );


            if (!lot) {
                return;
            }


            const status =
                (
                    lot.status || ''
                )
                    .toLowerCase()
                    .trim();


            const type =
                (
                    lot.type || ''
                ).trim();

            if (
                status !== 'available'
            ) {
                return;
            }


            if (
                [
                    'Model House',
                    'Playground & Community Amenities',
                ].includes(
                    type
                )
            ) {
                return;
            }

            const url =
                new URL(
                    '/client/reservations',
                    window.location.origin
                );


            url.searchParams.set(
                'lot_id',
                lot.id
            );


            url.searchParams.set(
                'type',
                type
            );

            if (
                type === 'House & Lot'
                &&
                lot.house_model?.id
            ) {
                url.searchParams.set(
                    'house_model_id',
                    lot.house_model.id
                );
            }


            window.location.href =
                url.toString();
        }

        function resetClientEstateGISView()
        {
            hideClientEstateTooltip();


            if (
                !window.clientEstateLeafletMap ||
                !window.clientEstateBoundaryLayer
            ) {
                return;
            }


            window
                .clientEstateLeafletMap
                .fitBounds(
                    window
                        .clientEstateBoundaryLayer
                        .getBounds(),
                    {
                        padding: [
                            30,
                            30
                        ],
                    }
                );
        }

        function escapeClientEstateHTML(
            value
        )
        {
            return String(
                value ?? ''
            )
                .replaceAll(
                    '&',
                    '&amp;'
                )
                .replaceAll(
                    '<',
                    '&lt;'
                )
                .replaceAll(
                    '>',
                    '&gt;'
                )
                .replaceAll(
                    '"',
                    '&quot;'
                )
                .replaceAll(
                    "'",
                    '&#039;'
                );
        }

        if (
            !window.clientEstateRefreshListenerBound
        ) {
            window.clientEstateRefreshListenerBound =
                true;


            Livewire.on(
                'refresh-client-leaflet-map',
                function()
                {
                    setTimeout(
                        function()
                        {
                            initClientEstateLeafletMap();
                        },
                        200
                    );
                }
            );
        }

        function bootClientEstateLeafletMap()
        {
            setTimeout(
                function()
                {
                    if (
                        document.getElementById(
                            'client-estate-leaflet-map'
                        )
                    ) {
                        initClientEstateLeafletMap();
                    }
                },
                200
            );
        }


        document.addEventListener(
            'livewire:initialized',
            function()
            {
                bootClientEstateLeafletMap();
            }
        );


        if (
            !window.clientEstateNavigationListenerBound
        ) {
            window.clientEstateNavigationListenerBound =
                true;


            document.addEventListener(
                'livewire:navigated',
                function()
                {
                    bootClientEstateLeafletMap();
                }
            );
        }


        if (
            document.readyState ===
                'interactive'
            ||
            document.readyState ===
                'complete'
        ) {
            bootClientEstateLeafletMap();
        }

    </script>

@endpush