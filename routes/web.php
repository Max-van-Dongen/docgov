<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::view('/','search');
//Route::view('results','results');

//Route::post('summarize-pdf', [PdfController::class, 'summarize']);
Route::post('upload-pdf', [FileController::class, 'uploadAndProcess']);
Route::get('search', [FileController::class, 'search']);
