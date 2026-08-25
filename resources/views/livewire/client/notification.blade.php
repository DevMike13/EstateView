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
                x-data="{ showChanges: false }"
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
                                    (
                                        $notification->title !== 'Client Ledger Updated' &&
                                        !in_array(auth()->user()->role, ['agent'])
                                    )
                                    ||
                                    (
                                        auth()->user()->role === 'agent' &&
                                        $notification->type === 'property_credited_to_agent'
                                    )
                                )
                                    <button
                                        wire:click.stop="openNotification({{ $notification->id }})"
                                        class="text-xs text-green-600 hover:text-green-700 font-medium flex items-center gap-1"
                                    >
                                        <x-heroicon-o-arrow-top-right-on-square class="h-3 w-3" />
                                        Open
                                    </button>
                                @endif

                                {{-- NEW: View Changes trigger, only when there's a diff --}}
                                @if(
                                    $notification->type === 'client_personal_info_updated'
                                    && !empty($data['changes'])
                                )
                                    <button
                                        type="button"
                                        @click.stop="showChanges = true"
                                        class="text-xs text-primary-600 hover:text-primary-700 font-medium flex items-center gap-1"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        View Changes
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

                {{-- NEW: Changes modal --}}
                @if(
                    $notification->type === 'client_personal_info_updated'
                    && !empty($data['changes'])
                )
                    <div
                        x-show="showChanges"
                        x-cloak
                        x-transition.opacity
                        class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/40 p-4"
                        @click.self="showChanges = false"
                    >
                        <div
                            x-show="showChanges"
                            x-transition
                            @click.stop
                            class="bg-white rounded-lg shadow-xl w-full max-w-sm p-5"
                        >
                            <div class="relative mb-4 border-b pb-3 pr-8">
                                <h3 class="text-sm font-semibold text-gray-900">
                                    Changed Information
                                </h3>

                                {{-- CLOSE BUTTON --}}
                                <button
                                    type="button"
                                    @click="showChanges = false"
                                    class="absolute -top-1 right-0 flex h-7 w-7 items-center justify-center
                                        rounded-full text-gray-400 transition
                                        hover:bg-gray-100 hover:text-gray-700"
                                    title="Close"
                                >
                                    <x-heroicon-o-x-mark class="w-5 h-5" />
                                </button>
                            </div>

                            <div class="space-y-3">
                                @foreach($data['changes'] as $change)
                                    @continue(!is_array($change) || !isset($change['label']))
                                    <div class="text-xs">
                                        <span class="font-medium text-gray-600">{{ $change['label'] }}</span>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <span class="line-through text-gray-400">{{ $change['old'] ?? '—' }}</span>
                                            <span>→</span>
                                            <span class="text-gray-800 font-medium">{{ $change['new'] ?? '—' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{-- CLOSE BUTTON --}}
                            <div class="flex justify-end mt-5 pt-3 border-t">
                                <button
                                    type="button"
                                    @click="showChanges = false"
                                    class="inline-flex items-center justify-center px-4 py-2
                                        text-xs font-medium text-gray-700 bg-white
                                        border border-gray-300 rounded-lg
                                        hover:bg-gray-50 hover:text-gray-900
                                        transition"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

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