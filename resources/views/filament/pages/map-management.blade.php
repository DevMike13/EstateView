<x-filament-panels::page>
    <wireui:scripts />
    @livewireStyles
    @livewireScripts

    @vite(['resources/css/custom.css', 'resources/css/app.css', 'resources/js/app.js'])
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.css">

    <livewire:fil-pages.map-management-page />

    <x-dialog z-index="z-50" blur="md" align="center" />
    <script>
        window.addEventListener('reload', event => {
            window.location.reload();
        })
        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/pannellum/build/pannellum.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    @filepondScripts
</x-filament-panels::page>
