<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationScore extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'application_id', 'scholarship_id',
        'score_breakdown', 'total_score', 'max_possible_score',
        'rank', 'tiebreaker_log', 'selection_result',
        'is_final', 'finalized_at', 'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'score_breakdown' => 'array',
            'tiebreaker_log' => 'array',
            'is_final' => 'boolean',
            'finalized_at' => 'datetime',
            'calculated_at' => 'datetime',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function scholarship(): BelongsTo
    {
        return $this->belongsTo(Scholarship::class);
    }

    protected static function booted(): void
    {
        static::updating(function (self $score) {
            if ($score->isDirty() && $score->getOriginal('finalized_at') !== null) {
                throw new \RuntimeException('Skor final yang sudah di-approve tidak dapat diubah.');
            }
        });
    }
}
