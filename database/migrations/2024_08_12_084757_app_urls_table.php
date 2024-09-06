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
        if(Schema::hasTable('app_urls')) {
            return;
        }
        Schema::create('app_urls', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->bigInteger('app_id')->default(0)->index('idx_app')->comment('应用ID');
            $table->unsignedInteger('type')->default(0)->comment('链接类型：0=A链接；1=B链接');
            $table->text('url')->nullable()->comment('跳转链接');
            $table->text('check_url')->nullable()->comment('测试链接');
            $table->unsignedTinyInteger('is_enable')->default(1)->comment('是否正常：0=否；1=是');
            $table->unsignedTinyInteger('is_reserved')->default(0)->comment('是否备用：0=否；1=是');
            $table->unsignedTinyInteger('is_in_used')->default(0)->comment('是否启用：0=否；1=是');
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
