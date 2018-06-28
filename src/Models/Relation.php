<?php

namespace Dion\Foa\Models;

use Dion\Foa\Events\UserRelatedModelCreating;
use Dion\Foa\Traits\UserRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relation extends Model
{
    use SoftDeletes, UserRelation;

    public $timestamps = true;

    public $toLoad = false;

    /** @var Relations  */
    public $repo;

    protected $table = 'relations';

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $fillable = [
        'base_id',
        'base_type_id',
        'target_id',
        'target_type_id',
        'name',
        'inverse_name',
        'users_id'
    ];

    protected $dispatchesEvents = [
        'creating' => UserRelatedModelCreating::class,
    ];

    protected $with = [];

    /**
     * @return BelongsTo
     */
    public function baseObject(): BelongsTo
    {
        return $this->belongsTo(Object::class, 'base_id');
    }

    /**
     * @return BelongsTo
     */
    public function targetObject(): BelongsTo
    {
        return $this->belongsTo(Object::class, 'target_id');
    }

    public function baseType(): BelongsTo
    {
        return $this->belongsTo(ObjectType::class, 'base_type_id');
    }

    public function targetType(): BelongsTo
    {
        return $this->belongsTo(ObjectType::class, 'target_type_id');
    }
}
