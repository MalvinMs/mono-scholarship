<div x-data x-on:open-document-url.window="window.open($event.detail.url, '_blank')">
    <div class="mb-6 animate-fade-in">
        <a href="{{ route('verification.queue') }}" wire:navigate class="inline-flex items-center gap-1 text-body-sm text-mute hover:text-ink transition-colors mb-4">
            <x-lucide-arrow-left class="size-3.5" />
            Kembali ke Antrian
        </a>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-display-xs text-ink">Detail Pendaftar</h1>
                <p class="mt-2 text-body-sm text-mute">
                    {{ $application->user?->name }} · {{ $application->registration_number }}
                </p>
            </div>
            <div class="flex shrink-0 items-center gap-2">
                @if($application->status !== 'rejected' && $application->status !== 'verified')
                    <x-ui.button variant="destructive" size="sm" wire:click="openBlacklistModal">
                        <x-lucide-shield-alert class="size-3.5" />
                        Blacklist
                    </x-ui.button>
                @endif
            </div>
        </div>
    </div>

    {{-- Status Bar --}}
    <div class="mb-6 flex flex-wrap items-center gap-3 rounded-sm border border-hairline bg-canvas p-4 animate-scale-in">
        <div class="flex items-center gap-2 text-body-sm">
            <span class="text-mute">Status:</span>
            <x-ui.badge :variant="match($application->status) {
                'submitted' => 'default',
                'under_review' => 'warning',
                'needs_revision' => 'destructive',
                'verified' => 'success',
                'rejected' => 'destructive',
                default => 'secondary'
            }">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</x-ui.badge>
        </div>
        <span class="text-hairline">|</span>
        <span class="text-body-sm text-mute">
            Submit: {{ $application->submitted_at?->format('d M Y H:i') ?? '-' }}
        </span>
        @if($application->verified_at)
            <span class="text-hairline">|</span>
            <span class="text-body-sm text-success">
                Terverifikasi: {{ $application->verified_at->format('d M Y H:i') }}
            </span>
        @endif
        @if($score?->is_final)
            <span class="text-hairline">|</span>
            <span class="text-body-sm font-medium text-success">Skor Final: {{ $score->total_score }} / {{ $score->max_possible_score }}</span>
        @elseif($score)
            <span class="text-hairline">|</span>
            <span class="text-body-sm font-medium text-ink">Skor Sementara: {{ $score->total_score }} / {{ $score->max_possible_score }}</span>
        @endif
    </div>

    {{-- Tabs Navigation --}}
    <x-ui.card padding="none" class="mb-6 overflow-hidden animate-scale-in">
        <div class="border-b border-hairline">
            <nav class="flex flex-wrap" role="tablist">
                @php
                    $tabs = [
                        'profile' => ['Profil', 'user'],
                        'documents' => ['Dokumen', 'file-check'],
                        'answers' => ['Jawaban', 'list-checks'],
                        'score' => ['Skor', 'bar-chart-3'],
                        'logs' => ['Log Verifikasi', 'scroll-text'],
                    ];
                @endphp
                @foreach($tabs as $key => [$label, $icon])
                    <button
                        wire:click="setTab('{{ $key }}')"
                        class="flex items-center gap-2 px-4 py-3 text-body-sm font-medium transition-colors border-b-2 -mb-px
                            {{ $activeTab === $key ? 'border-primary text-primary' : 'border-transparent text-mute hover:text-ink hover:border-hairline' }}"
                    >
                        <x-dynamic-component :component="'lucide-' . $icon" class="size-4" />
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">
            {{-- Profile Tab --}}
            @if($activeTab === 'profile')
                <div>
                    @php $profile = $application->snapshot_profile; @endphp
                    <h3 class="text-body-sm-strong text-ink mb-4">Data Diri</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-caption text-mute">Nama Lengkap</p>
                            <p class="text-body-sm font-medium text-ink">{{ $profile['name'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">NIK</p>
                            <p class="text-body-sm font-medium font-mono text-ink">{{ $profile['nik'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Email</p>
                            <p class="text-body-sm text-ink">{{ $profile['email'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Telepon</p>
                            <p class="text-body-sm text-ink">{{ $profile['phone'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Tanggal Lahir</p>
                            <p class="text-body-sm text-ink">{{ $profile['birth_date'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Tempat Lahir</p>
                            <p class="text-body-sm text-ink">{{ $profile['birth_place'] ?? '-' }}</p>
                        </div>
                    </div>

                    <h3 class="text-body-sm-strong text-ink mt-6 mb-4">Alamat</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <p class="text-caption text-mute">Alamat</p>
                            <p class="text-body-sm text-ink">{{ $profile['address'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Desa/Kelurahan</p>
                            <p class="text-body-sm text-ink">{{ $profile['village'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Kecamatan</p>
                            <p class="text-body-sm text-ink">{{ $profile['district'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Kota/Kabupaten</p>
                            <p class="text-body-sm text-ink">{{ $profile['city'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Provinsi</p>
                            <p class="text-body-sm text-ink">{{ $profile['province'] ?? '-' }}</p>
                        </div>
                    </div>

                    <h3 class="text-body-sm-strong text-ink mt-6 mb-4">Pendidikan</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-caption text-mute">Jenjang</p>
                            <p class="text-body-sm font-medium text-ink">{{ $profile['education_level'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Nama Instansi</p>
                            <p class="text-body-sm text-ink">{{ $profile['university_name'] ?? $profile['school_name'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Jurusan</p>
                            <p class="text-body-sm text-ink">{{ $profile['major'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-mute">Semester</p>
                            <p class="text-body-sm text-ink">{{ $profile['current_semester'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>

            {{-- Documents Tab --}}
            @elseif($activeTab === 'documents')
                <div>
                    <h3 class="text-body-sm-strong text-ink mb-4">Dokumen Pendaftar</h3>
                    @if($documents->isEmpty())
                        <x-ui.empty-state icon="file-text" title="Tidak ada dokumen" description="Pendaftar belum mengunggah dokumen." />
                    @else
                        <div class="space-y-3">
                            @foreach($documents as $doc)
                                <div class="flex items-start justify-between gap-4 rounded-sm border border-hairline p-4 transition-colors hover:bg-canvas-soft">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-body-sm-strong text-ink">{{ $doc->doc_label ?: $doc->qualification?->name ?: 'Dokumen' }}</p>
                                            <x-ui.badge :variant="match($doc->verification_status) {
                                                'approved' => 'success',
                                                'rejected' => 'destructive',
                                                default => 'secondary'
                                            }" class="text-[10px]">
                                                {{ $doc->verification_status === 'pending' ? 'Belum Diverifikasi' : ucfirst($doc->verification_status) }}
                                            </x-ui.badge>
                                        </div>
                                        <p class="text-caption text-mute">{{ $doc->file_name }} · {{ number_format($doc->file_size / 1024, 1) }} KB</p>
                                        @if($doc->rejection_reason)
                                            <p class="mt-2 text-caption text-error bg-error/5 rounded-md px-2 py-1">
                                                Alasan: {{ $doc->rejection_reason }}
                                            </p>
                                        @endif

                                        @if($doc->mime_type && str_starts_with($doc->mime_type, 'image/'))
                                            <img src="{{ $doc->temporaryViewUrl(5) }}" alt="{{ $doc->file_name }}"
                                                 class="mt-3 w-full max-h-48 object-contain rounded-sm border border-hairline cursor-pointer"
                                                 onclick="window.open(this.src, '_blank')" title="Klik untuk memperbesar">
                                        @endif

                                        <div class="flex flex-wrap items-center gap-2 mt-3">
                                            <x-ui.button size="xs" variant="ghost" wire:click="viewDocument({{ $doc->id }})">
                                                <x-lucide-eye class="size-3" />
                                                Lihat Dokumen
                                            </x-ui.button>
                                            @if($doc->verification_status === 'pending' || $doc->verification_status === 'rejected')
                                                <x-ui.button size="xs" variant="outline" wire:click="approveDocument({{ $doc->id }})">
                                                    <x-lucide-check class="size-3" />
                                                    Setujui
                                                </x-ui.button>
                                                <x-ui.button size="xs" variant="destructive" wire:click="openRejectModal({{ $doc->id }})">
                                                    <x-lucide-x class="size-3" />
                                                    Tolak
                                                </x-ui.button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            {{-- Answers Tab --}}
            @elseif($activeTab === 'answers')
                <div>
                    <h3 class="text-body-sm-strong text-ink mb-4">Jawaban Qualification</h3>
                    @if($answers->isEmpty())
                        <x-ui.empty-state icon="list" title="Tidak ada jawaban" description="Pendaftar belum mengisi form qualification." />
                    @else
                        <div class="space-y-3">
                            @foreach($answers as $answer)
                                @php
                                    $q = $answer->qualification;
                                    $currentLabel = match($q?->type) {
                                        'single_choice' => $answer->selectedOption?->label ?? '-',
                                        'multi_choice' => $answer->selectedOptions()->pluck('label')->implode(', ') ?: '-',
                                        'numeric_range' => $answer->numeric_value ?? '-',
                                        'text' => $answer->text_value ?? '-',
                                        default => '-',
                                    };
                                @endphp
                                @php
                                    $isApproved = in_array($answer->id, $approvedAnswerIds);
                                @endphp
                                <div class="flex items-start justify-between gap-4 rounded-sm border border-hairline p-4 transition-colors {{ $answer->is_corrected_by_verifier ? 'bg-warning/5 border-warning/20' : ($isApproved ? 'bg-success/5 border-success/20' : 'hover:bg-canvas-soft') }}">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-body-sm font-medium text-ink">{{ $q?->name ?? 'Pertanyaan #' . $answer->qualification_id }}</p>
                                            @if($answer->is_corrected_by_verifier)
                                                <x-ui.badge variant="warning" class="text-[10px]">Dikoreksi</x-ui.badge>
                                            @elseif($isApproved)
                                                <x-ui.badge variant="success" class="text-[10px]">Terverifikasi</x-ui.badge>
                                            @endif
                                        </div>
                                        <p class="text-body-sm text-ink">{{ $currentLabel }}</p>
                                        <p class="text-caption text-mute mt-1">
                                            Skor: {{ $answer->computed_score }}
                                            @if($answer->is_corrected_by_verifier && $answer->original_selected_option_id)
                                                <span class="text-warning">(dikoreksi oleh {{ $answer->corrector?->name }})</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-2">
                                        @if(!$isApproved && !$answer->is_corrected_by_verifier)
                                            <x-ui.button size="xs" variant="outline" wire:click="approveAnswer({{ $answer->id }})">
                                                <x-lucide-check class="size-3" />
                                                Setujui
                                            </x-ui.button>
                                        @endif
                                        @if(in_array($q?->type, ['single_choice', 'multi_choice', 'numeric_range', 'text']))
                                            <x-ui.button size="xs" variant="outline" wire:click="openCorrectModal({{ $answer->id }})">
                                                <x-lucide-pencil class="size-3" />
                                                Koreksi
                                            </x-ui.button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            {{-- Score Tab --}}
            @elseif($activeTab === 'score')
                <div>
                    <h3 class="text-body-sm-strong text-ink mb-4">Breakdown Skor</h3>
                    @if($score && $score->score_breakdown)
                        <div class="rounded-sm border border-hairline overflow-hidden">
                            <table class="w-full text-body-sm">
                                <thead class="bg-canvas-soft">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left text-caption font-medium text-mute">Indikator</th>
                                        <th class="px-4 py-2.5 text-left text-caption font-medium text-mute">Jawaban</th>
                                        <th class="px-4 py-2.5 text-right text-caption font-medium text-mute w-20">Skor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-hairline">
                                    @foreach($score->score_breakdown as $breakdown)
                                        <tr>
                                            <td class="px-4 py-2.5 text-ink">{{ $breakdown['name'] }}</td>
                                            <td class="px-4 py-2.5 text-mute">{{ $breakdown['answer_label'] }}</td>
                                            <td class="px-4 py-2.5 text-right font-medium text-ink">{{ $breakdown['score'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-canvas-soft border-t border-hairline">
                                    <tr>
                                        <td colspan="2" class="px-4 py-2.5 font-semibold text-ink">Total</td>
                                        <td class="px-4 py-2.5 text-right font-bold text-ink">{{ $score->total_score }} / {{ $score->max_possible_score }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-4 flex items-center gap-3 text-caption text-mute">
                            <x-ui.badge :variant="$score->is_final ? 'success' : 'warning'">
                                {{ $score->is_final ? 'Skor Final' : 'Skor Sementara' }}
                            </x-ui.badge>
                            @if($score->calculated_at)
                                <span>Dihitung: {{ $score->calculated_at->format('d M Y H:i') }}</span>
                            @endif
                        </div>
                    @else
                        <x-ui.empty-state icon="bar-chart-3" title="Belum ada skor" description="Skor akan dihitung setelah semua dokumen diverifikasi." />
                    @endif
                </div>

            {{-- Logs Tab --}}
            @elseif($activeTab === 'logs')
                <div>
                    <h3 class="text-body-sm-strong text-ink mb-4">Log Verifikasi</h3>
                    @if($logs->isEmpty())
                        <x-ui.empty-state icon="scroll-text" title="Belum ada log" description="Belum ada aktivitas verifikasi pada pendaftaran ini." />
                    @else
                        <div class="space-y-2">
                            @foreach($logs as $log)
                                <div class="flex items-start gap-3 rounded-sm border border-hairline p-3 text-body-sm">
                                    <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-canvas-soft border border-hairline">
                                        <x-dynamic-component
                                            :component="'lucide-' . match($log->action) {
                                                'document_approved', 'answer_approved' => 'check',
                                                'document_rejected' => 'x',
                                                'document_rerequested' => 'rotate-ccw',
                                                'answer_corrected' => 'pencil',
                                                'applicant_blacklisted' => 'shield-alert',
                                                'status_changed' => 'refresh-cw',
                                                default => 'info'
                                            }"
                                            class="size-3.5 text-mute"
                                        />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-body-sm text-ink">
                                            <span class="font-medium">{{ $log->verifier?->name ?? 'Sistem' }}</span>
                                            <span class="text-mute">
                                                {{ match($log->action) {
                                                    'document_approved' => 'menyetujui dokumen',
                                                    'answer_approved' => 'menyetujui jawaban',
                                                    'document_rejected' => 'menolak dokumen',
                                                    'document_rerequested' => 'meminta upload ulang',
                                                    'answer_corrected' => 'mengoreksi jawaban',
                                                    'applicant_blacklisted' => 'memblokir pendaftar',
                                                    'status_changed' => 'mengubah status',
                                                    default => $log->action,
                                                } }}
                                            </span>
                                        </p>
                                        @if($log->field_changed)
                                            <p class="text-caption text-mute mt-0.5">
                                                {{ $log->field_changed }}: {{ $log->old_value }} → {{ $log->new_value }}
                                            </p>
                                        @endif
                                        <p class="text-caption text-mute mt-1">{{ $log->reason }}</p>
                                        <p class="text-caption text-mute/60 mt-1">{{ $log->created_at?->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </x-ui.card>

    {{-- Rejection Modal --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-ink/40 backdrop-blur-sm" wire:click="closeRejectModal"></div>
            <div class="relative bg-canvas border border-hairline rounded-md shadow-level-4 w-full max-w-md mx-4 p-6 animate-scale-in">
                <h3 class="text-display-xs text-ink">Tolak Dokumen</h3>
                <p class="text-body-sm text-mute mt-2">Jelaskan alasan penolakan dokumen ini.</p>
                <div class="mt-6">
                    <label class="block text-body-sm-strong text-ink mb-1.5">Alasan Penolakan</label>
                    <textarea
                        wire:model="rejectionReason"
                        rows="4"
                        class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm ring-offset-canvas placeholder:text-mute focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        placeholder="Minimal 10 karakter..."
                    ></textarea>
                    @error('rejectionReason') <p class="text-caption text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <x-ui.button variant="outline" wire:click="closeRejectModal">Batal</x-ui.button>
                    <x-ui.button variant="destructive" wire:click="rejectDocument">Tolak Dokumen</x-ui.button>
                </div>
            </div>
        </div>
    @endif

    {{-- Correction Modal --}}
    @if($showCorrectModal)
        @php
            $corrAnswer = $this->correctingAnswerId ? $application->answers->find($this->correctingAnswerId) : null;
            $corrQ = $corrAnswer?->qualification;
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-ink/40 backdrop-blur-sm" wire:click="closeCorrectModal"></div>
            <div class="relative bg-canvas border border-hairline rounded-md shadow-level-4 w-full max-w-md mx-4 p-6 animate-scale-in">
                <h3 class="text-display-xs text-ink">Koreksi Jawaban</h3>
                <p class="text-body-sm text-mute mt-2">{{ $corrQ?->name ?? 'Jawaban' }}</p>
                <div class="mt-6">
                    <label class="block text-body-sm-strong text-ink mb-1.5">Nilai Baru</label>
                    @if($corrQ?->type === 'single_choice')
                        <select wire:model="correctedValue" class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Pilih opsi...</option>
                            @foreach($corrQ->options as $opt)
                                <option value="{{ $opt->id }}">{{ $opt->label }} (Skor: {{ $opt->value }})</option>
                            @endforeach
                        </select>
                    @elseif($corrQ?->type === 'numeric_range')
                        <input type="number" step="0.01" wire:model="correctedValue" class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    @else
                        <textarea wire:model="correctedValue" rows="3" class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    @endif
                </div>
                <div class="mt-4">
                    <label class="block text-body-sm-strong text-ink mb-1.5">Alasan Koreksi</label>
                    <textarea
                        wire:model="correctionReason"
                        rows="3"
                        class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Minimal 10 karakter..."
                    ></textarea>
                    @error('correctionReason') <p class="text-caption text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <x-ui.button variant="outline" wire:click="closeCorrectModal">Batal</x-ui.button>
                    <x-ui.button variant="primary" wire:click="correctAnswer">Simpan Koreksi</x-ui.button>
                </div>
            </div>
        </div>
    @endif

    {{-- Blacklist Modal --}}
    @if($showBlacklistModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-ink/40 backdrop-blur-sm" wire:click="closeBlacklistModal"></div>
            <div class="relative bg-canvas border border-hairline rounded-md shadow-level-4 w-full max-w-md mx-4 p-6 animate-scale-in">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-destructive/10 border border-destructive/20">
                        <x-lucide-shield-alert class="size-6 text-destructive" />
                    </div>
                    <div>
                        <h3 class="text-display-xs text-ink">Blacklist Pendaftar</h3>
                        <p class="text-body-sm text-mute mt-1">Tindakan ini akan memblokir pendaftar dari semua program.</p>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="block text-body-sm-strong text-ink mb-1.5">Alasan Blacklist</label>
                    <textarea
                        wire:model="blacklistReason"
                        rows="4"
                        class="w-full rounded-sm border border-hairline bg-canvas px-3 py-2 text-body-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="Jelaskan pelanggaran yang dilakukan, minimal 10 karakter..."
                    ></textarea>
                    @error('blacklistReason') <p class="text-caption text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <x-ui.button variant="outline" wire:click="closeBlacklistModal">Batal</x-ui.button>
                    <x-ui.button variant="destructive" wire:click="blacklistApplicant">Konfirmasi Blacklist</x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
