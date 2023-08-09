<?php

namespace Database\Seeders;

use App\Enums\BoolStatus;
use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->isAdmin()->isActive()->count(3)->create();
        $firstUser = User::where("type", UserType::Admin)->first();
        if(isset($firstUser['id'])){
            $firstUser->email = 'admin@fakenewschecker.net';
            $firstUser->saveOrFail();
        }
        User::factory()->isModerator()->isActive()->count(50)->create();
        User::factory()->isSubscriber()->isActive()->count(50)->create();
    }
}
