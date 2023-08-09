<?php

use App\Services\ApiService;
use App\Services\ApprovalEmailService;
use App\Services\GroupService;
use App\Services\OrderService;
use App\Services\PermissionService;
use App\Services\PriceService;
use App\Services\ProductService;
use App\Services\SslCertificateService;
use App\Services\TransactionService;
use App\Services\UserService;

function getServiceInstance($name)
{
    $model = "\\App\\Models\\" . $name;
    $model = new $model();
    $repository = "\\App\\Repositories\\" . $name . "Repository";
    $repository = new $repository($model);
    $service = "\\App\\Services\\" . $name . "Service";
    return new $service($repository);
}

function getProductServiceInstance(): ?ProductService
{
    return getServiceInstance("Product");
}

