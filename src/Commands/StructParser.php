<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 17.05.17
 * Time: 11:06
 */

namespace Dion\Foa\Commands;


use Dion\Foa\Models\ObjectType;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class StructParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foa:struct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generates / updates your database schema';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fileSystem = new Filesystem();

        $path = config_path('struct.json');

        if (! $fileSystem->isFile($path)) {
            $this->error('struct.json is not published in config path till yet');

            return;
        }

        $json = $fileSystem->get($path);

        $structures = array_get(recursiveToArray((array) json_decode($json)), 'struct', []);

        if (empty($structures)) {
            $this->error('no structs defined');

            return;
        }

        foreach ($structures as $type => $struct) {
            $this->upsertStructure($type, $struct);
        }
    }

    protected function upsertStructure($type, array $struct)
    {
        $message = 'updated ' . $type;

        $objectType = foa_objectTypes()->findByName($type);

        if (! $objectType instanceof ObjectType) {
            $objectType = foa_objectTypes()->insert(['name' => $type]);

            $message = 'inserted ' . $type;
        }

        $rules = $objectType->rules;

        $rules->schema = array_get($struct, 'rules', []);
        $rules->relations = array_get($struct, 'relations', []);
        $rules->static = array_get($struct, 'static', false);

        $objectType->update([
            'rules' => $rules
        ]);

        $this->info($message);
    }
}