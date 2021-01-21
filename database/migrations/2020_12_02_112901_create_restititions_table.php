<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestititionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restititions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('createdby_id')->unsigned();
            $table->foreign('createdby_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->integer('decharge_id')->unsigned();
            $table->foreign('decharge_id')->references('id')->on('decharges')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->integer('checklist_id')->unsigned();
            $table->foreign('checklist_id')->references('id')->on('checklists')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->dateTime('date_restitition');
            $table->string('motif_restitition');
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
        Schema::dropIfExists('restititions');
    }
}
