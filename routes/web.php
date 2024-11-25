<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

//Route::get('/', function () {
//    return view('search');
//});

Route::post('/summarize-pdf', [PdfController::class, 'summarize']);
