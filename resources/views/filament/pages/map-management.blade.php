<x-filament-panels::page>
    @livewireScripts
    @livewireStyles
    <wireui:scripts />
    @vite(['resources/css/custom.css', 'resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css">
    <livewire:fil-pages.map-management-page />

    <script>
        window.addEventListener('reload', event => {
            window.location.reload();
        })
        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.js"></script>
    @filepondScripts
</x-filament-panels::page>
