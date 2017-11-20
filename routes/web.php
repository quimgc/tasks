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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {
    //    Route::get('/link1', function ()    {
//        // Uses Auth Middleware
//    });

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_routes
//
//    Route::get('/tasks',function(){
//
//       return view('tasks');
//
//    });


    Route::view('/tasks','tasks');

    Route::view('/tokens','tokens');

    Route::get('/tasks','TaskController@index');

    Route::post('/tasks','TaskController@store');
    Route::delete('/tasks','TaskController@destroy');

//Els api s'ha de passar a api.php i refactoritzar tests per a que estiguin autenticats
    Route::get('api/tasks','ApiTaskController@index');

    Route::post('api/v1/tasks','ApiTaskController@store');
    Route::delete('api/v1/tasks/{task}','ApiTaskController@destroy');

    Route::put('api/v1/tasks/{task}','ApiTaskController@update');
    Route::get('api/v1/users','ApiUserController@index');


});

