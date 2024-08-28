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
        if(Schema::hasTable('files')) {
            return;
        }
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->comment('ID');
            $table->string('file_name', 255)->default('')->comment('文件名');
            $table->string('save_path', 255)->default('')->comment('英文名称');
            $table->string('origin_name', 255)->default('')->comment('中文名称');
            $table->string('extension', 15)->default('')->comment('文件后缀');
            $table->unsignedInteger('file_size')->default(0)->comment('文件大小：单位B');
            $table->timestamps();
            $table->softDeletes();
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
