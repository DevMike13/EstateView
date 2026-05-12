<div class="flex justify-center items-center w-full h-screen">
    <div class="flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400"
        x-data="otpVerification({{ $remainingSeconds }})"
        x-init="startCountdown()"
    >
        <h1 class="text-center mb-2 font-semibold">OTP Verification</h1>
        <p class="text-center mb-8 text-xs">Enter the OTP sent to your email.</p>
        <div class="flex gap-x-3 mb-5" data-hs-pin-input="">
            @foreach(range(0, 5) as $index)
                <input 
                    type="text" 
                    maxlength="1"
                    class="block w-9.5 text-center bg-transparent border-t-transparent focus:outline-none border-b-2 border-x-transparent border-b-gray-200 sm:text-sm focus:border-t-transparent focus:border-x-transparent focus:border-b-[#ffc71c] focus:ring-0 disabled:opacity-50 disabled:pointer-events-none dark:border-b-neutral-700 dark:text-neutral-400  dark:placeholder-neutral-500 dark:focus:ring-neutral-600 dark:focus:border-b-neutral-600" 
                    placeholder="⚬" 
                    data-hs-pin-input-item=""
                    wire:model.lazy="otp.{{ $index }}"
                >
            @endforeach
        </div>
        @if (session('error'))
            <div>
                <p class="text-red-500 text-xs text-center mb-5">
                    {{ session('error') }}
                </p>
            </div>
        @endif
        <a href="#" wire:click="verifyOtp" class="py-3 px-4 inline-flex rounded-full justify-center gap-x-2 text-sm font-semibold border border-transparent bg-[#101727] text-white hover:bg-[#101727] disabled:opacity-50 disabled:pointer-events-none">
            <span wire:loading.remove wire:target="verifyOtp">
                Verify
            </span>
        
            <!-- Loading indicator -->
            <span wire:loading wire:target="verifyOtp">
                Verifying…
            </span>
        </a>

        <div class="text-center mt-10">
            <p>Didn't receive code?</p>
            <button 
                x-bind:disabled="countdown > 0 || loading"
                x-on:click="resendOtp()"
                class="hover:cursor-pointer"
            >
                <span x-show="!loading" 
                        x-bind:class="countdown > 0 ? 'text-gray-500' : 'text-blue-600'">
                    Resend
                    <template x-if="countdown > 0">
                        <span>
                            <span class="text-gray-500"> - </span> 
                            <span x-text="countdown" class="text-gray-500"></span>
                            <span class="text-gray-500">s</span>
                        </span>
                    </template>
                </span>
                <span x-show="loading">Sending…</span>
            </button>
        </div>
    </div>
</div>
<script>
    function otpVerification(initialSeconds) {
        return {
            countdown: initialSeconds,
            loading: false,
    
            startCountdown() {
                if (this.countdown > 0) {
                    const interval = setInterval(() => {
                        if (this.countdown > 0) {
                            this.countdown--;
                        } else {
                            clearInterval(interval);
                        }
                    }, 1000);
                }
            },
    
            resendOtp() {
                if (this.countdown === 0 && !this.loading) {
                    this.loading = true;
                    @this.call('resendOtp')
                        .then((seconds) => {
                            this.countdown = {{ $resendCountdown }};
                            this.startCountdown();
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                }
            }
        }
    }
</script>
<script>
    window.addEventListener('reload-page', () => {
        location.reload();
    });
</script>