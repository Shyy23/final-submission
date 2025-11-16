<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="mr-3 text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Profile') }}
                </h2>
            </div>
            <div class="text-sm text-gray-500">
                <i class="fas fa-user-circle mr-1"></i>
                Kelola informasi akun Anda
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Profile Information Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg flex items-center justify-center mr-4 shadow-md">
                            <i class="fas fa-user text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Informasi Profile
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Perbarui informasi profil dan alamat email akun Anda
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            {{-- Update Password Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mr-4 shadow-md">
                            <i class="fas fa-lock text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Update Password
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Pastikan akun Anda menggunakan password yang kuat dan aman
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            {{-- Delete Account Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
                <div class="p-6 border-b border-red-100 bg-gradient-to-r from-red-50 to-pink-50">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-pink-500 rounded-lg flex items-center justify-center mr-4 shadow-md">
                            <i class="fas fa-exclamation-triangle text-xl text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Hapus Akun
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Hapus akun Anda secara permanen
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <livewire:profile.delete-user-form />
                </div>
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-500">
                © {{ date('Y') }} Submission System. All rights reserved.
            </p>
        </div>
    </footer>
</x-app-layout>