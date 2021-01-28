<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title'); // affectation ou restitution
            $table->string('sub_title'); // ex: commercial : affectation de vehicule xxxx-xx-xx 
            $table->string('url'); // clickable if !is_read
            $table->boolean('is_read')->default(false);
            
            $table->string("type")->nullable();
            $table->integer("type_id")->nullable();

            $table->integer('from_id')->unsigned();
            $table->foreign('from_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->integer('to_id')->unsigned();
            $table->foreign('to_id')->references('id')->on('users')
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
        Schema::dropIfExists('notifications');
    }
}
