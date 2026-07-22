<div class="min-h-screen bg-gray-50 pb-16 pt-40">
  <div class="max-w-4xl mx-auto px-6 sm:px-8 lg:px-12">
    <div class="mb-8">
      <h1 class="text-4xl font-light text-gray-900 mb-3">Notifications</h1>
      <div class="flex items-center justify-between">
        <p class="text-gray-600">
            You have {{ $this->unreadCount }} unread
            notification{{ $this->unreadCount != 1 ? 's' : '' }}
        </p>
        <button  wire:click="markAllAsRead" class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-4 w-4">
            <path d="M20 6 9 17l-5-5"></path>
          </svg>Mark all as read </button>
      </div>
    </div>
    <div class="space-y-3" wire:poll.5s>

        @forelse($this->notifications as $notification)

            @php
                $isUnread = !$notification->pivot->read_at;
                $data = $notification->data ?? [];
                $clientUrl = $data['client_url'] ?? null;

                $status = strtolower($data['status'] ?? '');

                $iconBg = 'bg-gray-100 text-gray-600';

                if (in_array($status, ['approved', 'completed'])) {
                    $iconBg = 'bg-green-100 text-green-600';
                } elseif (in_array($status, ['pending'])) {
                    $iconBg = 'bg-yellow-100 text-yellow-600';
                } elseif (in_array($status, ['rejected', 'declined'])) {
                    $iconBg = 'bg-red-100 text-red-600';
                } elseif (in_array($status, ['cancelled'])) {
                    $iconBg = 'bg-gray-100 text-gray-600';
                } elseif ($notification->type === 'appointment_updated') {
                    $iconBg = 'bg-blue-100 text-blue-600';
                }
            @endphp

            <div
                class="bg-white rounded-xl p-6 shadow-sm border transition-all
                {{ $isUnread ? 'border-blue-200 bg-blue-50/30' : 'border-gray-100' }}"
            >
                <div class="flex gap-4">

                    {{-- ICON --}}
                    <div class="flex-shrink-0 h-10 w-10 rounded-lg flex items-center justify-center {{ $iconBg }}">

                        @if(str_contains($notification->type, 'appointment'))
                            <x-heroicon-o-calendar class="h-5 w-5" />
                        @elseif(str_contains($notification->type, 'reservation'))
                            <x-heroicon-o-home class="h-5 w-5" />
                        @else
                            <x-heroicon-o-bell class="h-5 w-5" />
                        @endif

                    </div>

                    <div class="flex-1 min-w-0">

                        <div class="flex items-start justify-between gap-4 mb-2">

                            <div>
                                <h3 class="font-medium text-gray-900">
                                    {{ $notification->title }}
                                </h3>

                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $notification->message }}
                                </p>

                                @if(isset($data['status']))
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'approved' => 'bg-green-100 text-green-700',
                                            'completed' => 'bg-blue-100 text-blue-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            'declined' => 'bg-red-100 text-red-700',
                                            'cancelled' => 'bg-gray-100 text-gray-700',
                                        ];
                                    @endphp

                                    <div class="mt-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium
                                            {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- UNREAD DOT --}}
                            @if($isUnread)
                                <div class="h-2 w-2 bg-blue-600 rounded-full flex-shrink-0 mt-2"></div>
                            @endif

                        </div>

                        <div class="flex items-center gap-4 mt-4">

                            <span class="text-xs text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>

                            <div class="flex gap-3">

                                @if($isUnread)
                                    <button
                                        wire:click="markAsRead({{ $notification->id }})"
                                        class="text-xs text-blue-600 hover:text-blue-700 font-medium"
                                    >
                                        Mark as read
                                    </button>
                                @endif

                                @if(
                                    $notification->title !== 'Client Ledger Updated' &&
                                    auth()->user()->role !== 'agent'
                                )
                                    <button
                                        wire:click.stop="openNotification({{ $notification->id }})"
                                        class="text-xs text-green-600 hover:text-green-700 font-medium flex items-center gap-1"
                                    >
                                        <x-heroicon-o-arrow-top-right-on-square class="h-3 w-3" />
                                        Open
                                    </button>
                                @endif

                                <button
                                    wire:click="deleteNotification({{ $notification->id }})"
                                    class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center gap-1"
                                >
                                    <x-heroicon-o-trash class="h-3 w-3" />
                                    Delete
                                </button>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

        @empty

            <div class="bg-white rounded-xl p-10 border text-center">
                <x-heroicon-o-bell-slash class="w-12 h-12 mx-auto text-gray-300 mb-3" />

                <h3 class="text-lg font-medium text-gray-700">
                    No Notifications
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    You're all caught up.
                </p>
            </div>

        @endforelse

    </div>
  </div>
</div>