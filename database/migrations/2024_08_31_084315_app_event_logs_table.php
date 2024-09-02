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
        if(Schema::hasTable('app_event_logs')) {
            return;
        }
        Schema::create('app_event_logs', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->bigInteger('app_version_id')->default(0)->comment('应用版本ID');
            $table->string('event_code', 30)->default('')->comment('事件代号');
            $table->string('sub_event_code', 30)->default('')->comment('事件子代号');
            $table->string('client_ip', 56)->default('')->comment('请求端IP');
            $table->string('client_ip_region_code', 3)->default('')->comment('请求端所在地区');
            $table->string('client_ip_sub_region_code', 3)->default('')->comment('请求端所在子地区');
            $table->string('device_id', 255)->default('')->comment('app设备唯一码');
            $table->string('lang_code', 50)->default('')->comment('语言代码');
            $table->string('domain', 255)->default('')->comment('请求端访问的域名');
            $table->string('remark',  255)->default('')->comment('备注说明');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_event_logs');
    }
};
