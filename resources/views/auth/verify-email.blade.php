<x-layouts.public :title="'Verifikasi Email'">
    <div class="flex min-h-[70vh] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <livewire:applicant.otp-verification channel="email" />
        </div>
    </div>
</x-layouts.public>
