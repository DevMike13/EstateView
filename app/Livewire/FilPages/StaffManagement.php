<?php

namespace App\Livewire\FilPages;

use App\Models\User;
use App\Models\UserInfo;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use WireUi\Traits\Actions;
use Illuminate\Support\Facades\DB;

class StaffManagement extends Component
{
    use Actions;

    public $staff;

    public $name, $email, $phone, $password, $is_active = 1;

    public $selectedStaffMember, $editName, $editEmail, $editPhone, $editPassword, $edit_is_active;

    public function mount()
    {
        $this->staff = User::where('role', 'staff')->latest()->get();
    }

    public function createStaffMemberAccount()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:50',
            ],

            'email' => [
                'required',
                'email',
                'max:50',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        // Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'staff',
            'is_active' => $this->is_active,
            'is_verified' => 0,
        ]);

        // Create user info
        UserInfo::create([
            'user_id' => $user->id,
            'phone' => '+63' . $this->phone,
        ]);

        // Reset form
        $this->reset(['name', 'email', 'phone', 'password', 'is_active']);

        Notification::make()
            ->title('Success!')
            ->body("Staff Account Created")
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function getSelectedStaffMember($id)
    {
        $staff = User::with('info')->findOrFail($id);

        $this->selectedStaffMember = $staff->id;

        $this->editName = $staff->name;
        $this->editEmail = $staff->email;
        $this->edit_is_active = $staff->is_active;

        $this->editPhone = $staff->info->phone ?? null;
        $this->editPassword = null;
    }

    public function editStaffMemberAccount()
    {
        if (!$this->selectedStaffMember) return;

        $this->validate([
            'editName' => [
                'required',
                'string',
                'max:50',
            ],

            'editEmail' => [
                'required',
                'email',
                'max:50',
                'unique:users,email,' . $this->selectedStaffMember,
            ],

            'editPhone' => [
                'required',
                'string',
                'max:20',
            ],

            'edit_is_active' => [
                'required',
                'boolean',
            ],

            'editPassword' => [
                'nullable',
                'string',
                'min:8',
                'max:20',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ]);

        $user = User::findOrFail($this->selectedStaffMember);

        $wasDeactivated = (bool) $user->is_active === true && (bool) $this->edit_is_active === false;

        if (
            ! empty($this->editPassword) &&
            Hash::check($this->editPassword, $user->password)
        ) {
            $this->addError(
                'editPassword',
                'The new password must be different from the current password.'
            );

            return;
        }

        $userData = [
            'name' => $this->editName,
            'email' => $this->editEmail,
            'is_active' => $this->edit_is_active,
        ];

        $passwordChanged = false;

        if (! empty($this->editPassword)) {
            $userData['password'] = Hash::make($this->editPassword);

            // Invalidate Remember Me login
            $userData['remember_token'] = null;

            $passwordChanged = true;
        }

        $user->update($userData);

        if ($passwordChanged || $wasDeactivated) {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

        $phone = $this->editPhone;
        $phone = preg_replace('/^\+63/', '', $phone);
        $phone = ltrim($phone, '0');

        $user->info()->update([
            'phone' => '+63' . $phone,
        ]);

        Notification::make()
            ->title('Updated!')
            ->body('Staff Member updated successfully.')
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function editStaffMemberConfirmation($name){
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => "Do you want to edit this staff info with name . ".  html_entity_decode('<span class="text-red-600 underline">' . $name . '</span>') . " ?",
            'acceptLabel' => 'Yes, update it',
            'method'      => 'editStaffMemberAccount',
            'icon'        => 'error',
            'params'      => $name
        ]);
    }

    public function deleteStaffMember($id)
    {
        $staff = User::findOrFail($id);

        $staff->info()->delete();
        
        $staff->delete();

        Notification::make()
            ->title('Deleted!')
            ->body('Staff Member removed successfully.')
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function deleteStaffMemberConfirmation($id, $staffName){
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => "Do you want to remove this staff Name: ".  html_entity_decode('<span class="text-red-600 underline">' . $staffName . '</span>') . " ?",
            'acceptLabel' => 'Yes, delete it',
            'method'      => 'deleteStaffMember',
            'icon'        => 'error',
            'params'      => $id
        ]);
    }

    public function reloadWeb(){

        $this->dispatch('reload');
        return redirect()->back();

    }
    
    public function render()
    {
        return view('livewire.fil-pages.staff-management');
    }
}
