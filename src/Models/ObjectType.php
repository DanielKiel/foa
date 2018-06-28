<?php

namespace Dion\Foa\Models;

use Dion\Foa\Events\UserRelatedModelCreating;
use Dion\Foa\Traits\UserRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectType extends Model
{
    use SoftDeletes, UserRelation;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var string
     */
    protected $table = 'objecttypes';

    /**
     * @var array
     */
    protected $dates = ['deleted_at', 'created_at', 'deleted_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'model_type',
        'rules',
        'users_id'
    ];

    protected $dispatchesEvents = [
        'creating' => UserRelatedModelCreating::class,
    ];

    /**
     * setting some default values here
     * @var array
     */
    protected $attributes = [
        'model_type' => Object::class,
        'rules' => '{}'
    ];

    protected $casts = [
        'rules' => 'object'
    ];

    public function hasRelations(): HasMany
    {
        return $this->hasMany(Relation::class, 'base_type_id');
    }

    public function belongsRelations(): BelongsToMany
    {
        return $this->belongsToMany(Relation::class, 'target_type_id');
    }
}
