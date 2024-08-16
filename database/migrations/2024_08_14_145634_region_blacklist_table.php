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
        Schema::create('region_blacklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->default(0)->comment('类型：0=黑名单；1=白名单');
            $table->string('region_code', 2)->default('')->comment('地区简码');
            $table->text('sub_region_codes')->nullable()->comment('子地区简码，使用英文逗号隔离');
            $table->unsignedTinyInteger('is_enable')->default(0)->comment('是否启用');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['type', 'region_code', 'deleted_at'], 'uniq_type_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_blacklists');
    }
};
