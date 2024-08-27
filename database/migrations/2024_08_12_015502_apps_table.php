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
        if(Schema::hasTable('apps')) {
            return;
        }
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            // 归档字段
            $table->string('name', 50)->default('')->comment('包名称');
            //$table->string('api_key', 48)->default('')->unique()->comment('接口访问密钥');
            //$table->string('region', 3)->default('')->comment('地区');
            $table->text('region_codes')->nullable()->comment('地区编码,多个使用英文逗号分隔');
            $table->unsignedTinyInteger('channel')->default(0)->comment('渠道：1=oppo;2=vivo;3=google;4=apple;5=xiaomi;6=oneplus;等');
            //$table->unsignedTinyInteger('submit_status')->default(0)->comment('提审状态');
            // 控制字段
            //$table->unsignedTinyInteger('enable_redirect')->default(0)->comment('是否启用跳转：0=否；1=是');
            //$table->string('redirect_group_code', 50)->default('')->comment('跳转链接分组');
            //$table->unsignedTinyInteger('enable_ip_whitelist')->default(0)->comment('是否启用ip白名单跳转：0=否；1=是');
            // 其他
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
        Schema::dropIfExists('apps');
    }
};
