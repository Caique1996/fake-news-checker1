<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn('avg');
            $table->dropColumn('is_fake');
            $table->dropColumn('is_humor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->integer('avg');
            $table->boolean('is_fake')->default(false);
            $table->boolean('is_humor')->default(false);
        });
    }
};
