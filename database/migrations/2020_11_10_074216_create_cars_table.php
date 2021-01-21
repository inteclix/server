<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->increments('id');
            $table->string("matricule")->unique();
            $table->string("prop"); // condor logistics ou electronics
            $table->string("old_matricule");
            $table->string("code_gps")->unique()->nullable();
            $table->string("genre");
            $table->string("marque");
            $table->string("type");
            $table->string("puissance");
            $table->string("energie");
            $table->string("carrosserie");
            $table->string("color")->nullable();

            $table->integer('createdby_id')->unsigned();;
            $table->foreign('createdby_id')->references('id')->on('users')
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
        Schema::dropIfExists('cars');
    }
}
