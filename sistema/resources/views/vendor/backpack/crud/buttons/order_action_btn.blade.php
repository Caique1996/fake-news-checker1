<?php

use App\Models\SslCerticateWithRelation;
use App\Models\SslCertificate;
use App\Enums\OrderStatusEnum;

$actionRoute = url($crud->route . '/actions/');
$orderActionData = [];

$actionRow = $entry;
if ($actionRow instanceof SslCerticateWithRelation || $actionRow instanceof SslCertificate) {
    $order = $actionRow->order()->first();
    $sslCertificate = null;
} else {
    $order = $actionRow;
    $sslCertificate = $order->getCertificate();
}

$orderActionData[] = [
    'textConfirm' => __("After the suspension, the certificate will stop working in the next few hours."),
    'status' => OrderStatusEnum::Suspended->value,
    'actionRoute' => $actionRoute,
    'actionNameIcon' => 'la la-pause',
    'class' => 'text-warning',
    'action' => 'change_status',
    'allowed_array' => OrderStatusEnum::canBeSuspended(),
];
$orderActionData[] = [
    'textConfirm' => __("After completing this action, the certificate will be reactivated in a few hours."),
    'status' => OrderStatusEnum::Complete->value,
    'actionRoute' => $actionRoute,
    'actionNameIcon' => 'la la-lock-open ',
    'class' => 'text-success',
    'action' => 'change_status',
    'allowed_array' => OrderStatusEnum::canBeunspended(),
];

$orderActionData[] = [
    'textConfirm' => __("cancell_confirmation_msg"),
    'status' => OrderStatusEnum::Cancelled->value,
    'actionRoute' => $actionRoute,
    'actionNameIcon' => 'la la-times',
    'class' => 'text-danger',
    'action' => 'change_status',
    'allowed_array' => OrderStatusEnum::canCancelled(),
];



$orderActionData[] = [
    'textConfirm' => __("Renewal requires a new payment which may be different from the purchase price. Do you wish to continue?"),
    'status' => null,
    'actionRoute' => $actionRoute,
    'title' => __("renew"),
    'actionNameIcon' => 'la la-sync',
    'class' => 'text-success',
    'action' => 'renew_order',
    'permission' => "Renew Order"
];

if (!is_null($sslCertificate)) {
    $orderActionData[] = [
        'html' => $sslCertificate->showManageBtn(),
        'permission' => 'Manage Certificate'
    ];
    if ($sslCertificate->status == \App\Enums\SslCertificateStatusEnum::Issued->value) {
        $orderActionData[] = [
            'html' => $sslCertificate->showDownloadBtn(),
            'permission' => 'Download Certificate'
        ];
    }


}
?>

@foreach ($orderActionData as $data)

    @if(!isset($data['html']))
        @if(!isset($data['permission']) && isset($data['allowed_array']) && in_array($order->status,$data['allowed_array']))
                <?php
                $permName = OrderStatusEnum::getPermissionNameByStatus($data['status']);
                if (canAccessGroupPerms($permName)) {
                    $data['title'] = __(strtolower($permName));
                    $data['postDataBtn'] = json_encode(['action' => $data['action'], 'value' => $data['status'], 'uuid' => $entry->uuid]);
                    $data['actionReqType'] = 'POST';
                }
                ?>
        @elseif(isset($data['permission']) && canAccessGroupPerms($data['permission']) &&  !isset($data['allowed_array']))
                <?php
                $data['postDataBtn'] = json_encode(['action' => $data['action'], 'uuid' => $entry->uuid]);
                $data['actionReqType'] = 'POST';
                ?>
        @endif

        @if(isset($data['postDataBtn']))
            @include("vendor.backpack.crud.html.custom_html_btn_ajax",$data)
        @endif
    @elseif(isset($data['html']) && canAccessGroupPerms($data['permission']))
        {!!$data['html']!!}
    @endif
@endforeach

{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts') @if (request()->ajax())
    @endpush
@endif

@if (!request()->ajax())
    @endpush
@endif
