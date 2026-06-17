<x-layouts.public :title="'Reset Password'">
    <div class="flex min-h-[70vh] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <x-ui.card padding="lg">
                <div class="mb-6 text-center">
                    <x-lucide-lock class="mx-auto size-8 text-primary" />
                    <h1 class="mt-4 text-display-xs text-ink">Reset Password</h1>
                    <p class="mt-1 text-body-sm text-mute">Masukkan password baru Anda</p>
                </div>
                <form method="POST" action="{{ url('/reset-password') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <x-ui.input label="Email" type="email" name="email" value="{{ old('email', $request->email) }}" readonly disabled />
                    <x-ui.input label="Password Baru" type="password" name="password" required />
                    <x-ui.input label="Konfirmasi Password" type="password" name="password_confirmation" required />
                    <x-ui.button variant="primary" class="w-full" type="submit">Reset Password</x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
