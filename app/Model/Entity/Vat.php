<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed id
 * @property mixed user_id
 * @property mixed user
 */
class Vat extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $with = [
        'user'
    ];

    protected $hidden = [
        'deleted_at',
        'user_id',
    ];

    protected $table = 'vat';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
