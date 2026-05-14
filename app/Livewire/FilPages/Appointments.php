<?php

namespace App\Livewire\FilPages;

use App\Mail\AppointmentApprovedMail;
use App\Mail\AppointmentCompletedMail;
use App\Mail\AppointmentDeclinedMail;
use App\Models\BlockedDate;
use App\Models\ClientAppointment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Url;
use Livewire\Component;
use WireUi\Traits\Actions;

class Appointments extends Component
{
    use Actions;

    #[Url(as: 'tab')]
    public $activeTab = 'pending';

    public $selectedDates = [];
    public $currentMonth;

    public function mount()
    {
        $this->currentMonth = Carbon::now()->startOfMonth();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getAppointmentsProperty()
    {
        return ClientAppointment::with('user.info')
            ->where('status', $this->activeTab)
            ->latest()
            ->get();
    }

     public function getPendingCountProperty()
    {
        return ClientAppointment::where('status', 'pending')->count();
    }

    public function getApprovedCountProperty()
    {
        return ClientAppointment::where('status', 'approved')->count();
    }

    public function getCompletedCountProperty()
    {
        return ClientAppointment::where('status', 'completed')->count();
    }

    public function getDeclinedCountProperty()
    {
        return ClientAppointment::where('status', 'declined')->count();
    }

    // CHANGE STATUS
    public function confirmApprove($id)
    {
        $this->dialog()->confirm([
            'title' => 'Approve Appointment?',
            'description' => 'This will mark the appointment as approved.',
            'acceptLabel' => 'Yes, approve',
            'method' => 'approve',
            'params' => $id,
            'icon' => 'success',
        ]);
    }
    public function approve($id)
    {
        $appointment = ClientAppointment::with('user')->findOrFail($id);

        $appointment->update([
            'status' => 'approved'
        ]);

        Mail::to($appointment->user->email)
            ->send(new AppointmentApprovedMail($appointment));

        $this->reloadWeb();
    }

    public function confirmDecline($id)
    {
        $this->dialog()->confirm([
            'title' => 'Decline Appointment?',
            'description' => 'This will permanently mark it as declined.',
            'acceptLabel' => 'Yes, decline',
            'method' => 'decline',
            'params' => $id,
            'icon' => 'error',
        ]);
    }
    public function decline($id)
    {
        $appointment = ClientAppointment::with('user')->findOrFail($id);

        $appointment->update([
            'status' => 'declined'
        ]);

        Mail::to($appointment->user->email)
            ->send(new AppointmentDeclinedMail($appointment));

        $this->reloadWeb();
    }

    public function confirmComplete($id)
    {
        $this->dialog()->confirm([
            'title' => 'Mark as Completed?',
            'description' => 'This will move appointment to completed.',
            'acceptLabel' => 'Yes, complete',
            'method' => 'complete',
            'params' => $id,
            'icon' => 'success',
        ]);
    }
    public function complete($id)
    {
        $appointment = ClientAppointment::with('user')->findOrFail($id);

        $appointment->update([
            'status' => 'completed'
        ]);

        Mail::to($appointment->user->email)
            ->send(new AppointmentCompletedMail($appointment));

        $this->reloadWeb();
    }

    public function confirmRestore($id)
    {
        $this->dialog()->confirm([
            'title' => 'Restore Appointment?',
            'description' => 'This will move it back to pending.',
            'acceptLabel' => 'Yes, restore',
            'method' => 'restore',
            'params' => $id,
            'icon' => 'warning',
        ]);
    }
    public function reopen($id)
    {
        ClientAppointment::findOrFail($id)->update([
            'status' => 'pending'
        ]);

        $this->reloadWeb();
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

    public function reloadWeb(){

        $this->dispatch('reload');
        return redirect()->back();

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