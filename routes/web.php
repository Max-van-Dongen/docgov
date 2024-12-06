<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;


//use POSTMAN to upload an array of pdfs using POST form-data pdf[] to this endpoint, it will handle the rest itself.
Route::post('upload-pdf', [FileController::class, 'uploadAndProcess']);


Route::view('/','search');
Route::get('search', [FileController::class, 'search']);
Route::view('result/{id}','result');
