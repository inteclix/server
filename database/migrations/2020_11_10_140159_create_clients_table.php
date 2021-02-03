<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string("code")->unique()->nullable();
            $table->string("designation")->unique();
            $table->string("localite")->nullable();
            $table->string("tel")->nullable();
            $table->integer('client_id')->unsigned()->nullable();;
            $table->foreign('client_id')->references('id')->on('clients')
                ->onDelete('restrict')
                ->onUpdate('restrict');

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
        Schema::dropIfExists('clients');
    }
}
