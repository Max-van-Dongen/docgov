<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\RelevancyController;
use App\Http\Controllers\ScrapeController;
use Illuminate\Support\Facades\Route;



//user accessible pages
Route::view('/','search');
Route::get('search', [FileController::class, 'search']);
Route::view('result/{id}','result-summary');
Route::view('result/{id}/raw','result-raw');


//Page to scrape a topic from open.overheid.nl (does not return anything and will time out the web server request, however it keeps alive until finished)
Route::view('/scrape','scrape-ui');


Route::get('/calculate-relevancy', [RelevancyController::class, 'calculateRelevancy']);


// "API" related endpoints
Route::get('/load-pdf', [FileController::class, 'showPdf']);
Route::get('/scrape-data', [FileController::class, 'processScrapedData']);
