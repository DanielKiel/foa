<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 28.06.18
 * Time: 15:18
 */

namespace Dion\Foa\Traits;


use App\User;
use Dion\Foa\Exceptions\ObjectAccessException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait UserRelation
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public static function boot()
    {
        static::retrieved(function($object) {
            if (auth()->guest()) {
                static::denyObjectAccess();
            }

            if ( $object->users_id !== auth()->user()->id) {
                static::denyObjectAccess();
            }

        });



        parent::boot();
    }

    /**
     * @throws ObjectAccessException
     */
    protected static function denyObjectAccess()
    {
        throw new ObjectAccessException('access is not allowed');
    }
}