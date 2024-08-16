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
        Schema::create('sub_regions', function (Blueprint $table) {
            $table->id();
            $table->string('region_code', 2)->default('')->comment('地区简码');
            $table->string('iso_code', 3)->default('')->comment('简码');
            $table->string('name_en', 100)->default('')->comment('英文名称');
            $table->string('name_cn', 100)->default('')->comment('中文名称');
            $table->unsignedTinyInteger('is_enable')->default(1)->comment('是否启用');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['country_code', 'iso_code'], 'uniq_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_regions');
    }
};
