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
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('iso_code', 2)->default('')->unique('uniq_iso_code')->comment('二字简码');
            $table->string('iso_code_3', 3)->default('')->comment('三字简码');
            $table->string('name_en', 50)->default('')->comment('英文名称');
            $table->string('name_cn', 50)->default('')->comment('中文名称');
            $table->unsignedInteger('no')->default(0)->comment('编号');
            $table->unsignedTinyInteger('is_enable')->default(1)->comment('是否启用');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
