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
            $table->integer("niveau_carburant"); // 1 to 100
            $table->integer("odometre"); // odometre 
            $table->integer("starts"); // etat de vehicule ***** five start
            // documents
            $table->boolean("carte_grise");
            $table->boolean("assurance");
            $table->boolean("scanner");
            $table->boolean("permis_circuler");
            $table->boolean("carnet_enter");
            $table->boolean("vignette");
            $table->boolean("carte_gpl");
            // equipments
            $table->boolean("gillet");
            $table->boolean("roue_secour");
            $table->boolean("cric");
            $table->boolean("poste_radio");
            $table->boolean("cle_roue");
            $table->boolean("extincteur");
            $table->boolean("boite_pharm");
            $table->boolean("triangle");
            $table->boolean("pochette_cle");
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
