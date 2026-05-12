<div class="w-full h-svh flex items-center justify-center">
    <div class="max-w-md w-full p-6 bg-white shadow rounded">
        <h2 class="text-xl font-bold mb-4 text-center">Verify Your Account</h2>

        @if (session('message'))
            <div class="text-green-600">{{ session('message') }}</div>
        @elseif (session('success'))
            <div class="text-green-600">{{ session('success') }}</div>
        @endif

        <form wire:submit.prevent="sendOtp" class="space-y-4">
            <x-input
                label="Email"
                wire:model="email" 
                placeholder="Enter your email" 
                suffix="@mail.com"
            />

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="sendOtp"
                class="w-full py-3 px-4 inline-flex rounded-full justify-center gap-x-2 text-sm font-semibold border border-transparent bg-[#101727] text-white hover:bg-[#101727] hover:cursor-pointer disabled:opacity-50 disabled:pointer-events-none"
            >
                <span wire:loading.remove wire:target="sendOtp">Send OTP</span>
                <span wire:loading wire:target="sendOtp">Sending...</span>
            </button>
        </form>
    </div>
</div>