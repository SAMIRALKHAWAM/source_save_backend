<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('old_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id');
            $table->foreign('file_id')->references('id')->on('files')->cascadeOnDelete();
            $table->unsignedBigInteger('group_user_id');
            $table->foreign('group_user_id')->references('id')->on('group_users')->cascadeOnDelete();
            $table->string('name');
            $table->mediumText('description');
            $table->decimal('size_MB');
            $table->string('url');
            $table->longText('diff')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_files');
    }
};
