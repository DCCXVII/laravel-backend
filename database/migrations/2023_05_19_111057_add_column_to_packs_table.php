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
        Schema::table('packs', function (Blueprint $table) {
            $table->string('teaser_url');
            $table->integer('courses_number');
            $table->enum('status', ['en_attente', 'accepté', 'rejecté'])->default('en_attente');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->time('duration');
            $table->enum('status', ['en_attente', 'accepté', 'rejecté'])->default('en_attente');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packs', function (Blueprint $table) {
            //
        });
    }
};
