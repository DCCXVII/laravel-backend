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
            $table->string('titre');
            $table->longText('description');
            $table->enum('niveau', ['débutant', 'intermédiaire',  'avancée']);
            $table->double('price');
            $table->foreignId('coach_id')->constrained('users');
            $table->foreignId('classe_id')->constrained('classes');
            $table->foreignId('discipline_id')->constrained('disciplines');
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
