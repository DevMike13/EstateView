<div class="bg-white">
  <section class="py-24 lg:py-32">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
      
      <div class="text-center mb-16">
        <h1 class="text-4xl lg:text-5xl font-light text-gray-900 mb-4">Cost Breakdown</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">Calculate your payment plan and monthly amortization</p>
      </div>

      <div class="grid lg:grid-cols-2 gap-8">
        
        <div>
          <h2 class="text-2xl font-light text-gray-900 mb-6">Property Details</h2>
          <div class="bg-white shadow-sm p-8 space-y-8">
            
            <div>
              <label class="block text-sm text-gray-700 mb-4 uppercase tracking-wide">Property Type</label>
              <div class="grid grid-cols-2 gap-4">
                @php
                  $options = [
                      'House & Lot' => 'House & Lot',
                      'Lot Only' => 'Lot Only',
                  ];
                @endphp

                @foreach($options as $value => $label)
                    <div>
                        <input 
                            wire:model.live="reservationType" 
                            type="radio" 
                            id="reservationType_{{ Str::slug($value) }}" 
                            name="reservationType" 
                            value="{{ $value }}" 
                            class="hidden peer"
                        >
                        <label
                            for="reservationType_{{ Str::slug($value) }}"
                            class="flex flex-col items-center justify-center w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-xl cursor-pointer transition-all duration-200
                                peer-checked:border-2 peer-checked:border-blue-600 peer-checked:text-blue-600 peer-checked:bg-blue-50/30
                                hover:text-gray-600 hover:bg-gray-50 shadow-sm"
                        >
                            @if($value === 'House & Lot')
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 mb-2">
                                    <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path>
                                    <path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 mb-2">
                                  <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path>
                                  <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            @endif

                            <span class="text-sm font-semibold tracking-wide text-center">{{ $label }}</span>
                        </label>
                    </div>
                @endforeach
              </div>
            </div>

            <div>
              @if ($reservationType && $reservationType === "House & Lot")
                <div class="mt-3">
                    <x-select
                        label="House Model"
                        wire:model.live="houseModelId"
                        placeholder="Select some house model"
                        :async-data="route('api.house-models.index')"
                        :template="[
                            'name'   => 'user-option',
                            'config' => ['src' => 'image']
                        ]"
                        option-label="name"
                        option-value="id"
                        option-description="description"
                    />
                </div>
              @endif
            </div>

            <div>
              <x-select
                key="lot-select-{{ $reservationType }}"
                label="Lot Location"
                wire:model.live="lotLocationId"
                placeholder="Select lot location"
                :async-data="$lotApiUrl"
                option-label="name"
                option-value="id"
                option-description="description"
              />
            </div>

            <div>
              <label class="block text-sm text-gray-700 mb-4 uppercase tracking-wide">Payment Option</label>
              <div class="space-y-3">
                
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                  <label class="flex items-center gap-3 p-4 cursor-pointer hover:bg-gray-50/50 transition">
                    <input type="radio" wire:model.live="paymentOption" name="paymentOption" class="text-blue-600 focus:ring-blue-500" value="bank-loan">
                    <span class="text-sm font-medium text-gray-900">Bank Loan (20% DP / 80% Loan)</span>
                  </label>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                  <label class="flex items-center gap-3 p-4 cursor-pointer hover:bg-gray-50/50 transition">
                    <input type="radio" wire:model.live="paymentOption" name="paymentOption" class="text-blue-600 focus:ring-blue-500" value="cash">
                    <span class="text-sm font-medium text-gray-900">Spot Cash (10% Account Discount)</span>
                  </label>
                </div>

              </div>
            </div>

          </div>
        </div>

        <div>
          <h2 class="text-2xl font-light text-gray-900 mb-6">Cost Summary</h2>
          <div class="bg-gray-900 shadow-sm p-8 text-white space-y-8 rounded-xl">
            
            <div class="flex items-center gap-4 pb-8 border-b border-white/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10 text-gray-400">
                <rect width="16" height="20" x="4" y="2" rx="2"></rect>
                <line x1="8" x2="16" y1="6" y2="6"></line>
                <line x1="16" x2="16" y1="14" y2="18"></line>
                <path d="M16 10h.01"></path>
                <path d="M12 10h.01"></path>
                <path d="M8 10h.01"></path>
                <path d="M12 14h.01"></path>
                <path d="M8 14h.01"></path>
                <path d="M12 18h.01"></path>
                <path d="M8 18h.01"></path>
              </svg>
              <div>
                <div class="text-sm text-white/60 uppercase tracking-wide mb-1">Total Contract Price</div>
                <div class="text-4xl font-light tracking-tight text-white">
                    ₱{{ number_format($totalContractPrice, 2) }}
                </div>
              </div>
            </div>

            <div class="space-y-6">
              @if(!$paymentOption)
                <div class="text-center py-8 text-white/50 border border-dashed border-white/10 rounded-lg">
                    Please select a payment option to see the detailed breakdown
                </div>
              @else
                <div class="space-y-4 text-sm">
                  
                  @if($lotPrice > 0)
                    <div class="flex justify-between items-center text-white/70">
                      <span>Lot Base Value:</span>
                      <span class="font-mono text-white">₱{{ number_format($lotPrice, 2) }}</span>
                    </div>
                  @endif

                  @if($reservationType === 'House & Lot' && $housePrice > 0)
                    <div class="flex justify-between items-center text-white/70">
                      <span>House Model Base Value:</span>
                      <span class="font-mono text-white">₱{{ number_format($housePrice, 2) }}</span>
                    </div>
                  @endif

                  <hr class="border-white/10 my-2">

                  @if($paymentOption === 'cash')
                    <div class="flex justify-between items-center text-green-400">
                      <span>10% Spot Cash Discount:</span>
                      <span class="font-mono">- ₱{{ number_format($cashDiscount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-base border-t border-white/20 pt-4 mt-2 font-semibold">
                      <span>Net Cash Amount Due:</span>
                      <span class="text-2xl font-light text-green-400 font-mono">₱{{ number_format($totalContractPrice, 2) }}</span>
                    </div>
                  @endif

                  @if($paymentOption === 'bank-loan')
                    <div class="flex justify-between items-center text-white/70">
                      <span>Required Downpayment Equity (20%):</span>
                      <span class="font-mono text-white">₱{{ number_format($downpaymentAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-white/70">
                      <span>Financed Loanable Balance (80%):</span>
                      <span class="font-mono text-white">₱{{ number_format($loanableAmount, 2) }}</span>
                    </div>

                    <div class="py-3 border-t border-b border-white/10 my-3">
                      <label class="block text-xs text-white/50 uppercase tracking-wider mb-2">Amortization Loan Term</label>
                      <select wire:model.live="loanTerm" class="w-full bg-gray-800 border border-white/20 rounded-lg p-2.5 text-white text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <option value="5">5 Years Term (Short-term)</option>
                        <option value="10">10 Years Term (Medium-term)</option>
                        <option value="15">15 Years Term (Standard)</option>
                        <option value="20">20 Years Term (Long-term)</option>
                      </select>
                    </div>

                    <div class="flex flex-col bg-blue-600/20 border border-blue-500/30 p-5 rounded-xl mt-4">
                      <span class="text-xs text-blue-300 uppercase tracking-wide mb-1 font-medium">Estimated Monthly Amortization (7% Fixed Rate)</span>
                      <span class="text-3xl font-light font-mono text-blue-400">
                        ₱{{ number_format($monthlyAmortization, 2) }}
                        <span class="text-xs font-normal text-white/50 font-sans"> / month</span>
                      </span>
                    </div>
                  @endif

                </div>
              @endif
            </div>

            <div class="bg-amber-500/10 border border-amber-500/20 p-5 rounded-xl">
              <p class="text-xs text-amber-200/80 leading-relaxed">
                <strong>Disclaimer Notice:</strong> This simulation provides estimations only. Final official computations may vary based on exact lot layout coordinates, project locations, specific developer fees, and actual approved banking interest values during validation.
              </p>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>
</div>