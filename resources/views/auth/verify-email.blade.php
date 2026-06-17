<x-layouts.public :title="'Verifikasi Email'">
    <div class="flex min-h-[70vh] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <x-ui.card padding="lg">
                <div class="mb-6 text-center">
                    <x-lucide-mail class="mx-auto size-8 text-primary" />
                    <h1 class="mt-4 text-display-xs text-ink">Verifikasi Email</h1>
                    <p class="mt-1 text-body-sm text-mute">Cek email Anda untuk link verifikasi. Belum menerima?</p>
                </div>
                <form method="POST" action="{{ url('/email/verification-notification') }}" class="space-y-4">
                    @csrf
                    <x-ui.button variant="primary" class="w-full" type="submit">Kirim Ulang Email</x-ui.button>
                </form>
                <form method="POST" action="{{ url('/logout') }}" class="mt-4">
                    @csrf
                    <x-ui.button variant="ghost" class="w-full" type="submit">Logout</x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
