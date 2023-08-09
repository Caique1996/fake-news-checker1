<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Api\V1\NewsController;
use App\Http\Api\V1\ImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => ['auth.api', 'throttle:120,1']], function () {
    Route::put('/news', [NewsController::class, 'store'])->name("api.news.store");
    Route::post('/image', [ImageController::class, 'store'])->name("api.images.store");

});
