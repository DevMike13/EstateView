<div wire:poll.5s="refreshLots">

    <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-100 mt-10">
        <div class="w-full h-auto flex justify-between items-center mb-5">
            <div>
                <h2 class="text-lg font-semibold">Subdivision Lot Map</h2>
                {{-- <p class="text-sm text-gray-500">Click on any lot to view details or assign to a client</p> --}}
            </div>
        </div>
        {{-- MAP LEGEND --}}
        <div class="mb-6">

            <div class="mb-3">
                <h3 class="text-sm font-semibold text-gray-800">
                    Map Legend
                </h3>

                <p class="text-xs text-gray-500">
                    Colors indicate the type and status of each mapped lot.
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">

                @foreach($typeColors as $type => $color)

                    <div
                        class="flex items-center gap-3 rounded-xl border border-gray-100 bg-white px-3 py-3 shadow-sm"
                    >

                        {{-- COLOR INDICATOR --}}
                        <div
                            class="w-10 h-10 rounded-lg border flex-shrink-0"
                            style="
                                background-color: {{ $color }}73;
                                border-color: {{ $color }};
                            "
                        ></div>

                        {{-- LABEL --}}
                        <div class="min-w-0">

                            <div class="text-xs sm:text-sm font-medium text-gray-700 leading-tight">
                                {{ $type }}
                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>
        <div class="relative mx-auto flex w-full justify-center">
            @if($map)
                <div class="relative inline-block max-w-full">
                    <img 
                        id="map-image"
                        src="{{ asset($map->image_path) }}"
                        usemap="#estate-map"
                        class="block max-w-full max-h-[80vh] w-auto h-auto object-contain"
                    />

                    <canvas 
                        id="lot-overlay"
                        class="absolute top-0 left-0 pointer-events-none"
                    ></canvas>
                </div>

                <div
                    id="lot-tooltip"
                    class="absolute hidden z-10 bg-white shadow-2xl overflow-visible rounded-xl border
                        w-[80vw] max-w-[320px] sm:max-w-[380px]"
                >
                    <div class="relative overflow-visible">





                        <div id="tooltip-arrow"></div>

                        <button
                            id="tooltip-close"
                            type="button"
                            class="absolute top-2 right-2 z-50
                                w-8 h-8
                                flex items-center justify-center
                                rounded-full
                                bg-white/90 hover:bg-white
                                shadow transition"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 text-gray-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 6l12 12M18 6L6 18"
                                />
                            </svg>
                        </button>

                        <div
                            id="tooltip-panorama"
                            class="w-full h-32 sm:h-40 rounded-t-xl overflow-hidden"
                        >
                        </div>

                        <div class="p-3 sm:p-4">

                            <div class="text-lg font-bold hidden" id="tooltip-id"></div>

                            <div class="flex justify-between items-center">
                                <div>
                                    <div
                                        class="text-base sm:text-lg font-bold break-words"
                                        id="tooltip-name"
                                    >
                                    </div>

                                    <div id="tooltip-under-construction" class="flex items-center gap-2 text-orange-600 text-xs font-semibold mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                        </svg>

                                        Under Construction
                                    </div>
                                    <div class="flex justify-center items-center -mt-1 gap-2">
                                        <div
                                            class="text-xs sm:text-sm text-gray-500 italic"
                                            id="tooltip-type"
                                        >
                                        </div>

                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-2">
                                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm6-2.438c0-.724.588-1.312 1.313-1.312h4.874c.725 0 1.313.588 1.313 1.313v4.874c0 .725-.588 1.313-1.313 1.313H9.564a1.312 1.312 0 0 1-1.313-1.313V9.564Z" clip-rule="evenodd" />
                                        </svg>

                                        <div
                                            class="text-xs sm:text-sm text-gray-500"
                                            id="tooltip-area"
                                        >
                                        </div>

                                    </div>
                                </div>

                                <div
                                    class="text-xs sm:text-sm mt-1 capitalize bg-green-200 text-green-800 px-4 py-2 rounded-full"
                                    id="tooltip-status"
                                >
                                </div>

                            </div>



                            <div class="border-2 border-dashed rounded-lg my-4 p-3 space-y-4">

                                <div id="tooltip-extra-section">
                                    <div class="flex items-center gap-1">
                                        <p
                                            id="tooltip-to"
                                            class="bg-blue-200 text-xs text-blue-800 px-2 rounded-full capitalize"
                                        >
                                        </p>

                                        <hr class="flex-1 border-t border-blue-500" />
                                    </div>

                                    <div class="flex items-center gap-3 bg-gray-100 p-2 rounded-lg mt-2">

                                        <img
                                            id="tooltip-user-picture"
                                            class="w-10 h-10 rounded-full object-cover border"
                                            src=""
                                            alt="User"
                                        />

                                        <div
                                            id="tooltip-user-name"
                                            class="text-sm font-semibold text-gray-800"
                                        >
                                        </div>

                                    </div>

                                </div>

                                <div id="tooltip-extra-section-model">
                                    <div class="flex items-center gap-1">
                                        <p class="bg-blue-200 text-xs text-blue-800 px-2 rounded-full capitalize">Model Name</p>

                                        <hr class="flex-1 border-t border-blue-500" />
                                    </div>
                                    <div class="flex items-center gap-3 bg-gray-100 p-2 rounded-lg mt-2">
                                        <img
                                            id="tooltip-model-picture"
                                            class="w-10 h-10 rounded object-cover border"
                                            src=""
                                            alt="Model"
                                        />

                                        <div
                                            id="tooltip-model-name"
                                            class="text-sm font-semibold text-gray-800"
                                        >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="text-lg text-gray-800 text-right font-semibold"
                                id="tooltip-price"

                            >
                            </div>
                            <div class="mt-4 text-right" id="tooltip-reserve-btn-wrapper">
                                <button
                                    id="tooltip-reserve-btn"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg shadow hidden"
                                >
                                    Reserve this lot
                                </button>
                            </div>
                            <div
                                class="text-gray-400 mt-2 hidden"
                                id="tooltip-coords"
                                style="font-size:8px;"
                            >
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
                            data-id="{{ $lot->id }}"
                            data-type="{{ $lot->type }}"
                            data-price="{{ $lot->price }}"
                            data-status="{{ $lot->status }}"
                            data-area="{{ $lot->lot_area }}"
                            data-name="{{ $lot->name }}"
                            data-image="{{ $lot->image ? asset('storage/' . $lot->image) : '' }}"
                            data-coords="{{ $lot->coords }}"

                            data-model-id="{{ $lot->house_model_id }}"
                            data-user-id="{{ $lot->user_id }}"
                            data-user-name="{{ $lot->user?->name }}"
                            data-user-picture="{{ asset($lot->user?->profile_picture) }}"

                            data-model-name="{{ $lot->houseModel?->model_name }}"
                            data-model-image="{{  asset('storage/' . $lot->houseModel?->image) }}"

                            data-under-construction="{{ $lot->is_under_construction ? 1 : 0 }}"
                        />
                    @endforeach
                </map>
            @endif
        </div>
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
    <script>

        function hexToRGBA(hex, opacity) {

            const r = parseInt(hex.substring(1, 3), 16);
            const g = parseInt(hex.substring(3, 5), 16);
            const b = parseInt(hex.substring(5, 7), 16);

            return `rgba(${r}, ${g}, ${b}, ${opacity})`;

        }

        function drawLots() {
            const mapImage = document.getElementById('map-image');
            const canvas = document.getElementById('lot-overlay');

            if (!mapImage || !canvas) return;

            // Wait until the original image dimensions are available.
            if (!mapImage.complete || !mapImage.naturalWidth || !mapImage.naturalHeight) {
                mapImage.addEventListener('load', drawLots, { once: true });
                return;
            }

            const rect = mapImage.getBoundingClientRect();
            const displayWidth = rect.width;
            const displayHeight = rect.height;

            if (!displayWidth || !displayHeight) return;

            canvas.width = displayWidth;
            canvas.height = displayHeight;
            canvas.style.width = displayWidth + "px";
            canvas.style.height = displayHeight + "px";

            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Always scale from the ORIGINAL database coordinates.
            // jQuery-rwdImageMaps modifies area.coords, so area.coords
            // must not be used for drawing the canvas overlay.
            const scaleX = displayWidth / mapImage.naturalWidth;
            const scaleY = displayHeight / mapImage.naturalHeight;

            const colors = {
                "Playground & Community Amenities": "#f2b879",
                "Model House": "#c8c9c3",
                "Lot Only": "#c4e0b7",
                "House & Lot": "#f8e89c",
                "Sold": "#e9b4ae",
            };

            document.querySelectorAll('map[name="estate-map"] area').forEach(area => {
                const originalCoords = (area.dataset.coords || '')
                    .split(',')
                    .map(Number);

                if (
                    originalCoords.length < 6 ||
                    originalCoords.some(Number.isNaN)
                ) {
                    return;
                }

                const coords = [];

                for (let i = 0; i < originalCoords.length; i += 2) {
                    coords.push(
                        originalCoords[i] * scaleX,
                        originalCoords[i + 1] * scaleY
                    );
                }

                const status = (area.dataset.status ?? '').toLowerCase().trim();
                const type = area.dataset.type;

                const color = status === 'sold'
                    ? colors["Sold"]
                    : (colors[type] || "#0096ff");

                // -------------------------
                // DRAW POLYGON
                // -------------------------
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
                ctx.fillStyle = hexToRGBA(color, 0.45);
                ctx.fill();
                ctx.strokeStyle = color;
                ctx.lineWidth = 2;
                ctx.stroke();

                // -------------------------
                // POLYGON CENTROID
                // -------------------------
                const n = coords.length / 2;
                let areaSum = 0;
                let cx = 0;
                let cy = 0;

                for (let i = 0; i < n; i++) {
                    const x1 = coords[i * 2];
                    const y1 = coords[i * 2 + 1];
                    const x2 = coords[((i + 1) % n) * 2];
                    const y2 = coords[((i + 1) % n) * 2 + 1];

                    const cross = (x1 * y2) - (x2 * y1);

                    areaSum += cross;
                    cx += (x1 + x2) * cross;
                    cy += (y1 + y2) * cross;
                }

                areaSum *= 0.5;

                if (areaSum !== 0) {
                    cx = cx / (6 * areaSum);
                    cy = cy / (6 * areaSum);
                } else {
                    cx = 0;
                    cy = 0;

                    for (let i = 0; i < coords.length; i += 2) {
                        cx += coords[i];
                        cy += coords[i + 1];
                    }

                    cx /= n;
                    cy /= n;
                }

                // -------------------------
                // SOLD LABEL
                // -------------------------
                if (status === 'sold') {

                    ctx.save();

                    ctx.globalAlpha = 0.8;

                    ctx.fillStyle = "#000000";

                    const soldFontSize =
                        displayWidth < 640 ? 6 :
                        displayWidth < 1024 ? 9 :
                        13;

                    ctx.font = `bold ${soldFontSize}px Arial`;

                    ctx.textAlign = "center";

                    ctx.textBaseline = "middle";

                    ctx.fillText("SOLD", cx, cy);

                    ctx.restore();

                }

                // -------------------------
                // RESERVED ICON
                // -------------------------
                // if (status === 'reserved') {
                //     drawLotIcon(ctx, cx, cy, 'reserved');
                // }

                // -------------------------
                // UNDER CONSTRUCTION ICON
                // -------------------------
                if ((area.dataset.underConstruction ?? '').toString() === '1') {
                    drawLotIcon(ctx, cx, cy, 'construction');
                }
            });
        }

        function drawLotIcon(ctx, cx, cy, type) {
            ctx.save();

            const radius = 11;

            ctx.beginPath();
            ctx.arc(cx, cy, radius, 0, Math.PI * 2);

            ctx.fillStyle = type === 'construction'
                ? "rgba(245, 158, 11, 0.9)"
                : "rgba(17, 24, 39, 0.85)";

            ctx.fill();

            ctx.strokeStyle = "rgba(255,255,255,0.2)";
            ctx.lineWidth = 1;
            ctx.stroke();

            let svg;

            if (type === 'reserved') {
                svg = `
                    <svg xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="5" y="11" width="14" height="9" rx="2"/>
                        <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                    </svg>
                `;
            } else {
                svg = `
                    <svg xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="white">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/>
                    </svg>
                `;
            }

            const icon = new Image();

            icon.src = "data:image/svg+xml;charset=utf-8,"
                + encodeURIComponent(svg);

            const size = 12;

            icon.onload = () => {
                ctx.drawImage(
                    icon,
                    cx - size / 2,
                    cy - size / 2,
                    size,
                    size
                );
            };

            ctx.restore();
        }

        window.addEventListener("load", drawLots);
        window.addEventListener("resize", drawLots);

    </script>

    {{-- LOT 360 PREVIEW --}}
    <script>

        function initLotTooltip() {

            const tooltip = document.getElementById('lot-tooltip');
            const tName = document.getElementById('tooltip-name');
            const tType = document.getElementById('tooltip-type');
            const tImage = document.getElementById('tooltip-image');
            const tCoords = document.getElementById('tooltip-coords');
            const tID = document.getElementById('tooltip-id');

            const tPrice = document.getElementById('tooltip-price');
            const tStatus = document.getElementById('tooltip-status');
            const tLotArea = document.getElementById('tooltip-area');

            const extraSection = document.getElementById('tooltip-extra-section');
            const extraSectionModel = document.getElementById('tooltip-extra-section-model');

            const tTo = document.getElementById('tooltip-to');
            const tUserPicture = document.getElementById('tooltip-user-picture');
            const tUserName = document.getElementById('tooltip-user-name');

            // const tUserPicture = document.getElementById('tooltip-user-picture');
            const tModelPicture = document.getElementById('tooltip-model-picture');
            const tModelName = document.getElementById('tooltip-model-name');

            const tUnderConstruction = document.getElementById('tooltip-under-construction');

            const closeBtn = document.getElementById('tooltip-close');



            const img = document.getElementById('map-image');

            let currentLotId = null;

            if (!img || !tooltip) return;

            function show(area, e) {



                currentLotId = area.dataset.id;
                const panoContainer = document.getElementById('tooltip-panorama');
                panoContainer.innerHTML = "";

                tName.textContent = area.dataset.name ?? 'No Name';
                tType.textContent = area.dataset.type ?? 'No Type';
                tCoords.textContent = area.dataset.coords ?? '';
                tID.textContent = area.dataset.id ?? '';

                const propertyType = (area.dataset.type ?? '').trim();

                const isNonReservable = [
                    'Model House',
                    'Playground & Community Amenities'
                ].includes(propertyType);

                if (isNonReservable) {

                    // HIDE PRICE
                    tPrice.style.display = 'none';
                    tPrice.textContent = '';

                    // HIDE STATUS
                    tStatus.style.display = 'none';
                    tStatus.textContent = '';

                } else {

                    // SHOW PRICE
                    tPrice.style.display = '';
                    tPrice.textContent =
                        '₱' + Number(area.dataset.price || 0).toLocaleString();

                    // SHOW STATUS
                    tStatus.style.display = '';
                    tStatus.textContent =
                        area.dataset.status ?? '';
                }
                // tLotArea.textContent = area.dataset.area + ' sqm' ?? '';
                tLotArea.innerHTML = `
                    <span style="display:flex; align-items:center; gap:6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>


                        <span>${area.dataset.area ?? ''} sqm</span>
                    </span>
                `;

                const reserveBtn = document.getElementById('tooltip-reserve-btn');

                const status = (area.dataset.status ?? '')
                    .toLowerCase()
                    .trim();

                /*
                |--------------------------------------------------------------------------
                | CHECK IF LOT BELONGS TO LOGGED-IN USER
                |--------------------------------------------------------------------------
                */

                const loggedInUserId = @json(auth()->id());

                const lotUserId = area.dataset.userId
                    ? Number(area.dataset.userId)
                    : null;

                const currentUserId = loggedInUserId
                    ? Number(loggedInUserId)
                    : null;

                const belongsToLoggedInUser =
                    lotUserId !== null &&
                    currentUserId !== null &&
                    lotUserId === currentUserId;

                /*
                |--------------------------------------------------------------------------
                | SHOW SOLD TO / RESERVED TO ONLY TO OWNER
                |--------------------------------------------------------------------------
                */

                if (
                    status !== 'available' &&
                    belongsToLoggedInUser
                ) {

                    extraSection.style.display = 'block';

                    tUserPicture.src =
                        area.dataset.userPicture || '/default-user.png';

                    tUserName.textContent =
                        area.dataset.userName || 'No User';

                    if (status === 'sold') {

                        tTo.textContent = 'Sold To';

                    } else if (status === 'reserved') {

                        tTo.textContent = 'Reserved To';

                    } else {

                        tTo.textContent =
                            (area.dataset.status || '') + ' To';

                    }

                } else {

                    extraSection.style.display = 'none';

                    tUserPicture.src = '';

                    tUserName.textContent = '';

                    tTo.textContent = '';

                }

                /*
                |--------------------------------------------------------------------------
                | KEEP EXISTING MODEL DATA
                |--------------------------------------------------------------------------
                */

                tModelPicture.src =
                    area.dataset.modelImage || '/default-model.png';

                tModelName.textContent =
                    area.dataset.modelName || 'No Model';

                const isClientUser = @json(auth()->check() && auth()->user()->role === 'user');

                if (
                    status === 'available' &&
                    isClientUser &&
                    !isNonReservable
                ) {
                    reserveBtn.classList.remove('hidden');
                } else {
                    reserveBtn.classList.add('hidden');
                }

                if (area.dataset.type === 'Model House' || area.dataset.type === 'House & Lot'){
                    extraSectionModel.style.display = 'block';
                    tModelPicture.src = area.dataset.modelImage || '/default-model.png';
                    tModelName.textContent = area.dataset.modelName || 'No Model';
                } else {
                    extraSectionModel.style.display = 'none';
                    tModelPicture.src = '';
                    tModelName.textContent = '';
                }

                const isUnderConstruction = area.dataset.underConstruction === "1";

                if (isUnderConstruction) {
                    tUnderConstruction.classList.remove('hidden');
                } else {
                    tUnderConstruction.classList.add('hidden');
                }

                reserveBtn.onclick = function () {

                    if (!currentLotId) return;

                    const area = document.querySelector(`area[data-id="${currentLotId}"]`);
                    if (!area) return;

                    const type = (area.dataset.type || '').trim();

                    const url = new URL('/client/reservations', window.location.origin);

                    url.searchParams.set('lot_id', currentLotId);
                    url.searchParams.set('type', type);

                    // ONLY pass model if House & Lot
                    if (type === 'House & Lot') {

                        // IMPORTANT: force string + trim + fallback
                        const modelId = (area.getAttribute('data-model-id') || '').trim();

                        if (modelId && modelId !== 'null' && modelId !== 'undefined') {
                            url.searchParams.set('house_model_id', modelId);
                        }
                    }

                    window.location.href = url.toString();
                };

                tooltip.classList.remove('hidden');

                currentLot = {
                    id: area.dataset.id,
                    name: area.dataset.name
                };

                // IMPORTANT: wait for layout BEFORE initializing pannellum
                requestAnimationFrame(() => {

                    tooltipViewer = pannellum.viewer(panoContainer, {
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

                // HANDLE ARROW

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

            closeBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                hide();
            });

            function bind() {
                let activeArea = null;

                document.querySelectorAll('map[name="estate-map"] area').forEach(area => {
                    // Prevent duplicate listeners when Livewire polls / morphs.
                    if (area.dataset.tooltipBound === '1') {
                        return;
                    }

                    area.dataset.tooltipBound = '1';

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

                    setTimeout(() => {
                        bind();
                        drawLots();
                    }, 300);

                } else {
                    setTimeout(waitForMap, 200);
                }

            }

            waitForMap();

            // Livewire safety hook (VERY IMPORTANT)
            // document.addEventListener('livewire:navigated', () => {

            //     setTimeout(() => {
            //         if (
            //             window.jQuery
            //             && $('img[usemap]').length
            //         ) {
            //             $('img[usemap]').rwdImageMaps();
            //         }

            //         bind();
            //         drawLots();
            //     }, 300);

            // });

            // Redraw only after Livewire actually detects changed lot data.
            Livewire.on('refresh-client-map', () => {

                setTimeout(() => {
                    if (
                        window.jQuery
                        && $('img[usemap]').length
                    ) {
                        $('img[usemap]').rwdImageMaps();
                    }

                    bind();
                    drawLots();
                }, 200);

            });
        }

        /*
        |--------------------------------------------------------------------------
        | INITIALIZE CLIENT MAP
        |--------------------------------------------------------------------------
        */

        function initClientMap() {

            setTimeout(() => {

                const img =
                    document.getElementById('map-image');

                const canvas =
                    document.getElementById('lot-overlay');

                /*
                * We may currently be on another page.
                */
                if (!img || !canvas) {
                    return;
                }

                /*
                * Initialize responsive image map.
                */
                if (
                    window.jQuery
                    && $('img[usemap]').length
                ) {

                    $('img[usemap]')
                        .rwdImageMaps();

                }

                /*
                * Initialize tooltip / area click events.
                */
                initLotTooltip();

                /*
                * Draw mapped lots.
                */
                if (
                    img.complete
                    && img.naturalWidth > 0
                    && img.naturalHeight > 0
                ) {

                    requestAnimationFrame(() => {
                        drawLots();
                    });

                } else {

                    img.addEventListener(
                        'load',
                        () => {

                            if (
                                window.jQuery
                                && $('img[usemap]').length
                            ) {

                                $('img[usemap]')
                                    .rwdImageMaps();

                            }

                            requestAnimationFrame(() => {
                                drawLots();
                            });

                        },
                        {
                            once: true
                        }
                    );

                }

            }, 150);
        }


        /*
        |--------------------------------------------------------------------------
        | NORMAL FULL PAGE LOAD
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            () => {

                initClientMap();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | LIVEWIRE WIRE:NAVIGATE
        |--------------------------------------------------------------------------
        |
        | This is the important part.
        |
        | DOMContentLoaded does not fire again when navigating
        | with wire:navigate.
        |
        */

        if (!window.clientMapNavigationListenerBound) {

            window.clientMapNavigationListenerBound = true;

            document.addEventListener(
                'livewire:navigated',
                () => {

                    initClientMap();

                }
            );

        }

    </script>

</div>