<div
    x-data="{ open: @entangle('open') }"
    x-on:toggle-custom-sidebar.window="open = !open"
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-x-full"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-full"
    class="fixed top-0 right-0 z-[9999] w-[420px] h-screen bg-white dark:bg-gray-900 shadow-xl border-l"
>
    <!-- HEADER -->
    <div class="p-4 flex justify-between items-center border-b">
        <h2 class="text-lg font-semibold">Notifications</h2>

        <div class="flex gap-3 text-xs">
            <button
                wire:click="markAllRead"
                class="text-blue-600 hover:underline"
            >
                Mark All Read
            </button>

            <button @click="open = false">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
    </div>

    <!-- LIST -->
    <div class="p-4 space-y-3 overflow-y-auto h-[calc(100%-64px)]">

        @forelse($this->notifications as $notification)

            <div class="relative p-3 border rounded transition
                {{ $notification->pivot->read_at ? 'bg-white' : 'bg-blue-50 border-blue-200' }}"
            >
                @if(!$notification->pivot->read_at)
                    <span class="absolute top-2 right-2 flex h-2.5 w-2.5">
                        
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                        
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>

                    </span>
                @endif
                @if(!empty($notification->data))
                    @php $data = $notification->data; @endphp
                        <div class="flex justify-between">
                        
                            <div class="flex-1">

                                <p class="font-semibold text-sm">
                                    {{ $notification->title }}
                                </p>

                                <p class="text-sm text-gray-600">
                                    {{ $notification->message }}
                                </p>

                            

                                <div class="mt-2 text-xs text-gray-500 space-y-1 border-t pt-2">

                                    @if(isset($data['client_name']))
                                        <p>
                                            <span class="font-semibold text-gray-600">Client:</span>
                                            {{ $data['client_name'] }}
                                        </p>
                                    @endif

                                    @if(isset($data['lot_name']))
                                        <p>
                                            <span class="font-semibold text-gray-600">Lot:</span>
                                            {{ $data['lot_name'] }}
                                        </p>
                                    @endif

                                    @if(isset($data['house_model']))
                                        <p>
                                            <span class="font-semibold text-gray-600">House Model:</span>
                                            {{ $data['house_model'] }}
                                        </p>
                                    @endif

                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'approved' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            'completed' => 'bg-blue-100 text-blue-700',
                                            'declined' => 'bg-red-100 text-red-700',
                                            'cancelled' => 'bg-gray-200 text-gray-700',
                                        ];
                                    @endphp

                                    @if(isset($data['status']))
                                        <p>
                                            <span class="font-semibold text-gray-600">Status:</span>
                                            @php
                                                $status = $data['status'] ?? 'pending';
                                            @endphp

                                            <span class="px-2 py-0.5 text-xs rounded
                                                {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-700' }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </p>
                                    @endif

                                    @if(isset($data['reservation_id']))
                                        <p>
                                            <span class="font-semibold text-gray-600">Ref #:</span>
                                            #{{ $data['reservation_id'] }}
                                        </p>
                                    @endif

                                </div>
                                

                                <span class="text-xs text-gray-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>

                            </div>
                        </div>

                    

                    <div class="flex items-center">
                        @if($notification->url)
                            <a
                                href="{{ $notification->url }}"
                                class="text-xs text-primary-600 mt-2 inline-flex items-center gap-1 hover:underline"
                            >
                                Open
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-3.5 h-3.5">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>

                            </a>
                        @endif
                        @if(!$notification->pivot->read_at)
                            <x-button right-icon="eye" label="Mark as read" class="max-h-10 border-none text-xs ml-auto"  wire:click="markAsRead({{ $notification->id }})" />
                        @endif
                    </div>

                @endif

            </div>

        @empty

            <p class="text-sm text-gray-500">
                No notifications
            </p>

        @endforelse

    </div>
</div>