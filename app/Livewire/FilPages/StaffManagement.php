<?php

namespace App\Livewire\FilPages;

use App\Models\User;
use App\Models\UserInfo;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use WireUi\Traits\Actions;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:8',
            'is_active' => 'required|boolean',
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
    }

    public function editStaffMemberAccount()
    {
        if (!$this->selectedStaffMember) return;

        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $this->selectedStaffMember,
            'editPhone' => 'required|string|max:20',
            'edit_is_active' => 'required|boolean',
            'editPassword' => 'nullable|min:8',
        ]);

        $user = User::findOrFail($this->selectedStaffMember);

        $user->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'is_active' => $this->edit_is_active,
        ]);

        if ($this->editPassword) {
            $user->update([
                'password' => Hash::make($this->editPassword),
            ]);
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
