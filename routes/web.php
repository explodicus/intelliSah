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
    return redirect()->route('sessions');
});

Route::get('/home', 'HomeController@index')->name('home');
Auth::routes();

Route::get('/sessions', 'GameSessionController@index')->name('sessions');
Route::get('/session/create', 'GameSessionController@create')->name('session.create');
Route::post('/session/store', 'GameSessionController@store')->name('session.store');

Route::get('/session/{session}', 'GameSessionController@show')->name('session.show');
Route::get('/session/{session}/subscribe', 'GameSessionController@subscribe')->name('session.subscribe');
Route::get('/session/{session}/subscribers', 'GameSessionController@subscribers')->name('session.subscribers');

Route::post('/game/{session}/handle', 'GameController@handle')->name('game.handle');
