<?php

namespace Database\Seeders;

use App\Models\MetaData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetaDataServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MetaData::setValue('api_quantity_limit', 5);
        MetaData::setValue('default_rate_limit', 30);
    }
}
