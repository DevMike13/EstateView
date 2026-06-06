<div
    wire:poll.5s
    x-data
    x-on:click="$dispatch('toggle-custom-sidebar')"
    class="relative flex items-center justify-center w-10 h-10 rounded-full cursor-pointer
           text-gray-600 hover:text-primary-600 hover:bg-gray-100
           dark:text-gray-300 dark:hover:bg-gray-800 transition"
>
    <x-heroicon-o-bell class="w-6 h-6" />

    @if($this->count > 0)
        <span class="absolute top-1 right-1 w-4 h-4 flex items-center justify-center
                     bg-red-500 text-white text-xs rounded-full">
            {{ $this->count > 99 ? '99+' : $this->count }}
        </span>
    @endif
</div>