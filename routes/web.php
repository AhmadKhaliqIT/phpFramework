<?php
namespace routes;
/* بسم الله الرحمن الرحیم */
/****************************************
 **** Sahand Framework ******************
 **** Core developers: ******************
 **** Ahmad Khaliq - Mojtaba Zadegi *****
 **** Email: Ahmad.Khaliq@yahoo.com *****
 **** Email: mojtaba.zadehgi@gmail.com **
 **** 2021  *****************************
 ***************************************/

use Core\Router\Route;


Route::get('/index','TestController@myTest')->name('myTest');
Route::get('/test1','Core\Collection\test@test')->name('test');
Route::get('/allah{id}/id',function($id){
    return 'AllahNejad ghomie '.$id;
})->name('test534');

Route::get('/blade','MjController@test_blade')->name('blade');
Route::get('/table','MjController@test_table')->name('table');
Route::get('/res','MjController@test_response_image')->name('test_response_image');
Route::get('/json','MjController@test_response_json')->name('test_response_json');

Route::get('/letters_drafts', 'LettersController@letters_drafts_list')->name('letters_drafts_list');
Route::get('/letters_drafts_list_datatable', 'LettersController@letters_drafts_list_datatable')->name('letters_drafts_list_datatable');
Route::post('/letters_drafts_save', 'LettersController@letters_drafts_save')->name('letters_drafts_save');


