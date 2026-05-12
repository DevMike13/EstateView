<div class="flex justify-center md:flex h-auto">
    <div class="hidden md:block w-1/2 h-screen sticky top-0 bg-cover bg-center relative" style="background-image: url('{{ asset('images/login-banner.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white px-10">
            
            <h1 class="text-4xl font-semibold">
                Welcome to EstateView
            </h1>

            <p class="mt-4 text-sm text-white/80 max-w-md">
                Your gateway to premium property management and reservation system.
            </p>

        </div>
    </div>
    <div class="md:w-[50%] flex flex-col justify-center px-20 gap-3 bg-white h-full">
        <div class="flex justify-center items-center md:mt-24">
            <a href="/">
                <img src="{{ asset('/images/estate-view-logo.png') }}" alt="" class="h-12">
            </a>
        </div>
        <h1 class="text-4xl md:text-4xl font-medium text-left">Sign In</h1>
        <p class="text-sm text-left">Access your account</p>
        <form wire:submit.prevent="login" class="w-full flex flex-col justify-center items-center mt-10">
            <div class="w-full flex flex-col justify-center items-center">
                <div class="space-y-6 w-[90%] md:w-full">
                    <div class="relative">
                        <x-input icon="user" label="Email Address" placeholder="Enter email" wire:model="email" class="py-3"/>
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-2 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                        </div>
                    </div>
                    
                    <div class="relative">
                        <x-inputs.password icon="key" label="Password" placeholder="Enter password" class="py-3" wire:model="password"/>
                        <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-2 peer-disabled:opacity-50 peer-disabled:pointer-events-none">
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <x-checkbox id="right-label" label="Remember me" wire:model.defer="model" />
                        <a href="{{ route('password.request') }}" class="underline text-sm">Forgot password?</a>
                    </div>
                </div>
            </div>
            <button type="submit" class="w-[90%] md:w-full py-3 px-4 mt-5 inline-flex justify-center items-center gap-x-2 text-base font-medium border border-transparent bg-[#101727] text-white hover:bg-slate-950 disabled:opacity-50 disabled:pointer-events-none">
                Log in
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                </svg>
            </button>
        </form>
        <div class="flex flex-col text-sm">
            <div class="flex justify-center gap-1 pt-3 px-8">
                <p class="text-gray-500">Don't have an account?</p>
                <a href="{{ route('register') }}">Sign up here</a>
            </div>
            <a href="{{ route('account.resend-verification') }}" class="mt-2 mx-auto">Haven’t verified yet?</a>
        </div>
        <div class="mx-auto">
            <a href="/" class="py-4 px-10 inline-flex items-center gap-x-2 text-sm border border-transparent bg-white text-black disabled:opacity-50 disabled:pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 15.75 3 12m0 0 3.75-3.75M3 12h18" />
                </svg>

                BACK TO HOME
            </a>
        </div>
    </div>
</div>
