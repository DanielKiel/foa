<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FoaInitialSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objects', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('objecttypes_id')->unsigned();
            $table->json('data');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['id', 'objecttypes_id', 'deleted_at']);
        });

        Schema::create('objecttypes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->string('name');
            $table->string('model_type')->default(\Dion\Foa\Models\BaseObject::class);
            $table->json('rules');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('relation_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('target_type_id')->unsigned();
            $table->bigInteger('base_type_id')->unsigned();
            $table->string('variant')->index();
            $table->string('name')->index();
            $table->string('inverse_name')->index();
            $table->timestamps();
            $table->softDeletes();

        });

        Schema::create('relations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('base_id')->unsigned();
            $table->bigInteger('target_id')->unsigned();
            $table->bigInteger('relation_type_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

        });

        Schema::table('objects', function(Blueprint $table) {
            $table->foreign('objecttypes_id')->references('id')->on('objecttypes');
            $table->index('deleted_at');
        });

        Schema::table('objecttypes', function(Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('relations', function(Blueprint $table) {
            $table->foreign('base_id')->references('id')->on('objects');
            $table->foreign('target_id')->references('id')->on('objects');
            $table->foreign('relation_type_id')->references('id')->on('relation_types');
        });

        Schema::table('relation_types', function(Blueprint $table) {
            $table->foreign('base_type_id')->references('id')->on('objecttypes');
            $table->foreign('target_type_id')->references('id')->on('objecttypes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('objects', function(Blueprint $table) {
            $table->dropForeign('objects_objecttypes_id_foreign');
        });

        Schema::table('relations', function(Blueprint $table) {
            $table->dropForeign('relations_base_id_foreign');
            $table->dropForeign('relations_target_id_foreign');
            $table->dropForeign(['relation_type_id']);
        });

        Schema::table('relation_types', function(Blueprint $table) {
            $table->dropForeign(['base_type_id']);
            $table->dropForeign(['target_type_id']);
        });


        Schema::dropIfExists('objects');
        Schema::dropIfExists('objecttypes');
        Schema::dropIfExists('relations');
        Schema::dropIfExists('relation_types');
    }
}
