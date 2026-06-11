<section>
    <p class="text-sm text-default-500 mb-6">
        Ensure your account is using a long, random password to stay secure.
    </p>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div>
            <label class="block font-medium text-default-900 text-sm mb-2" for="update_password_current_password">Current Password</label>
            <input class="form-input" id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" placeholder="••••••••" required />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- New Password -->
        <div>
            <label class="block font-medium text-default-900 text-sm mb-2" for="update_password_password">New Password</label>
            <input class="form-input" id="update_password_password" name="password" type="password" autocomplete="new-password" placeholder="••••••••" required />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block font-medium text-default-900 text-sm mb-2" for="update_password_password_confirmation">Confirm Password</label>
            <input class="form-input" id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="••••••••" required />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Action Button -->
        <div class="flex items-center gap-4 pt-4 border-t border-default-200">
            <button type="submit" class="btn bg-primary text-white cursor-pointer">Save Changes</button>

            @if (session('status') === 'password-updated')
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
