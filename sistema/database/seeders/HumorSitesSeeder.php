<?php

namespace Database\Seeders;

use App\Enums\BoolStatus;
use App\Models\GoogleSearchResult;
use App\Models\HumorSite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HumorSitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = [];
        $rows[] = [
            'site' => 'https://oglobo.globo.com/blogs/humor/sensacionalista/',
            'status' => BoolStatus::Active
        ];
        foreach ($rows as $row){
            $model= new HumorSite();
            $model->site=$row['site'];
            $model->status=BoolStatus::Active;
            $model->save();
        }
    }
}
