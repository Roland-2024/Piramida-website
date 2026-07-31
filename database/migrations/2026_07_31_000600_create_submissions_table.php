<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40);
            $table->string('status', 20)->default('new');
            $table->string('name');
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->nullableMorphs('related');
            $table->json('details')->nullable();
            $table->string('attachment_disk', 30)->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime', 100)->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status', 'created_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
