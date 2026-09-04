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
use App\Models\Notification;
use App\Models\UserInfo;

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
    public $fullName;

    public function mount()
    {
        $user = Auth::user();

        $info = $user->info;

        if ($user->role === 'agent') {
            $this->fullName = $user->name;
        }

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
        if (Auth::user()->role === 'agent') {
            $this->validate([
                'fullName' => 'required|string|max:50',
                'email'    => 'required|email|max:50|unique:users,email,' . Auth::id(),
                'phone'    => 'required|string|max:20',
            ]);
        } else {
            $this->validate([
                'firstName'  => 'required|string|max:255',
                'middleName' => 'required|string|max:255',
                'lastName'   => 'required|string|max:255',
                'suffix'     => 'nullable|string|max:255',
                'email'      => 'required|email|max:255|unique:users,email,' . Auth::id(),
                'phone'      => 'required|string|max:20',
            ]);
        }

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

        /*
        |--------------------------------------------------------------------------
        | STORE OLD VALUES BEFORE UPDATE
        |--------------------------------------------------------------------------
        */

        $oldUser = [
            'name'  => $user->name,
            'email' => $user->email,
        ];

        $info = $user->info;

        $oldInfo = [
            'first_name'  => $info?->first_name,
            'middle_name' => $info?->middle_name,
            'last_name'   => $info?->last_name,
            'suffix'      => $info?->suffix,
            'phone'       => $info?->phone,
        ];

        /*
        |--------------------------------------------------------------------------
        | UPDATE USERS TABLE
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'agent') {

            $user->updateQuietly([
                'name'  => $this->fullName,
                'email' => $this->email,
            ]);

        } else {

            $user->updateQuietly([
                'name'  => $this->lastName . ", " . $this->firstName,
                'email' => $this->email,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE USER_INFOS TABLE
        |--------------------------------------------------------------------------
        */

        $newPhone = '+63' . ltrim($this->phone, '0');

        if ($user->role === 'agent') {

            $userInfo = $user->info()->firstOrNew([
                'user_id' => $user->id,
            ]);

            $userInfo->phone = $newPhone;

            $userInfo->saveQuietly();

        } else {

            $userInfo = $user->info()->firstOrNew([
                'user_id' => $user->id,
            ]);

            $userInfo->first_name = $this->firstName;
            $userInfo->middle_name = $this->middleName;
            $userInfo->last_name = $this->lastName;
            $userInfo->suffix = $this->suffix;
            $userInfo->phone = $newPhone;

            $userInfo->saveQuietly();
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD COMBINED CHANGES
        |--------------------------------------------------------------------------
        */

        $changes = [];

        if ($user->role === 'agent') {

            if ($oldUser['name'] !== $this->fullName) {
                $changes['name'] = [
                    'label' => 'Full Name',
                    'old'   => $oldUser['name'],
                    'new'   => $this->fullName,
                ];
            }

            if ($oldUser['email'] !== $this->email) {
                $changes['email'] = [
                    'label' => 'Email Address',
                    'old'   => $oldUser['email'],
                    'new'   => $this->email,
                ];
            }

            if ($oldInfo['phone'] !== $newPhone) {
                $changes['phone'] = [
                    'label' => 'Phone Number',
                    'old'   => $oldInfo['phone'],
                    'new'   => $newPhone,
                ];
            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | CLIENT CHANGES
            |--------------------------------------------------------------------------
            |
            | Do not track users.name separately because it is generated from
            | Last Name + First Name. Otherwise changing the last name would show
            | both "Full Name" and "Last Name".
            |
            */

            $normalize = function ($value) {
                if ($value === null) {
                    return '';
                }

                return trim((string) $value);
            };

            if ($oldUser['email'] !== $this->email) {
                $changes['email'] = [
                    'label' => 'Email Address',
                    'old'   => $oldUser['email'],
                    'new'   => $this->email,
                ];
            }

            if (
                $normalize($oldInfo['first_name'])
                !== $normalize($this->firstName)
            ) {
                $changes['first_name'] = [
                    'label' => 'First Name',
                    'old'   => $oldInfo['first_name'],
                    'new'   => $this->firstName,
                ];
            }

            if (
                $normalize($oldInfo['middle_name'])
                !== $normalize($this->middleName)
            ) {
                $changes['middle_name'] = [
                    'label' => 'Middle Name',
                    'old'   => $oldInfo['middle_name'],
                    'new'   => $this->middleName,
                ];
            }

            if (
                $normalize($oldInfo['last_name'])
                !== $normalize($this->lastName)
            ) {
                $changes['last_name'] = [
                    'label' => 'Last Name',
                    'old'   => $oldInfo['last_name'],
                    'new'   => $this->lastName,
                ];
            }

            if (
                $normalize($oldInfo['suffix'])
                !== $normalize($this->suffix)
            ) {
                $changes['suffix'] = [
                    'label' => 'Suffix',
                    'old'   => $oldInfo['suffix'],
                    'new'   => $this->suffix,
                ];
            }

            if (
                $normalize($oldInfo['phone'])
                !== $normalize($newPhone)
            ) {
                $changes['phone'] = [
                    'label' => 'Phone Number',
                    'old'   => $oldInfo['phone'],
                    'new'   => $newPhone,
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE ONE COMBINED NOTIFICATION
        |--------------------------------------------------------------------------
        */

        if (! empty($changes)) {

            $changedFields = collect($changes)
                ->pluck('label')
                ->map(fn ($label) => strtolower($label))
                ->values();

            if ($changedFields->count() === 1) {

                $changesText = $changedFields->first();

            } elseif ($changedFields->count() === 2) {

                $changesText = $changedFields->implode(' and ');

            } else {

                $lastField = $changedFields->pop();

                $changesText = $changedFields->implode(', ')
                    . ', and '
                    . $lastField;
            }

            $isAgent = $user->role === 'agent';

            $notification = Notification::create([
                'title' => $isAgent
                    ? 'Agent Profile Updated'
                    : 'Client Profile Updated',

                'message' => "{$user->name} has made changes to their {$changesText}.",

                'type' => $isAgent
                    ? 'agent_profile_updated'
                    : 'client_personal_info_updated',

                'url' => $isAgent
                    ? route('filament.ev-admin.pages.agent-management')
                    : route('filament.ev-admin.pages.client-records'),

                'data' => [
                    'user_id'    => $user->id,
                    'user_name'  => $user->name,
                    'user_email' => $user->email,
                    'role'       => $user->role,
                    'changes'    => $changes,
                ],

                'created_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | SEND ONLY TO ADMIN + STAFF
            |--------------------------------------------------------------------------
            */

            $staffAdmins = User::whereIn('role', ['admin', 'staff'])
                ->where('id', '!=', auth()->id())
                ->pluck('id');

            $notification->users()->attach(
                $staffAdmins->toArray()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | REFRESH MODEL VALUES
        |--------------------------------------------------------------------------
        */

        $user->refresh();

        /*
        |--------------------------------------------------------------------------
        | SUCCESS MESSAGE
        |--------------------------------------------------------------------------
        */

        $this->notification()->success(
            $title = 'Profile Updated',
            $description = 'Your personal details have been updated successfully!'
        );
    }

    public function updatePassword()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | VALIDATE PASSWORD FIELDS
        |--------------------------------------------------------------------------
        */

        $this->validate(
            [
                'currentPassword' => [
                    'required',
                    'string',
                ],

                'newPassword' => [
                    'required',
                    'string',
                    'min:8',
                    'max:20',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*?&#^()_\-+=]/',
                ],

                'confirmPassword' => [
                    'required',
                    'same:newPassword',
                ],
            ],
            [
                'currentPassword.required' =>
                    'Please enter your current password.',

                'newPassword.required' =>
                    'Please enter a new password.',

                'newPassword.min' =>
                    'The new password must be at least 8 characters.',

                'newPassword.max' =>
                    'The new password must not exceed 20 characters.',

                'newPassword.regex' =>
                    'The new password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',

                'confirmPassword.required' =>
                    'Please confirm your new password.',

                'confirmPassword.same' =>
                    'The password confirmation does not match.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | CHECK CURRENT PASSWORD
        |--------------------------------------------------------------------------
        */

        if (! Hash::check(
            $this->currentPassword,
            $user->password
        )) {
            $this->addError(
                'currentPassword',
                'The current password is incorrect.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | NEW PASSWORD MUST BE DIFFERENT
        |--------------------------------------------------------------------------
        */

        if (Hash::check(
            $this->newPassword,
            $user->password
        )) {
            $this->addError(
                'newPassword',
                'The new password must be different from your current password.'
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE PASSWORD
        |--------------------------------------------------------------------------
        */

        $user->update([
            'password' => Hash::make(
                $this->newPassword
            ),

            // Invalidates "remember me" authentication.
            'remember_token' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | RESET PASSWORD FIELDS
        |--------------------------------------------------------------------------
        */

        $this->reset([
            'currentPassword',
            'newPassword',
            'confirmPassword',
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        $this->notification()->success(
            $title = 'Password Changed',
            $description =
                'Your security password has been updated successfully!'
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
