<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('study_programs', function (Blueprint $table) {
            $table->id('study_id');
            $table->string('study_name');
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->string('nim', 11)->primary();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('study_id');
            $table->string('ktm_path', 255)->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('study_id')->references('study_id')->on('study_programs')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id('position_id');
            $table->string('position_name');
            $table->timestamps();
        });

        Schema::create('leaders', function (Blueprint $table) {
            $table->string('nid', 11)->primary();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('position_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('position_id')->references('position_id')->on('positions')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('leaders');
        Schema::dropIfExists('study_programs');
        Schema::dropIfExists('positions');
    }
};
