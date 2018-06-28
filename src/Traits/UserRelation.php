<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 28.06.18
 * Time: 15:18
 */

namespace Dion\Foa\Traits;


use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UserRelation
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}