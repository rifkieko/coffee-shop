<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-[#2A1A13] leading-tight">{{ __('Profile') }}</h2>
                <p class="text-xs uppercase tracking-[0.5em] text-[#ad9e88]">{{ __('Account Settings') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-[#f7f8fc] dark:bg-[#0f1117] px-4 py-10 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            <div class="rounded-3xl border border-gray-200 bg-gradient-to-br from-[#fffdf9] to-[#f1f2f7] px-6 py-8 shadow-lg shadow-amber-100 dark:border-gray-700 dark:from-gray-900/40 dark:to-gray-900/50">
                <h3 class="text-lg font-semibold text-[#2A1A13]">{{ __('Profile Information') }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Update your account details so we can keep everything in sync with your profile.') }}</p>
            </div>

            <div class="space-y-6">
                <section class="rounded-3xl border border-gray-200 bg-white px-6 py-8 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white px-6 py-8 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white px-6 py-8 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
