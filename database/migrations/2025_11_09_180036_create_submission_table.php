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
            $table->string('department_name')->nullable();
            $table->string('department_code', 10)->nullable();
            $table->text('note')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected', 'verified'])
                    ->default('pending');
            $table->string('document_path', 255)->nullable();
            $table->string('qr_url', 255)->nullable();
            $table->text('feedback')->nullable();
            $table->boolean('sent_to_leader')->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('representative_nim')
                    ->references('nim')
                    ->on('students')
                    ->onDelete('cascade');

            $table->foreign('admin_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');

            $table->foreign('leader_id')
                    ->references('nid')
                    ->on('leaders')
                    ->onDelete('set null');
        });

        Schema::create('submission_members', function (Blueprint $table) {
            $table->id('member_id');
            $table->unsignedBigInteger('submission_id');
            $table->string('student_nim', 11);
            $table->boolean('is_representative')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('submission_id')
                  ->references('submission_id')
                  ->on('submissions')
                  ->onDelete('cascade');
                  
            $table->foreign('student_nim')
                  ->references('nim')
                  ->on('students')
                  ->onDelete('cascade');
                  
            // Unique constraint
            $table->unique(['submission_id', 'student_nim'], 'unique_submission_member');
        });
        
        // Indexes untuk optimasi query
        Schema::table('submissions', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes terlebih dahulu
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        
        // Drop tables
        Schema::dropIfExists('submission_members');
        Schema::dropIfExists('submissions');
    }
};