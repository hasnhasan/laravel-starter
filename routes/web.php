<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return view('welcome');
});

Route::prefix('admin')->group(function() {
    Auth::routes();
    Route::namespace('Backend')->group(function() {

        Route::get('/', 'DashboardController@index')->name('home');

        Route::get('/media-manager', 'MediaController@index')->name('media-manager.list');
        Route::get('/media-manager/popup', 'MediaController@popup')->name('media-manager.popup');
        Route::any('/media-manager/action', 'MediaController@mediaActions')->name('media-manager.action');


        Route::any('/categories', 'CategoryController@index')->name('category.list');


        Route::get('/articles/{module?}', 'ArticleController@index')->name('article.list');
        Route::get('/article/create', 'ArticleController@createOrUpdateForm')->name('article.create');
        Route::get('/article/{id?}', 'ArticleController@createOrUpdateForm')->name('article.update');
        Route::post('/article/{idOrCreate}', 'ArticleController@createOrUpdate');
        Route::delete('/articles', 'ArticleController@delete')->name('article.delete');



    });
});
