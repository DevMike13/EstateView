<div class="flex-1 min-w-0">
    <div class="w-full mb-5 mx-auto p-4 bg-[#f9fafc] rounded-xl shadow">
        <!-- Tab Nav -->
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex gap-x-1 whitespace-nowrap min-w-max">

                <!-- Pending -->
                <button wire:click="setTab('pending')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'pending'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}">
                    <span>Pending</span>

                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->pendingCount }}
                    </span>
                </button>

                <!-- Approved -->
                <button wire:click="setTab('approved')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'approved'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}">
                    <span>Approved</span>
                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->approvedCount }}
                    </span>
                </button>

                <!-- Completed -->
                <button wire:click="setTab('completed')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'completed'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}">
                    <span>Completed</span>
                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->completedCount }}
                    </span>
                </button>

                <!-- Declined -->
                <button wire:click="setTab('declined')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'declined'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}">
                    <span>Declined</span>
                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->declinedCount }}
                    </span>
                </button>

                <button wire:click="setTab('cancelled')"
                    class="py-4 px-4 text-sm font-medium border-b-2 flex items-center gap-2
                    {{ $activeTab === 'cancelled'
                        ? 'border-[#129c45] text-[#129c45]'
                        : 'border-transparent text-gray-500' }}">
                    <span>Cancelled</span>
                    <span class="w-5 h-5 flex items-center justify-center rounded-full text-xs bg-gray-200">
                        {{ $this->cancelledCount }}
                    </span>
                </button>

            </nav>
        </div>
        <!-- End Tab Nav -->

        <!-- Tab Content -->
        <div class="mt-5">

            @forelse($this->appointments as $appointment)

                <div
                    x-data="{
                        open: @js($highlight == $appointment->id),
                        shouldScroll: @js($highlight == $appointment->id),
                    }"
                    x-init="
                        if (shouldScroll) {
                            setTimeout(() => {
                                $el.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            }, 500);
                        }
                    "
                    class="bg-white rounded-xl shadow border mb-3 overflow-hidden
                        {{ $highlight == $appointment->id ? 'ring-2 ring-green-500' : '' }}"
                >
                    {{-- CLICKABLE CLIENT HEADER --}}
                    <button
                        type="button"
                        x-on:click="open = !open"
                        class="w-full flex items-center justify-between gap-3 p-4 text-left
                            hover:bg-gray-50 transition"
                    >
                        <div class="flex items-center gap-3 min-w-0">

                            <div class="h-10 w-10 bg-green-100 rounded-lg
                                        flex items-center justify-center flex-shrink-0">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="h-5 w-5 text-green-600"
                                >
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate">
                                    {{ $appointment->name }}
                                </h3>

                                <p class="text-xs text-gray-500">
                                    Click to view full details
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">

                            <span class="hidden sm:inline-flex px-3 py-1 rounded-full text-xs font-medium
                                {{
                                    match($appointment->status) {
                                        'pending'   => 'bg-yellow-100 text-yellow-700',
                                        'approved'  => 'bg-green-100 text-green-700',
                                        'completed' => 'bg-blue-100 text-blue-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        'declined',
                                        'rejected'  => 'bg-red-100 text-red-700',
                                        default     => 'bg-gray-100 text-gray-700',
                                    }
                                }}"
                            >
                                {{ $appointment->status === 'pending'
                                    ? 'Pending Review'
                                    : ucfirst($appointment->status) }}
                            </span>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="text-gray-500 transition-transform duration-200"
                                x-bind:class="{ 'rotate-180': open }"
                            >
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </div>
                    </button>

                    {{-- EXPANDED DETAILS --}}
                    <div
                        x-show="open"
                        x-collapse
                        class="border-t border-gray-100"
                    >
                        <div class="p-5">

                            {{-- MOBILE STATUS --}}
                            <div class="sm:hidden mb-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium
                                    {{
                                        match($appointment->status) {
                                            'pending'   => 'bg-yellow-100 text-yellow-700',
                                            'approved'  => 'bg-green-100 text-green-700',
                                            'completed' => 'bg-blue-100 text-blue-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            'declined',
                                            'rejected'  => 'bg-red-100 text-red-700',
                                            default     => 'bg-gray-100 text-gray-700',
                                        }
                                    }}"
                                >
                                    {{ $appointment->status === 'pending'
                                        ? 'Pending Review'
                                        : ucfirst($appointment->status) }}
                                </span>
                            </div>

                            {{-- CONTACT DETAILS --}}
                            <div class="grid sm:grid-cols-2 gap-4 mb-4">

                                <div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Email Address
                                    </div>

                                    <div class="text-sm font-medium text-gray-900 break-all">
                                        {{ $appointment->user?->email ?? 'No email address' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Phone Number
                                    </div>

                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $appointment->user?->info?->phone ?? 'No phone number' }}
                                    </div>
                                </div>
                            </div>

                            {{-- APPOINTMENT DETAILS --}}
                            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">

                                <div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Appointment Type
                                    </div>

                                    <div class="font-medium text-gray-900">
                                        {{ $appointment->appointment_type ?? 'Not specified' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Date
                                    </div>

                                    <div class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Time
                                    </div>

                                    <div class="font-medium text-gray-900 flex items-center gap-1">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-4 w-4"
                                        >
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>

                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500 mb-1">
                                        Submitted
                                    </div>

                                    <div class="font-medium text-gray-900">
                                        {{ $appointment->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            {{-- NOTES --}}
                            @if(filled($appointment->notes))
                                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="text-xs font-medium text-blue-900 mb-1">
                                        Notes
                                    </div>

                                    <div class="text-sm text-blue-800 whitespace-pre-line">
                                        {{ $appointment->notes }}
                                    </div>
                                </div>
                            @endif

                            {{-- ACTION BUTTONS --}}
                            <div class="flex flex-col sm:flex-row gap-3 w-full">

                                @if($activeTab === 'pending')
                                    <x-button
                                        wire:click="confirmApprove({{ $appointment->id }})"
                                        icon="check"
                                        label="Approve"
                                        class="w-full bg-[#00a73e] hover:bg-green-900
                                            text-white rounded-lg font-semibold"
                                    />

                                    <x-button
                                        wire:click="confirmDecline({{ $appointment->id }})"
                                        icon="x-mark"
                                        label="Decline"
                                        class="w-full border border-red-400 text-red-900
                                            rounded-lg font-semibold"
                                    />

                                @elseif($activeTab === 'approved')
                                    <x-button
                                        wire:click="confirmComplete({{ $appointment->id }})"
                                        icon="check"
                                        label="Mark as Completed"
                                        class="w-full bg-blue-600 hover:bg-blue-800
                                            text-white rounded-lg font-semibold"
                                    />

                                @elseif($activeTab === 'declined')
                                    <x-button
                                        wire:click="confirmRestore({{ $appointment->id }})"
                                        icon="arrow-uturn-left"
                                        label="Restore"
                                        class="w-full bg-gray-600 hover:bg-gray-800
                                            text-white rounded-lg font-semibold"
                                    />
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="text-center py-10 text-gray-500 border-2 border-dashed rounded-lg">
                    <p class="italic text-gray-400">
                        No appointments found.
                    </p>
                </div>
            @endforelse

        </div>
        <!-- End Tab Content -->
    </div>


    <div class="w-full max-w-4xl mx-auto p-4 bg-[#f9fafc] rounded-xl shadow">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-4">

            <x-button.circle  wire:click="previousMonth" icon="chevron-left" />

            <h2 class="text-lg font-semibold">
                {{ $currentMonth->format('F Y') }}
            </h2>

            <x-button.circle  wire:click="nextMonth" icon="chevron-right" />
        </div>

        {{-- WEEKDAYS --}}
        <div class="grid grid-cols-7 text-center text-xs text-gray-500 mb-2">
            <div>Sun</div><div>Mon</div><div>Tue</div>
            <div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
        </div>

        {{-- GRID --}}
        <div class="grid grid-cols-7 gap-2">

            {{-- EMPTY START --}}
            @for ($i = 0; $i < $startDay; $i++)
                <div></div>
            @endfor

            {{-- DATES --}}
            @foreach($dates as $date)

                @php
                    $isPast = $date['past'];
                    $isBlocked = in_array($date['date'], $blocked);
                    $isSelected = in_array($date['date'], $selectedDates);

                    $classes = '';

                    // 🚫 PAST
                    if ($isPast) {
                        $classes = 'bg-transparent text-gray-300 cursor-not-allowed';
                    }

                    // 🔴 BLOCKED
                    elseif ($isBlocked) {
                        $classes = 'bg-red-500 text-white relative';
                    }

                    // ⚪ AVAILABLE
                    else {
                        $classes = 'bg-gray-100 text-gray-700 hover:bg-gray-200';
                    }

                    // 🔵 SELECTED
                    if ($isSelected && !$isPast) {
                        $classes .= ' ring-2 ring-blue-500';
                    }
                @endphp

                <button
                    wire:click="toggleDate('{{ $date['date'] }}')"
                    @disabled($isPast)
                    class="
                        h-10 rounded-xl transition
                        flex items-center justify-center
                        {{ $classes }}
                    "
                >
                    {{ $date['day'] }}

                    @if($isBlocked)
                        <span
                            wire:click.stop="confirmRemoveBlockedDate('{{ $date['date'] }}')"
                            class="absolute top-1 right-1 text-xs bg-white text-red-600 px-1 rounded-full cursor-pointer"
                        >
                            ×
                        </span>
                    @endif
                </button>

            @endforeach

        </div>

        <div class="mt-4">
            <button
                wire:click="confirmBlockDates"
                class="px-4 py-2 bg-black text-white rounded-lg disabled:opacity-50"
                @disabled(empty($selectedDates))
            >
                Save Blocked Dates
            </button>
        </div>

    </div>
</div>