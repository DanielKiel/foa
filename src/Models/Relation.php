<?php

namespace Dion\Foa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relation extends Model
{

    public $timestamps = true;

    public $toLoad = false;

    /** @var Relations  */
    public $repo;

    protected $table = 'relations';

    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $fillable = [
        'base_id',
        'target_id',
        'relation_type_id',
    ];

    protected $with = [];

    /**
     * @return BelongsTo
     */
    public function baseObject(): BelongsTo
    {
        return $this->belongsTo(BaseObject::class, 'base_id');
    }

    /**
     * @return BelongsTo
     */
    public function targetObject(): BelongsTo
    {
        return $this->belongsTo(BaseObject::class, 'target_id');
    }

    public function relationType(): BelongsTo
    {
        return $this->belongsTo(RelationType::class, 'relation_type_id');
    }
}
