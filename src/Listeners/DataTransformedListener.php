<?php

namespace Dion\Foa\Listeners;

use Dion\Foa\Contracts\AttributeCasterInterface;
use Dion\Foa\Events\DataTransformed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class DataTransformedListener
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
    public function handle(DataTransformed $event)
    {
        $schema = foa_objectTypes()->getSchema($event->objectType);

        foreach ($schema as $attribute => $cast) {

            if (! array_has($event->data, $attribute)) {
                continue;
            }

            $value = array_get($event->data, $attribute);

            $caster = resolve(AttributeCasterInterface::class);

            if (method_exists($caster, $cast)) {
                $value = $caster->{$cast}($value, $event->data);
            }
            else {
                try {
                    settype($value, $cast);
                }
                catch(\Exception $e) {
                    Log::error($e->getMessage());
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
        return decrypt($value);
    }
}
