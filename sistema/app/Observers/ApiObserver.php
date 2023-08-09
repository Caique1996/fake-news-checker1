<?php

namespace App\Observers;

use App\Exceptions\ModelObserverException;
use App\Models\Api;
use App\Models\MetaData;
use Doctrine\DBAL\Exception;
use Illuminate\Support\Str;

class ApiObserver
{


    /**
     * Handle the Api "creating" event.
     *
     * @param \App\Models\Api $api
     * @return void
     */
    public function creating(Api $api)
    {
        $apiQuantityLimit = MetaData::getValue('api_quantity_limit');
        $countApis = Api::where("user_id", $api->user_id)->count();
        if ($countApis >= $apiQuantityLimit) {
            throw new \Exception(__("You have reached the maximum amount(:quantity) of APIS. Delete another API to create new APIs.", ['quantity' => $apiQuantityLimit]));
        }
        $api->name = "Default";
        $api->token = Str::random(60);;
    }


}
