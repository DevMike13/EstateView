<?php

namespace App\Livewire\Agent;

use App\Models\LotReservation;
use App\Models\Notification;
use App\Models\CommissionRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Agent Dashboard')]
class DashboardPage extends Component
{
    public int $activeClients = 0;

    public int $propertiesManaged = 0;

    public float $commissionEarned = 0;

    public float $commissionPending = 0;

    public $recentActivities;

    public function mount(): void
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData(): void
    {
        $agentId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Active clients
        |--------------------------------------------------------------------------
        |
        | Counts unique clients who have a reservation assigned to this agent.
        | Add or remove statuses here based on your reservation workflow.
        |
        */
        $this->activeClients = LotReservation::query()
            ->where('agent_id', $agentId)
            ->whereIn('status', [
                'pending',
                'approved',
                'reserved',
                'active',
            ])
            ->distinct()
            ->count('user_id');

        /*
        |--------------------------------------------------------------------------
        | Properties managed
        |--------------------------------------------------------------------------
        |
        | Counts unique lots assigned to this agent through reservations.
        |
        */
        $this->propertiesManaged = LotReservation::query()
            ->where('agent_id', $agentId)
            ->whereNotNull('lot_id')
            ->distinct()
            ->count('lot_id');

        /*
        |--------------------------------------------------------------------------
        | Commission
        |--------------------------------------------------------------------------
        |
        | Shows the agent's actual commission amounts instead of the total
        | contract price of properties.
        |
        */
        $commissionQuery = CommissionRequest::query()
            ->where('agent_id', $agentId);

        /*
        |--------------------------------------------------------------------------
        | Commission Earned
        |--------------------------------------------------------------------------
        |
        | Total commission already paid by admin.
        |
        */
        $this->commissionEarned = (float) (clone $commissionQuery)
            ->where('status', 'paid')
            ->sum('requested_amount');

        /*
        |--------------------------------------------------------------------------
        | Commission Pending
        |--------------------------------------------------------------------------
        |
        | Commission already requested by the agent but still waiting
        | for admin processing/payment.
        |
        */
        $this->commissionPending = (float) (clone $commissionQuery)
            ->whereIn('status', [
                'pending',
                'approved',
            ])
            ->sum('requested_amount');

        /*
        |--------------------------------------------------------------------------
        | Recent activity
        |--------------------------------------------------------------------------
        |
        | Your observers already send notifications to the assigned agent,
        | so the agent's latest notifications can serve as recent activity.
        |
        */
        $this->recentActivities = Notification::query()
            ->whereHas('users', function ($query) use ($agentId) {
                $query->where('users.id', $agentId);
            })
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.agent.dashboard-page');
    }
}