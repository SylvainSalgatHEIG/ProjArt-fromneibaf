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
        Schema::create('deadlines', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('name', 100);
            $table->string('description', 250);
            $table->enum('type', ['examen', 'rendu']);
            $table->integer('course_id')->unsigned();
            $table->foreign('course_id')->references('id')->on('courses')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('groups')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deadlines');
    }
};
