<?php

namespace App\Livewire\Agent;

use App\Models\LotReservation;
use App\Models\Notification;
use App\Models\PurchaseAccount;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Agent Dashboard')]
class DashboardPage extends Component
{
    public int $activeClients = 0;

    public int $propertiesManaged = 0;

    public float $totalSales = 0;

    public float $monthlySales = 0;

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
        | Sales
        |--------------------------------------------------------------------------
        |
        | PurchaseAccount belongs to LotReservation through reservation_id.
        | Replace "total_contract_price" if your actual amount column has a
        | different name, such as total_price or contract_price.
        |
        */
        $salesQuery = PurchaseAccount::query()
            ->whereHas('reservation', function ($query) use ($agentId) {
                $query->where('agent_id', $agentId);
            });

        $this->totalSales = (float) (clone $salesQuery)
            ->sum('total_contract_price');

        $this->monthlySales = (float) (clone $salesQuery)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_contract_price');

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
