<div class="bg-white">
  <section class="py-24 lg:py-32">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
      
      <div class="text-center mb-16">
        <h1 class="text-4xl lg:text-5xl font-light text-gray-900 mb-4">Account Settings</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">Manage your profile and account preferences</p>
      </div>

      @if (session()->has('profile_success'))
        <div class="max-w-3xl mx-auto mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg">
            {{ session('profile_success') }}
        </div>
      @endif

      @if (session()->has('password_success'))
        <div class="max-w-3xl mx-auto mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg">
            {{ session('password_success') }}
        </div>
      @endif

      <div class="grid lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1">
          <div class="bg-white shadow-sm p-8 border border-gray-100 rounded-xl">
            <h2 class="text-lg text-gray-900 mb-6 uppercase tracking-wide font-medium">Profile Picture</h2>
            <div class="flex flex-col items-center">
              
              <div class="h-32 w-32 bg-gray-900 flex items-center justify-center mb-6 relative overflow-hidden group rounded-lg">
                @if ($photo)
                    <img src="{{ $photo->temporaryUrl() }}" class="h-full w-full object-cover">
                @elseif (auth()->user()->profile_picture)
                    <img src="{{ auth()->user()->profile_picture }}" class="h-full w-full object-cover">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-16 w-16 text-white">
                      <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                @endif

                <div wire:loading wire:target="photo" class="absolute inset-0 bg-gray-950/70 z-10">
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="text-xs text-white animate-pulse font-medium">Uploading...</span>
                    </div>
                </div>
              </div>

              <label class="w-full">
                <input type="file" wire:model="photo" class="hidden" accept="image/*">
                <div class="px-6 py-3 bg-gray-900 text-white hover:bg-gray-800 transition-colors text-sm mb-3 uppercase tracking-wide text-center cursor-pointer font-medium rounded">
                  Upload Photo
                </div>
              </label>

              @if(auth()->user()->profile_picture || $photo)
                <button type="button" wire:click="removePhoto" class="px-6 py-3 text-red-600 hover:text-red-700 text-sm uppercase tracking-wide w-full font-medium transition">
                  Remove Photo
                </button>
              @endif

              @error('photo') 
                <span class="text-xs text-red-600 mt-2 text-center">{{ $message }}</span> 
              @enderror
            </div>
          </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
          
          <form wire:submit.prevent="confirmProfileUpdate" class="space-y-6">
            <div class="bg-white shadow-sm p-8 space-y-6 border border-gray-100 rounded-xl">
              <h2 class="text-lg text-gray-900 mb-2 flex items-center gap-3 uppercase tracking-wide font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-gray-400">
                  <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>Personal Information
              </h2>
              
              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label for="firstName" class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">First Name</label>
                  <input id="firstName" type="text" wire:model.blur="firstName" class="w-full px-4 py-3 border @error('firstName') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-gray-900 @enderror rounded focus:ring-2 focus:border-transparent outline-none">
                  @error('firstName') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                  <label for="middleName" class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">Middle Name</label>
                  <input id="middleName" type="text" wire:model.blur="middleName" class="w-full px-4 py-3 border @error('middleName') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-gray-900 @enderror rounded focus:ring-2 focus:border-transparent outline-none">
                  @error('middleName') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                  <label for="lastName" class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">Last Name</label>
                  <input id="lastName" type="text" wire:model.blur="lastName" class="w-full px-4 py-3 border @error('lastName') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-gray-900 @enderror rounded focus:ring-2 focus:border-transparent outline-none">
                  @error('lastName') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                  <label for="suffix" class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">Suffix</label>
                  <input id="suffix" type="text" placeholder="Jr., Sr., III" wire:model.blur="suffix" class="w-full px-4 py-3 border border-gray-300 focus:ring-2 focus:ring-gray-900 focus:border-transparent rounded outline-none">
                  @error('suffix') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
              </div>
            </div>

            @if(auth()->user()->role === 'agent')

              <div class="bg-white shadow-sm p-8 space-y-6 border border-gray-100 rounded-xl">

                  <div class="flex items-start justify-between gap-4">

                      <div>
                          <h2 class="text-lg text-gray-900 flex items-center gap-3 uppercase tracking-wide font-medium">

                              <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  width="24"
                                  height="24"
                                  viewBox="0 0 24 24"
                                  fill="none"
                                  stroke="currentColor"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  class="h-6 w-6 text-gray-400"
                              >
                                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                  <circle cx="9" cy="7" r="4"></circle>
                                  <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                              </svg>

                              Professional Information
                          </h2>

                          <p class="mt-2 text-xs text-gray-500">
                              Professional details assigned and managed by the administrator.
                          </p>
                      </div>

                      <span class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-[10px] font-semibold uppercase tracking-wide text-gray-500">
                          Read Only
                      </span>

                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                      {{-- Agent ID --}}
                      <div>
                          <label class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">
                              Agent ID
                          </label>

                          <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">

                              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white border border-gray-200">
                                  <x-icon
                                      name="identification"
                                      class="h-4 w-4 text-gray-500"
                                  />
                              </div>

                              <div class="min-w-0">
                                  @if($professionalAgentId)
                                      <p class="truncate text-sm font-semibold text-gray-900">
                                          {{ $professionalAgentId }}
                                      </p>
                                  @else
                                      <p class="text-sm text-gray-400">
                                          Not assigned
                                      </p>
                                  @endif

                                  <p class="mt-0.5 text-[10px] text-gray-400">
                                      Company agent identifier
                                  </p>
                              </div>

                          </div>
                      </div>

                      {{-- Real Estate License --}}
                      <div>
                          <label class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">
                              Real Estate License Number
                          </label>

                          <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">

                              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white border border-gray-200">
                                  <x-icon
                                      name="document-check"
                                      class="h-4 w-4 text-gray-500"
                                  />
                              </div>

                              <div class="min-w-0">
                                  @if($realEstateLicenseNumber)
                                      <p class="truncate text-sm font-semibold text-gray-900">
                                          {{ $realEstateLicenseNumber }}
                                      </p>
                                  @else
                                      <p class="text-sm text-gray-400">
                                          Not provided
                                      </p>
                                  @endif

                                  <p class="mt-0.5 text-[10px] text-gray-400">
                                      Registered professional license
                                  </p>
                              </div>

                          </div>
                      </div>

                  </div>

                  {{-- Commission Rate --}}
                  <div class="border-t border-gray-100 pt-5">

                      <div class="flex items-center justify-between rounded-xl border border-purple-100 bg-purple-50 p-4">

                          <div>
                              <p class="text-xs font-semibold uppercase tracking-wide text-purple-500">
                                  Current Commission Rate
                              </p>

                              <p class="mt-1 text-xs text-purple-400">
                                  This percentage is configured by the administrator.
                              </p>
                          </div>

                          <div class="text-right">
                              @if(! is_null($commissionPercentage))
                                  <p class="text-2xl font-semibold text-purple-700">
                                      {{ number_format(
                                          $commissionPercentage,
                                          2
                                      ) }}%
                                  </p>
                              @else
                                  <p class="text-sm font-medium text-purple-400">
                                      Not set
                                  </p>
                              @endif
                          </div>

                      </div>

                  </div>

              </div>

          @endif
            <div class="bg-white shadow-sm p-8 space-y-6 border border-gray-100 rounded-xl">
              <h2 class="text-lg text-gray-900 mb-2 flex items-center gap-3 uppercase tracking-wide font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-gray-400">
                  <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                </svg>Contact Information
              </h2>
              
              <div class="space-y-4">
                <div>
                  <label for="email" class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">Email Address</label>
                  <input id="email" type="email" wire:model.blur="email" class="w-full px-4 py-3 border @error('email') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-gray-900 @enderror rounded focus:ring-2 focus:border-transparent outline-none">
                  @error('email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                  <label for="phone" class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">Contact Number</label>
                  <div class="relative flex items-center">
                    <span class="absolute left-4 text-gray-400 text-sm font-medium select-none pointer-events-none">+63</span>
                    <input id="phone" type="tel" placeholder="9123456789" wire:model.blur="phone" class="w-full pl-14 pr-4 py-3 border @error('phone') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-gray-900 @enderror rounded focus:ring-2 focus:border-transparent outline-none">
                  </div>
                  @error('phone') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
              </div>
            </div>

            <button type="submit" class="w-full px-6 py-4 bg-gray-900 text-white hover:bg-gray-800 transition-colors flex items-center justify-center gap-2 uppercase tracking-wide text-sm font-medium rounded-lg shadow-sm">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
                <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"></path>
                <path d="M7 3v4a1 1 0 0 0 1 1h7"></path>
              </svg>
              <span>Save Profile Changes</span>
            </button>
          </form>

          <form wire:submit.prevent="updatePassword" class="bg-white shadow-sm p-8 space-y-6 border border-gray-100 rounded-xl">
            <h2 class="text-lg text-gray-900 mb-2 flex items-center gap-3 uppercase tracking-wide font-medium">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-gray-400">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>Change Password
            </h2>
            
            <div class="space-y-4">
              <div>
                <label for="currentPassword" class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">Current Password</label>
                <input id="currentPassword" type="password" placeholder="••••••••" wire:model.blur="currentPassword" class="w-full px-4 py-3 border @error('currentPassword') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-gray-900 @enderror rounded focus:ring-2 focus:border-transparent outline-none">
                @error('currentPassword') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
              </div>
              
              <div>
                <label for="newPassword" class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">New Password</label>
                <input id="newPassword" type="password" placeholder="••••••••" wire:model.blur="newPassword" class="w-full px-4 py-3 border @error('newPassword') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-gray-900 @enderror rounded focus:ring-2 focus:border-transparent outline-none">
                @error('newPassword') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
              </div>
              
              <div>
                <label for="confirmPassword" class="block text-xs text-gray-500 mb-2 uppercase tracking-wide">Confirm New Password</label>
                <input id="confirmPassword" type="password" placeholder="••••••••" wire:model.blur="confirmPassword" class="w-full px-4 py-3 border @error('confirmPassword') border-red-500 focus:ring-red-500 @else border-gray-300 focus:ring-gray-900 @enderror rounded focus:ring-2 focus:border-transparent outline-none">
                @error('confirmPassword') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
              </div>
            </div>

            <button type="submit" class="w-full px-6 py-4 bg-gray-900 text-white hover:bg-gray-800 transition-colors flex items-center justify-center gap-2 uppercase tracking-wide text-sm font-medium rounded-lg shadow-sm">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
                <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"></path>
                <path d="M7 3v4a1 1 0 0 0 1 1h7"></path>
              </svg>
              <span>Update Password</span>
            </button>
          </form>

          <div class="mt-8 pt-8 border-t border-gray-200">
            <button type="button" wire:click="logout" class="w-full px-6 py-4 bg-red-600 text-white hover:bg-red-700 transition-colors flex items-center justify-center gap-2 uppercase tracking-wide text-sm font-medium rounded-lg shadow-sm">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" x2="9" y1="12" y2="12"></line>
              </svg>
              <span>Logout Account</span>
            </button>
            <p class="text-center text-xs text-gray-500 mt-3">You will be logged out of your secure session and returned to the authentication landing portal.</p>
          </div>

        </div>
      </div>
      
    </div>
  </section>
</div>