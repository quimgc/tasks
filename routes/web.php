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

use App\Mail\customMail;
use App\Mail\Hello;
use App\Mail\HelloUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'auth'], function () {


    //TIMELINE
    Route::get('tasks/timeline', 'TasksTimelineController@index');



    //JS  + AJAX/AXIOS
    Route::view('/tokens', 'tokens');
    Route::view('/tasks', 'tasks');


    Route::get('tasks_php', 'TaskController@index');
    Route::get('tasks_php/create', 'TaskController@create');
    Route::get('tasks_php/edit/{task}', 'TaskController@edit');
    Route::get('tasks_php/{task}', 'TaskController@show');
    Route::post('tasks_php/{task}', 'TaskController@show');
    Route::post('tasks_php', 'TaskController@store');
    Route::put('tasks_php/{task}', 'TaskController@update');
    Route::delete('tasks_php/{task}', 'TaskController@destroy');

    //email
//    Route::get('/email', function () {
//        return view('/emails/email');
//    });
    Route::get('/email', 'EmailController@index');
    Route::post('/email', 'EmailController@store');

    Route::post('/sendmail', function (Request $request) {
        $sendTo = $request->emailto;
        $subject = $request->subject;
        $body = $request->body;
        $mail = new customMail($subject, $body);
        Mail::to($sendTo)->send($mail);
    });

    //Els api s'ha de passar a api.php i refactoritzar tests per a que estiguin autenticats, per autenticar:    $this->actingAs($user,'api');

    //proves
    Route::get('/test_send_email', function () {
        $user = User::find(1);
        $hello = new Hello($user);
        Mail::to($user)->send($hello);
    });

    Route::get('/test_send_email2', function () {
        $user = User::find(1);
        $hello = new HelloUser($user);
        Mail::to($user)->send($hello);
    });
});
