<?php

namespace Dion\Foa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelationType extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    public $toLoad = false;

    /** @var Relations  */
    public $repo;

    protected $table = 'relation_types';

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $fillable = [
        'base_type_id',
        'target_type_id',
        'name',
        'inverse_name',
        'variant'
    ];

    protected $with = [];

    public function baseType(): BelongsTo
    {
        return $this->belongsTo(ObjectType::class, 'base_type_id');
    }

    public function targetType(): BelongsTo
    {
        return $this->belongsTo(ObjectType::class, 'target_type_id');
    }
}
