<?php

namespace App\Livewire\FilPages;

use App\Models\PaymentQrCode;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;
use WireUi\Traits\Actions;

class PaymentQrCodes extends Component
{
    use WithFilePond, Actions, WithFileUploads;

    public $payment_method;
    public $account_name;
    public $account_number;
    public $qr_image;
    public $is_active = true;

    public $editId;
    public $edit_payment_method;
    public $edit_account_name;
    public $edit_account_number;
    public $edit_qr_image;
    public $edit_existing_qr_image;
    public $edit_is_active = true;

    public function save()
    {
        $this->validate([
            'payment_method' => 'required|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'qr_image' => 'required|image|max:2048',
        ]);

        $path = $this->qr_image->store('payment-qr-codes', 'public');

        PaymentQrCode::create([
            'payment_method' => $this->payment_method,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'qr_image' => $path,
            'is_active' => $this->is_active,
        ]);

        $this->reset([
            'payment_method',
            'account_name',
            'account_number',
            'qr_image',
            'is_active',
        ]);

        $this->is_active = true;

        Notification::make()
            ->title('QR Code Uploaded')
            ->success()
            ->send();
    }

    public function edit($id)
    {
        $qr = PaymentQrCode::findOrFail($id);

        $this->editId = $qr->id;
        $this->edit_payment_method = $qr->payment_method;
        $this->edit_account_name = $qr->account_name;
        $this->edit_account_number = $qr->account_number;
        $this->edit_existing_qr_image = $qr->qr_image;
        $this->edit_is_active = (bool) $qr->is_active;
        $this->edit_qr_image = null;

        $this->dispatch('openModal', name: 'editQrCodeModal');
    }

    public function update()
    {
        $this->validate([
            'edit_payment_method' => 'required|string|max:255',
            'edit_account_name' => 'nullable|string|max:255',
            'edit_account_number' => 'nullable|string|max:255',
            'edit_qr_image' => 'nullable|image|max:2048',
            'edit_is_active' => 'boolean',
        ]);

        $qr = PaymentQrCode::findOrFail($this->editId);

        $path = $qr->qr_image;

        if ($this->edit_qr_image) {
            if ($qr->qr_image && Storage::disk('public')->exists($qr->qr_image)) {
                Storage::disk('public')->delete($qr->qr_image);
            }

            $path = $this->edit_qr_image->store('payment-qr-codes', 'public');
        }

        $qr->update([
            'payment_method' => $this->edit_payment_method,
            'account_name' => $this->edit_account_name,
            'account_number' => $this->edit_account_number,
            'qr_image' => $path,
            'is_active' => $this->edit_is_active,
        ]);

        $this->reset([
            'editId',
            'edit_payment_method',
            'edit_account_name',
            'edit_account_number',
            'edit_qr_image',
            'edit_existing_qr_image',
            'edit_is_active',
        ]);

        Notification::make()
            ->title('QR Code Updated')
            ->success()
            ->send();

        $this->dispatch('reload');
        return redirect()->back();
    }

    public function confirmUpdate()
    {
        $method = ucfirst(str_replace('_', ' ', $this->edit_payment_method));

        $this->dialog()->confirm([
            'title'       => 'Update QR Code?',
            'description' => "Do you want to update the <span class='font-semibold text-blue-600'>{$method}</span> QR code?",
            'acceptLabel' => 'Yes, update it',
            'rejectLabel' => 'Cancel',
            'method'      => 'update',
            'icon'        => 'question',
        ]);
    }

    public function delete($id)
    {
        $qr = PaymentQrCode::findOrFail($id);

        if ($qr->qr_image && Storage::disk('public')->exists($qr->qr_image)) {
            Storage::disk('public')->delete($qr->qr_image);
        }

        $qr->delete();

        Notification::make()
            ->title('QR Code Deleted')
            ->danger()
            ->send();

        $this->dispatch('reload');
        return redirect()->back();
    }

    public function confirmDelete($id)
    {
        $qr = PaymentQrCode::findOrFail($id);

        $method = ucfirst(str_replace('_', ' ', $qr->payment_method));

        $this->dialog()->confirm([
            'title'       => 'Delete QR Code?',
            'description' => "Are you sure you want to delete the <span class='font-semibold text-red-600'>{$method}</span> QR code?",
            'acceptLabel' => 'Yes, delete it',
            'rejectLabel' => 'Cancel',
            'method'      => 'delete',
            'params'      => $id,
            'icon'        => 'error',
        ]);
    }

    public function render()
    {
        return view('livewire.fil-pages.payment-qr-codes', [
            'qrCodes' => PaymentQrCode::latest()->get(),
        ]);
    }
}