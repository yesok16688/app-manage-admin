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
        Schema::create('redirect_urls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order')->default(0)->comment('优先级,数值越大优先级越低');
            $table->string('group_code', 50)->index('idx_code')->comment('分组码');
            $table->string('url', 256)->default('')->comment('跳转链接');
            $table->unsignedTinyInteger('is_enable')->default(1)->comment('是否启用：0=否；1=是');
            $table->string('remark', 256)->default('')->comment('备注');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirect_urls');
    }
};
