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
        if(Schema::hasTable('langs')) {
            return;
        }
        Schema::create('langs', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('lang_code', 50)->default('')->comment('应用名称');
            $table->string('name_en', 255)->default('')->comment('英文名称');
            $table->string('name_cn', 255)->default('')->comment('中文名称');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langs');
    }
};
