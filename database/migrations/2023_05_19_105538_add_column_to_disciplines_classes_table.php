<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->string('background_img');
        });
        Schema::table('classes', function (Blueprint $table) {
            $table->string('background_img');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->bigInteger('views_number');
            $table->bigInteger('sells_number');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->bigInteger('views_number');
            $table->bigInteger('sells_number');
        });
        Schema::table('packs', function (Blueprint $table) {
            $table->bigInteger('views_number');
            $table->bigInteger('sells_number');
        });
    } 

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      
    }
};
