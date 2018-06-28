<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 28.06.18
 * Time: 15:27
 */

namespace Dion\Foa\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRelatedModelCreating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $model;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}