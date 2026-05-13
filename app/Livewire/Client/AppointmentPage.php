<?php

namespace App\Livewire\Client;

use App\Models\BlockedDate;
use App\Models\ClientAppointment;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;

#[Title('Appointment')]
class AppointmentPage extends Component
{
    use Actions;
    
    public $selectedDate;
    public $currentMonth;
    public $blockedDates = [];

    public $appointmentType;
    public $timeSlot;
    public $notes;
    public $name;
    public $phone;

    public function mount()
    {
        $this->currentMonth = Carbon::now()->startOfMonth();
        $this->blockedDates = BlockedDate::pluck('date')->toArray();

        $this->selectedDate = session('selectedDate', null);
    }

    public function previousMonth()
    {
        $this->currentMonth = $this->currentMonth->copy()->subMonth();
    }

    public function nextMonth()
    {
        $this->currentMonth = $this->currentMonth->copy()->addMonth();
    }

    public function getDatesProperty()
    {
        $dates = [];

        $start = $this->currentMonth->copy()->startOfMonth();
        $end = $this->currentMonth->copy()->endOfMonth();

        while ($start <= $end) {

            $date = $start->format('Y-m-d');

            $dates[] = [
                'date' => $date,
                'day' => $start->day,
                'past' => $start->isPast(),

                // CONNECTS TO ADMIN BLOCKED DATES
                'available' => !in_array($date, $this->blockedDates),
            ];

            $start->addDay();
        }

        return $dates;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;

        session(['selectedDate' => $date]);
    }

    public function getStartDayProperty()
    {
        return $this->currentMonth
            ->copy()
            ->startOfMonth()
            ->dayOfWeekIso;
    }

    public function getTimeSlotsProperty()
    {
        return [
            '09:00 AM',
            '10:00 AM',
            '11:00 AM',
            '01:00 PM',
            '02:00 PM',
            '03:00 PM',
            '04:00 PM',
            '05:00 PM',
        ];
    }

    public function bookAppointment()
    {
        $this->validate([
            'selectedDate' => 'required|date',
            'timeSlot' => 'required',
            'appointmentType' => 'required',
            'name' => 'required|string',
            'phone' => 'required',
        ]);

        // past date check
        if (Carbon::parse($this->selectedDate)->isPast()) {
            $this->addError('selectedDate', 'Cannot book past dates.');
            return;
        }

        // blocked date check
        if (in_array($this->selectedDate, $this->blockedDates)) {
            $this->addError('selectedDate', 'This date is not available.');
            return;
        }

        // prevent double booking same date + time
        $exists = ClientAppointment::where('appointment_date', $this->selectedDate)
            ->where('appointment_time', $this->timeSlot)
            ->exists();

        if ($exists) {
            $this->addError('timeSlot', 'This time is already booked.');
            return;
        }

        $time = Carbon::createFromFormat('h:i A', $this->timeSlot)->format('H:i:s');

        // save appointment
        ClientAppointment::create([
            'user_id' => auth()->id(),
            'appointment_date' => $this->selectedDate,
            'appointment_time' => $time,
            'appointment_type' => $this->appointmentType,
            'name' => $this->name,
            'phone' => '+63' . $this->phone,
            'notes' => $this->notes,
            'status' => 'pending',
        ]);

        $this->notification()->success(
            $title = 'Appointment Created',
            $description = 'Appointment booked successfully!'
        );

        $this->reset([
            'selectedDate',
            'timeSlot',
            'appointmentType',
            'name',
            'phone',
            'notes',
        ]);

        session()->forget('selectedDate');

        // $this->reloadWeb();
    }

    public function confirmAppointmentConfirmation($date)
    {
        $this->dialog()->confirm([
            'title' => 'Confirm Appointment?',
            'description' => 'Do you want to book this appointment on ' . $date . '?',
            'acceptLabel' => 'Yes, confirm booking',
            'method' => 'bookAppointment',
            'icon' => 'question',
            'params' => $date
        ]);
    }

    public function reloadWeb(){

        $this->dispatch('reload');
        return redirect()->back();

    }

    public function render()
    {
        return view('livewire.client.appointment-page', [
            'dates' => $this->dates,
            'startDay' => $this->startDay,
            'timeSlots' => $this->timeSlots,
        ]);
    }
}
