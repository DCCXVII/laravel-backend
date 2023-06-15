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
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->string('titre')->unique();
            $table->longText('description');
            $table->enum('niveau', ['débutant', 'intermédiaire',  'avancée']);
            $table->double('price');
            $table->string('background_image');
            $table->bigInteger('views_number');
            $table->bigInteger('sells_number');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('discipline_id')->constrained('disciplines')->onDelete('cascade');
            $table->string('teaser_url')->nullable();
            $table->enum('status', ['en_attente', 'accepté', 'refusé'])->default('en_attente');
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
        Schema::dropIfExists('packs');
    }
};
