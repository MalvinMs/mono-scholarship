<x-layouts.public :title="'Home'">
    {{-- Section 1: Hero with Mesh Gradient Backdrop --}}
    <section class="relative overflow-hidden bg-canvas pt-20 pb-24 lg:pt-32 lg:pb-40">
        {{-- Mesh gradient backdrop --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute inset-0 bg-gradient-to-br from-[#007cf0]/30 via-[#00dfd8]/20 via-[#7928ca]/20 to-[#ff0080]/20 blur-3xl"></div>
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-gradient-to-r from-[#007cf0] via-[#7928ca] to-[#ff0080] opacity-20 blur-[100px]"></div>
        </div>

        <div class="relative max-w-[1400px] mx-auto px-6 text-center">
            {{-- Eyebrow mono caption --}}
            <div class="inline-flex items-center gap-2 rounded-full bg-canvas-soft px-3 py-1 mb-8 border border-hairline">
                <x-lucide-sparkles class="size-3.5 text-mute" />
                <span class="text-caption-mono">Platform beasiswa terintegrasi</span>
            </div>

            {{-- Headline --}}
            <h1 class="text-display-xl text-ink max-w-4xl mx-auto">
                Wujudkan impian pendidikanmu dengan beasiswa terbaik.
            </h1>

            {{-- Subheadline --}}
            <p class="mt-6 text-body-lg text-body max-w-2xl mx-auto">
                Satu platform untuk mengelola ribuan pendaftar. Proses seleksi otomatis, transparan, dan akuntabel.
            </p>

            {{-- CTAs --}}
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                @guest
                    <x-ui.button variant="primary" size="pill" href="{{ url('/daftar') }}">
                        <x-lucide-user-plus class="size-5" />
                        Daftar Sekarang
                    </x-ui.button>
                    <x-ui.button variant="secondary" size="pill" href="{{ route('announcement.list') }}">
                        Lihat Program
                        <x-lucide-arrow-right class="size-5" />
                    </x-ui.button>
                @else
                    @php
                        $user = auth()->user();
                        if ($user->hasAnyRole(['super-admin', 'admin'])) {
                            $dashboardUrl = url('/admin/dashboard');
                        } elseif ($user->hasRole('verifier')) {
                            $dashboardUrl = url('/verifikasi');
                        } elseif ($user->hasRole('approver')) {
                            $dashboardUrl = url('/approver/dashboard');
                        } elseif ($user->hasRole('treasurer')) {
                            $dashboardUrl = url('/keuangan/pencairan');
                        } else {
                            $dashboardUrl = url('/dashboard');
                        }
                    @endphp
                    <x-ui.button variant="primary" size="pill" href="{{ $dashboardUrl }}">
                        <x-lucide-layout-dashboard class="size-5" />
                        Buka Dashboard
                    </x-ui.button>
                    <x-ui.button variant="secondary" size="pill" href="{{ route('announcement.list') }}">
                        Lihat Pengumuman
                        <x-lucide-arrow-right class="size-5" />
                    </x-ui.button>
                @endguest
            </div>
        </div>
    </section>

    {{-- Section 2: Stats Bar (Credibility Indicators) --}}
    <section class="bg-canvas-soft py-12 border-y border-hairline">
        <div class="max-w-[1400px] mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                <div class="text-center">
                    <div class="text-display-md text-ink">5,000+</div>
                    <div class="mt-1 text-body-sm text-mute">Total Pendaftar</div>
                </div>
                <div class="text-center">
                    <div class="text-display-md text-ink">12</div>
                    <div class="mt-1 text-body-sm text-mute">Program Aktif</div>
                </div>
                <div class="text-center">
                    <div class="text-display-md text-ink">1,200+</div>
                    <div class="mt-1 text-body-sm text-mute">Penerima Terpilih</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 3: Features Section (6 Cards 3x2 Grid) --}}
    <section id="fitur" class="py-20 lg:py-32 bg-canvas-soft">
        <div class="max-w-[1400px] mx-auto px-6">
            {{-- Section header --}}
            <div class="text-center mb-16">
                <span class="text-caption-mono text-mute">Fitur Unggulan</span>
                <h2 class="mt-4 text-display-lg text-ink">Semua yang kamu butuhkan dalam satu platform.</h2>
                <p class="mt-4 text-body-md text-body max-w-2xl mx-auto">
                    Dari pendaftaran hingga pengumuman, semua proses terintegrasi secara digital.
                </p>
            </div>

            {{-- 3x2 Feature grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Feature 1 --}}
                <x-ui.card variant="default" padding="lg">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-canvas-soft-2 border border-hairline mb-6">
                        <x-lucide-layers class="size-6 text-primary" />
                    </div>
                    <h3 class="text-display-xs text-ink">Multi-Program</h3>
                    <p class="mt-3 text-body-sm text-body">
                        Kelola banyak program beasiswa dalam satu platform yang terpusat dan terorganisir.
                    </p>
                </x-ui.card>

                {{-- Feature 2 --}}
                <x-ui.card variant="default" padding="lg">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-canvas-soft-2 border border-hairline mb-6">
                        <x-lucide-calculator class="size-6 text-primary" />
                    </div>
                    <h3 class="text-display-xs text-ink">Dynamic Scoring</h3>
                    <p class="mt-3 text-body-sm text-body">
                        Formula skor otomatis tanpa coding, mendukung pembobotan dinamis sesuai kriteria.
                    </p>
                </x-ui.card>

                {{-- Feature 3 --}}
                <x-ui.card variant="default" padding="lg">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-canvas-soft-2 border border-hairline mb-6">
                        <x-lucide-shield-check class="size-6 text-primary" />
                    </div>
                    <h3 class="text-display-xs text-ink">Akuntabilitas Penuh</h3>
                    <p class="mt-3 text-body-sm text-body">
                        Audit trail immutable untuk setiap tindakan dan proses persetujuan.
                    </p>
                </x-ui.card>

                {{-- Feature 4 --}}
                <x-ui.card variant="default" padding="lg">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-canvas-soft-2 border border-hairline mb-6">
                        <x-lucide-file-check class="size-6 text-primary" />
                    </div>
                    <h3 class="text-display-xs text-ink">Verifikasi Dokumen</h3>
                    <p class="mt-3 text-body-sm text-body">
                        Sistem verifikasi dokumen terintegrasi dengan alur kerja multi-level.
                    </p>
                </x-ui.card>

                {{-- Feature 5 --}}
                <x-ui.card variant="default" padding="lg">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-canvas-soft-2 border border-hairline mb-6">
                        <x-lucide-bell class="size-6 text-primary" />
                    </div>
                    <h3 class="text-display-xs text-ink">Notifikasi Real-time</h3>
                    <p class="mt-3 text-body-sm text-body">
                        Pemberitahuan otomatis via WhatsApp dan Email untuk setiap status penting.
                    </p>
                </x-ui.card>

                {{-- Feature 6 --}}
                <x-ui.card variant="default" padding="lg">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-canvas-soft-2 border border-hairline mb-6">
                        <x-lucide-bar-chart-3 class="size-6 text-primary" />
                    </div>
                    <h3 class="text-display-xs text-ink">Laporan & Analitik</h3>
                    <p class="mt-3 text-body-sm text-body">
                        Dashboard analitik lengkap dengan ekspor PDF dan Excel untuk kebutuhan reporting.
                    </p>
                </x-ui.card>
            </div>
        </div>
    </section>

    {{-- Section 4: How It Works (Alur Proses) --}}
    <section class="py-20 lg:py-32 bg-canvas">
        <div class="max-w-[1400px] mx-auto px-6">
            {{-- Section header --}}
            <div class="text-center mb-12 lg:mb-16">
                <span class="text-caption-mono text-mute">Cara Kerja</span>
                <h2 class="mt-4 text-display-lg text-ink">Daftar dalam 5 langkah mudah.</h2>
            </div>

            {{-- Steps --}}
            @php
                $steps = [
                    ['icon' => 'user-plus', 'title' => 'Daftar', 'desc' => 'Buat akun dan lengkapi profil'],
                    ['icon' => 'clipboard-list', 'title' => 'Isi Form', 'desc' => 'Lengkapi data dan dokumen'],
                    ['icon' => 'shield-check', 'title' => 'Verifikasi', 'desc' => 'Tunggu verifikasi dokumen'],
                    ['icon' => 'users', 'title' => 'Seleksi', 'desc' => 'Proses penilaian otomatis'],
                    ['icon' => 'trophy', 'title' => 'Pengumuman', 'desc' => 'Cek hasil seleksi'],
                ];
            @endphp

            {{-- Mobile: Vertical Timeline --}}
            <div class="md:hidden">
                <div class="flex flex-col gap-8">
                    @foreach($steps as $index => $step)
                        <div class="relative flex items-start gap-4">
                            {{-- Timeline Column --}}
                            <div class="relative flex flex-col items-center w-12 shrink-0">
                                {{-- Step number --}}
                                <div class="relative z-10 flex w-12 h-12 shrink-0 items-center justify-center rounded-full bg-primary text-on-primary font-bold text-lg">
                                    <span>{{ $index + 1 }}</span>
                                </div>
                                
                                {{-- Connector line --}}
                                @if(!$loop->last)
                                    <div class="absolute left-1/2 -translate-x-1/2 top-12 bottom-[-2rem] w-px bg-hairline"></div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 pt-1.5">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-dynamic-component :component="'lucide-' . $step['icon']" class="w-4 h-4 text-mute" />
                                    <h3 class="text-body-md-strong text-ink">{{ $step['title'] }}</h3>
                                </div>
                                <p class="text-body-sm text-mute">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Desktop: Horizontal with connectors --}}
            <div class="hidden md:grid grid-cols-5 gap-6">
                @foreach($steps as $index => $step)
                    <div class="relative flex flex-col items-center text-center">
                        {{-- Step number --}}
                        <div class="relative z-10 flex w-14 h-14 lg:w-16 lg:h-16 items-center justify-center rounded-full bg-primary text-on-primary text-lg lg:text-xl font-bold mb-4">
                            <span>{{ $index + 1 }}</span>
                        </div>
                        {{-- Icon --}}
                        <x-dynamic-component :component="'lucide-' . $step['icon']" class="w-5 h-5 lg:w-6 lg:h-6 text-mute mb-2" />
                        {{-- Title --}}
                        <h3 class="text-body-sm-strong lg:text-body-md-strong text-ink">{{ $step['title'] }}</h3>
                        {{-- Description --}}
                        <p class="mt-1 text-caption lg:text-body-sm text-mute">{{ $step['desc'] }}</p>

                        {{-- Connector line (hidden on last item) --}}
                        @if(!$loop->last)
                            <div class="absolute top-7 lg:top-8 left-[60%] w-[80%] h-px bg-hairline"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Section 5: Program Showcase --}}
    <section id="program" class="py-20 lg:py-32 bg-canvas-soft">
        <div class="max-w-[1400px] mx-auto px-6">
            {{-- Section header --}}
            <div class="text-center mb-16">
                <span class="text-caption-mono text-mute">Program Tersedia</span>
                <h2 class="mt-4 text-display-lg text-ink">Pilih program beasiswa yang sesuai.</h2>
                <p class="mt-4 text-body-md text-body max-w-2xl mx-auto">
                    Berbagai program beasiswa dari pemerintah dan institusi ternama.
                </p>
            </div>

            {{-- Program cards grid --}}
            @if($programs->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($programs as $program)
                        <x-ui.card variant="large" padding="lg" class="group flex flex-col">
                            {{-- Header with gradient icon --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex size-12 items-center justify-center rounded-lg
                                    @if($loop->iteration % 3 === 1) bg-gradient-develop
                                    @elseif($loop->iteration % 3 === 2) bg-gradient-preview
                                    @else bg-gradient-ship @endif text-on-primary">
                                    <x-lucide-building-2 class="size-6" />
                                </div>
                                <x-ui.badge :variant="$program->status === 'open' ? 'success' : ($program->status === 'announced' ? 'warning' : 'secondary')">
                                    @if($program->status === 'open')
                                        Dibuka
                                    @elseif($program->status === 'announced')
                                        Diumumkan
                                    @else
                                        Ditutup
                                    @endif
                                </x-ui.badge>
                            </div>

                            {{-- Title --}}
                            <h3 class="text-display-xs text-ink group-hover:text-link transition-colors">{{ $program->name }}</h3>

                            {{-- Description --}}
                            <p class="mt-2 text-body-sm text-body flex-1">
                                {{ Str::limit($program->description, 100) }}
                            </p>

                            {{-- Metadata --}}
                            <div class="mt-6 flex items-center gap-4 text-caption text-mute">
                                <div class="flex items-center gap-1.5">
                                    <x-lucide-calendar class="size-3.5" />
                                    <span>{{ $program->academic_year }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <x-lucide-users class="size-3.5" />
                                    <span>{{ $program->quota_primary }} Kuota</span>
                                </div>
                            </div>

                            {{-- Dates --}}
                            <div class="mt-3 text-caption text-mute">
                                {{ $program->date_start->format('d M Y') }} - {{ $program->date_end->format('d M Y') }}
                            </div>

                            {{-- CTA --}}
                            <div class="mt-6">
                                @if($program->status === 'open')
                                    @guest
                                        <x-ui.button variant="primary" size="md" href="{{ url('/login') }}" class="w-full justify-center">
                                            <x-lucide-user-plus class="size-4" />
                                            Login untuk Daftar
                                        </x-ui.button>
                                    @else
                                        <x-ui.button variant="primary" size="md" href="{{ route('application.form', $program->slug) }}" class="w-full justify-center">
                                            <x-lucide-file-text class="size-4" />
                                            Daftar Sekarang
                                        </x-ui.button>
                                    @endguest
                                @elseif($program->status === 'announced')
                                    <x-ui.button variant="secondary" size="md" href="{{ route('announcement', $program->slug) }}" class="w-full justify-center">
                                        <x-lucide-eye class="size-4" />
                                        Lihat Pengumuman
                                    </x-ui.button>
                                @else
                                    <x-ui.button variant="outline" size="md" href="{{ route('program.detail', $program->slug) }}" class="w-full justify-center" disabled>
                                        <x-lucide-lock class="size-4" />
                                        Pendaftaran Ditutup
                                    </x-ui.button>
                                @endif
                            </div>
                        </x-ui.card>
                    @endforeach
                </div>
            @else
                {{-- Empty state --}}
                <x-ui.card variant="empty" padding="xl">
                    <div class="text-center">
                        <x-lucide-inbox class="mx-auto size-12 text-mute mb-4" />
                        <h3 class="text-display-xs text-ink">Belum Ada Program</h3>
                        <p class="mt-2 text-body-sm text-body">
                            Program beasiswa akan segera tersedia. Silakan cek kembali nanti.
                        </p>
                    </div>
                </x-ui.card>
            @endif

            {{-- View all link --}}
            <div class="mt-12 text-center">
                <x-ui.button variant="secondary" size="pill" href="{{ route('program.list') }}">
                    Lihat Semua Program
                    <x-lucide-arrow-right class="size-5" />
                </x-ui.button>
            </div>
        </div>
    </section>

    {{-- Section 6: Trust / Quote Section --}}
    <section class="py-20 lg:py-32 bg-canvas">
        <div class="max-w-[1400px] mx-auto px-6">
            <div class="max-w-3xl mx-auto text-center">
                <x-lucide-quote class="mx-auto size-10 text-mute mb-6" />
                <blockquote class="text-display-md text-ink tracking-tight">
                    "Platform ini telah membantu kami mengelola ribuan pendaftar dengan efisien dan transparan."
                </blockquote>
                <div class="mt-8 flex items-center justify-center gap-4">
                    <div class="flex size-12 items-center justify-center rounded-full bg-canvas-soft-2 border border-hairline">
                        <x-lucide-building class="size-6 text-mute" />
                    </div>
                    <div class="text-left">
                        <div class="text-body-sm-strong text-ink">Pemerintah Kota Madiun</div>
                        <div class="text-caption text-mute">Mitra Pengelola Beasiswa</div>
                    </div>
                </div>
            </div>

            {{-- Logo strip / partner logos --}}
            <div class="mt-16 pt-16 border-t border-hairline">
                <p class="text-center text-caption-mono text-mute mb-8">Didukung oleh</p>
                <div class="flex flex-wrap items-center justify-center gap-8 md:gap-16 opacity-60">
                    <div class="flex items-center gap-2 text-body-sm-strong text-body">
                        <x-lucide-building-2 class="size-5" />
                        <span>Kota Madiun</span>
                    </div>
                    <div class="flex items-center gap-2 text-body-sm-strong text-body">
                        <x-lucide-graduation-cap class="size-5" />
                        <span>Dinas Pendidikan</span>
                    </div>
                    <div class="flex items-center gap-2 text-body-sm-strong text-body">
                        <x-lucide-landmark class="size-5" />
                        <span>Baznas</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 7: Dark CTA Band (Polarity Flip) --}}
    <section class="py-20 lg:py-32 bg-primary dark:bg-canvas-soft border-t border-transparent dark:border-hairline">
        <div class="max-w-[1400px] mx-auto px-6 text-center">
            <h2 class="text-display-lg text-on-primary dark:text-ink max-w-3xl mx-auto">
                Siap untuk mewujudkan impian pendidikanmu?
            </h2>
            <p class="mt-6 text-body-lg text-on-primary/80 dark:text-mute max-w-2xl mx-auto">
                Bergabung dengan ribuan penerima beasiswa yang telah merasakan manfaat platform ini.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                @guest
                    <x-ui.button variant="secondary" size="pill" href="{{ url('/daftar') }}">
                        <x-lucide-user-plus class="size-5" />
                        Daftar Gratis
                    </x-ui.button>
                    <a href="{{ route('announcement.list') }}" class="inline-flex items-center gap-2 rounded-pill h-12 px-6 text-button-lg transition-all border border-on-primary/40 text-on-primary hover:bg-on-primary/10 dark:border-hairline-strong dark:text-ink dark:hover:bg-canvas-soft-2">
                        Lihat Pengumuman
                        <x-lucide-arrow-right class="size-5" />
                    </a>
                @else
                    <x-ui.button variant="secondary" size="pill" href="{{ $dashboardUrl ?? url('/dashboard') }}">
                        <x-lucide-layout-dashboard class="size-5" />
                        Buka Dashboard
                    </x-ui.button>
                    <a href="{{ route('announcement.list') }}" class="inline-flex items-center gap-2 rounded-pill h-12 px-6 text-button-lg transition-all border border-on-primary/40 text-on-primary hover:bg-on-primary/10 dark:border-hairline-strong dark:text-ink dark:hover:bg-canvas-soft-2">
                        Lihat Pengumuman
                        <x-lucide-arrow-right class="size-5" />
                    </a>
                @endguest
            </div>
        </div>
    </section>

    </x-layouts.public>
