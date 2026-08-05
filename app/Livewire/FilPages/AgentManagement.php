<?php

namespace App\Livewire\FilPages;

use App\Models\User;
use App\Models\UserInfo;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use WireUi\Traits\Actions;

class AgentManagement extends Component
{

    use Actions;

    public $agent;

    public $name, $email, $phone, $password, $is_active = 1;

    public $commissionPercentage;
    public $professionalAgentId;
    public $realEstateLicenseNumber;

    public $selectedAgentMember, $editName, $editEmail, $editPhone, $editPassword, $edit_is_active;

    public $editCommissionPercentage;
    public $editProfessionalAgentId;
    public $editRealEstateLicenseNumber;

    public function mount()
    {
        $this->loadAgents();
    }

    private function loadAgents(): void
    {
        $this->agent = User::query()
            ->with('info')
            ->where('role', 'agent')
            ->latest()
            ->get();
    }

    public function createAgentMemberAccount()
    {
        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
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
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'commissionPercentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'professionalAgentId' => [
                'nullable',
                'string',
                'max:100',
                'unique:user_infos,professional_agent_id',
            ],

            'realEstateLicenseNumber' => [
                'nullable',
                'string',
                'max:100',
                'unique:user_infos,real_estate_license_number',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'agent',
            'is_active' => $validated['is_active'],
            'is_verified' => 0,
        ]);

        UserInfo::create([
            'user_id' => $user->id,
            'phone' => $this->normalizePhilippinePhone(
                $validated['phone']
            ),

            'commission_percentage' =>
                $validated['commissionPercentage'] ?? null,

            'professional_agent_id' =>
                $validated['professionalAgentId'] ?: null,

            'real_estate_license_number' =>
                $validated['realEstateLicenseNumber'] ?: null,
        ]);

        $this->resetCreateForm();

        Notification::make()
            ->title('Success!')
            ->body('Agent account created successfully.')
            ->success()
            ->send();

        $this->loadAgents();

        $this->reloadWeb();
    }

    public function getSelectedAgentMember($id)
    {
        $agent = User::query()
            ->with('info')
            ->where('role', 'agent')
            ->findOrFail($id);

        $this->selectedAgentMember = $agent->id;

        $this->editName = $agent->name;
        $this->editEmail = $agent->email;
        $this->editPhone = $agent->info?->phone;
        $this->editPassword = null;
        $this->edit_is_active = $agent->is_active;

        $this->editCommissionPercentage =
            $agent->info?->commission_percentage;

        $this->editProfessionalAgentId =
            $agent->info?->professional_agent_id;

        $this->editRealEstateLicenseNumber =
            $agent->info?->real_estate_license_number;
    }

    public function editAgentMemberAccount()
    {
        if (! $this->selectedAgentMember) {
            return;
        }

        $agentInfoId = UserInfo::query()
            ->where('user_id', $this->selectedAgentMember)
            ->value('id');

        $validated = $this->validate([
            'editName' => [
                'required',
                'string',
                'max:255',
            ],

            'editEmail' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->selectedAgentMember),
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
            ],

            'editCommissionPercentage' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'editProfessionalAgentId' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique(
                    'user_infos',
                    'professional_agent_id'
                )->ignore($agentInfoId),
            ],

            'editRealEstateLicenseNumber' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique(
                    'user_infos',
                    'real_estate_license_number'
                )->ignore($agentInfoId),
            ],
        ]);

        $user = User::query()
            ->where('role', 'agent')
            ->findOrFail($this->selectedAgentMember);

        $userData = [
            'name' => $validated['editName'],
            'email' => $validated['editEmail'],
            'is_active' => $validated['edit_is_active'],
        ];

        if (! empty($validated['editPassword'])) {
            $userData['password'] = Hash::make(
                $validated['editPassword']
            );
        }

        $user->update($userData);

        /*
         * updateOrCreate also works if an older agent does not yet
         * have a UserInfo record.
         */
        $user->info()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'phone' => $this->normalizePhilippinePhone(
                    $validated['editPhone']
                ),

                'commission_percentage' =>
                    $validated['editCommissionPercentage'] ?? null,

                'professional_agent_id' =>
                    $validated['editProfessionalAgentId'] ?: null,

                'real_estate_license_number' =>
                    $validated['editRealEstateLicenseNumber'] ?: null,
            ]
        );

        Notification::make()
            ->title('Updated!')
            ->body('Agent member updated successfully.')
            ->success()
            ->send();

        $this->loadAgents();

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

    private function normalizePhilippinePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '63')) {
            $digits = substr($digits, 2);
        }

        $digits = ltrim($digits, '0');

        return '+63' . $digits;
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
