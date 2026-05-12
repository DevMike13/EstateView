<?php

namespace App\Livewire\Auth;

use App\Mail\NewOtp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Account Verification')]
class AccountVerification extends Component
{
    public $user;
    public $otp = [];
    public $user_id;

    public $resendCountdown = 60;
    public $nextResendTime;

    protected $listeners = ['tick'];

    public function mount($user_id) {
        $this->user_id = $user_id;
        $this->user = User::findOrFail($this->user_id);
        $this->nextResendTime = session()->get("otp_next_resend_{$this->user_id}", now()->timestamp);

        if (now()->timestamp >= $this->nextResendTime) {
            $this->nextResendTime = now()->timestamp;
        }
    }

    public function verifyOtp()
    {
        $enteredOtp = implode('', $this->otp);
        if ($this->user->otp === $enteredOtp) {
            $this->user->is_verified = true;
            $this->user->otp = null;
            $this->user->save();
            
            auth()->login($this->user);
            return redirect()->route('user.home');
        } else {
            session()->flash('error', 'Invalid OTP');
        }
    }

    public function resendOtp()
    {
        if ($this->canResend()) {
            $newOtp = rand(100000, 999999);
            $this->user->otp = $newOtp;
            $this->user->save();

            Mail::to($this->user->email)->send(new NewOtp($newOtp));

            $this->nextResendTime = now()->addSeconds($this->resendCountdown)->timestamp;

            session()->put("otp_next_resend_{$this->user_id}", $this->nextResendTime);

            $this->dispatch('start-otp-countdown', [
                'seconds' => $this->resendCountdown
            ]);

            $this->dispatch('reload-page');
        }
    }

    public function canResend()
    {
        return now()->timestamp >= $this->nextResendTime;
    }
    
    public function render()
    {
        return view('livewire.auth.account-verification', [
            'remainingSeconds' => max($this->nextResendTime - now()->timestamp, 0)
        ]);
    }
}
