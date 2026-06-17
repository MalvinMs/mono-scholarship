<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'application_id', 'qualification_id',
        'doc_label', 'file_path', 'file_name',
        'file_size', 'mime_type', 'uploaded_at',
        'verification_status',
        'verified_by', 'verified_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function temporaryViewUrl(int $minutes = 60): string
    {
        return Storage::disk('minio')->temporaryUrl(
            $this->file_path,
            now()->addMinutes($minutes)
        );
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(Qualification::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
