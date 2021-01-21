<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarDriverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_driver', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('car_id')->unsigned();
            $table->foreign('car_id')->references('id')->on('cars')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->integer('driver_id')->unsigned();
            $table->foreign('driver_id')->references('id')->on('drivers')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->date('date_affectation_driver');
            $table->boolean("carte_grise");
            $table->boolean("carnet");
            $table->boolean("pochette_cle");
            $table->boolean("boite_pharm");
            $table->boolean("triangle");
            $table->boolean("extincteur");
            $table->boolean("gillet");
            $table->boolean("assurance");
            $table->boolean("vignette");
            $table->boolean("carte_gpl");
            $table->boolean("permis_circuler");
            $table->boolean("scanner");
            $table->string("affectation_comment");
            $table->boolean("roue_secour");
            $table->boolean("cric");
            $table->boolean("poste_radio");
            $table->boolean("cle_roue");
            $table->integer("cle_vehicule"); // double cle
            $table->integer("km"); // odometre

            $table->date('date_restitition_driver')->nullable();
            $table->boolean("carte_grise_restitition")->nullable();
            $table->boolean("carnet_restitition")->nullable();
            $table->boolean("pochette_cle_restitition")->nullable();
            $table->boolean("boite_pharm_restitition")->nullable();
            $table->boolean("triangle_restitition")->nullable();
            $table->boolean("extincteur_restitition")->nullable();
            $table->boolean("gillet_restitition")->nullable();
            $table->boolean("assurance_restitition")->nullable();
            $table->boolean("vignette_restitition")->nullable();
            $table->boolean("carte_gpl_restitition")->nullable();
            $table->boolean("permis_circuler_restitition")->nullable();
            $table->boolean("scanner_restitition")->nullable();
            $table->string("restitition_comment")->nullable();
            $table->boolean("roue_secour_restitition")->nullable();
            $table->boolean("cric_restitition")->nullable();
            $table->boolean("poste_radio_restitition")->nullable();
            $table->boolean("cle_roue_restitition")->nullable();
            $table->integer("cle_vehicule_restitition")->nullable(); // double cle
            $table->integer("km_restitition")->nullable(); // odometre
            $table->string('motif_restitition_driver')->nullable();

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
        Schema::dropIfExists('car_driver');
    }
}
