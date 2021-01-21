<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->integer('driver1_id')->unsigned();
            $table->foreign('driver1_id')->references('id')->on('drivers')
                ->onDelete('restrict')
                ->onUpdate('restrict');
                
            $table->integer('driver2_id')->unsigned();
            $table->foreign('driver2_id')->references('id')->on('drivers')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            // par default cree un status - en attente de chargement
            $table->integer('car_state_id')->unsigned();
            $table->foreign('car_state_id')->references('id')->on('car_states')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            // lieux
            $table->string("numero")->nullable(); // numero 
            $table->string("l_chargement"); // code_postal
            $table->string("l_dechargement"); // code_postal
            // dates
            $table->dateTime('date_mission');
            $table->dateTime('date_bon_chargement')->nullable();
            $table->dateTime('date_depart')->nullable();
            $table->dateTime('date_arrive_destination')->nullable(); // client
            $table->dateTime('date_depart_destination')->nullable(); // client
            $table->dateTime('date_arrive')->nullable();
            $table->integer("km")->nullable();

            $table->string("observation")->nullable();

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
        Schema::dropIfExists('missions');
    }
}
