<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDechargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('decharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('createdby_id')->unsigned();
            $table->foreign('createdby_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('acceptedby_id')->unsigned()->nullable();
            $table->foreign('acceptedby_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->dateTime('date_decharge');
            $table->dateTime('date_fin_prestation')->nullable();
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
        Schema::dropIfExists('decharges');
    }
}
