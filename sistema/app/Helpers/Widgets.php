<?php

use Backpack\CRUD\app\Library\Widget;
use App\Enums\SslCertificateStatusEnum;
use Illuminate\Support\Arr;


function widgetModelData($extraData)
{
    $data = [
        'type' => 'view',
        'view' => 'vendor.backpack.widgets.ajax_progress'
    ];
    $data = $data + $extraData;
    return Widget::add($data)->section('before_content');
}
