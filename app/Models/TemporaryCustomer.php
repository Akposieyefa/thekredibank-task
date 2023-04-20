<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method create(array $array)
 * @method whereSlug(string $slug)
 * @method latest()
 */
class TemporaryCustomer extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected  $fillable = [
        'user_id', 'first_name', 'last_name', 'email', 'status', 'slug', 'approval_status'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
