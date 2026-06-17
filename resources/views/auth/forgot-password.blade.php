<x-layouts.public :title="'Lupa Password'">
    <div class="flex min-h-[70vh] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <x-ui.card padding="lg">
                <div class="mb-6 text-center">
                    <x-lucide-key-round class="mx-auto size-8 text-primary" />
                    <h1 class="mt-4 text-display-xs text-ink">Lupa Password</h1>
                    <p class="mt-1 text-body-sm text-mute">Masukkan email untuk menerima link reset</p>
                </div>
                <form method="POST" action="{{ url('/forgot-password') }}" class="space-y-4">
                    @csrf
                    <x-ui.input label="Email" type="email" name="email" required />
                    <x-ui.button variant="primary" class="w-full" type="submit">Kirim Link Reset</x-ui.button>
                </form>
                <p class="mt-6 text-center text-body-sm text-mute">
                    <a href="{{ url('/login') }}" class="font-medium text-link hover:underline">Kembali ke Login</a>
                </p>
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
