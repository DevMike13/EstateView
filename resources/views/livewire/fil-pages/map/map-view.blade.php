<div>
    <div class="bg-white rounded-2xl shadow-md p-5 border border-gray-100 mt-10">
        <div class="w-full h-auto flex justify-between items-center mb-5">
            <div>
                <h2 class="text-lg font-semibold">Subdivision Lot Map</h2>
                <p class="text-sm text-gray-500">Click on any lot to view details or assign to a client</p>
            </div>
            <div class="flex justify-end">
                <x-button
                    label="New Lot Area"
                    x-on:click="
                        $openModal('newLot');

                        setTimeout(() => {
                            window.dispatchEvent(
                                new CustomEvent('redraw-create-lot-map')
                            );
                        }, 300);
                    "
                    primary
                />
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
                    class="absolute hidden z-10 bg-white shadow-2xl overflow-visible rounded-xl border
                        w-[80vw] max-w-[320px] sm:max-w-[380px]"
                >
                    <div class="relative overflow-visible">

                        <div id="tooltip-arrow" class="absolute w-0 h-0 z-20 pointer-events-none"></div>

                         <button
                            id="tooltip-close"
                            type="button"
                            class="absolute top-2 right-2 z-50 w-8 h-8 flex items-center justify-center rounded-full bg-white/90 hover:bg-white shadow transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5 text-gray-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                            </svg>
                        </button>

                        <div
                            id="tooltip-panorama"
                            class="w-full h-32 sm:h-40 rounded-t-xl overflow-hidden"
                        ></div>

                        <div class="p-3 sm:p-4">

                            <div class="text-lg font-bold hidden" id="tooltip-id"></div>

                            <div class="flex justify-between items-center">
                                <div>
                                    <div
                                        class="text-base sm:text-lg font-bold break-words"
                                        id="tooltip-name"
                                    ></div>
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
                                        ></div>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-2">
                                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm6-2.438c0-.724.588-1.312 1.313-1.312h4.874c.725 0 1.313.588 1.313 1.313v4.874c0 .725-.588 1.313-1.313 1.313H9.564a1.312 1.312 0 0 1-1.313-1.313V9.564Z" clip-rule="evenodd" />
                                        </svg>

                                        <div
                                            class="text-xs sm:text-sm text-gray-500"
                                            id="tooltip-area"
                                        ></div>
                                    </div>
                                </div>

                                <div
                                    class="text-xs sm:text-sm mt-1 capitalize bg-green-200 text-green-800 px-4 py-2 rounded-full"
                                    id="tooltip-status"
                                ></div>
                            </div>
                            

                            <div class="border-2 border-dashed rounded-lg my-4 p-3 space-y-4">

                                <div id="tooltip-extra-section">
                                    <!-- USER -->
                                    <div class="flex items-center gap-1">
                                        <p id="tooltip-to" class="bg-blue-200 text-xs text-blue-800 px-2 rounded-full capitalize"></p>

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
                                        ></div>
                                    </div>
                                </div>

                                <div id="tooltip-extra-section-model">
                                    <!-- MODEL -->
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
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="text-lg text-gray-800 text-right font-semibold"
                                id="tooltip-price"
                            ></div>

                            <div
                                class="text-gray-400 mt-2 hidden"
                                id="tooltip-coords"
                                style="font-size:8px;"
                            ></div>

                            <div class="mt-4 flex flex-col sm:flex-row gap-2 sm:justify-end">

                                <x-button
                                    icon="pencil-square"
                                    label="Edit"
                                    class="w-full sm:w-auto rounded-lg"
                                    x-on:click="
                                        $wire.set(
                                            'activeLotId',
                                            document.getElementById('tooltip-id').textContent
                                        );
                                        $wire.loadEditLot();
                                        $openModal('editLot');
                                    "
                                />

                               <x-button
                                    icon="trash"
                                    label="Delete"
                                    class="w-full sm:w-auto bg-red-600 hover:bg-red-600 text-white rounded-lg"
                                    x-on:click="
                                        const id = document.getElementById('tooltip-id').textContent;
                                        const name = document.getElementById('tooltip-name').textContent;

                                        $wire.deleteLotConfirmation(id, name);
                                    "
                                />

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

                            data-user-name="{{ $lot->user?->name }}"
                            data-user-id="{{ $lot->user?->id }}"
                            data-user-picture="{{ asset($lot->user?->profile_picture) }}"

                            data-model-name="{{ $lot->houseModel?->model_name }}"
                            data-model-image="{{  asset('storage/' . $lot->houseModel?->image) }}"

                            data-under-construction="{{ $lot->is_under_construction ? 1 : 0 }}"
                        />
                    @endforeach
                </map>
                
            @endif
        </div>

        <x-modal blur name="newLot" persistent align="center" max-width="6xl">
            <form
                wire:submit.prevent="createLotArea"
                class="w-full"
                x-data="lotDrawer(
                    @js(
                        collect($lots)->map(fn ($lot) => [
                            'coords' => $lot->coords,
                            'type' => $lot->type,
                            'status' => $lot->status,
                        ])->values()->toArray()
                    )
                )"
                x-init="init()"
            >
                <x-card title="Create New Lot Area">
                    <div class="mt-3 relative">
                        @if($map)
                            <img 
                                id="modal-map-image"
                                src="{{ asset($map->image_path) }}"
                                class="w-full cursor-crosshair"
                                @click="addPoint($event)"
                                @load="onImageLoad()"
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
                    <div class="mt-1 flex justify-end gap-x-2">
                        <x-button xs icon="arrow-path-rounded-square" rounded dark label="Reset Points" @click="resetPoints()" />
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
                            class="pr-28"
                            label="Lot Area"
                            placeholder="100"
                            suffix="sqm"
                            wire:model.defer="lotArea"
                        />
                    </div>

                    <div class="mt-3">
                        <x-native-select
                            label="Property Type"
                            wire:model.live="lotType"
                        >
                            <option value="">Select Type</option>
                            <option value="Playground & Community Amenities">Playground & Community Amenities</option>
                            <option value="Model House">Model House</option>
                            <option value="Lot Only">Lot Only</option>
                            <option value="House & Lot">House & Lot</option>
                            {{-- <option value="Sold">Sold</option> --}}
                        </x-native-select>
                    </div>

                    @if (
                        $lotType &&
                        !in_array($lotType, [
                            'Model House',
                            'Playground & Community Amenities'
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
                            'Playground & Community Amenities'
                        ])
                    )
                        <div class="mt-3">
                            <h2 class="text-[#15233C] font-tertiary font-medium text-sm mb-1">
                                Status
                            </h2>

                            <div class="grid w-full gap-2 grid-cols-3">
                                @php
                                    $options = [
                                        'available' => 'Available',
                                        'sold' => 'Sold',
                                        'reserved' => 'Reserved',
                                    ];
                                @endphp

                                @foreach($options as $value => $label)
                                    <div>
                                        <input
                                            wire:model.live="lotStatus"
                                            type="radio"
                                            id="lotStatus{{ $value }}"
                                            name="lotStatus"
                                            value="{{ $value }}"
                                            class="hidden peer"
                                        >

                                        <label
                                            for="lotStatus{{ $value }}"
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

                    @if ($lotStatus && $lotStatus !== 'available')
                        <div class="mt-3">
                            <x-select
                                label="Client Name"
                                wire:model.defer="userId"
                                placeholder="Select some client"
                                :async-data="route('api.users.index')"
                                :template="[
                                    'name'   => 'user-option',
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
                                    'name'   => 'user-option',
                                    'config' => ['src' => 'image']
                                ]"
                                option-label="name"
                                option-value="id"
                                option-description="description"
                            />
                        </div>
                    @endif


                    <div class="mt-3 flex items-center justify-between bg-gray-50 p-3 rounded-lg border">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700">
                                Under Construction
                            </h3>
                            <p class="text-xs text-gray-400">
                                Mark this lot as under construction
                            </p>
                        </div>

                        <x-toggle wire:model.defer="isUnderConstruction" />
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
                            <x-button flat label="Cancel" @click="closeModal()" x-on:click="close" wire:click="reloadWeb" />
                            <x-button primary label="Save" type="submit" @click="$wire.lotCoordinates = coordsString" />
                        </div>
                    </x-slot>

                </x-card>
            </form>

            <script>
                function lotDrawer(existingLots = []) {
                    return {
                        points: [],
                        existingLots: existingLots,
                        coordsString: '',
                        canvas: null,
                        ctx: null,
                        img: null,

                        init() {
                            this.canvas = this.$refs.canvas;
                            this.img = this.$refs.img;
                            this.ctx = this.canvas.getContext('2d');

                            window.addEventListener(
                                'resize',
                                () => this.redraw()
                            );

                            window.addEventListener(
                                'redraw-create-lot-map',
                                () => {
                                    this.$nextTick(() => {
                                        this.redraw();
                                    });
                                }
                            );

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

                            if (
                                !this.canvas ||
                                !this.img ||
                                !this.img.naturalWidth ||
                                !this.img.naturalHeight
                            ) {
                                return;
                            }

                            this.canvas.width =
                                this.img.clientWidth;

                            this.canvas.height =
                                this.img.clientHeight;

                            const ctx = this.ctx;

                            ctx.clearRect(
                                0,
                                0,
                                this.canvas.width,
                                this.canvas.height
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | SCALE
                            |--------------------------------------------------------------------------
                            |
                            | Database coordinates use the original / natural map dimensions.
                            |
                            */

                            const scaleX =
                                this.img.clientWidth /
                                this.img.naturalWidth;

                            const scaleY =
                                this.img.clientHeight /
                                this.img.naturalHeight;


                            /*
                            |--------------------------------------------------------------------------
                            | EXISTING LOT COLORS
                            |--------------------------------------------------------------------------
                            */

                            const colors = {
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


                            /*
                            |--------------------------------------------------------------------------
                            | DRAW EXISTING MAPPED LOTS
                            |--------------------------------------------------------------------------
                            */

                            this.existingLots.forEach(lot => {

                                if (!lot.coords) {
                                    return;
                                }

                                const originalCoords =
                                    lot.coords
                                        .split(',')
                                        .map(Number);

                                if (
                                    originalCoords.length < 6 ||
                                    originalCoords.some(Number.isNaN)
                                ) {
                                    return;
                                }

                                const coords = [];

                                for (
                                    let i = 0;
                                    i < originalCoords.length;
                                    i += 2
                                ) {

                                    coords.push(
                                        originalCoords[i] * scaleX,
                                        originalCoords[i + 1] * scaleY
                                    );
                                }


                                const status =
                                    (lot.status || '')
                                        .toLowerCase()
                                        .trim();

                                const color =
                                    status === 'sold'
                                        ? colors["Sold"]
                                        : (
                                            colors[lot.type]
                                            || "#0096ff"
                                        );


                                ctx.beginPath();

                                for (
                                    let i = 0;
                                    i < coords.length;
                                    i += 2
                                ) {

                                    if (i === 0) {

                                        ctx.moveTo(
                                            coords[i],
                                            coords[i + 1]
                                        );

                                    } else {

                                        ctx.lineTo(
                                            coords[i],
                                            coords[i + 1]
                                        );
                                    }
                                }

                                ctx.closePath();


                                /*
                                * Existing lot fill.
                                */
                                ctx.fillStyle =
                                    hexToRGBA(
                                        color,
                                        0.35
                                    );

                                ctx.fill();


                                /*
                                * Existing lot border.
                                */
                                ctx.strokeStyle =
                                    color;

                                ctx.lineWidth =
                                    2;

                                ctx.stroke();


                                /*
                                * SOLD label.
                                */
                                if (status === 'sold') {

                                    const xValues = [];
                                    const yValues = [];

                                    for (
                                        let i = 0;
                                        i < coords.length;
                                        i += 2
                                    ) {

                                        xValues.push(
                                            coords[i]
                                        );

                                        yValues.push(
                                            coords[i + 1]
                                        );
                                    }

                                    const centerX =
                                        (
                                            Math.min(...xValues) +
                                            Math.max(...xValues)
                                        ) / 2;

                                    const centerY =
                                        (
                                            Math.min(...yValues) +
                                            Math.max(...yValues)
                                        ) / 2;

                                    const fontSize =
                                        Math.max(
                                            5,
                                            Math.min(
                                                14,
                                                14 * scaleX
                                            )
                                        );

                                    ctx.save();

                                    ctx.fillStyle =
                                        "#000000";

                                    ctx.globalAlpha =
                                        0.8;

                                    ctx.font =
                                        `bold ${fontSize}px Arial`;

                                    ctx.textAlign =
                                        "center";

                                    ctx.textBaseline =
                                        "middle";

                                    ctx.fillText(
                                        "SOLD",
                                        centerX,
                                        centerY
                                    );

                                    ctx.restore();
                                }

                            });


                            /*
                            |--------------------------------------------------------------------------
                            | DRAW NEW LOT CURRENTLY BEING CREATED
                            |--------------------------------------------------------------------------
                            */

                            if (this.points.length === 0) {
                                return;
                            }

                            ctx.beginPath();

                            this.points.forEach(
                                (p, i) => {

                                    const x =
                                        p.x * scaleX;

                                    const y =
                                        p.y * scaleY;

                                    if (i === 0) {

                                        ctx.moveTo(
                                            x,
                                            y
                                        );

                                    } else {

                                        ctx.lineTo(
                                            x,
                                            y
                                        );
                                    }
                                }
                            );

                            /*
                            * Don't close it visually until we have enough
                            * points to form a polygon.
                            */
                            if (this.points.length >= 3) {
                                ctx.closePath();

                                ctx.fillStyle =
                                    "rgba(0,150,255,0.30)";

                                ctx.fill();
                            }

                            ctx.strokeStyle =
                                "#0096ff";

                            ctx.lineWidth =
                                3;

                            ctx.stroke();


                            /*
                            |--------------------------------------------------------------------------
                            | NEW LOT POINTS
                            |--------------------------------------------------------------------------
                            */

                            this.points.forEach(p => {

                                const x =
                                    p.x * scaleX;

                                const y =
                                    p.y * scaleY;

                                ctx.beginPath();

                                ctx.arc(
                                    x,
                                    y,
                                    5,
                                    0,
                                    Math.PI * 2
                                );

                                ctx.fillStyle =
                                    "#000000";

                                ctx.fill();

                                ctx.strokeStyle =
                                    "#ffffff";

                                ctx.lineWidth =
                                    1;

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

        <x-modal blur name="editLot" persistent align="center" max-width="6xl">
            <form
                wire:submit.prevent="updateLot"
                class="w-full"
                x-data="editLotDrawer(
                    @entangle('editLotCoordinates'),
                    @entangle('editLotType'),
                    @entangle('editLotStatus')
                )"
                x-init="init()"
            >
                <x-card title="Edit Lot Area">
                    <div class="mt-3 relative w-full">
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
                                class="absolute top-0 left-0 pointer-events-none z-10"
                                x-ref="canvas"
                            ></canvas>
                        @endif
                    </div>

                    <!-- Reset Button -->
                    <div class="mt-1 flex justify-end gap-x-2">
                        <x-button xs icon="arrow-path-rounded-square" rounded dark label="Reset Points" @click="resetPoints()" />
                    </div>

                    <div class="mt-3">
                        <x-input
                            label="Lot Name"
                            placeholder="Ex: Block 1, Lot 43"
                            wire:model.defer="editLotName"
                        />
                    </div>

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

                    <div class="mt-3">
                        <x-native-select
                            label="Lot Type"
                            wire:model="editLotType"
                        >
                            <option value="">Select Type</option>
                            <option value="Playground & Community Amenities">Playground & Community Amenities</option>
                            <option value="Model House">Model House</option>
                            <option value="Lot Only">Lot Only</option>
                            <option value="House & Lot">House & Lot</option>
                            <option value="Sold">Sold</option>
                        </x-native-select>
                    </div>

                    @if (
                        $editLotType &&
                        !in_array($editLotType, [
                            'Model House',
                            'Playground & Community Amenities'
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

                    @if (
                        $editLotType &&
                        !in_array($editLotType, [
                            'Model House',
                            'Playground & Community Amenities'
                        ])
                    )
                        <div class="mt-3">
                            <h2 class="text-[#15233C] font-tertiary font-medium text-sm mb-1">
                                Status
                            </h2>

                            <div class="grid w-full gap-2 grid-cols-3">
                                @php
                                    $options = [
                                        'available' => 'Available',
                                        'sold' => 'Sold',
                                        'reserved' => 'Reserved',
                                    ];
                                @endphp

                                @foreach($options as $value => $label)
                                    <div>
                                        <input
                                            wire:model.live="editLotStatus"
                                            type="radio"
                                            id="editLotStatus{{ $value }}"
                                            name="editLotStatus"
                                            value="{{ $value }}"
                                            class="hidden peer"
                                        >

                                        <label
                                            for="editLotStatus{{ $value }}"
                                            class="inline-flex items-center justify-center w-full p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer
                                                peer-checked:border-2 peer-checked:border-blue-600 peer-checked:text-blue-600
                                                hover:text-gray-600 hover:bg-gray-100 transition text-sm font-medium"
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

                    @if ($editLotStatus && $editLotStatus !== 'available')
                        <div class="mt-3">
                            <x-select
                                label="Client Name"
                                wire:model.defer="editUserId"
                                placeholder="Select some client"
                                :async-data="route('api.users.index')"
                                :template="[
                                    'name'   => 'user-option',
                                    'config' => ['src' => 'profile_picture']
                                ]"
                                option-label="name"
                                option-value="id"
                                option-description="email"
                            />
                        </div>
                    @endif
                   
                    
                    @if ($editLotType && ($editLotType == 'Model House' || $editLotType == 'House & Lot'))
                        <div class="mt-3">
                            <x-select
                                label="House Model"
                                wire:model.defer="editHouseModelId"
                                placeholder="Select some house model"
                                :async-data="route('api.house-models.index')"
                                :template="[
                                    'name'   => 'user-option',
                                    'config' => ['src' => 'image']
                                ]"
                                option-label="name"
                                option-value="id"
                                option-description="description"
                            />
                        </div>
                    @endif

                    <div class="mt-3 flex items-center justify-between bg-gray-50 p-3 rounded-lg border">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700">
                                Under Construction
                            </h3>
                            <p class="text-xs text-gray-400">
                                Mark this lot as under construction
                            </p>
                        </div>

                        <x-toggle wire:model.defer="editIsUnderConstruction" />
                    </div>

                    <div class="mt-4">
                        @if($editLotImagePreview)
                            <div class="shrink-0">
                                <p class="text-sm font-medium text-gray-700 mb-2">
                                    Current Image
                                </p>

                                <img
                                    src="{{ asset('storage/' . $editLotImagePreview) }}"
                                    class="w-full h-auto object-cover rounded-xl border border-gray-200 shadow-sm"
                                >
                            </div>
                        @endif
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-2">
                            Lot Image
                        </label>

                        <x-filepond::upload
                            wire:model="editLotImage"
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
                            <x-button flat label="Cancel" @click="closeModal()" x-on:click="close" wire:click="reloadWeb" />
                            <x-button primary label="Save" type="submit" @click="$wire.editLotCoordinates = coordsString" />
                        </div>
                    </x-slot>

                </x-card>
            </form>

            <script>
                function editLotDrawer(
                    entangledCoords = '',
                    entangledType = '',
                    entangledStatus = ''
                ) {
                    return {
                        points: [],
                        coordsString: entangledCoords,
                        lotType: entangledType,
                        lotStatus: entangledStatus,
                        canvas: null,
                        ctx: null,
                        img: null,

                        init() {

                            this.canvas = this.$refs.canvas;
                            this.img = this.$refs.img;
                            this.ctx = this.canvas.getContext('2d');

                            // WATCH livewire updates
                            this.$watch('coordsString', (value) => {

                                if (!value) return;

                                const coords = value.split(',');

                                this.points = [];

                                for (let i = 0; i < coords.length; i += 2) {

                                    this.points.push({
                                        x: parseInt(coords[i]),
                                        y: parseInt(coords[i + 1])
                                    });

                                }

                                this.$nextTick(() => {
                                    this.redraw();
                                });

                            });

                            this.$watch('lotType', () => {
                                this.redraw();
                            });

                            this.$watch('lotStatus', () => {
                                this.redraw();
                            });

                            // initial draw if already loaded
                            if (this.coordsString) {

                                const coords = this.coordsString.split(',');

                                this.points = [];

                                for (let i = 0; i < coords.length; i += 2) {

                                    this.points.push({
                                        x: parseInt(coords[i]),
                                        y: parseInt(coords[i + 1])
                                    });

                                }

                                this.$nextTick(() => {
                                    this.redraw();
                                });

                            }

                            window.addEventListener('resize', () => this.redraw());

                        },

                        addPoint(e) {

                            if (!this.img.naturalWidth || !this.img.naturalHeight) return;

                            const x = Math.round(
                                (e.offsetX / this.img.clientWidth) * this.img.naturalWidth
                            );

                            const y = Math.round(
                                (e.offsetY / this.img.clientHeight) * this.img.naturalHeight
                            );

                            this.points.push({x, y});

                            this.updateCoordsString();

                            this.redraw();
                        },

                        resetPoints() {

                            this.points = [];
                            this.coordsString = '';

                            this.redraw();

                        },

                        updateCoordsString() {

                            this.coordsString = this.points
                                .map(p => `${p.x},${p.y}`)
                                .join(',');

                        },

                        redraw() {

                            if (!this.canvas || !this.img) return;

                            this.canvas.width = this.img.clientWidth;
                            this.canvas.height = this.img.clientHeight;

                            const ctx = this.ctx;

                            ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

                            if (this.points.length === 0) return;

                            ctx.beginPath();

                            this.points.forEach((p, i) => {

                                const x = p.x * this.img.clientWidth / this.img.naturalWidth;
                                const y = p.y * this.img.clientHeight / this.img.naturalHeight;

                                if (i === 0) ctx.moveTo(x, y);
                                else ctx.lineTo(x, y);

                            });

                            ctx.closePath();

                            const colors = {
                                "Playground & Community Amenities": "#f2b879",
                                "Model House": "#c8c9c3",
                                "Lot Only": "#c4e0b7",
                                "House & Lot": "#f8e89c",
                                "Sold": "#e9b4ae",
                            };

                            const status = (this.lotStatus || '')
                                .toLowerCase()
                                .trim();

                            const color = status === 'sold'
                                ? colors["Sold"]
                                : (colors[this.lotType] || "#0096ff");

                            ctx.fillStyle = hexToRGBA(color, 0.45);
                            ctx.fill();

                            ctx.strokeStyle = color;
                            ctx.lineWidth = 2;
                            ctx.stroke();

                            // draw points
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

            /*
             * IMPORTANT:
             * Wait for the original image dimensions.
             * All mapped lot coordinates are stored against
             * the natural/original map image size.
             */
            if (!img.complete || !img.naturalWidth || !img.naturalHeight) {

                img.addEventListener('load', drawLots, { once: true });

                return;
            }

            const rect = img.getBoundingClientRect();

            const displayWidth = rect.width;

            const displayHeight = rect.height;

            if (!displayWidth || !displayHeight) return;

            canvas.width = displayWidth;

            canvas.height = displayHeight;

            canvas.style.width = displayWidth + "px";

            canvas.style.height = displayHeight + "px";

            const ctx = canvas.getContext("2d");

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            /*
             * Always scale from the ORIGINAL database coordinates.
             *
             * jQuery-rwdImageMaps modifies area.coords whenever the
             * image changes size. Using area.coords here causes the
             * polygons to be scaled repeatedly and become scattered.
             */
            const scaleX = displayWidth / img.naturalWidth;

            const scaleY = displayHeight / img.naturalHeight;

            const colors = {

                "Playground & Community Amenities": "#f2b879",
                "Model House": "#c8c9c3",
                "Lot Only": "#c4e0b7",
                "House & Lot": "#f8e89c",
                "Sold": "#e9b4ae",

            };

            document.querySelectorAll('map[name="estate-map"] area').forEach(area => {

                /*
                 * data-coords is never changed by rwdImageMaps,
                 * so this remains the reliable source.
                 */
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

                const type = area.dataset.type;

                const status = (area.dataset.status || '')
                    .toLowerCase()
                    .trim();

                const color = status === 'sold'
                    ? colors["Sold"]
                    : (colors[type] || "#0096ff");

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

                const xValues = [];
                const yValues = [];

                for (let i = 0; i < coords.length; i += 2) {
                    xValues.push(coords[i]);
                    yValues.push(coords[i + 1]);
                }

                const cx =
                    (Math.min(...xValues) + Math.max(...xValues)) / 2;

                const cy =
                    (Math.min(...yValues) + Math.max(...yValues)) / 2;

                if (status === 'sold') {
                    const xValues = [];
                    const yValues = [];

                    for (let i = 0; i < coords.length; i += 2) {
                        xValues.push(coords[i]);
                        yValues.push(coords[i + 1]);
                    }

                    const centerX =
                        (Math.min(...xValues) + Math.max(...xValues)) / 2;

                    const centerY =
                        (Math.min(...yValues) + Math.max(...yValues)) / 2;

                    ctx.save();
                    ctx.globalAlpha = 0.8;
                    ctx.fillStyle = "#000000";

                    /*
                     * Keep SOLD readable while scaling with the map.
                     */
                    const soldFontSize = Math.max(
                        5,
                        Math.min(14, 14 * scaleX)
                    );

                    ctx.font = `bold ${soldFontSize}px Arial`;

                    ctx.textAlign = "center";
                    ctx.textBaseline = "middle";
                    ctx.fillText("SOLD", centerX, centerY);
                    ctx.restore();
                }

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
            const closeBtn = document.getElementById('tooltip-close');
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

                const hidePriceAndStatus = [
                    'Model House',
                    'Playground & Community Amenities'
                ].includes(propertyType);

                if (hidePriceAndStatus) {
                    // Hide price
                    tPrice.style.display = 'none';
                    tPrice.textContent = '';

                    // Hide status
                    tStatus.style.display = 'none';
                    tStatus.textContent = '';
                } else {
                    // Show price
                    tPrice.style.display = '';
                    tPrice.textContent =
                        '₱' + Number(area.dataset.price || 0).toLocaleString();

                    // Show status
                    tStatus.style.display = '';
                    tStatus.textContent = area.dataset.status ?? '';
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

                tTo.textContent = area.dataset.status + ' to'  ?? '';
                
                const status = (area.dataset.status ?? '').toLowerCase().trim();

                if (status === 'available') {
                    extraSection.style.display = 'none';

                    tModelPicture.src = area.dataset.modelImage || '/default-model.png';
                    tModelName.textContent = area.dataset.modelName || 'No Model';
                } else {
                    extraSection.style.display = 'block';

                    tUserPicture.src = area.dataset.userPicture || '/default-user.png';
                    tUserName.textContent = area.dataset.userName || 'No User';

                    tModelPicture.src = area.dataset.modelImage || '/default-model.png';
                    tModelName.textContent = area.dataset.modelName || 'No Model';

                }

                const hasModel =
                    area.dataset.modelName &&
                    area.dataset.modelName.trim() !== '';

                const supportsModel =
                    area.dataset.type === 'Model House' ||
                    area.dataset.type === 'House & Lot';

                if (supportsModel && hasModel) {
                    extraSectionModel.style.display = 'block';

                    tModelPicture.src =
                        area.dataset.modelImage || '/default-model.png';

                    tModelName.textContent =
                        area.dataset.modelName;
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
                

                tooltip.classList.remove('hidden');

                currentLot = {
                    id: area.dataset.id,
                    name: area.dataset.name
                };

                // 👇 IMPORTANT: wait for layout BEFORE initializing pannellum
                requestAnimationFrame(() => {
                    tooltipViewer = pannellum.viewer(panoContainer, {
                        type: "equirectangular",
                        panorama: area.dataset.image,
                        autoLoad: true,
                        showControls: false
                    });
                });

                move(area);
            }

            function getAreaCenter(area) {
                const coords = area.coords.split(',').map(Number);

                const xValues = [];
                const yValues = [];

                for (let i = 0; i < coords.length; i += 2) {
                    xValues.push(coords[i]);
                    yValues.push(coords[i + 1]);
                }

                return {
                    x: (Math.min(...xValues) + Math.max(...xValues)) / 2,
                    y: (Math.min(...yValues) + Math.max(...yValues)) / 2
                };
            }

            function move(area) {
                const center = getAreaCenter(area);

                const containerWidth = img.clientWidth;
                const containerHeight = img.clientHeight;

                const tooltipWidth = tooltip.offsetWidth;
                const tooltipHeight = tooltip.offsetHeight;

                const gap = 14;
                const padding = 10;

                let placedAbove = true;

                // Center tooltip horizontally over the lot
                let x = center.x - tooltipWidth / 2;
                let y = center.y - tooltipHeight - gap;

                // Not enough space above, place it below
                if (y < padding) {
                    y = center.y + gap;
                    placedAbove = false;
                }

                // Keep tooltip inside map
                x = Math.max(
                    padding,
                    Math.min(x, containerWidth - tooltipWidth - padding)
                );

                y = Math.max(
                    padding,
                    Math.min(y, containerHeight - tooltipHeight - padding)
                );

                tooltip.style.left = `${x}px`;
                tooltip.style.top = `${y}px`;

                const arrow = document.getElementById('tooltip-arrow');

                arrow.style.position = 'absolute';
                arrow.style.width = '0';
                arrow.style.height = '0';
                arrow.style.transform = 'translateX(-50%)';

                if (placedAbove) {
                    arrow.style.top = 'auto';
                    arrow.style.bottom = '-10px';

                    arrow.style.borderLeft = '10px solid transparent';
                    arrow.style.borderRight = '10px solid transparent';
                    arrow.style.borderTop = '10px solid white';
                    arrow.style.borderBottom = '0';
                } else {
                    arrow.style.top = '-10px';
                    arrow.style.bottom = 'auto';

                    arrow.style.borderLeft = '10px solid transparent';
                    arrow.style.borderRight = '10px solid transparent';
                    arrow.style.borderBottom = '10px solid white';
                    arrow.style.borderTop = '0';
                }

                // Lot center relative to tooltip
                let arrowX = center.x - x;

                arrowX = Math.max(
                    20,
                    Math.min(arrowX, tooltipWidth - 20)
                );

                arrow.style.left = `${arrowX}px`;
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
</div>
