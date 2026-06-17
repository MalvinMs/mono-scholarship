<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disbursement extends Model
{
    protected $fillable = [
        'application_id', 'scholarship_id',
        'bank_name', 'account_number', 'account_holder_name',
        'amount', 'status', 'notes',
        'disbursed_at', 'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'account_number' => 'encrypted',
            'disbursed_at' => 'datetime',
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

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
