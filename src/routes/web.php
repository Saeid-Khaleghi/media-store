<?php

Route::group(['namespace'=>'Khaleghi\Media\Http\Controllers', 'middleware' => 'web'], function(){

    Route::get('media/create','MediaController@create');
    Route::post('media','MediaController@store')->name('media.store');
});

