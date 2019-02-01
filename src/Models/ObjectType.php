<?php

namespace Dion\Foa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObjectType extends Model
{
    use SoftDeletes;

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
        'rules'
    ];

    /**
     * setting some default values here
     * @var array
     */
    protected $attributes = [
        'model_type' => BaseObject::class,
        'rules' => '{}'
    ];

    protected $casts = [
        'rules' => 'object'
    ];

    public function hasRelationTypes(): HasMany
    {
        return $this->hasMany(RelationType::class, 'base_type_id');
    }

    public function belongsRelationTypes(): HasMany
    {
        return $this->hasMany(RelationType::class, 'target_type_id');
    }
}
