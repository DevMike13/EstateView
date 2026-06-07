<?php

namespace App\Livewire\FilPages;

use App\Models\User;
use App\Models\UserInfo;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use WireUi\Traits\Actions;

class AgentManagement extends Component
{

    use Actions;

    public $agent;

    public $name, $email, $phone, $password, $is_active = 1;

    public $selectedAgentMember, $editName, $editEmail, $editPhone, $editPassword, $edit_is_active;

    public function mount()
    {
        $this->agent = User::where('role', 'agent')->latest()->get();
    }

    public function createAgentMemberAccount()
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
            'role' => 'agent',
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
            ->body("Agent Account Created")
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function getSelectedAgentMember($id)
    {
        $agent = User::with('info')->findOrFail($id);

        $this->selectedAgentMember = $agent->id;

        $this->editName = $agent->name;
        $this->editEmail = $agent->email;
        $this->edit_is_active = $agent->is_active;

        $this->editPhone = $agent->info->phone ?? null;
    }

    public function editAgentMemberAccount()
    {
        if (!$this->selectedAgentMember) return;

        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $this->selectedAgentMember,
            'editPhone' => 'required|string|max:20',
            'edit_is_active' => 'required|boolean',
            'editPassword' => 'nullable|min:8',
        ]);

        $user = User::findOrFail($this->selectedAgentMember);

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
            ->body('Agent Member updated successfully.')
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function editAgentMemberConfirmation($name){
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => "Do you want to edit this agent info with name . ".  html_entity_decode('<span class="text-red-600 underline">' . $name . '</span>') . " ?",
            'acceptLabel' => 'Yes, update it',
            'method'      => 'editAgentMemberAccount',
            'icon'        => 'error',
            'params'      => $name
        ]);
    }

    public function deleteAgentMember($id)
    {
        $agent = User::findOrFail($id);

        $agent->info()->delete();
        
        $agent->delete();

        Notification::make()
            ->title('Deleted!')
            ->body('Agent Member removed successfully.')
            ->success()
            ->send();

        $this->reloadWeb();
    }

    public function deleteAgentMemberConfirmation($id, $agentName){
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => "Do you want to remove this agent Name: ".  html_entity_decode('<span class="text-red-600 underline">' . $agentName . '</span>') . " ?",
            'acceptLabel' => 'Yes, delete it',
            'method'      => 'deleteAgentMember',
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
        return view('livewire.fil-pages.agent-management');
    }
}
