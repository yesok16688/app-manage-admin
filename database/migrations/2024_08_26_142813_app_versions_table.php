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
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id()->comment('应用版本ID');
            $table->bigInteger('app_id')->default(0)->index('idx_app')->comment('应用ID');
            // 基本信息
            $table->string('app_name', 50)->default('')->comment('应用名称');
            $table->string('api_key', 48)->default('')->unique()->comment('接口访问密钥');
            $table->string('version', 15)->default('')->comment('版本号');
            $table->text('icon')->nullable()->comment('应用图标');
            $table->text('description')->nullable()->comment('应用详情');
            $table->text('download_link')->nullable()->comment('下载链接');
            $table->tinyInteger('status')->default(0)->comment('提审状态（待提审/审核中/上架成功/投放中/被下架）[审核中强制关闭所有跳转行为]');
            // 控制信息
            $table->string('ip_blacklist', 255)->default('')->comment('IP黑名单,多个使用英文逗号分隔');
            $table->tinyInteger('is_region_limit')->default(1)->comment('限制除上架地区之外地区IP的跳转');
            $table->string('lang_blacklist', 255)->default('')->comment('手机语言黑名单,多个使用英文逗号分隔');
            $table->tinyInteger('disable_jump')->default(0)->comment('强制关闭跳转，高优先级');
            $table->string('ip_whitelist', 255)->default('')->comment('IP白名单,多个使用英文逗号分隔; 强制解除IP所有限制，最高优先级');
            // 额外信息
            $table->tinyInteger('upgrade_mode')->default(0)->comment('升级模式：0=不升级；1=提示升级；2=强制升级');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
