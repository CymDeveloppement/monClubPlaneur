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
use Illuminate\Support\Facades\Gate;

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::post('/flightDay', 'HomeController@addFlightDay')->name('flightDay');
Route::post('/flightDay/delete', 'HomeController@deleteFlightDay')->name('deleteFlightDay');
Route::get('/flightDayBoard', 'HomeController@getFlightDay')->name('flightDayBoard');
Route::any('/saisie', 'HomeController@saisie')->name('saisie');
Route::get('/saisie/deleteLast', 'HomeController@deleteLastTransaction')->name('deleteLast');
Route::any('/planches', 'HomeController@planches')->name('planches');
Route::any('/carnet', 'HomeController@carnet')->name('carnet');
Route::post('/pay/add', 'HomeController@addPay')->name('addPay');
Route::get('/addFlight', 'HomeController@addFlight')->name('addFlight');
Route::post('/alertRead', 'HomeController@alertRead')->name('alertRead');

	Route::post('/admin/addUser', 'admin@addUser')->name('addUser')->middleware('can:admin');
	Route::get('/usersList', 'admin@usersList')->name('usersList')->middleware('can:admin');
	Route::get('/validTransactions', 'admin@getValidTransactions')->name('validTransactions')->middleware('can:admin');
	Route::post('/validTransactionPost', 'admin@ValidTransactions')->name('validTransactionPost')->middleware('can:admin');
	Route::post('/validNewTrDate', 'admin@validNewTrDate')->name('validNewTrDate')->middleware('can:admin');
	Route::get('/updateSolde', 'admin@updateSolde')->name('updateSolde')->middleware('can:admin');
	Route::post('/validNewAdminFlight', 'admin@validNewAdminFlight')->name('validNewAdminFlight')->middleware('can:admin');
	Route::get('/route', 'admin@flightList')->name('aircraftFlights')->middleware('can:admin');
	Route::get('/vol', 'admin@flightList')->name('pilotFlights')->middleware('can:admin');
	Route::get('/controlData', 'admin@updateAndControlData')->name('updateAndControlData')->middleware('can:admin');


