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

Route::get('decks/editDecks', 'DecksController@editDecks');
Route::get('decks/getDecks', 'DecksController@getDecks');
Route::resource('decks', 'DecksController');
Route::resource('cards', 'CardsController');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
