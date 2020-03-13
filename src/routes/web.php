<?php

Route::get('/crud', "TableController@index")->name('crud.table-view');
Route::post('/crud-generate', "TableController@generate")->name('crud.generate');
