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
        Schema::create('url_handle_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('url_id')->default(0)->comment('对应redirect_url表id');
            $table->integer('http_status')->default(0)->comment('http响应码');
            $table->tinyInteger('status')->default(0)->comment('处理结果');
            $table->string('client_ip', 56)->default('')->comment('客户端IP');
            $table->string('client_ip_region', 3)->default('')->comment('ip所属地区');
            $table->string('client_ip_sub_region', 3)->default('')->comment('ip所属子地区');
            $table->string('remark')->default('')->comment('备注');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_handle_logs');
    }
};
