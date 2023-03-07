<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', 'AuthController@login');
Route::post('/logout', 'AuthController@logout');
Route::post('/refresh', 'AuthController@refresh');
Route::post('/me', 'AuthController@me');
Route::post('/register', 'AuthController@register');

Route::post('/schedules', 'ScheduleController@index');
Route::post('/schedule', 'ScheduleController@store');
Route::post('/schedule/edit', 'ScheduleController@update');
Route::post('/schedule/delete', 'ScheduleController@destroy');