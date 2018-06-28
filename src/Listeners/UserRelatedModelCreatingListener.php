<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 28.06.18
 * Time: 15:26
 */

namespace Dion\Foa\Listeners;


use Dion\Foa\Events\UserRelatedModelCreating;
use Dion\Foa\Exceptions\NotAuthenticated;

class UserRelatedModelCreatingListener
{
    /**
     * @param UserRelatedModelCreating $event
     * @throws NotAuthenticated
     */
    public function handle(UserRelatedModelCreating $event)
    {
        if (auth()->guest()) {
            throw new NotAuthenticated();
        }

        $event->model->users_id = auth()->user()->id;
    }
}