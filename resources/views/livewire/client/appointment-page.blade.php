<div class="w-full h-auto pt-10 md:pt-40 p-5 md:p-0">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-light text-gray-900 mb-3">My Appointments</h1>
        <p class="text-gray-600">Book and manage your property visits and consultations</p>
    </div>
    {{-- MY APPOINTMENTS --}}
    <div class="w-full max-w-5xl mx-auto mt-10">

        <h2 class="text-xl font-semibold mb-4">
            My Appointments
        </h2>

        <div class="w-full mb-5 mx-auto p-4 bg-[#f9fafc] rounded-xl shadow">

            {{-- TAB NAV --}}
            <div class="border-b border-gray-200 overflow-x-auto">
                <nav class="flex gap-x-1 whitespace-nowrap min-w-max">

                    {{-- Pending --}}
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

                    {{-- Approved --}}
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

                    {{-- Completed --}}
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

                    {{-- Cancelled --}}
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

            {{-- TAB CONTENT --}}
            <div class="space-y-4 mt-5">

                @forelse($this->appointments as $appointment)

                    @php

                        $statusConfig = match($appointment->status) {
                            'pending' => [
                                'bg' => 'bg-yellow-100',
                                'text' => 'text-yellow-700',
                                'label' => 'Pending Review'
                            ],
                            'approved' => [
                                'bg' => 'bg-green-100',
                                'text' => 'text-green-700',
                                'label' => 'Confirmed'
                            ],
                            'completed' => [
                                'bg' => 'bg-blue-100',
                                'text' => 'text-blue-700',
                                'label' => 'Completed'
                            ],
                            'cancelled' => [
                                'bg' => 'bg-red-100',
                                'text' => 'text-red-700',
                                'label' => 'Cancelled'
                            ],
                            default => [
                                'bg' => 'bg-gray-100',
                                'text' => 'text-gray-700',
                                'label' => ucfirst($appointment->status)
                            ]
                        };

                    @endphp

                    <div
                        x-data="{
                            shouldScroll: @js((int) $highlight === (int) $appointment->id)
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
                        class="bg-white rounded-xl shadow-sm border border-gray-100 p-6
                            {{ (int) $highlight === (int) $appointment->id
                                ? 'ring-2 ring-green-500 bg-green-50/30'
                                : '' }}"
                    >

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                            <div class="flex-1">

                                {{-- HEADER --}}
                                <div class="flex flex-wrap items-center gap-3 mb-3">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="h-5 w-5 text-gray-600">

                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                        <path d="M3 10h18"></path>

                                    </svg>

                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $appointment->appointment_type }}
                                    </h3>

                                    <span class="px-3 py-1 rounded-full text-xs font-medium flex items-center gap-1
                                        {{ $statusConfig['bg'] }}
                                        {{ $statusConfig['text'] }}">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-3 w-3">

                                            <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                                            <path d="m9 11 3 3L22 4"></path>

                                        </svg>

                                        {{ $statusConfig['label'] }}

                                    </span>
                                    {{-- VIEW ATTACHMENT --}}
@if($appointment->document_path)
    <a
        href="{{ asset('storage/' . $appointment->document_path) }}"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
               text-xs font-medium text-blue-600 bg-blue-50
               hover:bg-blue-100 hover:text-blue-700 transition"
    >
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
            class="h-3.5 w-3.5"
        >
            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>

        View Attachment
    </a>
@endif

                                </div>

                                {{-- DATE + TIME --}}
                                <div class="grid sm:grid-cols-2 gap-3 text-sm text-gray-600">

                                    <div class="flex items-center gap-2">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-4 w-4">

                                            <path d="M8 2v4"></path>
                                            <path d="M16 2v4"></path>
                                            <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                            <path d="M3 10h18"></path>

                                        </svg>

                                        <span>
                                            {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('F d, Y') }}
                                        </span>

                                    </div>

                                    <div class="flex items-center gap-2">

                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="h-4 w-4">

                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>

                                        </svg>

                                        <span>
                                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                        </span>

                                    </div>

                                </div>

                                {{-- NOTES --}}
                                @if($appointment->notes)

                                    <div class="mt-3 text-sm text-gray-600">
                                        <span class="font-medium">Notes:</span>
                                        {{ $appointment->notes }}
                                    </div>

                                @endif

                                {{-- CREATED --}}
                                <div class="mt-3 text-xs text-gray-400">
                                    Submitted {{ $appointment->created_at->diffForHumans() }}
                                </div>

                            </div>

                            {{-- ACTIONS --}}
                            <div class="md:ml-4">

                                @if($appointment->status === 'pending')

                                    <x-button
                                        wire:click="confirmCancelAppointment({{ $appointment->id }})"
                                        label="Cancel Appointment"
                                        icon="x-mark"
                                        negative
                                    />

                                @endif

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="bg-white rounded-xl border border-dashed p-10 text-center">

                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-10 w-10 mx-auto text-gray-300 mb-3"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2z"/>

                        </svg>

                        <p class="text-gray-500">
                            No appointments found.
                        </p>

                    </div>

                @endforelse

            </div>

        </div>

    </div>
    <div class="w-full h-auto max-w-5xl mx-auto">

        <h2 class="text-xl font-extralight mb-5">
            Book New Appointment
        </h2>

        <div class="w-full max-w-5xl mx-auto p-8 bg-white rounded-2xl shadow">

            <h4 class="text-sm font-semibold mb-5">
                Select Date
            </h4>

            <div class="w-full mx-auto p-4 bg-[#f9fafc] rounded-2xl">

                {{-- CALENDAR HEADER --}}
                <div class="flex items-center justify-between mb-6">

                    <x-button.circle
                        wire:click="previousMonth"
                        icon="chevron-left"
                    />

                    <h2 class="text-lg font-semibold">
                        {{ $currentMonth->format('F Y') }}
                    </h2>

                    <x-button.circle
                        wire:click="nextMonth"
                        icon="chevron-right"
                    />

                </div>

                {{-- WEEKDAYS --}}
                <div class="grid grid-cols-7 gap-2 mb-2 text-center text-xs font-medium text-gray-500">

                    <div>Sun</div>
                    <div>Mon</div>
                    <div>Tue</div>
                    <div>Wed</div>
                    <div>Thu</div>
                    <div>Fri</div>
                    <div>Sat</div>

                </div>

                {{-- CALENDAR --}}
                <div class="grid grid-cols-7 gap-2">

                    {{-- EMPTY CELLS BEFORE FIRST DAY --}}
                    @for(
                        $i = 0;
                        $i < $startDay;
                        $i++
                    )
                        <div></div>
                    @endfor

                    {{-- DAYS --}}
                    @foreach(
                        $dates
                        as $date
                    )

                        @php
                            $isPast =
                                $date['past'];

                            $isAvailable =
                                $date['available'];

                            $isSelected =
                                $selectedDate
                                    === $date['date'];

                            $isToday =
                                $date['date']
                                    === now()
                                        ->format(
                                            'Y-m-d'
                                        );
                        @endphp

                        <button
                            type="button"
                            wire:click="
                                selectDate(
                                    '{{ $date['date'] }}'
                                )
                            "
                            @disabled(
                                $isPast
                                || ! $isAvailable
                            )
                            class="
                                relative
                                h-10
                                rounded-xl
                                transition
                                flex
                                items-center
                                justify-center

                                {{ $isPast
                                    ? 'bg-transparent text-gray-300 cursor-not-allowed'
                                    : (
                                        ! $isAvailable
                                        ? 'bg-blue-100 text-blue-600 cursor-not-allowed'
                                        : 'bg-[#f3f4f6] text-gray-700 hover:bg-gray-200 cursor-pointer'
                                    )
                                }}

                                {{ $isSelected
                                    ? '!bg-[#101727] !text-white'
                                    : ''
                                }}

                                {{ $isToday && ! $isSelected
                                    ? 'ring-2 ring-green-400'
                                    : ''
                                }}
                            "
                        >

                            <span class="text-sm font-semibold">
                                {{ $date['day'] }}
                            </span>

                            @if($isToday)

                                <span
                                    class="
                                        absolute
                                        bottom-0.5
                                        h-1
                                        w-1
                                        rounded-full

                                        {{ $isSelected
                                            ? 'bg-white'
                                            : 'bg-green-500'
                                        }}
                                    "
                                ></span>

                            @endif

                        </button>

                    @endforeach

                </div>

            </div>

            {{-- SELECTED DATE --}}
            @if($selectedDate)

                <div class="mt-5 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">

                    <p class="text-xs text-gray-500">
                        Selected date
                    </p>

                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ Carbon\Carbon::parse(
                            $selectedDate
                        )->format(
                            'l, F d, Y'
                        ) }}
                    </p>

                </div>

            @endif

            @error('selectedDate')

                <p class="mt-2 text-xs text-red-500">
                    {{ $message }}
                </p>

            @enderror

            {{-- LEGEND --}}
            <div class="flex flex-wrap gap-4 items-center my-5 text-xs">

                {{-- AVAILABLE --}}
                <div class="flex items-center gap-2">

                    <span class="w-7 h-7 rounded bg-[#f3f4f6] border"></span>

                    <span class="text-gray-600">
                        Available
                    </span>

                </div>

                {{-- TODAY --}}
                <div class="flex items-center gap-2">

                    <span class="w-7 h-7 rounded bg-[#f3f4f6] border-2 border-green-400"></span>

                    <span class="text-gray-600">
                        Today
                    </span>

                </div>

                {{-- BLOCKED --}}
                <div class="flex items-center gap-2">

                    <span class="w-7 h-7 rounded bg-blue-100 border border-blue-300"></span>

                    <span class="text-gray-600">
                        Not Available
                    </span>

                </div>

                {{-- PAST --}}
                <div class="flex items-center gap-2">

                    <span class="w-7 h-7 rounded bg-gray-200 border"></span>

                    <span class="text-gray-600">
                        Past Date
                    </span>

                </div>

            </div>

            {{-- FORM --}}
            <div class="w-full mx-auto mt-10">

                {{-- APPOINTMENT TYPE --}}
                <div>

                    <x-select
                        label="Appointment Type"
                        placeholder="Choose appointment type"
                        :options="[
                            'Property Tripping',
                            'Loan Consultation',
                            'Reservation Assistance',
                            'Payment Discussion',
                            'General Inquiry'
                        ]"
                        wire:model.live="appointmentType"
                    />

                    @error('appointmentType')

                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

                {{-- TIME SLOT --}}
                <div class="mt-5">

                    @if($selectedDate)

                        @if(count($timeSlots))

                            <x-select
                                label="Select Time"
                                placeholder="Choose an available time slot"
                                :options="$timeSlots"
                                wire:model.live="timeSlot"
                            />

                            <p class="mt-1 text-[11px] text-gray-400">
                                Booked and already-passed time slots are automatically hidden.
                            </p>

                        @else

                            <div class="rounded-xl border border-orange-100 bg-orange-50 p-4">

                                <div class="flex items-start gap-2">

                                    <x-icon
                                        name="clock"
                                        class="h-5 w-5 shrink-0 text-orange-500"
                                    />

                                    <div>

                                        <p class="text-sm font-medium text-orange-700">
                                            No available time slots
                                        </p>

                                        <p class="mt-1 text-xs text-orange-600">
                                            All appointment slots for this date are either booked or have already passed.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif

                    @else

                        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-4">

                            <div class="flex items-center gap-2">

                                <x-icon
                                    name="calendar-days"
                                    class="h-5 w-5 text-gray-400"
                                />

                                <p class="text-sm text-gray-500">
                                    Select an available date first to view its available time slots.
                                </p>

                            </div>

                        </div>

                    @endif

                    @error('timeSlot')

                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

                {{-- NOTES --}}
                <div class="mt-5">

                    <x-textarea
                        wire:model.defer="notes"
                        label="Additional Notes (Optional)"
                        placeholder="Please describe any special requests or additional information..."
                    />

                    @error('notes')

                        <p class="mt-1 text-xs text-red-500">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

                {{-- CLIENT INFO --}}
                {{-- <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>

                        <x-input
                            label="Your Name"
                            placeholder="Your name"
                            wire:model.defer="name"
                            class="py-3"
                        />

                        @error('name')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                    <div>

                        <x-inputs.phone
                            label="Contact No."
                            placeholder="+63 912 345 6789"
                            mask="['+63 ### ### ####']"
                            class="py-3"
                            wire:model.live="phone"
                        />

                        @error('phone')

                            <p class="mt-1 text-xs text-red-500">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div> --}}

                {{-- OPTIONAL CONCERN ATTACHMENT --}}
<div class="mt-5">

    <label class="text-sm font-medium text-gray-700">
        Concern Attachment
        <span class="font-normal text-gray-400">
            (Optional)
        </span>
    </label>

    <p class="mt-1 mb-3 text-xs text-gray-500">
        You may attach an image or document related to your concern.
    </p>

    <x-filepond::upload
        wire:model="document"
        :accepted-file-types="[
            'image/png',
            'image/jpeg',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]"
    />

    @error('document')
        <p class="mt-1 text-sm text-red-500">
            {{ $message }}
        </p>
    @enderror

</div>
                {{-- ACTION --}}
                <div class="mt-6 flex justify-end">

                    <x-button
                        wire:click="
                            confirmAppointmentConfirmation(
                                '{{ $selectedDate }}'
                            )
                        "
                        icon="calendar"
                        lg
                        label="SCHEDULE APPOINTMENT"
                        class="px-5 py-2 bg-black text-white rounded-lg hover:!bg-gray-800 transition"
                        :disabled="
                            empty($selectedDate)
                            || empty($timeSlot)
                            || empty($appointmentType)
                            // || empty($name)
                            // || empty($phone)
                        "
                    />

                </div>

            </div>

        </div>

    </div>
</div>
