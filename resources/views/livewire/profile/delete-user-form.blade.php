<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">

    {{-- Warning Alert --}}
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 mr-3"></i>
            <div>
                <p class="text-sm font-semibold text-red-900 mb-2">
                    Peringatan: Tindakan ini tidak dapat dibatalkan!
                </p>
                <p class="text-sm text-red-800">
                    Setelah akun Anda dihapus, semua data dan informasi akan dihapus secara permanen.
                    Pastikan Anda telah mengunduh data yang ingin disimpan sebelum menghapus akun.
                </p>
            </div>
        </div>
    </div>

    {{-- Delete Button --}}
    <div>
        <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-semibold rounded-lg shadow-sm transition-all duration-300">
            <i class="fas fa-trash-alt mr-2"></i>
            Hapus Akun
        </button>
    </div>

    {{-- Confirmation Modal --}}
    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">

            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">
                    Apakah Anda yakin ingin menghapus akun?
                </h2>
                <p class="text-sm text-gray-600">
                    Tindakan ini akan menghapus semua data Anda secara permanen dan tidak dapat dibatalkan.
                </p>
            </div>

            {{-- Password Confirmation --}}
            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-key text-gray-400 mr-2"></i>
                    Masukkan Password untuk Konfirmasi
                </label>
                <div class="relative">
                    <input wire:model="password"
                        id="password"
                        name="password"
                        type="password"
                        class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                        placeholder="Masukkan password Anda" />
                    <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button"
                    x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </button>

                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white font-semibold rounded-lg shadow-sm transition-all duration-300">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Ya, Hapus Akun Saya
                </button>
            </div>
        </form>
    </x-modal>
</section>