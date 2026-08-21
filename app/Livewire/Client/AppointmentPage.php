<?php

namespace App\Livewire\Client;

use App\Models\BlockedDate;
use App\Models\ClientAppointment;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;

#[Title('Appointment')]
class AppointmentPage extends Component
{
    use Actions, WithFileUploads, WithFilePond;

    public $selectedDate;
    public $currentMonth;
    public $blockedDates = [];

    public $appointmentType;
    public $timeSlot;
    public $notes;
    // public $name;
    // public $phone;
    public $document;

    public $activeTab = 'pending';

    public ?int $highlight = null;

    public function mount()
    {
        $this->currentMonth = Carbon::now()
            ->startOfMonth();

        $this->blockedDates = BlockedDate::query()
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)
                    ->format('Y-m-d');
            })
            ->toArray();

        $this->selectedDate =
            session('selectedDate');

        $allowedTabs = [
            'pending',
            'approved',
            'completed',
            'cancelled',
        ];

        $requestedTab = request()
            ->query(
                'activeTab',
                'pending'
            );

        $this->activeTab = in_array(
            $requestedTab,
            $allowedTabs,
            true
        )
            ? $requestedTab
            : 'pending';

        $this->highlight = request()
            ->query('highlight')
            ? (int) request()
                ->query('highlight')
            : null;

        /*
        |--------------------------------------------------------------------------
        | Show Appointment Success Toast After Refresh
        |--------------------------------------------------------------------------
        */

        if (session()->has('appointment_success')) {
            $this->notification()->success(
                'Appointment Created',
                'Appointment booked successfully!'
            );
        }
    }

    public function rules(): array
    {
        return [
            'document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:10240',
            ],
        ];
    }

    public function validateUploadedFile()
    {
        $this->validate([
            'document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:10240',
            ],
        ]);

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Appointment Tabs
    |--------------------------------------------------------------------------
    */

    public function setTab($tab)
    {
        $allowedTabs = [
            'pending',
            'approved',
            'completed',
            'cancelled',
        ];

        if (! in_array(
            $tab,
            $allowedTabs,
            true
        )) {
            return;
        }

        $this->activeTab = $tab;
    }

    /*
    |--------------------------------------------------------------------------
    | Calendar Navigation
    |--------------------------------------------------------------------------
    */

    public function previousMonth()
    {
        $this->currentMonth =
            $this->currentMonth
                ->copy()
                ->subMonth();
    }

    public function nextMonth()
    {
        $this->currentMonth =
            $this->currentMonth
                ->copy()
                ->addMonth();
    }

    /*
    |--------------------------------------------------------------------------
    | Calendar Dates
    |--------------------------------------------------------------------------
    */

    public function getDatesProperty()
    {
        $dates = [];

        $start = $this->currentMonth
            ->copy()
            ->startOfMonth();

        $end = $this->currentMonth
            ->copy()
            ->endOfMonth();

        /*
         * Config timezone:
         * Asia/Manila
         */
        $today = Carbon::today();

        while ($start <= $end) {

            $date =
                $start->format('Y-m-d');

            $dates[] = [
                'date' => $date,

                'day' => $start->day,

                /*
                 * Today is NOT considered past.
                 */
                'past' => $start
                    ->copy()
                    ->startOfDay()
                    ->lt($today),

                /*
                 * Admin blocked date.
                 */
                'available' => ! in_array(
                    $date,
                    $this->blockedDates,
                    true
                ),
            ];

            $start->addDay();
        }

        return $dates;
    }

    /*
    |--------------------------------------------------------------------------
    | Select Date
    |--------------------------------------------------------------------------
    */

    public function selectDate($date)
    {
        $selected =
            Carbon::parse($date)
                ->startOfDay();

        /*
         * Do not allow yesterday or older.
         */
        if (
            $selected->lt(
                Carbon::today()
            )
        ) {
            return;
        }

        /*
         * Do not allow blocked date.
         */
        if (
            in_array(
                $selected->format('Y-m-d'),
                $this->blockedDates,
                true
            )
        ) {
            return;
        }

        $this->selectedDate =
            $selected->format('Y-m-d');

        /*
         * Reset time when changing date.
         *
         * Important because a previously-selected
         * time may not be available on the new date.
         */
        $this->timeSlot = null;

        session([
            'selectedDate' =>
                $this->selectedDate,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Calendar Start Day
    |--------------------------------------------------------------------------
    */

    public function getStartDayProperty()
    {
        /*
         * Blade calendar starts with Sunday.
         *
         * Carbon dayOfWeek:
         * Sunday = 0
         * Monday = 1
         * ...
         */
        return $this->currentMonth
            ->copy()
            ->startOfMonth()
            ->dayOfWeek;
    }

    /*
    |--------------------------------------------------------------------------
    | Base Time Slots
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
    | Available Time Slots
    |--------------------------------------------------------------------------
    |
    | The dropdown will contain ONLY:
    |
    | - future times
    | - unbooked times
    |
    */

    public function getTimeSlotsProperty()
    {
        /*
         * No date selected yet.
         *
         * We return nothing so the client first
         * needs to choose a date.
         */
        if (! $this->selectedDate) {
            return [];
        }

        $selectedDate =
            Carbon::parse(
                $this->selectedDate
            );

        /*
         * Safety:
         * selected date is already in the past.
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
         * Safety:
         * selected date was blocked.
         */
        if (
            in_array(
                $selectedDate
                    ->format('Y-m-d'),
                $this->blockedDates,
                true
            )
        ) {
            return [];
        }

        /*
         * Get already occupied times.
         *
         * Cancelled appointments are ignored
         * because their slot should become
         * available again.
         */
        $bookedTimes =
            ClientAppointment::query()
                ->whereDate(
                    'appointment_date',
                    $selectedDate
                        ->format('Y-m-d')
                )
                ->whereNotIn(
                    'status',
                    ['cancelled', 'declined']
                )
                ->pluck(
                    'appointment_time'
                )
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
                 * Convert:
                 *
                 * 09:00 AM
                 *
                 * into:
                 *
                 * 09:00:00
                 */
                $databaseTime =
                    Carbon::createFromFormat(
                        'h:i A',
                        $slot
                    )
                    ->format('H:i:s');

                /*
                 * Hide already-booked slot.
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
                 * If selected date is today,
                 * hide slots whose time has
                 * already passed.
                 */
                if (
                    $selectedDate
                        ->isToday()
                ) {
                    $slotDateTime =
                        Carbon::createFromFormat(
                            'Y-m-d h:i A',
                            $selectedDate
                                ->format('Y-m-d')
                                . ' '
                                . $slot
                        );

                    /*
                     * Current or past time should
                     * not be bookable.
                     */
                    if (
                        $slotDateTime
                            ->lte(now())
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
    | Book Appointment
    |--------------------------------------------------------------------------
    */

    public function bookAppointment()
    {
        $this->validate([
            'selectedDate' => [
                'required',
                'date',
            ],

            'timeSlot' => [
                'required',
                'string',
            ],

            'appointmentType' => [
                'required',
                'string',
            ],

            // 'name' => [
            //     'required',
            //     'string',
            //     'max:255',
            // ],

            // 'phone' => [
            //     'required',
            //     'string',
            //     'max:30',
            // ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:10240',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Date validation
        |--------------------------------------------------------------------------
        */

        $selectedDate =
            Carbon::parse(
                $this->selectedDate
            )
            ->startOfDay();

        /*
         * Today is allowed.
         * Yesterday and older are not.
         */
        if (
            $selectedDate
                ->lt(Carbon::today())
        ) {
            $this->addError(
                'selectedDate',
                'Cannot book past dates.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Blocked Date
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $selectedDate
                    ->format('Y-m-d'),
                $this->blockedDates,
                true
            )
        ) {
            $this->addError(
                'selectedDate',
                'This date is not available.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Ensure selected time is one of our valid slots
        |--------------------------------------------------------------------------
        */

        if (
            ! in_array(
                $this->timeSlot,
                $this->baseTimeSlots(),
                true
            )
        ) {
            $this->addError(
                'timeSlot',
                'Invalid appointment time.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Build Appointment Date/Time
        |--------------------------------------------------------------------------
        */

        $appointmentDateTime =
            Carbon::createFromFormat(
                'Y-m-d h:i A',
                $selectedDate
                    ->format('Y-m-d')
                    . ' '
                    . $this->timeSlot
            );

        /*
         * Prevent booking a passed time today.
         */
        if (
            $appointmentDateTime
                ->lte(now())
        ) {
            $this->addError(
                'timeSlot',
                'This appointment time has already passed.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Convert to DB Time
        |--------------------------------------------------------------------------
        */

        $time =
            Carbon::createFromFormat(
                'h:i A',
                $this->timeSlot
            )
            ->format('H:i:s');

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Booking
        |--------------------------------------------------------------------------
        |
        | Ignore cancelled/declined appointments
        | so their slots can be reused.
        |
        */

        $exists =
            ClientAppointment::query()
                ->whereDate(
                    'appointment_date',
                    $selectedDate
                        ->format('Y-m-d')
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
                'timeSlot',
                'This time slot has already been booked.'
            );

            /*
             * Reset it because the dropdown will
             * remove the slot on the next render.
             */
            $this->timeSlot = null;

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize Phone
        |--------------------------------------------------------------------------
        */

        // $phone =
        //     preg_replace(
        //         '/\D+/',
        //         '',
        //         $this->phone
        //     );

        /*
         * Remove existing country prefix.
         */
        // if (
        //     str_starts_with(
        //         $phone,
        //         '63'
        //     )
        // ) {
        //     $phone =
        //         substr(
        //             $phone,
        //             2
        //         );
        // }

        /*
         * Remove leading zero.
         */
        // $phone =
        //     ltrim(
        //         $phone,
        //         '0'
        //     );

        /*
        |--------------------------------------------------------------------------
        | Save
        |--------------------------------------------------------------------------
        */

        $documentPath = null;

        if ($this->document) {
            $documentPath = $this->document->store(
                'appointment-documents',
                'public'
            );
        }

        ClientAppointment::create([
            'user_id' =>
                auth()->id(),

            'appointment_date' =>
                $selectedDate
                    ->format('Y-m-d'),

            'appointment_time' =>
                $time,

            'appointment_type' =>
                $this->appointmentType,

            // 'name' =>
            //     $this->name,

            // 'phone' =>
            //     '+63' . $phone,

            'notes' =>
                $this->notes,

            'document_path' =>
                $documentPath,

            'status' =>
                'pending',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        session()->flash(
            'appointment_success',
            true
        );

        session()->forget(
            'selectedDate'
        );

        return redirect()
            ->route('client.appointment');
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm Appointment
    |--------------------------------------------------------------------------
    */

    public function confirmAppointmentConfirmation(
        $date
    ) {
        /*
         * Validate before showing confirmation.
         *
         * This avoids displaying confirmation
         * when required information is missing.
         */
        $this->validate([
            'selectedDate' => [
                'required',
                'date',
            ],

            'timeSlot' => [
                'required',
            ],

            'appointmentType' => [
                'required',
            ],

            // 'name' => [
            //     'required',
            //     'string',
            // ],

            // 'phone' => [
            //     'required',
            // ],
            'document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,doc,docx',
                'max:10240',
            ],
        ]);

        $formattedDate =
            Carbon::parse($date)
                ->format('F d, Y');

        $this->dialog()->confirm([
            'title' =>
                'Confirm Appointment?',

            'description' =>
                'Do you want to book this appointment on '
                . $formattedDate
                . ' at '
                . $this->timeSlot
                . '?',

            'acceptLabel' =>
                'Yes, confirm booking',

            'rejectLabel' =>
                'Cancel',

            'method' =>
                'bookAppointment',

            'icon' =>
                'question',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Client Appointments
    |--------------------------------------------------------------------------
    */

    public function getAppointmentsProperty()
    {
        return ClientAppointment::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->where(
                'status',
                $this->activeTab
            )
            ->latest()
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Counts
    |--------------------------------------------------------------------------
    */

    public function getPendingCountProperty()
    {
        return ClientAppointment::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->where(
                'status',
                'pending'
            )
            ->count();
    }

    public function getApprovedCountProperty()
    {
        return ClientAppointment::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->where(
                'status',
                'approved'
            )
            ->count();
    }

    public function getCompletedCountProperty()
    {
        return ClientAppointment::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->where(
                'status',
                'completed'
            )
            ->count();
    }

    public function getCancelledCountProperty()
    {
        return ClientAppointment::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->where(
                'status',
                'cancelled'
            )
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Cancel Appointment
    |--------------------------------------------------------------------------
    */

    public function cancelAppointment(
        $id
    ) {
        $appointment =
            ClientAppointment::query()
                ->where(
                    'user_id',
                    auth()->id()
                )
                ->findOrFail($id);

        if (
            $appointment->status
                !== 'pending'
        ) {
            return;
        }

        $appointment->update([
            'status' =>
                'cancelled',
        ]);

        $this->notification()->success(
            'Appointment Cancelled',
            'Your appointment has been cancelled.'
        );
    }

    public function confirmCancelAppointment(
        $id
    ) {
        $this->dialog()->confirm([
            'title' =>
                'Cancel Appointment?',

            'description' =>
                'Are you sure you want to cancel this appointment?',

            'acceptLabel' =>
                'Yes, Cancel',

            'rejectLabel' =>
                'No',

            'method' =>
                'cancelAppointment',

            'params' =>
                $id,

            'icon' =>
                'warning',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Reload
    |--------------------------------------------------------------------------
    */

    public function reloadWeb()
    {
        $this->dispatch(
            'reload'
        );

        return redirect()
            ->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'livewire.client.appointment-page',
            [
                'dates' =>
                    $this->dates,

                'startDay' =>
                    $this->startDay,

                'timeSlots' =>
                    $this->timeSlots,

                'appointments' =>
                    $this->appointments,
            ]
        );
    }
}