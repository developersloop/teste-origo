<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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

Route::prefix('link')->group(function () {
    Route::get('/', 'LinksController@links')->name('links');
    Route::get('/disabled-for-test/{id}', 'LinksController@disableExpirateForTestCypress')->name('links');
    Route::post('/', 'LinksController@shortLink')->name('short-link');
    Route::put('/{id}', 'LinksController@updateLink')->name('update-link');
    Route::delete('/{id?}/{link?}/{short_link?}', 'LinksController@deleteLink')->name('delete-link');
    Route::get('/disable/{id}', 'LinksController@disable')->name('link-disable');
});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
