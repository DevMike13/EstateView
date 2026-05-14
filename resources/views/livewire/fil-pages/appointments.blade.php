<div class="w-full h-auto">
    <div class="w-full max-w-4xl mb-5 mx-auto p-4 bg-[#f9fafc] rounded-xl shadow">
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

            </nav>
        </div>
        <!-- End Tab Nav -->

        <!-- Tab Content -->
        <div class="mt-5">

            @forelse($this->appointments as $appointment)

                <div class="bg-white rounded-xl p-5 shadow border mb-4">
                    
                    <div class="flex items-start mb-4 gap-3 md:gap-4">
                        <div class="h-10 w-10 md:h-12 md:w-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-5 w-5 md:h-6 md:w-6 text-green-600">
                            <path d="M8 2v4"></path>
                            <path d="M16 2v4"></path>
                            <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                            <path d="M3 10h18"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mb-1">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900">{{ $appointment->name }}</h3>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">{{ $appointment->status === 'pending' ? 'Pending Review' : ucfirst($appointment->status) }}</span>
                            </div>
                            <div class="text-xs md:text-sm text-gray-600 space-y-1">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user h-4 w-4 flex-shrink-0">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="break-all">{{ $appointment->user->email }}</span>
                                <span class="hidden sm:inline">•</span>
                                <span class="hidden sm:inline">{{ $appointment->user->info->phone }}</span>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-4 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Appointment Type</div>
                            <div class="font-medium text-gray-900">{{ $appointment->appointment_type ?? '' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Date</div>
                            <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Time</div>
                            <div class="font-medium text-gray-900 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 mb-1">Submitted</div>
                            <div class="font-medium text-gray-900">{{ $appointment->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    @if(!empty($appointment->notes))
                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="text-xs font-medium text-blue-900 mb-1">
                                Notes
                            </div>

                            <div class="text-sm text-blue-800">
                                {{ $appointment->notes }}
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center gap-5 w-full">
                        @if($activeTab === 'pending')
                            <x-button 
                                wire:click="confirmApprove({{ $appointment->id }})"
                                icon="check" 
                                label="Approve" 
                                class="w-full bg-[#00a73e] hover:bg-green-900 text-white rounded-lg font-semibold" 
                            />
                            <x-button 
                                wire:click="confirmDecline({{ $appointment->id }})"
                                icon="x-mark" 
                                label="Decline" 
                                class="w-full border border-red-400 text-red-900 rounded-lg font-semibold" 
                            />
                        @elseif($activeTab === 'approved')
                            <x-button
                                icon="check"
                                label="Mark Completed"
                                wire:click="confirmComplete({{ $appointment->id }})"
                                class="w-full bg-blue-600 hover:bg-blue-800 text-white rounded-lg font-semibold"
                            />

                        @elseif($activeTab === 'declined')

                            <x-button
                                icon="arrow-uturn-left"
                                label="Restore"
                                wire:click="confirmRestore({{ $appointment->id }})"
                                class="w-full bg-gray-600 hover:bg-gray-800 text-white rounded-lg font-semibold"
                            />

                        @endif
                    </div>
                </div>

            @empty
                <div class="col-span-3 text-center py-10 text-gray-500 border-2 border-dashed rounded-lg">
                    <p class="italic text-gray-400">No appointments found.</p>
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