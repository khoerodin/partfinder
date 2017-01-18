<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    Route::get('/', 'PartNumberController@index');

    Route::get('search', 'PartNumberController@search');
    Route::get('excel', 'PartNumberController@excel');

    Route::get('me', 'MeController@index');
    Route::post('me/update', 'MeController@update');

    Route::get('import', 'ImportController@index');
    Route::post('import/upload', 'ImportController@upload')->name('upload');
    Route::get('import/read/{file}', 'ImportController@read');
    Route::get('import/import-part-number/{file}', 'ImportController@importPartNumber');

    Route::resource('users', 'UserController');
    Route::resource('users.roles', 'UserRoleController');
    Route::post('users/{user}/roles', 'UserRoleController@update');
    Route::resource('users.permissions', 'UserPermissionController');
    Route::post('users/{user}/permissions', 'UserPermissionController@update');

    Route::resource('roles', 'RoleController');
    Route::get('roles/{role}/permissions', 'RolePermissionController@index');
    Route::post('roles/{role}/permissions', 'RolePermissionController@update');

});
