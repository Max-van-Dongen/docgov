<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ScrapeController;
use Illuminate\Support\Facades\Route;


//use POSTMAN to upload an array of pdfs using POST form-data pdf[] to this endpoint, it will handle the rest itself.
Route::post('upload-pdf', [FileController::class, 'uploadAndProcess']);


Route::view('/','search');
Route::get('search', [FileController::class, 'search']);
Route::view('result/{id}','result-summary');
Route::view('result/{id}/raw','result-raw');

Route::get('/scrape-data', [FileController::class, 'processScrapedData']);

Route::get('/load-pdf', [FileController::class, 'showPdf']);
Route::view('/scrape','scrape-ui');
