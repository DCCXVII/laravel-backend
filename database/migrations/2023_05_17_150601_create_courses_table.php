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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('titre')->unique();
            $table->string('url');
            $table->longText('description');
            $table->enum('niveau', ['Débutant', 'Intermédiaire',  'avancée']);
            $table->double('price');
            $table->string('background_image');
            $table->bigInteger('views_number');
            $table->bigInteger('sells_number');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('discipline_id')->constrained('disciplines')->onDelete('cascade');
            $table->foreignId('classe_id')->constrained('classes')->onDelete('cascade');
            $table->time('duration');
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
        Schema::dropIfExists('courses');
    }
};
