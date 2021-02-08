<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('createdby_id')->unsigned();
            $table->foreign('createdby_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('driver_id')->unsigned();
            $table->foreign('driver_id')->references('id')->on('drivers')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('decharge_id')->unsigned()->nullable();
            $table->foreign('decharge_id')->references('id')->on('decharges')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->dateTime('date_checklist');

            // status vehicule
            $table->integer("niveau_carburant")->nullable(); // 1 to 100
            $table->integer("odometre")->nullable(); // odometre 
            $table->integer("starts")->nullable(); // etat de vehicule ***** five start
            // documents
            $table->boolean("carte_grise")->nullable();
            $table->boolean("assurance")->nullable();
            $table->boolean("assurance_marchandises")->nullable();
            $table->boolean("scanner")->nullable();
            $table->boolean("permis_circuler")->nullable();
            $table->boolean("carnet_enter")->nullable();
            $table->boolean("vignette")->nullable();
            $table->boolean("carte_gpl")->nullable();
            // equipments
            $table->boolean("gillet")->nullable();
            $table->boolean("roue_secour")->nullable();
            $table->boolean("cric")->nullable();
            $table->boolean("poste_radio")->nullable();
            $table->boolean("cle_roue")->nullable();
            $table->boolean("extincteur")->nullable();
            $table->boolean("boite_pharm")->nullable();
            $table->boolean("triangle")->nullable();
            $table->boolean("pochette_cle")->nullable();
            $table->integer("cle_vehicule")->default(1); // double cle
            $table->string("observation")->nullable();
            // timestamps
            $table->timestamps(); // update_at is date checklist
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklists');
    }
}
