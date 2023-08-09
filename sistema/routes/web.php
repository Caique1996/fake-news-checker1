<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    $o= new \App\Libs\GoogleCustomSearch();
    $searches=\App\Models\Search::where("id",5)->first();
    dd($o->search($searches->search_term));
    return redirect('admin/login');
});
