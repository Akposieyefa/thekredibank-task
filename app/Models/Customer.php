<?php

namespace App\Models;

use App\Traits\UsesUuidTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method whereSlug(string $slug)
 * @method latest()
 * @method create(array $array)
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes, Sluggable, UsesUuidTrait;

    /**
     * @var string[]
     */
    protected  $fillable = [
        'user_id', 'first_name', 'last_name', 'email', 'slug'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return array[]
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'first_name'
            ]
        ];
    }

}
