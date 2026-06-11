<section class="space-y-6">
    <div class="p-4 bg-danger/10 border border-danger/20 rounded-md">
        <h3 class="text-sm font-semibold text-danger flex items-center gap-2 mb-2">
            <i data-lucide="shield-alert" class="size-4.5"></i>
            Permanently delete account
        </h3>
        <p class="text-sm text-danger/80">
            Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
        </p>
    </div>

    <div>
        <button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="btn bg-danger text-white cursor-pointer"
        >
            Delete Account
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white dark:bg-zinc-900">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-default-900">
                Are you sure you want to delete your account?
            </h2>

            <p class="mt-2 text-sm text-default-500">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <div class="mt-6">
                <label for="password" class="block font-medium text-default-900 text-sm mb-2">Password</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="form-input w-full md:w-3/4"
                    placeholder="Enter your password to confirm"
                    required
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn border border-default-300 text-default-700 hover:bg-default-150 cursor-pointer" x-on:click="$dispatch('close')">
                    Cancel
                </button>

                <button type="submit" class="btn bg-danger text-white cursor-pointer">
                    Delete Account
                </button>
            </div>
        </form>
    </x-modal>
</section>
