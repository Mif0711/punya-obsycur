<?php

namespace App\Models;

use Database\Factories\MemberCardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'card_number', 'issued_at', 'status', 'note'])]
class MemberCard extends Model
{
    /** @use HasFactory<MemberCardFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_LOST = 'lost';

    public const STATUS_REPLACED = 'replaced';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
