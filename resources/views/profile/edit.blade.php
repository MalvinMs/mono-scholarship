<x-layouts.app :title="'Profil Saya'">
    <div class="max-w-3xl mx-auto space-y-8">
        {{-- Header --}}
        <div>
            <h1 class="text-display-sm text-ink">Profil Saya</h1>
            <p class="text-body-sm text-mute mt-1">Kelola data diri dan password akun Anda</p>
        </div>

        {{-- Section 1: Data Diri & Pendidikan --}}
        <x-ui.card padding="lg">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex size-10 items-center justify-center rounded-sm bg-primary/10">
                    <x-lucide-circle-user class="size-5 text-primary" />
                </div>
                <div>
                    <h2 class="text-display-xs text-ink">Data Diri & Pendidikan</h2>
                    <p class="text-body-sm text-mute">Informasi pribadi dan latar belakang pendidikan</p>
                </div>
            </div>

            @if (session('status') === 'profile-information-updated')
                <div class="mb-6">
                    <x-ui.alert variant="success">
                        <p>Profil berhasil diperbarui.</p>
                    </x-ui.alert>
                </div>
            @endif

            @if ($errors->updateProfileInformation->any())
                <div class="mb-6">
                    <x-ui.alert variant="destructive">
                        @foreach ($errors->updateProfileInformation->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </x-ui.alert>
                </div>
            @endif

            <form method="POST" action="{{ url('/user/profile-information') }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Data diri --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input label="Nama Lengkap" type="text" name="name" :value="old('name', auth()->user()->name)" required />
                    <x-ui.input label="Email" type="email" name="email" :value="old('email', auth()->user()->email)" required />
                    <x-ui.input label="Nomor WhatsApp" type="text" name="phone" :value="old('phone', auth()->user()->phone)" />
                    <x-ui.input label="NIK" type="text" value="********" disabled readonly />
                    <x-ui.input label="Tempat Lahir" type="text" name="birth_place" :value="old('birth_place', auth()->user()->birth_place)" />
                    <x-ui.input label="Tanggal Lahir" type="date" name="birth_date" :value="old('birth_date', auth()->user()->birth_date?->format('Y-m-d'))" />
                </div>

                {{-- Alamat --}}
                <x-ui.input label="Alamat" type="text" name="address" :value="old('address', auth()->user()->address)" />
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-ui.input label="Desa/Kelurahan" type="text" name="village" :value="old('village', auth()->user()->village)" />
                    <x-ui.input label="Kecamatan" type="text" name="district" :value="old('district', auth()->user()->district)" />
                    <x-ui.input label="Kota/Kabupaten" type="text" name="city" :value="old('city', auth()->user()->city)" />
                    <x-ui.input label="Provinsi" type="text" name="province" :value="old('province', auth()->user()->province)" />
                </div>

                {{-- Pendidikan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-body-sm-strong mb-2">Jenjang Pendidikan</label>
                        <select name="education_level" class="flex h-10 w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm text-ink transition-colors focus-visible:outline-none focus-visible:border-hairline-strong focus-visible:ring-[3px] focus-visible:ring-primary/20">
                            <option value="">Pilih Jenjang</option>
                            @foreach (['SMA','SMK','MA','PAKET_C','D3','D4','S1','S2'] as $level)
                                <option value="{{ $level }}" @selected(old('education_level', auth()->user()->education_level) === $level)>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-ui.input label="Nama Sekolah/Universitas" type="text" name="school_name" :value="old('school_name', auth()->user()->school_name)" />
                    <x-ui.input label="NISN" type="text" name="nisn" :value="old('nisn', auth()->user()->nisn)" />
                    <x-ui.input label="Nama Universitas" type="text" name="university_name" :value="old('university_name', auth()->user()->university_name)" />
                    <x-ui.input label="Jurusan/Program Studi" type="text" name="major" :value="old('major', auth()->user()->major)" />
                    <x-ui.input label="Semester Saat Ini" type="number" name="current_semester" :value="old('current_semester', auth()->user()->current_semester)" min="1" max="14" />
                </div>

                <div class="flex justify-end pt-2">
                    <x-ui.button variant="primary" type="submit">Simpan Data Diri</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- Section 2: Ganti Password --}}
        <x-ui.card padding="lg">
            <div class="flex items-center gap-3 mb-6">
                <div class="flex size-10 items-center justify-center rounded-sm bg-primary/10">
                    <x-lucide-lock class="size-5 text-primary" />
                </div>
                <div>
                    <h2 class="text-display-xs text-ink">Ganti Password</h2>
                    <p class="text-body-sm text-mute">Ubah password akun Anda</p>
                </div>
            </div>

            @if (session('status') === 'password-updated')
                <div class="mb-6">
                    <x-ui.alert variant="success">
                        <p>Password berhasil diperbarui.</p>
                    </x-ui.alert>
                </div>
            @endif

            @if ($errors->updatePassword->any())
                <div class="mb-6">
                    <x-ui.alert variant="destructive">
                        @foreach ($errors->updatePassword->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </x-ui.alert>
                </div>
            @endif

            <form method="POST" action="{{ url('/user/password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <x-ui.input label="Password Saat Ini" type="password" name="current_password" required />
                <x-ui.input label="Password Baru" type="password" name="password" required />
                <x-ui.input label="Konfirmasi Password Baru" type="password" name="password_confirmation" required />

                <div class="flex justify-end pt-2">
                    <x-ui.button variant="primary" type="submit">Ganti Password</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>
