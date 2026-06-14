<main>
    <div class="min-h-screen bg-[#F5F5F5] pb-8">

        <div class="bg-white pt-8 pb-6 px-6 sticky top-0 z-10">

            <div class="text-center mb-8">
                <h1 class="text-4xl lg:text-5xl font-light text-gray-900">
                    My Bills
                </h1>
            </div>

            @if($this->accounts->first())
                @php
                    $mainAccount = $this->accounts->first();
                @endphp

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-light text-gray-900 mb-1">
                                {{ $mainAccount->reservation?->type ?? 'Property Account' }}
                            </h2>

                            <p class="text-sm font-light text-gray-600">
                                {{ Str::headline($mainAccount->payment_scheme) }}
                            </p>
                        </div>

                        <div class="text-right">
                            <div class="text-2xl font-light text-gray-900">
                                ₱{{ number_format($mainAccount->remaining_balance, 2) }}
                            </div>

                            <p class="text-sm font-light text-gray-600 mt-1">
                                Balance
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div class="px-6 pt-6 max-w-5xl mx-auto">

            @forelse($this->accounts as $account)

                @php
                    $billings = $account->billings->sortBy('due_date');

                    $firstPayableBilling = $billings
                        ->whereIn('status', ['unpaid', 'partial'])
                        ->first();

                    $unpaidBillings = $billings
                        ->whereIn('status', ['unpaid', 'partial']);

                    $paidBillings = $billings
                        ->where('status', 'paid');

                    $currentBills = $firstPayableBilling
                        ? collect([$firstPayableBilling])
                        : collect();

                    $upcomingBills = $unpaidBillings
                        ->filter(fn ($billing) => ! $firstPayableBilling || $billing->id !== $firstPayableBilling->id);
                @endphp

                <div
                    x-data="{ tab: 'unpaid' }"
                    class="mb-8"
                >

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-light text-gray-900 mb-1">
                                    {{ $account->reservation?->type ?? 'Property Account' }}
                                </h2>

                                <p class="text-sm font-light text-gray-600">
                                    @if($account->payment_scheme === 'bank_loan')
                                        {{ number_format($account->reservation?->downpayment_percentage ?? 20, 0) }}% Equity Payables
                                    @else
                                        {{ Str::headline($account->payment_scheme) }}
                                    @endif
                                </p>
                            </div>

                            <div class="text-right">
                                <div class="text-2xl font-light text-gray-900">
                                    ₱{{ number_format($account->remaining_balance, 2) }}
                                </div>

                                <p class="text-sm font-light text-gray-600 mt-1">
                                    Balance
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-100 rounded-xl p-1 flex gap-1 mb-6">
                        <button
                            type="button"
                            x-on:click="tab = 'unpaid'"
                            class="flex-1 py-3 rounded-lg font-light transition-all"
                            :class="tab === 'unpaid'
                                ? 'bg-white text-gray-900 shadow-sm'
                                : 'text-gray-600'"
                        >
                            Unpaid
                        </button>

                        <button
                            type="button"
                            x-on:click="tab = 'paid'"
                            class="flex-1 py-3 rounded-lg font-light transition-all"
                            :class="tab === 'paid'
                                ? 'bg-white text-gray-900 shadow-sm'
                                : 'text-gray-600'"
                        >
                            Paid
                        </button>
                    </div>

                    <div x-show="tab === 'unpaid'">

                        <div class="mb-8">
                            <h3 class="text-lg font-light text-gray-900 mb-4">
                                Current Bills
                            </h3>

                            <div class="space-y-4">
                                @forelse($currentBills as $billing)
                                    @include('livewire.client.partials.bill-card', [
                                        'billing' => $billing,
                                        'isPayable' => true,
                                    ])
                                @empty
                                    <div class="text-sm text-gray-400 italic">
                                        No current bills.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-light text-gray-900 mb-4">
                                Upcoming Bills
                            </h3>

                            <div class="space-y-4">
                                @forelse($upcomingBills as $billing)
                                    @include('livewire.client.partials.bill-card', [
                                        'billing' => $billing,
                                        'isPayable' => false,
                                    ])
                                @empty
                                    <div class="text-sm text-gray-400 italic">
                                        No upcoming bills.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>

                    <div x-show="tab === 'paid'">
                        <h3 class="text-lg font-light text-gray-900 mb-4">
                            Paid Bills
                        </h3>

                        <div class="space-y-4">
                            @forelse($paidBillings as $billing)
                                @include('livewire.client.partials.bill-card', [
                                    'billing' => $billing,
                                    'isPayable' => false,
                                ])
                            @empty
                                <div class="text-sm text-gray-400 italic">
                                    No paid bills yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            @empty
                <div class="text-center py-10 border-2 border-dashed rounded-lg text-gray-400">
                    No billing account found.
                </div>
            @endforelse

        </div>

        <x-modal blur name="payBill" align="center">

            <x-card title="Upload Payment Proof">

                <x-select
                    label="Payment Method"
                    wire:model="paymentMethod"
                    :options="[
                        ['id' => 'bank_transfer', 'name' => 'Bank Transfer'],
                        ['id' => 'gcash', 'name' => 'GCash'],
                        ['id' => 'maya', 'name' => 'Maya'],
                        ['id' => 'cash', 'name' => 'Cash'],
                    ]"
                    option-label="name"
                    option-value="id"
                />

                <div class="mt-4">
                    <x-input
                        label="Reference Number"
                        wire:model="referenceNo"
                    />
                </div>

                <div class="mt-4">
                    <label class="text-sm font-medium">
                        Proof of Payment
                    </label>

                    <x-filepond::upload
                        wire:model="proofOfPayment"
                    />
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-2">
                        <x-button flat label="Cancel" x-on:click="close" />

                        <x-button
                            primary
                            label="Submit Payment"
                            wire:click="submitPayment"
                        />
                    </div>
                </x-slot>

            </x-card>

        </x-modal>

    </div>
</main>