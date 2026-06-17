<x-layouts.public :title="'2FA Authentication'">
    <div class="flex min-h-[70vh] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <x-ui.card padding="lg">
                <div class="mb-6 text-center">
                    <x-lucide-fingerprint class="mx-auto size-8 text-primary" />
                    <h1 class="mt-4 text-display-xs text-ink">Two-Factor Authentication</h1>
                    <p class="mt-1 text-body-sm text-mute">Masukkan kode dari aplikasi autentikator Anda</p>
                </div>
                <form method="POST" action="{{ url('/two-factor-challenge') }}" class="space-y-4">
                    @csrf
                    <x-ui.input label="Kode 6 Digit" name="code" required />
                    <x-ui.button variant="primary" class="w-full" type="submit">Verifikasi</x-ui.button>
                </form>
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-hairline"></span></div>
                    <div class="relative flex justify-center text-caption uppercase"><span class="bg-canvas px-2 text-mute">atau</span></div>
                </div>
                <form method="POST" action="{{ url('/two-factor-challenge') }}" class="space-y-4">
                    @csrf
                    <x-ui.input label="Recovery Code" name="recovery_code" required />
                    <x-ui.button variant="outline" class="w-full" type="submit">Gunakan Recovery Code</x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
