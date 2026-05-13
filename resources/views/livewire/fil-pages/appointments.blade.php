<div class="pt-40">
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