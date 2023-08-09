<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserCrudController;
use App\Http\Controllers\Admin\ImageSearchCrudController;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array)config('backpack.base.web_middleware', 'web'),
        (array)config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::get('dashboard', function (){
        return redirect("admin/news");
    })->name('admin.dashboard.index');

    Route::crud('user', 'UserCrudController');
    Route::crud('api', 'ApiCrudController');
    Route::post('/user/search_select2', [UserCrudController::class, 'search_select2'])->name('admin.user.search_select');
    Route::crud('api-request', 'ApiRequestCrudController');
    Route::crud('blocked-ip', 'BlockedIpCrudController');

    Route::crud('domain', 'DomainCrudController');
    Route::get('image-search/{checksum}/download', [ImageSearchCrudController::class, 'downloadImage'])->name('image-search.download');
    Route::crud('image-search', 'ImageSearchCrudController');


    Route::crud('news', 'NewsCrudController');
    Route::crud('search-with-object', 'SearchWithObjectCrudController');
    Route::crud('review', 'ReviewCrudController');
    Route::crud('review-source', 'ReviewSourceCrudController');
    Route::crud('humor-site', 'HumorSiteCrudController');
    Route::crud('google-search-result', 'GoogleSearchResultCrudController');
}); // this should be the absolute last line of this file