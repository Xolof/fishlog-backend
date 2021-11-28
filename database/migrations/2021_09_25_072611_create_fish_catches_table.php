<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFishCatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fish_catches', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('species');
            $table->integer('length');
            $table->integer('weight');
            $table->date('date');
            $table->string('location');
            $table->timestamps();
            
            // $table->foreign('user_id')
            //         ->references('id')
            //         ->on('users')
            //         ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fish_catches');
    }
}
