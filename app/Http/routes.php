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
Route::group(['prefix' => 'api/v1', 'middleware' => 'cors'], function () {
    Route::get('topics/{authUser}', 'MobileTopicController@index');
    Route::get('create', 'MobileTopicController@create');
    Route::post('store', 'MobileTopicController@store');
    Route::post('add_connection', 'MobileTopicController@addConnection');
    Route::post('accept_connection', 'MobileTopicController@acceptConnection');
    Route::get('get_connections', 'MobileTopicController@getConnections');
    Route::get('/topic/{id}/{authEmail}/inspires', 'MobileTopicController@inspiresUser');
    Route::get('/topic/{id}/{authEmail}/uninspire', 'MobileTopicController@uninspiresUser');
    Route::get('/comments/{id}', 'MobileTopicController@getComments');
    Route::post('post_comment/{id}/{authEmail}', 'MobileTopicController@postComment');
    Route::get('/get_user_by/{name}/{AuthUserEmail}', 'MobileTopicController@getUserByName');
    Route::get('get_user/{id}/{auth}', 'MobileTopicController@getUser');
    Route::get('get_user_byid/{id}', 'MobileTopicController@getUserById');
    Route::get('/get_topic/{id}/{AuthEmail}', 'MobileTopicController@getTopic');
    Route::get('search_by/{input}', 'MobileTopicController@searchBy');
    Route::get('get_categories/{name}', 'MobileTopicController@getCategories');
});


Route::group(['prefix' => 'api/v1', 'middleware' => 'cors'], function()
{
    Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
    Route::post('authenticate', 'AuthenticateController@authenticate');
    Route::get('authenticate/user', 'AuthenticateController@getAuthenticatedUser');
});

// Event Logs Controller
Route::group(['prefix' => 'api/v1', 'middleware' => 'cors'], function()
{
    Route::post('logsViewed/{username}', 'EventsController@logsViewed');
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
    //
});

Route::group(['middleware' => 'web'], function () {
    Route::get('topic_of_the_day/{slug}',['as' => 'topicPage', 'uses' => 'PostController@topicPage', 'middleware' => ['auth']]);
    Route::auth();
    Route::get('/user/{username}/profile', ['as' => 'profile', 'uses' => 'PagesController@userProfile', 'middleware' => ['auth']]);
    Route::get('/', 'PagesController@index');
    Route::get('/home', 'HomeController@index');
    Route::post('/create_topic', ['as' => 'create_topic', 'uses' => 'PostController@createTopic', 'middleware' => ['auth']]);
    Route::post('post_comment', ["as" => 'postComment', 'uses' => 'PostController@postComment', 'middleware' => ['auth']]);
    Route::get('/topic/{id}/inspires', ['as' => 'inspiresUser', 'uses' => 'PostController@inspiresUser', 'middleware' => 'auth']);
    Route::get('/topic/{id}/uninspire', ['as' => 'uninspiresUser', 'uses' => 'PostController@uninspiresUser' , 'middleware' => ['auth']]);
    Route::get('/admin/dashboard', ['as' => 'adminDashboard', 'uses' => 'AdminController@dashboard', 'middleware' => ['auth']]);
    Route::post('/create_topic_title', ['as' => 'create_topic_title', 'uses' => 'AdminController@createTopicTitle', 'middleware' => ['auth']]);
    Route::post('/hash_tags', 'TagsController@getTags');
    Route::get('/add_connection', ['as' => 'add_connection', 'uses' => 'UserProfileController@addConnection']);
    Route::get('/accept_connection', ['as' => 'accept_connection', 'uses' => 'UserProfileController@acceptConnection']);
    Route::get('/requests', ['as' => 'requests', 'uses' => 'UserProfileController@connectionRequests']);
    Route::get('/connections', ['as' => 'connections', 'uses' => 'UserProfileController@connections']);
});
