<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', function () {
    return view('welcome');

});


Route::post('/userRegister', 'ClientUserController@store');
Route::post('/userLogin', 'ClientUserController@login');
Route::post('/userAdd', 'ClientUserController@add');
Route::post('/userFind', 'ClientUserController@find');
Route::get('/userTest', 'ClientUserController@test');
Route::post('/userSetLocation', 'ClientUserController@setLocation');

Route::get('/userGetLocation', 'ClientUserController@getLocation');
Route::post('/lockUser', 'ClientUserController@lockUser');
Route::post('/trackUser', 'ClientUserController@trackUser');
//Route::

/**
 * 测试
 */
Route::group(['prefix' => 'admin'], function () {
    Route::get('users', ['middleware' => 'test_old', function ()    {
    }]);
});



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');

    Route::get('/about', 'SiteController@about');
    Route::get('/user_manage', 'SiteController@managerUser');
    Route::get('/show_map', 'SiteController@showMap');
    Route::get('/statistic_data', 'SiteController@statisticData');
    //    防止 出现为定义的  $errors报错
//    Route::get('/articles','ArticleController@index');
//    Route::get('/articles/create','ArticleController@create');
//    Route::get('/articles/{id}','ArticleController@show');
//    Route::post('/articles','ArticleController@store');
//    Route::get('/articles/{id}/edit','ArticleController@edit');
//    Route::post('/articles/{id}/edit','ArticleController@update');
    Route::resource('articles', 'ArticleController');

});

