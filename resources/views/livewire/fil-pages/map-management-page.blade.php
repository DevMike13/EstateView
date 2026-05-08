<div>
    <div class="mb-5 flex justify-end">
        <x-button label="New Lot Area" x-on:click="$openModal('newLot')" primary />
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

        <script>
            document.addEventListener("livewire:init", () => {
                pannellum.viewer('panorama', {
                    type: "equirectangular",
                    panorama: "{{ asset('images/shot-panoramic-composition-living-room.jpg') }}",
                    autoLoad: true
                });
            });
        </script>

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
</div>
