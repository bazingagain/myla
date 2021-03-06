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
Route::post('/userAgree', 'ClientUserController@agree');
//用户登录时同步好友表及头像
Route::post('/userSyncRelationTable', 'ClientUserController@userSyncRelationTable');

//前台用户功能
Route::post('/userFind', 'ClientUserController@find');
Route::post('/userDetail', 'ClientUserController@detail');
Route::post('/modifyUserInfo', 'ClientUserController@modifyUserInfo');
Route::post('/saveUserInfo', 'ClientUserController@saveUserInfo');
Route::post('/deleteUserInfo', 'ClientUserController@deleteUserInfo');
Route::post('/createUser', 'ClientUserController@createUser');
Route::post('/saveCreateUser', 'ClientUserController@saveCreateUser');

//后台用户功能
Route::post('/htuserFind', 'UserController@find');
Route::post('/htuserDetail', 'UserController@detail');
Route::post('/htmodifyUserInfo', 'UserController@modifyUserInfo');
Route::post('/htsaveUserInfo', 'UserController@saveUserInfo');
Route::post('/htdeleteUserInfo', 'UserController@deleteUserInfo');
Route::post('/htcreateUser', 'UserController@createUser');
Route::post('/htsaveCreateUser', 'UserController@saveCreateUser');



Route::get('/feedbackAll', 'ClientUserController@feedbackAll');

Route::post('/feedbackDetail', 'ClientUserController@feedbackDetail');
Route::post('/handleFeedback', 'FeedbackController@handleFeedback');
Route::post('/sendFeedbackResponse', 'FeedbackController@sendFeedbackResponse');
Route::post('/deleteFeedback', 'FeedbackController@deleteFeedback');



Route::get('/userTest', 'ClientUserController@test');
Route::post('/userSetLocation', 'ClientUserController@setLocation');

Route::get('/userGetLocation', 'ClientUserController@getLocation');
Route::post('/lockUser', 'ClientUserController@lockUser');
Route::post('/trackUser', 'ClientUserController@trackUser');
Route::get('/updateUserLoc', 'ClientUserController@updateUserLoc');
//共享URL
Route::get('/showSingleUserLoc', 'ClientUserController@showSingleUserLoc');


Route::post('/userModifyPassword', 'ClientUserController@modifyPassword');
Route::post('/userFeedback', 'ClientUserController@feedback');

//共享位置
Route::post('/userShareLocation', 'ClientUserController@shareLocation');
Route::post('/getContactLocation', 'ClientUserController@getContactLocation');
Route::post('/agreeShareLocation', 'ClientUserController@agreeShareLocation');
Route::post('/closeShareLocation', 'ClientUserController@closeShareLocation');

Route::post('/showAllShareUser', 'ClientUserController@showAllShareUser');



Route::post('/sendPush', 'ClientUserController@sendPush');

Route::group(['prefix' => 'userProfile'], function () {
    Route::post('setSignature','ClientUserController@setSignature');
    Route::post('setNickname','ClientUserController@setNickname');
    Route::post('setIcon','ClientUserController@setIcon');
    Route::post('setSex','ClientUserController@setSex');
    Route::post('setAddress','ClientUserController@setAddress');
    Route::post('updateTempshare','ClientUserController@updateTempshare');
});

Route::group(['prefix' => 'getUserNumInfo'], function () {
    Route::get('year', 'StatisticController@getUserNumInfoYear');
    Route::get('thirtyday', 'StatisticController@getUserNumInfoThirthday');
    Route::get('sevenday', 'StatisticController@getUserNumInfoSevenday');
});

Route::group(['prefix' => 'getMessageInfo'], function () {
    Route::get('year', 'StatisticController@getMessageInfoYear');
    Route::get('thirtyday', 'StatisticController@getMessageInfoThirthday');
    Route::get('sevenday', 'StatisticController@getMessageInfoSevenday');
});

Route::get('getUserAddressInfo', 'StatisticController@getUserAddressInfo');
Route::get('getUserSexInfo', 'StatisticController@getUserSexInfo');

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

Route::get('/showSignalUserLoc/{sharename}', 'SiteController@showSignalUserLoc');

Route::group(['middleware' => ['web']], function () {
});


Route::group(['middleware' => 'web'], function () {
    Route::auth();
    Route::get('/showTrack', 'SiteController@showTrack');
    Route::get('/home', 'HomeController@index');

    Route::get('/about', 'SiteController@about');
    Route::get('/houtai_manager', 'SiteController@houtaiUser');
    Route::get('/user_manage', 'SiteController@managerUser');
    Route::get('/show_map', 'SiteController@showMap');
    Route::get('/statistic_data', 'SiteController@statisticData');
    Route::get('/show_feedback', 'SiteController@showFeedback');
    Route::get('/func_test', 'SiteController@showFunctest');
    //    防止 出现为定义的  $errors报错
//    Route::get('/articles','ArticleController@index');
//    Route::get('/articles/create','ArticleController@create');
//    Route::get('/articles/{id}','ArticleController@show');
//    Route::post('/articles','ArticleController@store');
//    Route::get('/articles/{id}/edit','ArticleController@edit');
//    Route::post('/articles/{id}/edit','ArticleController@update');
    Route::resource('articles', 'ArticleController');

});

