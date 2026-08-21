<div class="w-full h-full flex flex-col justify-center">
    <div class="flex justify-center items-center md:mt-10 mb-10">
        <a href="/">
            <img src="{{ asset('/images/estate-view-logo.png') }}" alt="" class="h-12">
        </a>
    </div>
    <div class="w-[90%] md:w-[55%] h-auto flex flex-col justify-center items-center gap-1 md:gap-0 mt-28 md:mt-0 py-5 px-5 md:pt-12 md:pb-10 bg-white mx-auto rounded-2xl">
        <h1 class="md:text-3xl text-2xl font-medium text-center">Create Account</h1>
        <p class="text-center">Register as a new client</p>
        <div class="w-full flex flex-col justify-center items-center">
            <form wire:submit.prevent="register" class="w-full flex justify-center">
                <div  class="w-full md:w-[90%]">
                    <ul class="relative flex flex-row gap-x-2 mb-3 md:mb-10 mt-10 overflow-x-auto">
                        <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group">
                            <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                            <span class="size-7 flex justify-center items-center flex-shrink-0 font-medium text-gray-800 rounded-full {{ $currentStep == 1 || $isFinishedStepOne == true ? 'bg-[#101727] text-white' : 'text-gray-800'}}">
                                <span class="{{ $isFinishedStepOne == true ? 'hidden' : ''}}">1</span>
                                <svg class="flex-shrink-0 size-3 {{ $isFinishedStepOne == true ? 'block' : 'hidden'}}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium text-gray-800 dark:text-neutral-200">
                                Personal Information
                            </span>
                            </span>
                            <div class="w-full h-px flex-1 {{ $isFinishedStepOne == true ? 'bg-blue-600' : 'bg-gray-200'}}"></div>
                        </li>
                    
                        <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group">
                            <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                            <span class="size-7 flex justify-center items-center flex-shrink-0 font-medium text-gray-800 rounded-full {{ $currentStep == 2 || $isFinishedStepOne == true ? 'bg-[#101727] text-white' : 'text-gray-800'}}">
                                <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">2</span>
                                <svg class="hidden flex-shrink-0 size-3 hs-stepper-success:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium text-gray-800 dark:text-neutral-200">
                                Create Account
                            </span>
                            </span>
                            <div class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-teal-600 dark:bg-neutral-700 dark:hs-stepper-success:bg-blue-600 dark:hs-stepper-completed:bg-teal-600"></div>
                        </li>

                        <li class="flex items-center gap-x-2 shrink basis-0 flex-1 group">
                            <span class="min-w-7 min-h-7 group inline-flex items-center text-xs align-middle">
                            <span class="size-7 flex justify-center items-center flex-shrink-0 font-medium text-gray-800 rounded-full {{ $currentStep == 3 || $isFinishedStepTwo == true ? 'bg-[#101727] text-white' : 'text-gray-800'}}">
                                <span class="hs-stepper-success:hidden hs-stepper-completed:hidden">3</span>
                                <svg class="hidden flex-shrink-0 size-3 hs-stepper-success:block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span class="ms-2 text-sm font-medium text-gray-800 dark:text-neutral-200">
                                Verify Account
                            </span>
                            </span>
                            <div class="w-full h-px flex-1 bg-gray-200 group-last:hidden hs-stepper-success:bg-blue-600 hs-stepper-completed:bg-teal-600 dark:bg-neutral-700 dark:hs-stepper-success:bg-blue-600 dark:hs-stepper-completed:bg-teal-600"></div>
                        </li>
                        <!-- End Item -->
                    </ul>
                    @if ($currentStep == 1)
                        <div class="md:p-2 p-4 h-auto bg-gray-50 flex justify-center items-center border border-dashed border-gray-200 rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                            <div class="w-full md:w-[95%]">
                                <div class="w-full flex flex-col md:flex-row gap-2">
                                    <div class="w-full space-y-3">
                                        <div>
                                            <label for="hs-trailing-icon" class="block text-sm font-medium mb-2 dark:text-white"></label>
                                            <div class="relative">
                                                <x-input label="First name" placeholder="Ex: Juan" class="py-3 -mt-1" wire:model.blur="firstName" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full space-y-3">
                                        <div>
                                            <label for="hs-trailing-icon" class="block text-sm font-medium mb-2 dark:text-white"></label>
                                            <div class="relative">
                                                <x-input label="Middle name" placeholder="Ex: Reyes" class="py-3 -mt-1" wire:model.blur="middleName" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full flex flex-col md:flex-row gap-2">
                                    <div class="w-full space-y-3">
                                        <div>
                                            <label for="hs-trailing-icon" class="block text-sm font-medium mb-2 dark:text-white"></label>
                                            <div class="relative">
                                                <x-input label="Last name" placeholder="Ex: Dela Cruz" class="py-3 -mt-1" wire:model.blur="lastName" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full space-y-3">
                                        <div>
                                            <label for="hs-trailing-icon" class="block text-sm font-medium mb-2 dark:text-white"></label>
                                            <div class="relative">
                                                <x-input label="Suffix" placeholder="Ex: Jr., Sr., III" class="py-3 -mt-1" wire:model.blur="suffix" />
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="w-full mt-4">
                                    <x-select
                                        label="Agent"
                                        placeholder="Select an agent (optional)"
                                        wire:model.defer="agentId"
                                        :options="$this->agents"
                                        :template="[
                                            'name' => 'user-option',
                                            'config' => ['src' => 'profile_picture']
                                        ]"
                                        option-label="name"
                                        option-value="id"
                                        option-description="professional_agent_id"
                                        searchable
                                        clearable
                                    />

                                    @error('agentId')
                                        <span class="text-xs text-red-500">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <div class="w-full flex flex-col md:flex-row gap-5 mt-5">
                                    <x-select
                                        label="Select Region"
                                        wire:model.blur="region"
                                        placeholder="Ex: REGION IV-A (CALABARZON)"
                                        :async-data="route('api.regions.index')"
                                        :template="[
                                            'region_description'   => 'user-option',
                                        ]"
                                        option-label="region_description"
                                        option-value="region_description"
                                        {{-- option-description="region_description" --}}
                                        
                                    />
                                    @if (!$region)
                                        <x-select
                                            label="Select City/Province"
                                            wire:model.blur="province"
                                            placeholder="Ex: CITY OF MANILA"
                                            {{-- :async-data="route('location.region', ['regionCode' => $regionCode])" --}}
                                            :template="[
                                                'province_description'   => 'user-option',
                                            ]"
                                            option-label="province_description"
                                            option-value="province_description"
                                            {{-- option-description="province_description" --}}
                                            disabled
                                        />
                                    @else
                                        <x-select
                                            label="Select City/Province"
                                            wire:model.blur="province"
                                            placeholder="Ex: CITY OF MANILA"
                                            :async-data="route('location.province', ['regionCode' => $regionCode])"
                                            :template="[
                                                'province_description'   => 'user-option',
                                            ]"
                                            option-label="province_description"
                                            option-value="province_description"
                                            {{-- option-description="province_description" --}}
                                        />
                                    @endif

                                </div>

                                <div class="w-full flex flex-col md:flex-row gap-5 mt-5 mb-4">
                                    @if (!$province)
                                    
                                        <x-select
                                            label="Select Municipality"
                                            wire:model.blur="municipality"
                                            placeholder="Ex: ATIMONAN"
                                            {{-- :async-data="route('location.province', ['provinceCode' => $provinceCode])" --}}
                                            :template="[
                                                'city_municipality_description'   => 'user-option',
                                            ]"
                                            option-label="city_municipality_description"
                                            option-value="city_municipality_description"
                                            {{-- option-description="city_municipality_description" --}}
                                            disabled
                                        />
                                    @else
                                        <x-select
                                            label="Select Municipality"
                                            wire:model.blur="municipality"
                                            placeholder="Ex: ATIMONAN"
                                            :async-data="route('location.municipality', ['provinceCode' => $provinceCode])"
                                            :template="[
                                                'city_municipality_description'   => 'user-option',
                                            ]"
                                            option-label="city_municipality_description"
                                            option-value="city_municipality_description"
                                            {{-- option-description="city_municipality_description" --}}
                                        />
                                    @endif
                                    
                                    @if (!$region || !$province || !$municipality)
                                        <x-select
                                            label="Select Barangay"
                                            wire:model.blur="barangay"
                                            placeholder="Ex: Poblacion II"
                                            {{-- :async-data="route('api.barangays.index')" --}}
                                            :template="[
                                                'barangay_description'   => 'user-option',
                                            ]"
                                            option-label="barangay_description"
                                            option-value="barangay_description"
                                            {{-- option-description="barangay_description" --}}
                                            disabled
                                        />
                                    @else
                                        <x-select
                                            label="Select Barangay"
                                            wire:model.blur="barangay"
                                            placeholder="Ex: Poblacion II"
                                            :async-data="route('location.barangay', ['municipalityCode' => $municipalityCode])"
                                            :template="[
                                                'barangay_description'   => 'user-option',
                                            ]"
                                            option-label="barangay_description"
                                            option-value="barangay_description"
                                            {{-- option-description="barangay_description" --}}
                                        />
                                    @endif

                                    <x-select
                                        label="State"
                                        placeholder="Select State"
                                        wire:model.defer="state"
                                        disabled
                                        
                                    >
                                        <x-select.user-option src="https://via.placeholder.com/500" label="Philippines" value="Philippines" />
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    @elseif($currentStep == 2)
                        <div class="md:p-2 p-4 h-auto bg-gray-50 flex justify-center items-center border border-dashed border-gray-200 rounded-xl dark:bg-neutral-800 dark:border-neutral-700">
                            <div class="w-[90%]">
                                <div class="w-full flex flex-col gap-2">
                                    <div class="w-full space-y-3 mb-2">
                                        <div>
                                            <label for="hs-trailing-icon" class="block text-sm font-medium mb-2 dark:text-white"></label>
                                            <div class="relative">
                                                <x-input label="Email" placeholder="ex: johndoe@gmai.com" class="py-3 -mt-1" wire:model.live.debounce.300ms="email" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full space-y-3">
                                        <div>
                                            <label for="hs-trailing-icon" class="block text-sm font-medium mb-2 dark:text-white"></label>
                                            <div class="relative">
                                                <x-inputs.phone label="Mobile No." placeholder="+63 912 345 6789" mask="['+63 ### ### ####']" class="py-3 -mt-1" wire:model.live.debounce.300ms="phone" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full flex flex-col md:flex-row gap-2 mt-4 mb-4">
                                    <div class="w-full space-y-3">
                                        <div>
                                            <label for="hs-trailing-icon" class="block text-sm font-medium mb-2 dark:text-white"></label>
                                            <div class="relative">
                                                <x-inputs.password label="Password" placeholder="Enter your password" class="py-3 -mt-1" wire:model.live.debounce.300ms="password" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full space-y-3">
                                        <div>
                                            <label for="hs-trailing-icon" class="block text-sm font-medium mb-2 dark:text-white"></label>
                                            <div class="relative whitespace-nowrap">
                                                <x-inputs.password label="Confirm Password" placeholder="Confirm password" class="py-3 -mt-1" wire:model.live.debounce.300ms="confirmPassword" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($currentStep == 3)
                    
                    @endif
                    <div class="flex pb-5">
                        @if ($currentStep > 1)
                            <button wire:click="backStep" type="button" class="justify-start mt-4 py-2 px-3 inline-flex items-center gap-x-1 text-sm font-semibold rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none"
                            {{  $currentStep == 1 ? 'disabled="disabled"' : '' }}
                            >
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m15 18-6-6 6-6"></path>
                                </svg>
                                Back
                            </button>
                        @endif

                        @if ($currentStep < 2)
                            <button wire:click="nextStep" type="button" class="ml-auto mt-4 py-2 px-3 inline-flex items-center gap-x-1 text-base font-semibold rounded-lg border border-transparent bg-[#101727] text-white hover:bg-[#101727] disabled:opacity-50 disabled:pointer-events-none"
                            {{ !$firstName || !$middleName || !$lastName  ? 'disabled="disabled"' : '' }}
                            >
                                Next
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </button>
                        @else
                            <button type="submit" 
                                @disabled(
                                    ! $termsAccepted || ! $firstName || ! $middleName || ! $lastName || ! $email || ! $phone || ! $password || ! $confirmPassword
                                ) 
                                class="ml-auto mt-4 py-2 px-3 inline-flex items-center gap-x-1 text-sm font-semibold rounded-lg border border-transparent bg-[#101727] text-white hover:bg-[#101727] disabled:opacity-50 disabled:pointer-events-none"
                            >
                                Finish
                                <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6"></path>
                                </svg>
                            </button>
                        @endif
                        
                    </div>
                    
                </div>
            </form>
        </div>
        
        <div class="flex flex-col w-[90%] md:w-[80%] md:mt-0">
            <hr>
            <div class="flex items-start gap-2 my-3">
                <input type="checkbox" id="terms" wire:model.live="termsAccepted">

                <label for="terms" class="text-sm text-gray-500">
                    I agree to the 
                    <a href="#" x-on:click="$openModal('termsModal');" class="underline text-[#101727]">Terms and Conditions</a> 
                    and 
                    <a href="#" class="underline text-[#101727]">Privacy Policy</a>
                </label>
            </div>
        </div>

        <div class="flex flex-col text-sm">
            <div class="flex justify-center gap-1 pt-3 px-8">
                <p class="text-gray-500">Already have an account?</p>
                <a href="{{ route('login') }}">Sign in here</a>
            </div>
        </div>
    </div>
    <div class="mx-auto my-7">
        <a href="/" class="py-4 px-10 inline-flex items-center gap-x-2 text-sm border border-transparent bg-transparent text-black disabled:opacity-50 disabled:pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
            </svg>

            BACK TO HOME
        </a>
    </div>

    <x-modal
        blur
        name="termsModal"
        align="center"
        max-width="6xl"
    >
        <x-card title="TERMS & CONDITIONS">
            <div class="max-h-[70vh] overflow-y-auto bg-gray-50 pr-0">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-0 py-6">

                    {{-- Main Card --}}
                    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">

                        {{-- Header --}}
                        <div class="px-6 sm:px-10 lg:px-14 pt-10">
                            <div class="border-b border-slate-400 pb-4">

                                <h1 class="text-sm font-bold tracking-wide text-slate-800">
                                    ESTATEVIEW
                                </h1>

                                <p class="text-[10px] sm:text-xs uppercase tracking-wide text-gray-500 mt-1">
                                    DGR Realty Corporation — Manhattan Residences Candelaria
                                </p>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="px-6 sm:px-10 lg:px-14 py-8 text-gray-600 text-sm leading-7">

                            {{-- Title --}}
                            <div class="mb-8">
                                <h2 class="text-2xl sm:text-3xl font-bold text-[#243f67] tracking-wide">
                                    WEBSITE TERMS AND CONDITIONS
                                </h2>

                                <p class="mt-3 text-xs italic text-gray-500">
                                    Last Updated: July 17, 2026
                                </p>
                            </div>

                            {{-- Introduction --}}
                            <div class="space-y-4 mb-8">
                                <p>
                                    Welcome to
                                    <strong class="text-gray-700">EstateView</strong>
                                    (accessible at
                                    <strong class="text-gray-700">
                                        https://estateview.online/
                                    </strong>).
                                    These Terms and Conditions ("Terms") govern your use of our
                                    website and outline the strict contractual policies and
                                    restrictions of
                                    <strong class="text-gray-700">
                                        DGR Realty Corporation
                                    </strong>
                                    ("Seller/Developer") concerning property inquiries,
                                    bookings, down payments, and restrictions for Manhattan
                                    Residences Candelaria.
                                </p>

                                <p>
                                    By using this website, you explicitly agree to these Terms.
                                </p>
                            </div>

                            {{-- Critical Notice --}}
                            <div class="mb-10 border-l-4 border-red-500 bg-red-50 px-5 py-4">
                                <h3 class="font-bold text-gray-800 uppercase text-xs sm:text-sm">
                                    Critical Notice on Reservations & Down Payments:
                                </h3>

                                <p class="mt-2 text-xs sm:text-sm leading-6 text-gray-600">
                                    All reservation fees, down payments, and subsequent monthly
                                    amortizations made are
                                    <strong class="text-gray-800">
                                        STRICTLY NON-REFUNDABLE.
                                    </strong>

                                    In the event of default, cancellation, or failure to comply
                                    with the terms of reservation or installment schedules,
                                    all sums of money paid shall be automatically forfeited in
                                    favor of the Seller/Developer as liquidated damages.
                                </p>
                            </div>


                            {{-- SECTION 1 --}}
                            <section class="mb-10">
                                <h3 class="border-l-2 border-[#243f67] pl-3 text-base sm:text-lg font-bold text-[#243f67] uppercase">
                                    1. Default, Cancellation, and Forfeiture Policies
                                </h3>

                                <div class="mt-4 space-y-4">
                                    <p>
                                        In accordance with the standard contractual stipulations
                                        of the project and applicable laws (including the
                                        Installment Buyer Protection Act / Republic Act No. 6552):
                                    </p>

                                    <div class="space-y-3 pl-1 sm:pl-4">

                                        <p>
                                            <strong class="text-gray-700">
                                                • Strict Forfeiture:
                                            </strong>

                                            If the buyer fails to pay any portion of the down
                                            payment or fails to pay any two (2) consecutive
                                            monthly installments when they fall due, the
                                            Seller/Developer has the absolute right to
                                            extra-judicially cancel the contract.
                                        </p>

                                        <p>
                                            Any and all amounts already paid shall be automatically
                                            forfeited in favor of the Seller/Developer as
                                            liquidated damages and/or property rentals.
                                        </p>

                                        <p>
                                            <strong class="text-gray-700">
                                                • Grace Periods & Maceda Law (R.A. 6552):
                                            </strong>

                                            Any rights to cash surrender values or specialized
                                            grace periods shall strictly follow the provisions of
                                            Republic Act No. 6552.
                                        </p>

                                        <p>
                                            If a buyer fails to collect a due cash surrender value
                                            within five (5) days from receipt of notice, the amount
                                            shall be deposited in trust and the contract shall be
                                            considered automatically cancelled.
                                        </p>

                                        <p>
                                            <strong class="text-gray-700">
                                                • No Restructuring:
                                            </strong>

                                            As a general rule, no restructuring of delinquent
                                            accounts shall be allowed, except upon an approved
                                            written request delivered to the main office, which may
                                            be granted
                                            <strong class="text-gray-800">
                                                ONCE
                                            </strong>
                                            during the lifetime of the contract under terms solely
                                            determined by the Seller/Developer.
                                        </p>

                                        <p>
                                            <strong class="text-gray-700">
                                                • Devaluation Clause:
                                            </strong>

                                            Should there be any official devaluation of the
                                            Philippine Peso, any unpaid balance on the date of such
                                            devaluation shall be adjusted in accordance with the
                                            devalued rate.
                                        </p>

                                        <p>
                                            <strong class="text-gray-700">
                                                • Interest Rate Fluctuations:
                                            </strong>

                                            If there is an increase in bank loan rates or other
                                            financial accommodation terms, the interest rates on
                                            the unpaid balances shall automatically amend to match
                                            the increased bank interest rate.
                                        </p>

                                    </div>
                                </div>
                            </section>


                            {{-- SECTION 2 --}}
                            <section class="mb-10">
                                <h3 class="border-l-2 border-[#243f67] pl-3 text-base sm:text-lg font-bold text-[#243f67] uppercase">
                                    2. Payment & Administrative Fees
                                </h3>

                                <div class="mt-4 space-y-4 pl-1 sm:pl-4">

                                    <p>
                                        <strong class="text-gray-700">
                                            • Authorized Offices:
                                        </strong>

                                        All official amortization and property payments must be
                                        settled directly at the
                                        <strong class="text-gray-800">
                                            Manhattan Residences Candelaria Sales Office
                                        </strong>
                                        or the main office of DGR Realty Corporation.
                                    </p>

                                    <p>
                                        No payment made to agents or brokers shall be recognized
                                        as valid unless official receipts are signed and issued by
                                        an authorized officer or cashier of the Seller/Developer.
                                    </p>

                                    <p>
                                        <strong class="text-gray-700">
                                            • Late Payment Penalties:
                                        </strong>

                                        Late payments are subject to an immediate surcharge or
                                        penalty of
                                        <strong class="text-gray-800">
                                            three percent (3%)
                                        </strong>
                                        per month of the amortization amount due.
                                    </p>

                                    <div>
                                        <p>
                                            <strong class="text-gray-700">
                                                • Administrative Fees:
                                            </strong>

                                            The following standard service fees apply:
                                        </p>

                                        <div class="mt-2 ml-6 space-y-2">
                                            <p>
                                                ➢
                                                <strong class="text-gray-800">
                                                    PHP 500.00
                                                </strong>
                                                for issuing a true copy of a lost contract.
                                            </p>

                                            <p>
                                                ➢
                                                <strong class="text-gray-800">
                                                    PHP 1,000.00
                                                </strong>
                                                for any request to amend the contract.
                                            </p>

                                            <p>
                                                ➢
                                                <strong class="text-gray-800">
                                                    PHP 1,000.00
                                                </strong>
                                                administrative fee for building permits.
                                            </p>

                                            <p>
                                                ➢
                                                <strong class="text-gray-800">
                                                    PHP 1,000.00
                                                </strong>
                                                transfer fee for any approved transfer, cession,
                                                or assignment of rights.
                                            </p>
                                        </div>
                                    </div>

                                </div>
                            </section>


                            {{-- SECTION 3 --}}
                            <section class="mb-10">
                                <h3 class="border-l-2 border-[#243f67] pl-3 text-base sm:text-lg font-bold text-[#243f67] uppercase">
                                    3. Property Use, Construction, and Homeowner Restrictions
                                </h3>

                                <p class="mt-4">
                                    All buyers are strictly bound by the following restrictive
                                    covenants intended to maintain the architectural harmony,
                                    safety, and value of the subdivision:
                                </p>

                                <div class="mt-5 border-l-4 border-amber-400 bg-gray-50 px-5 sm:px-7 py-6 space-y-4">

                                    <h4 class="font-bold text-gray-800">
                                        Subdivision & Building Regulations:
                                    </h4>

                                    <p>
                                        <strong class="text-gray-700">
                                            • Residential Use Only:
                                        </strong>

                                        Lots must not be subdivided and must be used exclusively
                                        for single-family residential purposes. Commercial
                                        leasing of animals is prohibited; pet ownership is
                                        limited to a maximum of three (3) domestic pets
                                        (dogs, cats, or caged birds).
                                    </p>

                                    <div class="border-2 border-purple-500 px-4 py-3">
                                        <p>
                                            <strong class="text-gray-700">
                                                • Design Standards:
                                            </strong>

                                            Residential building must be built with strong
                                            concrete materials, painted in harmonious shades
                                            approved by the Seller/Developer.
                                            Residential building must not exceed a height limit of
                                            <strong class="text-gray-800">
                                                9.0 meters
                                            </strong>.

                                            No exterior firewall construction is allowed on
                                            property boundaries unless permitted by the National
                                            Building Code and approved in writing by the
                                            Seller/Developer.
                                        </p>
                                    </div>

                                    <p>
                                        <strong class="text-gray-700">
                                            • Fencing & Timeline:
                                        </strong>

                                        Buyers are obligated to construct a perimeter fence
                                        (maximum 7 feet at the rear/sides, and 6 feet fronting
                                        streets) at their own expense.
                                        Residential houses must be constructed within
                                        <strong class="text-gray-800">
                                            five (5) years
                                        </strong>
                                        from the date of full development of the subdivision.
                                    </p>

                                    <p>
                                        <strong class="text-gray-700">
                                            • Prohibited Uses:
                                        </strong>

                                        No property or lot shall be utilized as a mortuary, nor for
                                        any noxious, flammable, hazardous, or offensive activities.
                                        Billiards and signs that obstruct views are strictly
                                        prohibited.
                                    </p>

                                    <p>
                                        <strong class="text-gray-700">
                                            • Homeowners' Association:
                                        </strong>

                                        Upon completion of the contract, the buyer automatically
                                        becomes a member of the project's recognized Lot
                                        Owners/Homeowners Association.
                                        Members are subject to monthly dues of
                                        <strong class="text-gray-800">
                                            PHP 3.00 per square meter
                                        </strong>
                                        of the lot purchased.

                                        Failure to settle dues or water utility charges constitutes
                                        grounds for utility disconnection and legal action.
                                    </p>

                                </div>
                            </section>


                            {{-- SECTION 4 --}}
                            <section class="mb-10">
                                <h3 class="border-l-2 border-[#243f67] pl-3 text-base sm:text-lg font-bold text-[#243f67] uppercase">
                                    4. Subdivision Infrastructure & Seller's Rights
                                </h3>

                                <div class="mt-4 space-y-4 pl-1 sm:pl-4">

                                    <p>
                                        <strong class="text-gray-700">
                                            • Utility Disconnection:
                                        </strong>

                                        The Seller/Developer reserves the right to enter
                                        properties and disconnect water or community utility
                                        connections if association dues, water bills, or
                                        amortization payments are delinquent.
                                    </p>

                                    <p>
                                        <strong class="text-gray-700">
                                            • Easements & Survey Rights:
                                        </strong>

                                        Representatives of the Seller/Developer retain the
                                        perpetual right of entry to inspect, maintain, survey, and
                                        lay essential water, gas, or electrical infrastructure.
                                    </p>

                                    <p>
                                        <strong class="text-gray-700">
                                            • Rights over Waterways:
                                        </strong>

                                        The Seller/Developer retains all rights, titles, ownership,
                                        and interests over creek beds, dry creeks, or bodies of
                                        flowing water adjacent to subdivision lots.
                                        Buyers have no legal claim over these adjacent waterways.
                                    </p>

                                    <p>
                                        <strong class="text-gray-700">
                                            • No Unauthorized Improvements:
                                        </strong>

                                        No buyer shall introduce external excavations, fill, or
                                        build structures outside their lot boundary, or alter
                                        common roadways without prior written permits from the
                                        Seller/Developer.
                                    </p>

                                </div>
                            </section>


                            {{-- SECTION 5 --}}
                            <section class="mb-10">
                                <h3 class="border-l-2 border-[#243f67] pl-3 text-base sm:text-lg font-bold text-[#243f67] uppercase">
                                    5. Governing Law and Legal Scope
                                </h3>

                                <p class="mt-4">
                                    These terms and conditions are integrated with and
                                    supplemental to the formal Contract to Sell of DGR Realty
                                    Corporation.

                                    Any disputes arising from the online booking system,
                                    reservation inquiries, or terms of payment shall be resolved
                                    under the laws of the Republic of the Philippines, with
                                    exclusive venue in the competent courts of the Philippines.
                                </p>
                            </section>


                            {{-- Legal Disclaimer --}}
                            <div class="border-t border-gray-200 pt-6 mt-12">
                                <p class="text-xs text-gray-500 leading-5">
                                    <strong class="text-gray-600">
                                        Legal Disclaimer:
                                    </strong>

                                    This web document serves to inform users of the strict terms,
                                    enforceability of payments, and property restrictions
                                    implemented by DGR Realty Corporation.

                                    Any digital reservation, transaction, or site registration on
                                    EstateView shall constitute complete and irrevocable
                                    acknowledgment of these rules.
                                </p>
                            </div>


                            {{-- Footer --}}
                            <div class="mt-10 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between text-xs text-gray-400">
                                <p>
                                    Manhattan Residences Candelaria — Terms of Service
                                </p>

                                <p>
                                    EstateView
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
            

            <x-slot name="footer">
                <div class="flex justify-end gap-2">

                    <x-button
                        flat
                        label="Close"
                        x-on:click="close"
                    />

                </div>
            </x-slot>

        </x-card>
    </x-modal>
</div>
