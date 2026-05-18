<?php

namespace App\Livewire\Client;

use App\Models\LotReservation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;

class ReservationPage extends Component
{
    use WithFilePond, Actions, WithFileUploads;
    
    public $reservationType, $houseModelId, $lotLocationId, $preferredPayment, $reservationNotes;

    public $doc_1x1 = [];
    public $doc_primary_ids = [];
    public $doc_billing = [];
    public $doc_psa = [];
    public $doc_income = [];
    public $doc_tin = [];

    public $lotApiUrl;

    public function mount()
    {
        $this->reservationType = "House & Lot";

        $this->lotApiUrl = route('api.lots.index', [
            'type' => $this->reservationType
        ]);

        if (session()->has('reservation_success')) {
            $this->notification()->success(
                'Reservation Created!',
                session()->get('reservation_success')
            );
        }
    }

    public function updatedReservationType()
    {
        $this->lotLocationId = null;
        $this->houseModelId = null;

        $this->lotApiUrl = route('api.lots.index', [
            'type' => $this->reservationType
        ]);
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

        session()->flash(
            'reservation_success',
            'Thank you for submitting your reservation. Your reservation is currently under review.'
        );

        return $this->redirect(url()->current(), navigate: true);
    }

    public function render()
    {
        return view('livewire.client.reservation-page');
    }
}
