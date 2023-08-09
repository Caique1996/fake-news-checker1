<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sqlView = "SELECT searches.*, (CASE type WHEN 'News' THEN (SELECT news.url from news where news.id=searches.object_id) WHEN 'Image' THEN (SELECT image_searches.image from image_searches where image_searches.id=searches.object_id) ELSE NULL END) as object_data,(SELECT count(*) FROM reviews where reviews.search_id=searches.id) as qty_reviews,(CASE type WHEN 'Image' THEN (SELECT image_searches.checksum from image_searches where image_searches.id=searches.object_id) ELSE NULL END) as checksum FROM `searches`;";
        \DB::statement("DROP VIEW IF EXISTS searches_with_objects;");
        \DB::statement("create view searches_with_objects as $sqlView");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
