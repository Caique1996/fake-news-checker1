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
        Schema::create('exceptions', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->ipAddress("ip");
            $table->string("file");
            $table->text("message");
            $table->text("trace");
            $table->text("extra_data")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exceptions');
    }
};
