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
        if(Schema::hasTable('app_files')) {
            return;
        }
        Schema::create('app_files', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedInteger('version_id')->default(0)->comment('应用版本ID');
            $table->unsignedInteger('file_id')->default(0)->comment('文件ID');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_files');
    }
};
