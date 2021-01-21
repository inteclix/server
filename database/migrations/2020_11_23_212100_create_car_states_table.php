<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_states', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->dateTime("state_date");
            $table->string("name");
            $table->string("observation")->nullable();

            $table->integer('createdby_id')->unsigned()->nullable();
            $table->foreign('createdby_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->integer('updatedby_id')->unsigned()->nullable();
            $table->foreign('updatedby_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_states');
    }
}
