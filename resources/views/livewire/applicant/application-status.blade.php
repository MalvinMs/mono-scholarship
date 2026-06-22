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
            @if($application->status === 'selected')
                <div class="shrink-0">
                    <x-ui.button href="{{ route('application.bank', $application->id) }}" variant="primary">
                        <x-lucide-banknote class="size-4" />Data Rekening
                    </x-ui.button>
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

    {{-- Tab Content: Logs --}}
    <div x-show="activeTab === 'logs'" class="animate-fade-in" style="display: none;">
        <x-ui.card>
            <div class="space-y-6">
                @php
                    $events = [];
                    // 1. Pendaftaran dibuat
                    $events[] = [
                        'icon' => 'file-text',
                        'title' => 'Pendaftaran Diajukan',
                        'description' => $application->registration_number,
                        'date' => $application->created_at,
                    ];
                    // 2. Status changes from verification_logs
                    foreach ($application->verificationLogs as $log) {
                        $verifierName = $log->verifier?->name ?? 'Verifikator';
                        $actionLabels = [
                            'document_approved' => 'Dokumen disetujui',
                            'document_rejected' => 'Dokumen ditolak',
                            'document_rerequested' => 'Dokumen diminta ulang',
                            'answer_corrected' => 'Jawaban dikoreksi',
                            'status_changed' => 'Status diubah menjadi ' . ucfirst(str_replace('_', ' ', $log->new_value ?? '')),
                            'applicant_blacklisted' => 'Pendaftar diblacklist',
                        ];
                        $title = $actionLabels[$log->action] ?? $log->action;
                        $description = $log->reason;
                        if ($log->field_changed) {
                            $description = "{$log->field_changed}: {$log->old_value} → {$log->new_value} — {$log->reason}";
                        }
                        $events[] = [
                            'icon' => match($log->action) {
                                'document_approved' => 'badge-check',
                                'document_rejected' => 'x-circle',
                                'answer_corrected' => 'pencil',
                                'applicant_blacklisted' => 'user-x',
                                default => 'activity',
                            },
                            'title' => $title,
                            'description' => $description,
                            'date' => $log->created_at,
                            'actor' => $verifierName,
                        ];
                    }
                    // 3. Score finalized
                    if ($application->status === 'verified' || $application->status === 'selected') {
                        $events[] = [
                            'icon' => 'shield-check',
                            'title' => 'Verifikasi Selesai — Skor Final',
                            'description' => $application->score ? 'Total skor: ' . $application->score->total_score . '/' . $application->score->max_possible_score : '',
                            'date' => $application->verified_at,
                        ];
                    }
                    // 4. Selection result
                    if ($application->score && $application->score->selection_result) {
                        $resultLabel = match($application->score->selection_result) {
                            'utama' => 'Lolos Utama',
                            'cadangan' => 'Lolos Cadangan',
                            default => 'Tidak Lolos',
                        };
                        $events[] = [
                            'icon' => match($application->score->selection_result) {
                                'utama' => 'trophy',
                                'cadangan' => 'list-checks',
                                default => 'x-circle',
                            },
                            'title' => 'Hasil Seleksi: ' . $resultLabel,
                            'description' => 'Ranking: #' . ($application->score->rank ?? 'N/A') . ' | Skor: ' . $application->score->total_score . '/' . $application->score->max_possible_score,
                            'date' => $application->score->finalized_at ?: $application->score->calculated_at,
                        ];
                    }
                    // 5. Selected
                    if ($application->status === 'selected') {
                        $events[] = [
                            'icon' => 'graduation-cap',
                            'title' => 'Ditetapkan sebagai Penerima',
                            'description' => 'Selamat! Anda telah ditetapkan sebagai penerima beasiswa.',
                            'date' => $application->selected_at,
                        ];
                    }
                    // Sort by date descending
                    usort($events, fn($a, $b) => ($b['date'] ?? now()) <=> ($a['date'] ?? now()));
                @endphp
                @foreach($events as $index => $event)
                    <div class="flex gap-4 relative">
                        @if($index < count($events) - 1)
                            <div class="absolute left-[11px] top-7 bottom-[-24px] w-px bg-hairline"></div>
                        @endif
                        <div class="mt-1 flex size-6 shrink-0 items-center justify-center rounded-full {{ $event['title'] === 'Pendaftaran Diajukan' ? 'bg-primary/10 ring-primary/20' : 'bg-canvas-soft ring-hairline/50' }} ring-4 z-10 border border-hairline">
                            <x-lucide-{{ $event['icon'] }} class="size-3 {{ $event['title'] === 'Pendaftaran Diajukan' ? 'text-primary' : 'text-mute' }}" />
                        </div>
                        <div>
                            <p class="text-body-sm-strong text-ink">{{ $event['title'] }}</p>
                            @if(!empty($event['description']))
                                <p class="text-body-sm text-mute mt-0.5">{{ $event['description'] }}</p>
                            @endif
                            @if(!empty($event['actor']))
                                <p class="text-caption text-mute mt-1">oleh {{ $event['actor'] }}</p>
                            @endif
                            <p class="text-caption text-mute mt-1">{{ $event['date'] ? $event['date']->format('d M Y, H:i') : '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>
</div>
