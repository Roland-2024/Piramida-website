<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('attachable_type');
            $table->unsignedBigInteger('attachable_id');
            $table->string('role', 30)->default('gallery');
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();

            $table->unique(
                ['attachable_type', 'attachable_id', 'media_id', 'role'],
                'media_attachments_unique'
            );
            $table->index(
                ['attachable_type', 'attachable_id', 'role', 'display_order'],
                'media_attachments_order_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_attachments');
    }
};
