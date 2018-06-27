<?php

namespace Dion\Foa\Listeners;

use Dion\Foa\Events\DataDefined;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DataDefinedListener
{


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DataDefined  $event
     * @return void
     */
    public function handle(DataDefined $event)
    {
        $schema = foa_objectTypes()->getSchema($event->objectType);

        foreach ($schema as $attribute => $cast) {

            if (! array_has($event->data, $attribute)) {
                continue;
            }

            $value = array_get($event->data, $attribute);

            if (method_exists($this, $cast)) {
                $value = $this->{$cast}($value);
            }
            else {
                try {
                    settype($value, $cast);
                }
                catch(\Exception $e) {

                }
            }

            array_set(
                $event->data,
                $attribute,
                $value
            );
        }
    }

    protected function password($value)
    {
        return encrypt($value);
    }
}
