Update the password for the currently authenticated user.Update the password for the currently authenticated user.<?php

                                                                                                                    use Illuminate\Support\Facades\Auth;
                                                                                                                    use Illuminate\Support\Facades\Hash;
                                                                                                                    use Illuminate\Validation\Rules\Password;
                                                                                                                    use Illuminate\Validation\ValidationException;
                                                                                                                    use Livewire\Volt\Component;

                                                                                                                    new class extends Component
                                                                                                                    {
                                                                                                                        public string $current_password = '';
                                                                                                                        public string $password = '';
                                                                                                                        public string $password_confirmation = '';

                                                                                                                        /**
                                                                                                                         * Update the password for the currently authenticated user.
                                                                                                                         */
                                                                                                                        public function updatePassword(): void
                                                                                                                        {
                                                                                                                            try {
                                                                                                                                $validated = $this->validate([
                                                                                                                                    'current_password' => ['required', 'string', 'current_password'],
                                                                                                                                    'password' => ['required', 'string', Password::defaults(), 'confirmed'],
                                                                                                                                ]);
                                                                                                                            } catch (ValidationException $e) {
                                                                                                                                $this->reset('current_password', 'password', 'password_confirmation');

                                                                                                                                throw $e;
                                                                                                                            }

                                                                                                                            Auth::user()->update([
                                                                                                                                'password' => Hash::make($validated['password']),
                                                                                                                            ]);

                                                                                                                            $this->reset('current_password', 'password', 'password_confirmation');

                                                                                                                            $this->dispatch('password-updated');
                                                                                                                        }
                                                                                                                    }; ?>

<section>
    <form wire:submit="updatePassword" class="space-y-6">

        {{-- Security Tips --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                <div>
                    <p class="text-sm font-medium text-blue-900 mb-1">Tips Keamanan Password:</p>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Gunakan minimal 8 karakter</li>
                        <li>Kombinasikan huruf besar, kecil, angka, dan simbol</li>
                        <li>Jangan gunakan password yang mudah ditebak</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Current Password --}}
        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-key text-gray-400 mr-2"></i>
                Password Saat Ini
            </label>
            <div class="relative">
                <input wire:model="current_password"
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="Masukkan password saat ini"
                    autocomplete="current-password" />
                <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        {{-- New Password --}}
        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-lock text-gray-400 mr-2"></i>
                Password Baru
            </label>
            <div class="relative">
                <input wire:model="password"
                    id="update_password_password"
                    name="password"
                    type="password"
                    class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="Masukkan password baru"
                    autocomplete="new-password" />
                <i class="fas fa-shield-alt absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-circle-check text-gray-400 mr-2"></i>
                Konfirmasi Password Baru
            </label>
            <div class="relative">
                <input wire:model="password_confirmation"
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                    placeholder="Konfirmasi password baru"
                    autocomplete="new-password" />
                <i class="fas fa-check-double absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-semibold rounded-lg shadow-sm transition-all duration-300">
                <i class="fas fa-save mr-2"></i>
                Update Password
            </button>

            <div x-data="{ shown: false }"
                x-on:password-updated.window="shown = true; setTimeout(() => shown = false, 3000)"
                x-show="shown"
                x-transition
                class="flex items-center px-4 py-2 bg-emerald-50 text-emerald-700 rounded-lg border border-emerald-200">
                <i class="fas fa-circle-check mr-2"></i>
                <span class="text-sm font-medium">Password berhasil diupdate!</span>
            </div>
        </div>
    </form>
</section>