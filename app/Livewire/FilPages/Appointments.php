<?php

namespace App\Livewire\FilPages;

use App\Models\BlockedDate;
use Illuminate\Support\Carbon;
use Livewire\Component;
use WireUi\Traits\Actions;

class Appointments extends Component
{
    use Actions;

    public $selectedDates = [];
    public $currentMonth;

    public function mount()
    {
        $this->currentMonth = Carbon::now()->startOfMonth();
    }

    public function previousMonth()
    {
        $this->currentMonth = $this->currentMonth->copy()->subMonth();
    }

    public function nextMonth()
    {
        $this->currentMonth = $this->currentMonth->copy()->addMonth();
    }

    public function toggleDate($date)
    {
        if (Carbon::parse($date)->isPast()) {
            return;
        }

        // if already blocked → do nothing (must unblock via X)
        if (BlockedDate::where('date', $date)->exists()) {
            return;
        }

        if (in_array($date, $this->selectedDates)) {
            $this->selectedDates = array_values(
                array_diff($this->selectedDates, [$date])
            );
        } else {
            $this->selectedDates[] = $date;
        }
    }

    public function saveBlockedDates()
    {
        foreach ($this->selectedDates as $date) {
            BlockedDate::updateOrCreate(
                ['date' => $date],
                ['reason' => 'Blocked by admin']
            );
        }

        $this->selectedDates = [];
    }

    public function confirmBlockDates()
    {
        $this->dialog()->confirm([
            'title' => 'Block Selected Dates?',
            'description' => 'Are you sure you want to block the selected dates?',
            'acceptLabel' => 'Yes, block them',
            'method' => 'saveBlockedDates',
            'icon' => 'error',
        ]);
    }

    public function confirmRemoveBlockedDate($date)
    {
        $this->dialog()->confirm([
            'title' => 'Unblock Date?',
            'description' => "Do you want to unblock {$date}?",
            'acceptLabel' => 'Yes, unblock it',
            'method' => 'removeBlockedDate',
            'params' => $date,
            'icon' => 'warning',
        ]);
    }

    public function removeBlockedDate($date)
    {
        BlockedDate::where('date', $date)->delete();
    }

    public function getDatesProperty()
    {
        $dates = [];

        $start = $this->currentMonth->copy()->startOfMonth();
        $end = $this->currentMonth->copy()->endOfMonth();
        $today = Carbon::today();

        while ($start <= $end) {

            $dates[] = [
                'date' => $start->format('Y-m-d'),
                'day' => $start->day,
                'past' => $start->lt($today),
            ];

            $start->addDay();
        }

        return $dates;
    }

    public function getStartDayProperty()
    {
        return $this->currentMonth
            ->copy()
            ->startOfMonth()
            ->dayOfWeek;
    }

    public function render()
    {
        return view('livewire.fil-pages.appointments', [
            'blocked' => BlockedDate::pluck('date')->toArray(),
            'dates' => $this->dates,
            'startDay' => $this->startDay,
        ]);
    }
}