<div class="w-full h-auto pt-40 bg-white">

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="text-center mb-16">
            <h1 class="text-4xl lg:text-5xl font-light text-gray-900 mb-4">Our Properties</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Explore our premium house models and available lots</p>
        </div>
        @if ($houseModels->count())
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($houseModels as $model)
                <div class="bg-white shadow-sm overflow-hidden hover:shadow-md transition-all group">
                    <div class="relative aspect-[4/3] overflow-hidden">
                        <img src="{{ $model->image 
                            ? asset('storage/' . $model->image) 
                            : 'https://images.unsplash.com/photo-1680868543815-b8666dba60f7' }}" alt="{{ $model->model_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    </div>
                    <div class="p-8">
                        <div class="mb-6">
                        <h3 class="text-3xl font-light text-gray-900">{{ $model->model_name }}</h3>
                        </div>
                        <div class="grid grid-cols-3 gap-6 mb-8 pb-8 border-b border-gray-100">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bed h-6 w-6 text-gray-400 mb-2 mx-auto">
                            <path d="M2 4v16"></path>
                            <path d="M2 8h18a2 2 0 0 1 2 2v10"></path>
                            <path d="M2 17h20"></path>
                            <path d="M6 8v9"></path>
                            </svg>
                            <div class="text-2xl font-light text-gray-900 mb-1">{{ $model->bedrooms }}</div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Bedrooms</div>
                        </div>
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bath h-6 w-6 text-gray-400 mb-2 mx-auto">
                            <path d="M10 4 8 6"></path>
                            <path d="M17 19v2"></path>
                            <path d="M2 12h20"></path>
                            <path d="M7 19v2"></path>
                            <path d="M9 5 7.621 3.621A2.121 2.121 0 0 0 4 5v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"></path>
                            </svg>
                            <div class="text-2xl font-light text-gray-900 mb-1">{{ $model->bathrooms }}</div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Bathrooms</div>
                        </div>
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-maximize h-6 w-6 text-gray-400 mb-2 mx-auto">
                            <path d="M8 3H5a2 2 0 0 0-2 2v3"></path>
                            <path d="M21 8V5a2 2 0 0 0-2-2h-3"></path>
                            <path d="M3 16v3a2 2 0 0 0 2 2h3"></path>
                            <path d="M16 21h3a2 2 0 0 0 2-2v-3"></path>
                            </svg>
                            <div class="text-2xl font-light text-gray-900 mb-1">{{ $model->floor_area }} sqm</div>
                            <div class="text-xs text-gray-600 uppercase tracking-wide">Floor Area</div>
                        </div>
                        </div>
                        <button 
                            wire:click="viewHouseTour({{ $model->id }})"
                            x-on:click="$openModal('viewTour')" 
                            class="w-full px-6 py-4 bg-gray-900 text-white hover:bg-gray-800 transition-colors flex items-center justify-center gap-2 text-sm uppercase tracking-wide"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-5 w-5">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>View 360° Virtual Tour 
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="col-span-3 text-center py-10 text-gray-500 border-2 border-dashed rounded-lg">
                <p class="italic text-gray-400">No house models found.</p>
            </div>
        @endif
    </div>
    
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

    {{-- <livewire:map.client-map-view-page /> --}}
    <livewire:map.client-leaflet-map-view-page />

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

                init() {
                    if (this.scenes.length === 0) {
                        return;
                    }

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

                    if (this.viewer) {
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
                                this.buildHotspots(scene),
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
