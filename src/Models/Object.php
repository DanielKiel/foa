<?php

namespace Dion\Foa\Models;

use Dion\Foa\Events\DataTransformed;
use Dion\Foa\Events\UserRelatedModelCreating;
use Dion\Foa\Traits\UserRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Object extends Model
{
    use SoftDeletes, UserRelation;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string
     */
    protected $table = 'objects';

    /**
     * @var array
     */
    protected $dates = ['deleted_at', 'created_at', 'deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'objecttypes_id', 'data', 'users_id'
    ];

    protected $casts = [
        'data' => 'object'
    ];

    /**
     * setting some default values here
     * @var array
     */
    protected $attributes = [
        'data' => '{}'
    ];

    protected $dispatchesEvents = [
        'creating' => UserRelatedModelCreating::class,
    ];

    public function objectType(): BelongsTo
    {
        return $this->belongsTo(ObjectType::class, 'objecttypes_id');
    }

    /**
     * @return HasMany
     */
    public function hasRelations(): HasMany
    {
        return $this->hasMany(Relation::class, 'target_id');
    }

    /**
     * @return HasMany
     */
    public function belongsRelations(): HasMany
    {
        return $this->hasMany(Relation::class, 'base_id');
    }

    public function toArray()
    {
        $all = recursiveToArray( (array) $this->data );

        array_set($all, 'id', $this->id);

        event($event = new DataTransformed($this->objectType, $all));

        return $event->data;
    }
}
