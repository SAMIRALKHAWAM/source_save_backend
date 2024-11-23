<?php

use App\Enums\FileStatusEnum;
use App\Enums\GroupStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_user_id');
            $table->foreign('group_user_id')->references('id')->on('group_users')->cascadeOnDelete();
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->decimal('size_MB');
            $table->string('url');
            $table->boolean('availability')->default(FileStatusEnum::AVAILABLE);
            $table->string('status')->default(GroupStatusEnum::PENDING);
            $table->unsignedBigInteger('reserved_by')->nullable();
            $table->foreign('reserved_by')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
