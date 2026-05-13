<div class="w-full h-auto pt-40">
    <div class="w-full h-auto max-w-5xl mx-auto">
    <h2 class="text-xl font-extralight mb-5">Book New Appointment</h2>
    <div class="w-full max-w-5xl mx-auto p-8 bg-white rounded-2xl shadow">
        <h4 class="text-sm font-semibold mb-5">Select Date</h4>
        <div class="w-full mx-auto p-4 bg-[#f9fafc] rounded-2xl">

            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-6">

                <x-button.circle wire:click="previousMonth" icon="chevron-left" />

                <h2 class="text-lg font-semibold">
                    {{ $currentMonth->format('F Y') }}
                </h2>

                <x-button.circle wire:click="nextMonth" icon="chevron-right" />
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

                {{-- EMPTY START --}}
                @for ($i = 0; $i < $startDay; $i++)
                    <div></div>
                @endfor

                {{-- DATES --}}
                @foreach($dates as $date)

                    @php
                        $isPast = $date['past'];
                        $isAvailable = $date['available'];
                    @endphp

                    <button
                        wire:click="selectDate('{{ $date['date'] }}')"
                        @disabled($isPast || !$isAvailable)

                        class="
                            h-10
                            rounded-xl
                            transition
                            flex
                            items-center
                            justify-center

                            {{ $isPast
                                ? 'bg-transparent text-gray-300 cursor-not-allowed'
                                : (!$isAvailable
                                    ? 'bg-blue-100 text-blue-600 cursor-not-allowed'
                                    : 'bg-[#f3f4f6] text-gray-700 hover:bg-gray-200 cursor-pointer')
                            }}

                            {{ $selectedDate === $date['date'] ? '!bg-[#101727] !text-white' : '' }}
                        "
                    >
                        <span class="text-sm font-semibold">
                            {{ $date['day'] }}
                        </span>
                    </button>

                @endforeach

            </div>

        </div>
        @if ($selectedDate)
            <h4 class="text-xs my-5">Selected: <span class="font-semibold text-sm">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</span></h4>
        @endif

        {{-- LEGENDS --}}
        <div class="flex flex-wrap gap-4 items-center my-5 text-xs">
            {{-- AVAILABLE --}}
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded bg-[#f3f4f6] border"></span>
                <span class="text-gray-600">Available</span>
            </div>

            {{-- BLOCKED --}}
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded bg-blue-100 border border-blue-300"></span>
                <span class="text-gray-600">Not Available</span>
            </div>

            {{-- PAST --}}
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded bg-gray-200 border"></span>
                <span class="text-gray-600">Past Date</span>
            </div>
        </div>

        {{-- FORM --}}
        <div class="w-full mx-auto mt-10">
            <div>
                <x-select
                    label="Appointment Type"
                    placeholder="Choose appointment type"
                    :options="['Property Tripping', 'Loan Consultation', 'Reservation Assistance', 'Payment Discussion', 'General Inquiry']"
                    wire:model.live="appointmentType"
                />
            </div>
            
            <div class="mt-5">
                <x-select
                    label="Select Time"
                    placeholder="Choose a time slot"
                    :options="$timeSlots"
                    wire:model.live="timeSlot"
                />
            </div>

            <div class="mt-5">
                <x-textarea wire:model="notes" label="Additional Notes (Optional)" placeholder="Please describe any special requests or additional information..." />
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Your Name" placeholder="your name" wire:model="name" class="py-3" />

                <x-inputs.phone label="Contact No." placeholder="+63 912 345 6789" mask="['+63 ### ### ####']" class="py-3" wire:model.live="phone" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-button
                    wire:click="confirmAppointmentConfirmation('{{ $selectedDate }}')"
                    icon="calendar"
                    lg
                    label="SCHEDULE APPOINTMENT"
                    class="px-5 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition"

                    :disabled="empty($selectedDate) || empty($timeSlot) || empty($appointmentType)"
                />
            </div>
        </div>
    </div>
    </div>
</div>
