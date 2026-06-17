<div class="max-w-6xl mx-auto" x-data="{ activeTab: 'profile' }">
    <div class="mb-8">
        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-1 text-body-sm text-mute hover:text-ink mb-4 transition-colors">
            <x-lucide-arrow-left class="size-4" />Dashboard Saya
        </a>
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-display-sm text-ink">{{ $application->scholarship->name }}</h1>
                    @php
                        $statusColors = ['draft' => 'secondary','submitted' => 'secondary','under_review' => 'warning','needs_revision' => 'warning','verified' => 'success','selected' => 'default','rejected' => 'destructive'];
                    @endphp
                    <x-ui.badge :variant="$statusColors[$application->status] ?? 'secondary'">
                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                    </x-ui.badge>
                </div>
                <div class="flex items-center gap-4 text-body-sm text-mute mt-2">
                    <span class="flex items-center gap-1"><x-lucide-file-text class="size-4" /> {{ $application->registration_number }}</span>
                    <span class="flex items-center gap-1"><x-lucide-calendar class="size-4" /> Diajukan: {{ $application->created_at ? $application->created_at->format('d M Y') : 'N/A' }}</span>
                </div>
            </div>
            @if($application->status === 'needs_revision')
                <div class="shrink-0">
                    <x-ui.button href="{{ route('application.revision', $application->id) }}" variant="destructive">Upload Ulang Dokumen</x-ui.button>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex items-center gap-6 border-b border-hairline mb-8 overflow-x-auto no-scrollbar">
        <button x-on:click="activeTab = 'profile'" class="pb-3 text-body-sm font-medium transition-colors border-b-2" :class="activeTab === 'profile' ? 'border-primary text-ink' : 'border-transparent text-mute hover:text-ink'">Profil & Identitas</button>
        <button x-on:click="activeTab = 'answers'" class="pb-3 text-body-sm font-medium transition-colors border-b-2" :class="activeTab === 'answers' ? 'border-primary text-ink' : 'border-transparent text-mute hover:text-ink'">Jawaban Kualifikasi</button>
        <button x-on:click="activeTab = 'documents'" class="pb-3 text-body-sm font-medium transition-colors border-b-2" :class="activeTab === 'documents' ? 'border-primary text-ink' : 'border-transparent text-mute hover:text-ink'">Berkas Dokumen</button>
        <button x-on:click="activeTab = 'logs'" class="pb-3 text-body-sm font-medium transition-colors border-b-2" :class="activeTab === 'logs' ? 'border-primary text-ink' : 'border-transparent text-mute hover:text-ink'">Riwayat Aktivitas</button>
    </div>

    {{-- Tab Content: Profile (Mock) --}}
    <div x-show="activeTab === 'profile'" class="animate-fade-in space-y-6">
        <x-ui.card>
            <h3 class="text-body-sm-strong text-ink mb-4 border-b border-hairline pb-4">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-body-sm">
                <div>
                    <p class="font-medium text-mute">Nama Lengkap</p>
                    <p class="mt-1 font-medium text-ink">{{ auth()->user()->name }}</p>
                </div>
                <div>
                    <p class="font-medium text-mute">Email</p>
                    <p class="mt-1 font-medium text-ink">{{ auth()->user()->email }}</p>
                </div>
                <div>
                    <p class="font-medium text-mute">Status Pelamar</p>
                    <p class="mt-1 font-medium text-ink">Mahasiswa Aktif</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Tab Content: Answers (Mock) --}}
    <div x-show="activeTab === 'answers'" class="animate-fade-in" style="display: none;">
        <x-ui.card padding="none">
            <x-ui.empty-state icon="help-circle" title="Tidak Ada Jawaban" description="Kualifikasi ini tidak memiliki pertanyaan tambahan." />
        </x-ui.card>
    </div>

    {{-- Tab Content: Documents --}}
    <div x-show="activeTab === 'documents'" class="animate-fade-in" style="display: none;">
        @if(count($application->documents) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($application->documents as $doc)
                    <x-ui.card class="flex flex-col group" padding="sm" interactive>
                        <div class="h-32 bg-canvas-soft rounded border border-dashed border-hairline flex items-center justify-center mb-4 group-hover:bg-canvas-soft-2 transition-colors">
                            <x-lucide-file-text class="size-8 text-mute/50 group-hover:text-primary transition-colors" />
                        </div>
                        <div class="flex-1">
                            <h4 class="text-body-sm-strong text-ink line-clamp-1" title="{{ $doc->doc_label ?: $doc->file_name }}">{{ $doc->doc_label ?: $doc->file_name }}</h4>
                            <p class="text-caption text-mute mt-1">{{ number_format($doc->file_size / 1024, 1) }} KB</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-hairline flex items-center justify-between">
                            <x-ui.badge :variant="$doc->verification_status === 'approved' ? 'success' : ($doc->verification_status === 'rejected' ? 'destructive' : 'secondary')">{{ $doc->verification_status === 'pending' ? 'Menunggu' : ($doc->verification_status === 'approved' ? 'Disetujui' : 'Ditolak') }}</x-ui.badge>
                            <x-ui.button variant="ghost" size="icon" class="size-7"><x-lucide-download class="size-4" /></x-ui.button>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @else
            <x-ui.card padding="none">
                <x-ui.empty-state icon="file-question" title="Belum Ada Dokumen" description="Tidak ada berkas dokumen yang diunggah untuk pendaftaran ini." />
            </x-ui.card>
        @endif
    </div>

    {{-- Tab Content: Logs (Mock) --}}
    <div x-show="activeTab === 'logs'" class="animate-fade-in" style="display: none;">
        <x-ui.card>
            <div class="space-y-6">
                <div class="flex gap-4 relative">
                    <div class="absolute left-[11px] top-7 bottom-[-24px] w-px bg-hairline"></div>
                    <div class="mt-1 flex size-6 shrink-0 items-center justify-center rounded-full bg-canvas-soft ring-4 ring-canvas z-10 border border-hairline">
                        <div class="size-2 rounded-full bg-mute"></div>
                    </div>
                    <div>
                        <p class="text-body-sm-strong text-ink">Pendaftaran Diajukan</p>
                        <p class="text-caption text-mute mt-1">{{ $application->created_at ? $application->created_at->format('d M Y, H:i') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
