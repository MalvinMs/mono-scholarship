<x-layouts.public :title="'Program Beasiswa'">
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-canvas pt-16 pb-20 lg:pt-24 lg:pb-28">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute inset-0 bg-gradient-to-br from-[#007cf0]/20 via-[#00dfd8]/10 to-[#7928ca]/10 blur-3xl"></div>
        </div>

        <div class="relative max-w-[1400px] mx-auto px-6 text-center">
            <h1 class="text-display-lg text-ink">Program Beasiswa</h1>
            <p class="mt-4 text-body-lg text-body max-w-2xl mx-auto">
                Jelajahi berbagai program beasiswa yang tersedia. Pilih yang sesuai dengan kebutuhan dan kriteria kamu.
            </p>
        </div>
    </section>

    {{-- Program List --}}
    <section class="py-12 lg:py-16 bg-canvas-soft">
        <div class="max-w-[1400px] mx-auto px-6">
            @if($programs->isEmpty())
                <x-ui.empty-state>
                    <x-slot:icon>
                        <x-lucide-graduation-cap class="size-12 text-mute" />
                    </x-slot:icon>
                    <x-slot:title>Belum Ada Program</x-slot:title>
                    <x-slot:description>
                        Saat ini belum ada program beasiswa yang tersedia. Cek kembali nanti.
                    </x-slot:description>
                </x-ui.empty-state>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($programs as $program)
                        <x-ui.card variant="large" padding="lg" class="group flex flex-col">
                            {{-- Header with gradient icon --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex size-12 items-center justify-center rounded-lg bg-gradient-develop text-on-primary">
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
                            <h3 class="text-display-xs text-ink group-hover:text-link transition-colors mb-2">
                                {{ $program->name }}
                            </h3>

                            {{-- Academic year --}}
                            <p class="text-body-sm text-mute mb-3">
                                {{ $program->academic_year }}
                            </p>

                            {{-- Description --}}
                            <p class="text-body-sm text-body line-clamp-3 flex-1">
                                {{ $program->description }}
                            </p>

                            {{-- Meta info --}}
                            <div class="mt-6 space-y-2 text-caption text-mute">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1.5">
                                        <x-lucide-calendar class="size-3.5" />
                                        <span>{{ $program->date_start?->format('d M Y') ?? '-' }} — {{ $program->date_end?->format('d M Y') ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1.5">
                                        <x-lucide-users class="size-3.5" />
                                        <span>{{ $program->quota_primary }} Kuota</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <x-lucide-file-text class="size-3.5" />
                                        <span>{{ $program->applications_count }} Pendaftar</span>
                                    </div>
                                </div>
                                @if($program->fund_amount)
                                    <div class="flex items-center gap-1.5">
                                        <x-lucide-banknote class="size-3.5" />
                                        <span>Rp {{ number_format($program->fund_amount, 0, ',', '.') }}/penerima</span>
                                    </div>
                                @endif
                            </div>

                            {{-- CTA --}}
                            <div class="mt-6 pt-4 border-t border-hairline">
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
                                        <x-lucide-trophy class="size-4" />
                                        Lihat Pengumuman
                                    </x-ui.button>
                                @else
                                    <x-ui.button variant="outline" size="md" href="{{ route('announcement', $program->slug) }}" class="w-full justify-center">
                                        <x-lucide-search class="size-4" />
                                        Lihat Detail
                                    </x-ui.button>
                                @endif
                            </div>
                        </x-ui.card>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
