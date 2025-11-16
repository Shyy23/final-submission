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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id('submission_id');
            $table->string('representative_nim', 11);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('leader_id', 11)->nullable();
            $table->string('company_name', 255);
            $table->text('address_company');
            $table->text('note', 255)->nullable();
            $table->enum(
                'status',
                ['pending', 'approved', 'rejected', 'verified']
            )->default('pending');
            $table->string('file_submission', 255);
            $table->string('qr_url', 255)->nullable();
            $table->text('feedback')->nullable();

            $table->foreign('representative_nim')->references('nim')->on('students')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('leader_id')->references('nid')->on('leaders')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('submission_members', function (Blueprint $table) {
            $table->id('member_id');
            $table->unsignedBigInteger('submission_id');
            $table->string('student_nim', 11);
            $table->boolean('is_representative')->default(false);
            $table->timestamps();

            $table->foreign('submission_id')->references('submission_id')->on('submissions')->onDelete('cascade');
            $table->foreign('student_nim')->references('nim')->on('students')->onDelete('cascade');
            $table->unique(['submission_id', 'student_nim']); // Mencegah duplikasi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions_members');
        Schema::dropIfExists('submissions
        ');
    }
};
