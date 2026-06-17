<x-layouts.public :title="'Daftar Akun'">
    <div class="flex min-h-[70vh] items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <x-ui.card padding="lg">
                <div class="mb-6 text-center">
                    <x-lucide-user-plus class="mx-auto size-10 text-primary" />
                    <h1 class="mt-4 text-display-xs text-ink">Daftar Akun</h1>
                    <p class="mt-1 text-body-sm text-mute">Buat akun Platform Beasiswa</p>
                </div>

                @if($errors->any())
                    <div class="mb-4">
                        <x-ui.alert variant="destructive">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </x-ui.alert>
                    </div>
                @endif

                <form method="POST" action="{{ url('/register') }}" class="space-y-4">
                    @csrf
                    <x-ui.input label="Nama Lengkap" name="name" required autofocus value="{{ old('name') }}" />
                    <x-ui.input label="NIK" name="nik" required value="{{ old('nik') }}" />
                    <x-ui.input label="Email" type="email" name="email" required value="{{ old('email') }}" />
                    <x-ui.input label="Nomor WhatsApp" name="phone" required value="{{ old('phone') }}" placeholder="081234567890" />
                    <div x-data="{ show: false }" class="space-y-2">
                        <label for="password" class="block text-body-sm-strong mb-2 peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                :type="show ? 'text' : 'password'"
                                id="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                class="flex h-10 w-full rounded-sm border border-hairline bg-canvas px-3 py-2 pr-10 text-body-sm text-ink transition-colors outline-none placeholder:text-mute focus-visible:border-hairline-strong focus-visible:ring-[3px] focus-visible:ring-primary/20"
                            >
                            <button
                                type="button"
                                tabindex="-1"
                                x-on:click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center justify-center w-10 rounded-r-sm text-mute transition-colors hover:text-ink focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-primary/50"
                                aria-label="Tampilkan password"
                            >
                                <x-lucide-eye x-cloak x-show="!show" class="size-4" />
                                <x-lucide-eye-off x-cloak x-show="show" class="size-4" />
                            </button>
                        </div>
                    </div>
                    <div x-data="{ show: false }" class="space-y-2">
                        <label for="password_confirmation" class="block text-body-sm-strong mb-2 peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <input
                                :type="show ? 'text' : 'password'"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                class="flex h-10 w-full rounded-sm border border-hairline bg-canvas px-3 py-2 pr-10 text-body-sm text-ink transition-colors outline-none placeholder:text-mute focus-visible:border-hairline-strong focus-visible:ring-[3px] focus-visible:ring-primary/20"
                            >
                            <button
                                type="button"
                                tabindex="-1"
                                x-on:click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center justify-center w-10 rounded-r-sm text-mute transition-colors hover:text-ink focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-primary/50"
                                aria-label="Tampilkan password"
                            >
                                <x-lucide-eye x-cloak x-show="!show" class="size-4" />
                                <x-lucide-eye-off x-cloak x-show="show" class="size-4" />
                            </button>
                        </div>
                    </div>
                    <x-ui.button variant="primary" class="w-full" type="submit">Daftar</x-ui.button>
                </form>

                <p class="mt-6 text-center text-body-sm text-mute">
                    Sudah punya akun? <a href="{{ url('/login') }}" class="font-medium text-link hover:underline">Login</a>
                </p>
            </x-ui.card>
        </div>
    </div>
</x-layouts.public>
