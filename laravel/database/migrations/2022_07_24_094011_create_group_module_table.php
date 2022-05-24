<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_module', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('semester');

            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('module_id')->unsigned();
            $table->foreign('module_id')->references('id')->on('modules')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_module');
    }
};
