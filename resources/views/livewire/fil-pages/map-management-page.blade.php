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

            <map name="estate-map">
                @foreach($lots as $lot)
                    <area
                        shape="poly"
                        coords="{{ $lot->coords }}"
                        href="#"
                        wire:click.prevent="openLot({{ $lot->id }})"
                        title="{{ $lot->name }}"
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

    <script>
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
    </script>
</div>
