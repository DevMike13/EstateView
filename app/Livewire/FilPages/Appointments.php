<?php

namespace App\Livewire\FilPages;

use App\Mail\AdminStaffCreatedAppointmentMail;
use App\Mail\AppointmentApprovedMail;
use App\Mail\AppointmentCompletedMail;
use App\Mail\AppointmentDeclinedMail;
use App\Models\BlockedDate;
use App\Models\ClientAppointment;
use Filament\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Url;
use Livewire\Component;
use WireUi\Traits\Actions;

class Appointments extends Component
{
    use Actions;

    #[Url]
    public $activeTab = 'pending';

    #[Url]
    public $highlight = null;

    protected $queryString = [
        'activeTab',
        'highlight',
    ];

    public $selectedDates = [];
    public $currentMonth;

    // SET APPOINTMENT
    public $setAppointmentDate = null;
    public $setAppointmentClientId = null;
    public $setAppointmentType = null;
    public $setAppointmentNotes = null;
    public $setAppointmentTime = null;
    public $showSetAppointmentModal = false;

    public function mount()
    {
        $this->currentMonth = Carbon::now()->startOfMonth();

        if (!in_array($this->activeTab, [
            'pending',
            'awaiting_client_confirmation',
            'approved',
            'completed',
            'declined',
            'cancelled'
        ])) {
            $this->activeTab = 'pending';
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->highlight = null;
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

    public function getAwaitingClientCountProperty()
    {
        return ClientAppointment::where(
            'status',
            'awaiting_client_confirmation'
        )->count();
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

    public function getCancelledCountProperty()
    {
        return ClientAppointment::where('status', 'cancelled')->count();
    }

    /*
    |--------------------------------------------------------------------------
    | ELIGIBLE USERS
    |--------------------------------------------------------------------------
    */

    public function getEligibleClientsProperty()
    {
        return User::query()
            ->where('role', 'user')
            ->whereHas('purchaseAccounts')
            ->with([
                'info',
                'purchaseAccounts.ledgers',
                'purchaseAccounts.billings',
            ])
            ->get()
            ->filter(function ($user) {

                return $user->purchaseAccounts->contains(
                    function ($account) {

                        /*
                        |--------------------------------------------------------------------------
                        | CASH PAYMENT
                        |--------------------------------------------------------------------------
                        */

                        if (
                            strtolower(
                                trim((string) $account->payment_scheme)
                            ) === 'cash'
                        ) {
                            return true;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | MUST HAVE LEDGER
                        |--------------------------------------------------------------------------
                        */

                        if ($account->ledgers->isEmpty()) {
                            return false;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | CHECK 50% OF BILLING SCHEDULE
                        |--------------------------------------------------------------------------
                        */

                        $billings = $account->billings
                            ->where('status', '!=', 'cancelled');

                        if ($billings->isEmpty()) {
                            return false;
                        }

                        $totalBills =
                            $billings->count();

                        $paidBills =
                            $billings
                                ->where('status', 'paid')
                                ->count();

                        if ($totalBills <= 0) {
                            return false;
                        }

                        return (
                            $paidBills / $totalBills
                        ) >= 0.50;
                    }
                );
            })
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                ];
            })
            ->values()
            ->toArray();
    }

    private function isClientEligibleForStaffAppointment(int $userId): bool
    {
        $user = User::query()
            ->where('role', 'user')
            ->with([
                'purchaseAccounts.ledgers',
                'purchaseAccounts.billings',
            ])
            ->find($userId);

        if (!$user) {
            return false;
        }

        return $user->purchaseAccounts->contains(
            function ($account) {

                /*
                |--------------------------------------------------------------------------
                | CASH PAYMENT
                |--------------------------------------------------------------------------
                */

                if (
                    strtolower(
                        trim((string) $account->payment_scheme)
                    ) === 'cash'
                ) {
                    return true;
                }

                /*
                |--------------------------------------------------------------------------
                | MUST HAVE LEDGER
                |--------------------------------------------------------------------------
                */

                if ($account->ledgers->isEmpty()) {
                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | CHECK 50% OF BILLING SCHEDULE
                |--------------------------------------------------------------------------
                */

                $billings = $account->billings
                    ->where('status', '!=', 'cancelled');

                if ($billings->isEmpty()) {
                    return false;
                }

                $totalBills =
                    $billings->count();

                $paidBills =
                    $billings
                        ->where('status', 'paid')
                        ->count();

                if ($totalBills <= 0) {
                    return false;
                }

                return (
                    $paidBills / $totalBills
                ) >= 0.50;
            }
        );
    }
    /*
    |--------------------------------------------------------------------------
    | BASE TIME SLOTS
    |--------------------------------------------------------------------------
    */

    private function baseTimeSlots(): array
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

    /*
    |--------------------------------------------------------------------------
    | AVAILABLE SET APPOINTMENT TIME SLOTS
    |--------------------------------------------------------------------------
    */

    public function getSetAppointmentTimeSlotsProperty()
    {
        /*
         * No selected appointment date.
         */
        if (!$this->setAppointmentDate) {
            return [];
        }

        $selectedDate = Carbon::parse(
            $this->setAppointmentDate
        );

        /*
         * Past date safety.
         */
        if (
            $selectedDate
                ->copy()
                ->startOfDay()
                ->lt(Carbon::today())
        ) {
            return [];
        }

        /*
         * Blocked date safety.
         */
        if (
            BlockedDate::query()
                ->whereDate(
                    'date',
                    $selectedDate->format('Y-m-d')
                )
                ->exists()
        ) {
            return [];
        }

        /*
         * Get already occupied times.
         *
         * Cancelled and declined appointments
         * are ignored just like the client side.
         */
        $bookedTimes =
            ClientAppointment::query()
                ->whereDate(
                    'appointment_date',
                    $selectedDate->format('Y-m-d')
                )
                ->whereNotIn(
                    'status',
                    [
                        'cancelled',
                        'declined',
                    ]
                )
                ->pluck('appointment_time')
                ->map(function ($time) {
                    return Carbon::parse($time)
                        ->format('H:i:s');
                })
                ->toArray();

        return collect(
            $this->baseTimeSlots()
        )
            ->filter(function ($slot) use (
                $selectedDate,
                $bookedTimes
            ) {

                /*
                 * Convert 09:00 AM to 09:00:00
                 */
                $databaseTime =
                    Carbon::createFromFormat(
                        'h:i A',
                        $slot
                    )
                    ->format('H:i:s');

                /*
                 * Hide booked slot.
                 */
                if (
                    in_array(
                        $databaseTime,
                        $bookedTimes,
                        true
                    )
                ) {
                    return false;
                }

                /*
                 * For today, hide already-passed slots.
                 */
                if ($selectedDate->isToday()) {

                    $slotDateTime =
                        Carbon::createFromFormat(
                            'Y-m-d h:i A',
                            $selectedDate->format('Y-m-d')
                            . ' '
                            . $slot
                        );

                    if (
                        $slotDateTime->lte(now())
                    ) {
                        return false;
                    }
                }

                return true;
            })
            ->values()
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | SET APPOINTMENT
    |--------------------------------------------------------------------------
    */

    // public function openSetAppointment($date)
    // {
    //     $appointmentDate = Carbon::parse($date)->startOfDay();

    //     if ($appointmentDate->lt(Carbon::today())) {
    //         return;
    //     }

    //     if (
    //         BlockedDate::where(
    //             'date',
    //             $appointmentDate->format('Y-m-d')
    //         )->exists()
    //     ) {
    //         $this->notification()->error(
    //             'Blocked Date',
    //             'You cannot create an appointment on a blocked date.'
    //         );

    //         return;
    //     }

    //     $this->resetValidation();

    //     $this->setAppointmentClientId = null;
    //     $this->setAppointmentNotes = null;
    //     $this->setAppointmentTime = null;
    //     $this->setAppointmentDate = $appointmentDate->format('Y-m-d');

    //     $this->showSetAppointmentModal = true;
    // }
    public function openSetAppointment()
    {
        $this->resetValidation();

        $this->setAppointmentDate = null;
        $this->setAppointmentClientId = null;
        $this->setAppointmentType = null;
        $this->setAppointmentNotes = null;
        $this->setAppointmentTime = null;

        $this->showSetAppointmentModal = true;
    }

    public function closeSetAppointmentModal()
    {
        $this->showSetAppointmentModal = false;

        $this->resetValidation();
    }

    public function createStaffAppointment()
    {
        $this->validate([
            'setAppointmentDate' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'setAppointmentClientId' => [
                'required',
                'exists:users,id',
            ],

            'setAppointmentType' => [
                'required',
                'string',
                'in:Property Document Consultation,House and Lot Document Processing,Contract to Sell Processing,Deed of Sale Preparation,Land Title Processing,Transfer of Land and House Title',
            ],

            'setAppointmentNotes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'setAppointmentTime' => [
                'required',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Client eligibility
        |--------------------------------------------------------------------------
        */

        if (
            !$this->isClientEligibleForStaffAppointment(
                (int) $this->setAppointmentClientId
            )
        ) {
            $this->addError(
                'setAppointmentClientId',
                'This user is not eligible for an appointment.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Date validation
        |--------------------------------------------------------------------------
        */

        $selectedDate =
            Carbon::parse(
                $this->setAppointmentDate
            )
            ->startOfDay();

        if (
            $selectedDate->lt(
                Carbon::today()
            )
        ) {
            $this->addError(
                'setAppointmentDate',
                'Cannot book past dates.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Blocked date
        |--------------------------------------------------------------------------
        */

        if (
            BlockedDate::query()
                ->whereDate(
                    'date',
                    $selectedDate->format('Y-m-d')
                )
                ->exists()
        ) {
            $this->addError(
                'setAppointmentDate',
                'This date is blocked.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Ensure selected time is valid
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $this->setAppointmentTime,
                $this->baseTimeSlots(),
                true
            )
        ) {
            $this->addError(
                'setAppointmentTime',
                'Invalid appointment time.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Build appointment date/time
        |--------------------------------------------------------------------------
        */

        $appointmentDateTime =
            Carbon::createFromFormat(
                'Y-m-d h:i A',
                $selectedDate->format('Y-m-d')
                . ' '
                . $this->setAppointmentTime
            );

        /*
         * Prevent passed time today.
         */
        if (
            $appointmentDateTime->lte(now())
        ) {
            $this->addError(
                'setAppointmentTime',
                'This appointment time has already passed.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Convert selected time to DB format
        |--------------------------------------------------------------------------
        */

        $time =
            Carbon::createFromFormat(
                'h:i A',
                $this->setAppointmentTime
            )
            ->format('H:i:s');

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate booking
        |--------------------------------------------------------------------------
        */

        $exists =
            ClientAppointment::query()
                ->whereDate(
                    'appointment_date',
                    $selectedDate->format('Y-m-d')
                )
                ->where(
                    'appointment_time',
                    $time
                )
                ->whereNotIn(
                    'status',
                    [
                        'cancelled',
                        'declined',
                    ]
                )
                ->exists();

        if ($exists) {
            $this->addError(
                'setAppointmentTime',
                'This time slot has already been booked.'
            );

            $this->setAppointmentTime = null;

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Client
        |--------------------------------------------------------------------------
        */

        $client = User::with('info')->findOrFail(
            $this->setAppointmentClientId
        );

        $creator = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $appointment = ClientAppointment::create([
            'user_id' => $client->id,

            'created_by' => $creator->id,

            'created_by_role' => $creator->role,

            'appointment_date' =>
                $selectedDate->format('Y-m-d'),

            'appointment_time' =>
                $time,

            'appointment_type' =>
                $this->setAppointmentType,

            'notes' =>
                $this->setAppointmentNotes,

            'name' =>
                $client->name,

            'phone' =>
                $client->info?->phone ?? '',

            'status' =>
                'awaiting_client_confirmation',
        ]);

        $performedBy = $creator->role === 'admin'
            ? 'Admin'
            : $creator->name;

        Mail::to($client->email)->send(
            new AdminStaffCreatedAppointmentMail(
                $appointment,
                $client,
                $performedBy
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Reset fields
        |--------------------------------------------------------------------------
        */

        $this->setAppointmentDate = null;
        $this->setAppointmentClientId = null;
        $this->setAppointmentType = null;
        $this->setAppointmentNotes = null;
        $this->setAppointmentTime = null;

        $this->resetValidation();

        /*
        |--------------------------------------------------------------------------
        | Close modal
        |--------------------------------------------------------------------------
        */

        $this->showSetAppointmentModal = false;

        /*
        |--------------------------------------------------------------------------
        | Show toast in the SAME request — no event bridge, no race condition
        |--------------------------------------------------------------------------
        */

        Notification::make()
            ->title('Appointment Sent')
            ->body('The appointment was created and is waiting for client confirmation.')
            ->success()
            ->send();
    }

    // CHANGE STATUS

    public function confirmApprove($id)
    {
        $this->dialog()->confirm([
            'title' => 'Approve Appointment?',
            'description' => 'This will mark the appointment as approved.',
            'acceptLabel' => 'Yes',
            'method' => 'approve',
            'params' => $id,
            'icon' => 'success',
        ]);
    }

    public function getAppointmentMinDateProperty()
    {
        $lastSlot = collect($this->baseTimeSlots())->last(); // '05:00 PM'

        $lastSlotToday = Carbon::createFromFormat(
            'Y-m-d h:i A',
            Carbon::today()->format('Y-m-d') . ' ' . $lastSlot
        );

        return now()->gte($lastSlotToday)
            ? Carbon::tomorrow()->startOfDay()
            : Carbon::today()->startOfDay();
    }

    public function approve($id)
    {
        $appointment = ClientAppointment::with('user')->findOrFail($id);

        $performedBy = auth()->user()->role === 'staff'
            ? auth()->user()->name
            : 'Admin';

        $appointment->update([
            'status' => 'approved'
        ]);

        Mail::to($appointment->user->email)
            ->send(new AppointmentApprovedMail(
                    $appointment,
                    $performedBy
                )
            );
        
        Notification::make()
            ->title('Appointment Approved')
            ->body('The appointment has been approved successfully.')
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function confirmDecline($id)
    {
        $this->dialog()->confirm([
            'title' => 'Decline Appointment?',
            'description' => 'This will permanently mark it as declined.',
            'acceptLabel' => 'Yes',
            'method' => 'decline',
            'params' => $id,
            'icon' => 'error',
        ]);
    }

    public function decline($id)
    {
        $appointment = ClientAppointment::with('user')->findOrFail($id);

        $performedBy = auth()->user()->role === 'staff'
            ? auth()->user()->name
            : 'Admin';

        $appointment->update([
            'status' => 'declined'
        ]);

        Mail::to($appointment->user->email)
            ->send(
                new AppointmentDeclinedMail(
                    $appointment,
                    $performedBy
                )
            );

        Notification::make()
            ->title('Appointment Declined')
            ->body('The appointment has been declined.')
            ->warning()
            ->send();

        $this->reloadWeb();
    }

    public function confirmComplete($id)
    {
        $this->dialog()->confirm([
            'title' => 'Mark as Completed?',
            'description' => 'This will move appointment to completed.',
            'acceptLabel' => 'Yes',
            'method' => 'complete',
            'params' => $id,
            'icon' => 'success',
        ]);
    }

    public function complete($id)
    {
        $appointment = ClientAppointment::with('user')->findOrFail($id);

        $performedBy = auth()->user()->role === 'staff'
            ? auth()->user()->name
            : 'Admin';

        $appointment->update([
            'status' => 'completed'
        ]);

        Mail::to($appointment->user->email)
            ->send(new AppointmentCompletedMail(
                    $appointment,
                    $performedBy
                )
            );
        
        Notification::make()
            ->title('Appointment Completed')
            ->body('The appointment has been marked as completed.')
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function confirmCancel($id)
    {
        $this->dialog()->confirm([
            'title' => 'Mark as Cancelled?',
            'description' => 'This will move the appointment to cancelled.',
            'acceptLabel' => 'Yes',
            'method' => 'cancel',
            'params' => $id,
            'icon' => 'warning',
        ]);
    }

    public function cancel($id)
    {
        $appointment = ClientAppointment::findOrFail($id);

        if ($appointment->status !== 'approved') {
            return;
        }

        $appointment->update([
            'status' => 'cancelled'
        ]);

        Notification::make()
            ->title('Appointment Cancelled')
            ->body('The appointment has been marked as cancelled.')
            ->warning()
            ->send();

        $this->reloadWeb();
    }

    public function confirmRestore($id)
    {
        $this->dialog()->confirm([
            'title' => 'Restore Appointment?',
            'description' => 'This will move it back to pending.',
            'acceptLabel' => 'Yes',
            'method' => 'reopen',
            'params' => $id,
            'icon' => 'warning',
        ]);
    }

    public function reopen($id)
    {
        ClientAppointment::findOrFail($id)->update([
            'status' => 'pending'
        ]);

        Notification::make()
            ->title('Appointment Restored')
            ->body('The appointment has been restored to pending.')
            ->success()
            ->send();

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

        Notification::make()
            ->title('Dates Blocked')
            ->body('The selected dates have been blocked successfully.')
            ->success()
            ->send();
    }

    public function confirmBlockDates()
    {
        $this->dialog()->confirm([
            'title' => 'Block Selected Dates?',
            'description' => 'Are you sure you want to block the selected dates?',
            'acceptLabel' => 'Yes',
            'method' => 'saveBlockedDates',
            'icon' => 'error',
        ]);
    }

    public function confirmRemoveBlockedDate($date)
    {
        $this->dialog()->confirm([
            'title' => 'Unblock Date?',
            'description' => "Do you want to unblock {$date}?",
            'acceptLabel' => 'Yes',
            'method' => 'removeBlockedDate',
            'params' => $date,
            'icon' => 'warning',
        ]);
    }

    public function removeBlockedDate($date)
    {
        BlockedDate::where('date', $date)->delete();

        Notification::make()
            ->title('Date Unblocked')
            ->body('The selected date is now available again.')
            ->success()
            ->send();
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

    public function reloadWeb()
    {
        $this->dispatch('reload');

        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.fil-pages.appointments', [
            'blocked' => BlockedDate::pluck('date')->toArray(),

            'confirmedDates' => ClientAppointment::query()
                ->where('status', 'approved')
                ->pluck('appointment_date')
                ->map(function ($date) {
                    return Carbon::parse($date)->format('Y-m-d');
                })
                ->toArray(),

            'dates' => $this->dates,
            'startDay' => $this->startDay,
        ]);
    }
}