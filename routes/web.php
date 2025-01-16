<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\RelevancyController;
use App\Http\Controllers\ScrapeController;
use App\Models\File;
use Illuminate\Support\Facades\Route;



//user accessible pages
Route::view('/','search');
Route::get('search', [FileController::class, 'search']);
Route::view('summary', 'summary');
Route::view('result/{id}','result-summary');
Route::view('result/{id}/raw','result-raw');
Route::get('/personality', function () {
    return view('personality');
});
Route::post('/personality', function () {
    if (request("name")) {
        session([
            "name" => request("name"),
            "age" => (int)request("age"),
            "location" => request("location"),
            "interests" => request("interests"),
            "profession" => request("profession"),
            "education" => request("education"),
            "preferred_topics" => request("preferred_topics"),
        ]);
    }
    return redirect('personality');
});



//Page to scrape a topic from open.overheid.nl (does not return anything and will time out the web server request, however it keeps alive until finished)
Route::view('/scrape','scrape-ui');


Route::get('/calculate-relevancy', [RelevancyController::class, 'calculateRelevancy']);


// "API" related endpoints
Route::get('/load-pdf', [FileController::class, 'showPdf']);
Route::get('/scrape-data', [FileController::class, 'processScrapedData']);
Route::get('/clean-pdf', [FileController::class, 'regeneratePdfData']);
Route::get('/clean-metadata', [FileController::class, 'regenerateMetaData']);
Route::post('/api/stream-personal-summary', function (\Illuminate\Http\Request $request, \App\Services\OpenAIService $service) {
    $text = $request->input('text', '');

    // Set headers for streaming response
    return response()->stream(function () use ($service, $text) {
        $service->summarizeTextPersonality($text);
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
});
Route::get('/api/stream-results-summary', function (\Illuminate\Http\Request $request, \App\Services\OpenAIService $service) {

    $query = $request->input('q');
    $text = "The user searched for: $query\n Dnd got the following Results:";

    // Perform the search
    $files = File::with(['people', 'keywords'])
        ->where('title', 'like', "%$query%")
        ->orWhere('summary', 'like', "%$query%")
        ->orWhere('short_desc', 'like', "%$query%")
        ->orWhereHas('people', function ($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        })
        ->orWhereHas('keywords', function ($q) use ($query) {
            $q->where('word', 'like', "%$query%");
        })
        ->orderBy('original_date', 'desc')
        ->take(4)
        ->get();
    $i = 1;
    foreach ($files as $file) {
        $text .= "Result: $i:\n";
        $text .= "Title: $file->title \n";
        $text .= "Description: $file->short_desc \n";
        $text .= "Date: $file->original_date \n";
        $i++;

    }

    // Set headers for streaming response
    return response()->stream(function () use ($service, $text) {
        $service->summarizeTextGeneral($text);
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
});
