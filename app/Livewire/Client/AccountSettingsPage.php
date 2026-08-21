<?php

namespace App\Livewire\Client;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Livewire\Attributes\Title;
use WireUi\Traits\Actions;

#[Title('Account')]
class AccountSettingsPage extends Component
{
    use WithFileUploads, Actions;

    // Profile Fields
    public $firstName;
    public $middleName;
    public $lastName;
    public $suffix;
    public $email;
    public $phone;
    public $photo;

    // Security Password Fields
    public $currentPassword;
    public $newPassword;
    public $confirmPassword;

    public $professionalAgentId;
    public $realEstateLicenseNumber;
    public $commissionPercentage;

    public function mount()
    {
        $user = Auth::user();

        $info = $user->info;

        $firstName = $info?->first_name;
        $middleName = $info?->middle_name;
        $lastName = $info?->last_name;

        if (
            empty($firstName)
            && empty($lastName)
            && ! empty($user->name)
        ) {
            $fullName = trim($user->name);

            if (str_contains($fullName, ',')) {

                [$last, $first] = array_map(
                    'trim',
                    explode(',', $fullName, 2)
                );

                $firstName = $first;
                $lastName = $last;

            } else {

                $parts = preg_split(
                    '/\s+/',
                    $fullName
                );

                if (count($parts) === 1) {

                    $firstName = $parts[0];

                } elseif (count($parts) >= 2) {

                    $firstName = array_shift($parts);

                    $lastName = array_pop($parts);

                    if (
                        empty($middleName)
                        && count($parts)
                    ) {
                        $middleName = implode(
                            ' ',
                            $parts
                        );
                    }
                }
            }
        }

        $this->firstName =
            $firstName ?? '';

        $this->middleName =
            $middleName ?? '';

        $this->lastName =
            $lastName ?? '';

        $this->suffix =
            $info?->suffix ?? '';

        $this->email =
            $user->email;


        // Strip "+63" for cleaner editing
        $rawPhone =
            $info?->phone ?? '';

        $this->phone =
            str_replace(
                '+63',
                '',
                $rawPhone
            );

        $this->professionalAgentId =
            $info?->professional_agent_id ?? null;

        $this->realEstateLicenseNumber =
            $info?->real_estate_license_number ?? null;

        $this->commissionPercentage =
            $info?->commission_percentage ?? null;
    }

    public function confirmProfileUpdate()
    {
        // First validate before showing the dialog box
        $this->validate([
            'firstName'  => 'required|string|max:255',
            'middleName' => 'required|string|max:255',
            'lastName'   => 'required|string|max:255',
            'suffix'     => 'nullable|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone'      => 'required|string|max:20',
        ]);

        $this->dialog()->confirm([
            'title'       => 'Save Account Changes?',
            'description' => 'Are you sure you want to update your profile information?',
            'icon'        => 'question',
            'acceptLabel' => 'Yes, save changes',
            'rejectLabel' => 'Cancel',
            'method'      => 'updateProfile',
        ]);
    }

    public function updateProfile()
    {
        $user = Auth::user();

        // 1. Update Core User Info Table Record
        $user->update([
            'name'  => $this->lastName . ", " . $this->firstName,
            'email' => $this->email,
        ]);

        // 2. Update Secondary Profile Info Table Record
        $user->info()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'last_name' => $this->lastName,
                'suffix' => $this->suffix,
                'phone' => '+63' . ltrim($this->phone, '0'),
            ]
        );

       
        $this->notification()->success(
            $title = 'Profile Updated',
            $description = 'Your personal details have been updated successfully!'
        );
    }

    public function updatePassword()
    {
        $user = Auth::user();

        $this->validate([
            'currentPassword' => 'required|string',
            'newPassword'     => 'required|string|min:8|max:255',
            'confirmPassword' => 'required|same:newPassword',
        ]);

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'The provided password does not match our records.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset(['currentPassword', 'newPassword', 'confirmPassword']);

        $this->notification()->success(
            $title = 'Password Changed',
            $description = 'Your security password has been updated successfully!'
        );
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:2048', // 2MB Max Size Check
        ]);

        $user = Auth::user();

        // Remove old avatar image file asset if it exists in local storage
        if ($user->profile_picture) {
            $oldPath = str_replace(asset('storage/'), '', $user->profile_picture);
            Storage::disk('public')->delete($oldPath);
        }

        // Store and assign new picture link path
        $path = $this->photo->store('avatars', 'public');
        $user->update([
            'profile_picture' => asset('storage/' . $path),
        ]);

        $this->notification()->success(
            $title = 'Photo Uploaded',
            $description = 'Your profile picture has been updated.'
        );
    }

    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            $oldPath = str_replace(asset('storage/'), '', $user->profile_picture);
            Storage::disk('public')->delete($oldPath);
        }

        $user->update([
            'profile_picture' => null,
        ]);

        $this->notification()->success(
            $title = 'Photo Removed',
            $description = 'Your profile photo has been completely removed.'
        );
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.client.account-settings-page');
    }
}
