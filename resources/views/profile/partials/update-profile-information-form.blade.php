<section>
    <p class="text-sm text-default-500 mb-6">
        Update your account's profile information and email address.
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <!-- Name -->
        <div>
            <label class="block font-medium text-default-900 text-sm mb-2" for="name">Name <span class="text-danger">*</span></label>
            <input class="form-input" id="name" name="name" type="text" value="{{ old('name', $user->name) }}" placeholder="Full Name" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <label class="block font-medium text-default-900 text-sm mb-2" for="email">Email Address <span class="text-danger">*</span></label>
            <input class="form-input" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="Email Address" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 rounded-md">
                    <p class="text-sm text-amber-800 dark:text-amber-300 flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="size-4"></i>
                        Your email address is unverified.
                    </p>

                    <button form="send-verification" class="mt-2 text-xs font-semibold text-primary hover:underline cursor-pointer">
                        Click here to re-send the verification email.
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-xs text-green-600 dark:text-green-400">
                            A new verification link has been sent to your email address.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Action Button -->
        <div class="flex items-center gap-4 pt-4 border-t border-default-200">
            <button type="submit" class="btn bg-primary text-white cursor-pointer">Save Changes</button>

            @if (session('status') === 'profile-updated')
                <span
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1.5 font-medium animate-fade-in"
                >
                    <i data-lucide="check-circle-2" class="size-4"></i>
                    Saved successfully.
                </span>
            @endif
        </div>
    </form>
</section>
