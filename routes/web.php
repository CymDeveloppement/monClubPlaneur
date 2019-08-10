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


Auth::routes();
Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/flightDay', 'HomeController@addFlightDay')->name('flightDay');
Route::post('/flightDay/delete', 'HomeController@deleteFlightDay')->name('deleteFlightDay');
Route::get('/flightDayBoard', 'HomeController@getFlightDay')->name('flightDayBoard');
Route::any('/saisie', 'HomeController@saisie')->name('saisie');
Route::get('/saisie/deleteLast', 'HomeController@deleteLastTransaction')->name('deleteLast');
Route::post('/admin/addUser', 'admin@addUser')->name('addUser');
Route::get('/usersList', 'admin@usersList')->name('usersList');
Route::get('/validTransactions', 'admin@getValidTransactions')->name('validTransactions');
Route::post('/validTransactionPost', 'admin@ValidTransactions')->name('validTransactionPost');
Route::post('/validNewTrDate', 'admin@validNewTrDate')->name('validNewTrDate');
Route::get('/updateSolde', 'admin@updateSolde')->name('updateSolde');
Route::post('/validNewAdminFlight', 'admin@validNewAdminFlight')->name('validNewAdminFlight');
Route::get('/route', 'admin@flightList')->name('aircraftFlights');
Route::get('/vol', 'admin@flightList')->name('pilotFlights');
Route::any('/planches', 'HomeController@planches')->name('planches');
Route::any('/carnet', 'HomeController@carnet')->name('carnet');

Route::post('/pay/add', 'HomeController@addPay')->name('addPay');
