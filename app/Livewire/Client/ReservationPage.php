<?php

namespace App\Livewire\Client;

use App\Models\LotReservation;
use App\Models\ReservationPayment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;

#[Title('Reservation')]
class ReservationPage extends Component
{
    use WithFilePond, Actions, WithFileUploads;

    public $activeTab = 'pending';
    
    public $reservationType, $houseModelId, $lotLocationId, $preferredPayment, $reservationNotes;

    public $doc_1x1 = [];
    public $doc_primary_ids = [];
    public $doc_billing = [];
    public $doc_psa = [];
    public $doc_income = [];
    public $doc_tin = [];

    public $lotApiUrl;

    public $reservationId;
    public $paymentMethod;
    public $referenceNo;
    public $proofOfPayment;

    public function mount()
    {
        $this->reservationType = in_array(request('type'), ['House & Lot', 'Lot Only'])
            ? request('type')
            : "House & Lot";

        $this->lotApiUrl = route('api.lots.index', [
            'type' => $this->reservationType
        ]);

        if (request()->has('lot_id')) {
            $this->lotLocationId = request('lot_id');
        }

        if (request()->has('house_model_id')) {
            $this->houseModelId = request('house_model_id');
        }

        if (session()->has('reservation_success')) {
            $this->notification()->success(
                'Reservation Created!',
                session()->get('reservation_success')
            );
        }
    }

    private function calculateReservationFee(LotReservation $reservation): float
    {
        return $reservation->type === 'House & Lot'
            ? 50000
            : 20000;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getReservationsProperty()
    {
        return LotReservation::with([
                'lot',
                'preferredPayment',
                'requiredDocuments',
                'houseModel',
                'latestReservationPayment',
            ])
            ->where('user_id', auth()->id())
            ->where('status', $this->activeTab)
            ->latest()
            ->get();
    }

    public function getPendingCountProperty()
    {
        return LotReservation::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->count();
    }

    public function getAwaitingReservationFeeCountProperty()
    {
        return LotReservation::where('user_id', auth()->id())
            ->where('status', 'awaiting_reservation_fee')
            ->count();
    }

    public function getReservationFeeSubmittedCountProperty()
    {
        return LotReservation::where('user_id', auth()->id())
            ->where('status', 'reservation_fee_submitted')
            ->count();
    }

    public function getApprovedCountProperty()
    {
        return LotReservation::where('user_id', auth()->id())
            ->where('status', 'approved')
            ->count();
    }

    public function getRejectedCountProperty()
    {
        return LotReservation::where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->count();
    }

    // public function updatedReservationType()
    // {
    //     $this->lotLocationId = null;
    //     $this->houseModelId = null;

    //     $this->lotApiUrl = route('api.lots.index', [
    //         'type' => $this->reservationType
    //     ]);
    // }
    public function updatedReservationType($value)
    {
        $this->lotApiUrl = route('api.lots.index', [
            'type' => $value
        ]);

        if ($this->lotLocationId) {
            $exists = \App\Models\Lot::where('id', $this->lotLocationId)
                ->where('type', $value)
                ->exists();

            if (! $exists) {
                $this->lotLocationId = null;
            }
        }
    }

    public function confirmReservation()
    {
        $this->dialog()->confirm([
            'title' => 'Confirm Appointment?',
            'description' => 'Do you want to book this reservation?',
            'acceptLabel' => 'Yes, confirm reservation',
            'method' => 'saveReservation',
            'icon' => 'question'
        ]);

    }

    public function saveReservation()
    {
        $this->validate([
            'reservationType' => 'required',
            'lotLocationId' => 'required',
            'preferredPayment' => 'required',

            'doc_1x1' => 'required|array|min:1',
            'doc_primary_ids' => 'required|array|min:1',
            'doc_billing' => 'required|array|min:1',
            'doc_psa' => 'required|array|min:1',
            'doc_income' => 'required|array|min:1',
            'doc_tin' => 'required|array|min:1',

            // FILE VALIDATION
            'doc_1x1.*' => 'file|max:20480',
            'doc_primary_ids.*' => 'file|max:20480',
            'doc_billing.*' => 'file|max:20480',
            'doc_psa.*' => 'file|max:20480',
            'doc_income.*' => 'file|max:20480',
            'doc_tin.*' => 'file|max:20480',
        ]);

        DB::transaction(function () {

            $reservation = LotReservation::create([
                'type' => $this->reservationType,
                'lot_id' => $this->lotLocationId,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'notes' => $this->reservationNotes,
                'house_model_id' => $this->houseModelId,
                'reserved_at' => now(),
            ]);

            $reservation->preferredPayment()->create([
                'payment_type' => $this->preferredPayment,
            ]);

            $saveDocs = function ($files, $type) use ($reservation) {
                foreach ($files as $file) {

                    $path = $file->store("reservations/{$reservation->id}/{$type}", 'public');

                    $reservation->requiredDocuments()->create([
                        'document_type' => $type,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            };

            $saveDocs($this->doc_1x1, '1x1_picture');
            $saveDocs($this->doc_primary_ids, 'primary_ids');
            $saveDocs($this->doc_billing, 'proof_billing');
            $saveDocs($this->doc_psa, 'psa_documents');
            $saveDocs($this->doc_income, 'proof_income');
            $saveDocs($this->doc_tin, 'tin_id');
        });

        $this->reset([
            'lotLocationId',
            'houseModelId',
            'preferredPayment',
            'doc_1x1',
            'doc_primary_ids',
            'doc_billing',
            'doc_psa',
            'doc_income',
            'doc_tin',
        ]);

        session()->flash('reservation_success', 'Thank you for submitting your reservation. Your reservation is currently under review.');
        
        return $this->redirect(request()->header('referer'), navigate: false);
    }

    public function submitReservationPayment()
    {
        $this->validate([
            'reservationId' => 'required|exists:lot_reservations,id',
            'paymentMethod' => 'required|in:cash,bank_transfer,gcash,maya',
            'referenceNo' => 'nullable|string|max:255',
            'proofOfPayment' => 'required|file|max:20480',
        ]);

        DB::transaction(function () {
            $reservation = LotReservation::where('user_id', auth()->id())
                ->where('status', 'awaiting_reservation_fee')
                ->findOrFail($this->reservationId);

            $path = $this->proofOfPayment->store(
                "reservation-payments/{$reservation->id}",
                'public'
            );

            ReservationPayment::create([
                'lot_reservation_id' => $reservation->id,
                'amount' => $this->calculateReservationFee($reservation),
                'payment_method' => $this->paymentMethod,
                'reference_no' => $this->referenceNo,
                'proof_of_payment' => $path,
                'paid_at' => now(),
                'status' => 'pending',
            ]);

            $reservation->update([
                'status' => 'reservation_fee_submitted',
            ]);
        });

        $this->reset([
            'reservationId',
            'paymentMethod',
            'referenceNo',
            'proofOfPayment',
        ]);

        $this->notification()->success(
            'Payment Submitted',
            'Your reservation fee payment is now waiting for admin verification.'
        );

        $this->dispatch('close-modal', name: 'reservationPayment');

        $this->activeTab = 'reservation_fee_submitted';
    }

    public function render()
    {
        return view('livewire.client.reservation-page');
    }
}
