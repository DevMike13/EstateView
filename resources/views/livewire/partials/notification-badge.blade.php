<a
    wire:poll.5s
    class="relative flex items-center justify-center w-10 h-10 rounded-full cursor-pointer {{ request()->is('client/notifications') ? 'bg-gray-100 text-primary-600' : 'text-gray-600' }} hover:text-primary-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 transition"
    href="{{ route('client.notification') }}"
>
    <x-heroicon-o-bell class="w-6 h-6" />

    @if($this->unreadCount > 0)
        <span class="absolute top-1 right-1 w-4 h-4 flex items-center justify-center bg-red-500 text-white text-xs rounded-full">
            {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
        </span>
    @endif
</a>