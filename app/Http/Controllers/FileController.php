<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Keyword;
use App\Models\Person;
use App\Services\PdfService;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    protected $pdfService;
    protected $openAIService;

    public function __construct(PdfService $pdfService, OpenAIService $openAIService)
    {
        $this->pdfService = $pdfService;
        $this->openAIService = $openAIService;
    }


    public function search(Request $request)
    {
        $query = $request->input('query');

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
            ->get();

        // Transform the results into an array
        $results = $files->map(function ($file) {
            return [
                'id' => $file->id,
                'title' => $file->title,
                'short_desc' => $file->short_desc,
                'location' => $file->location,
                'people' => $file->people->pluck('name')->take(7),
                'keywords' => $file->keywords->pluck('word')->take(7),
                "type_document" => $file->type_document,
                "type_category" => $file->type_category,
                'created_at' => $file->original_date,
            ];
        });

        // Pass results to the view
        return view('results', compact('results'));
    }



    public function processScrapedData(): JsonResponse
    {
        try {
            $scrapedData = ScrapeController::scrapeData();// $this->scrapeVlaardingen()->getData(true); // Call scrapeVlaardingen
            $processedFiles = [];

            foreach ($scrapedData as $item) {
                $pdfUrl = $item['pdf_url'];

                if (File::where('location', $pdfUrl)->exists()) {
                    Log::info('PDF URL already processed: ' . $pdfUrl);
                    continue;
                }

                // Fetch PDF content
                $pdfResponse = Http::get($pdfUrl);

                if ($pdfResponse->failed()) {
                    Log::error('Failed to fetch PDF from ' . $pdfUrl, ['status' => $pdfResponse->status()]);
                    continue;
                }

                $tempPdfPath = storage_path('app/temp_' . time() . '.pdf');
                file_put_contents($tempPdfPath, $pdfResponse->body());

                // Extract text from PDF
                $text = $this->pdfService->extractText($tempPdfPath);
                if ($text == "") {
                    continue;
                }
                $text_full = $text;
                $text = $this->truncateTextToTokenLimit($text, 10000);

                // Use AI service to process data
                $summary = $this->openAIService->summarizeText($text);
                $title = $this->openAIService->generateTitle($text);
                $desc = $this->openAIService->generateShortDescription($text);
                $peopleList = $this->openAIService->extractPeople($text);
                $keywordsList = $this->openAIService->extractKeywords($text);

                // Save file record
                $fileRecord = File::create([
                    'location' => $pdfUrl,
                    'summary' => $summary,
                    'title' => $title,
                    'short_desc' => $desc,
                    'type_document' => $item['type_document'] ?? null,
                    'type_category' => $item['type_category'] ?? null,
                    'original_date' => isset($item['original_date']) ? date('Y-m-d H:i:s', strtotime($item['original_date'])) : null,
                    'full_text' => $text_full,
                ]);

                // Process people
                foreach ($peopleList as $peopleText) {
                    $names = preg_split('/\R/', trim($peopleText), -1, PREG_SPLIT_NO_EMPTY);

                    foreach ($names as $name) {
                        $cleanedName = preg_replace('/^\d+\.\s*/', '', trim($name));
                        $cleanedName = strtolower($cleanedName);
                        if (!empty($cleanedName)  && strlen($cleanedName) <= 20) {
                            $person = Person::firstOrCreate(['name' => $cleanedName]);
                            $fileRecord->people()->attach($person->id);
                        }
                    }
                }

                // Process keywords
                foreach ($keywordsList as $keywordsText) {
                    $keywords = preg_split('/\R/', trim($keywordsText), -1, PREG_SPLIT_NO_EMPTY);

                    foreach ($keywords as $keyword) {
                        $cleanedKeyword = preg_replace('/^\d+\.\s*/', '', trim($keyword));
                        $cleanedKeyword = strtolower($cleanedKeyword);
                        if (!empty($cleanedKeyword) && strlen($cleanedKeyword) <= 20) {
                            $keywordModel = Keyword::firstOrCreate(['word' => $cleanedKeyword]);
                            $fileRecord->keywords()->attach($keywordModel->id);
                        }
                    }
                }

                $processedFiles[] = $fileRecord;

                // Clean up temp file
                @unlink($tempPdfPath);
            }

            return response()->json([
                'message' => 'Scraped data processed successfully!',
                'data' => $processedFiles,
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing scraped data: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    protected function truncateTextToTokenLimit($text, $tokenLimit)
    {
        // Approximate tokens per character for English (1 token ≈ 4 characters)
        $approxCharsPerToken = 4;

        // Calculate the character limit
        $charLimit = $tokenLimit * $approxCharsPerToken;

        // Truncate the text to the character limit
        if (strlen($text) > $charLimit) {
            $text = substr($text, 0, $charLimit);
        }

        return $text;
    }

    public function showPdf(Request $request)
    {
        // URL of the PDF
        $url = $request->input('url'); // Example: 'https://example.com/sample.pdf'

        // Validate that URL is provided
        if (!$url) {
            return response()->json(['error' => 'URL is required'], 400);
        }

        try {
            // Fetch PDF content
            $response = Http::withoutVerifying()->get($url);

            // Check if the response is successful
            if ($response->failed()) {
                return response()->json(['error' => 'Failed to fetch the PDF'], 400);
            }

            // Return the PDF content as a response
            return response($response->body(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="document.pdf"',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function regeneratePdfData()
    {
        $files = File::whereNotNull('full_text')->where('full_text', '!=', '')->orderBy('updated_at','asc')->where('id',282)->get();
        $totalFiles = $files->count();
        if ($totalFiles === 0) {
            echo "No files found.\n";
            return;
        }

        // Track start time
        $startTime = microtime(true);
        $processedCount = 0;

        foreach ($files as $file) {
            // Calculate how many have already been processed (i.e., before this file)
            $processedSoFar = $processedCount;

            // Don’t do average time math if it’s the first iteration
            if ($processedSoFar > 0) {
                $elapsed = microtime(true) - $startTime;
                $avgTimePerFile = $elapsed / $processedSoFar;
                $remaining = $totalFiles - $processedSoFar;
                $estimatedTimeRemaining = $remaining * $avgTimePerFile;
            } else {
                $estimatedTimeRemaining = 0;
            }

            // Output progress info
            echo "Processing file "
                . ($processedCount + 1) . " / $totalFiles ... ";
            echo "Estimated time remaining: "
                . gmdate("H:i:s", $estimatedTimeRemaining) . "\n";

            try {

                $text = $file->full_text;
                $text = $this->truncateTextToTokenLimit($text, 8000);
                if (!$text) {
                    Log::warning('File with ID ' . $file->id . ' has no full_text.');
                    continue;
                }

                // Generate metadata using OpenAI service
                $summary = $this->openAIService->summarizeText($text);
                $title = $this->openAIService->generateTitle($text);
                $desc = $this->openAIService->generateShortDescription($text);

                // Save updates back to the database
                echo "Title: " . $title . "\n";
                echo "Summary: " . $summary . "\n";
                echo "Desc: " . $desc . "\n";
                $file->summary = $summary;
                $file->title = $title;
                $file->short_desc = $desc;
                $file->save();

            } catch (\Exception $e) {
                Log::error('Error processing File ID ' . $file->id, ['message' => $e->getMessage()]);
            }

            $processedCount++;
        }
    }

    public function regenerateMetaData()
    {
        echo "Truncating Person and Keyword tables...\n";

        // Truncate related tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Person::truncate();
        Keyword::truncate();
        DB::statement('TRUNCATE `pdf_people`;');
        DB::statement('TRUNCATE `pdf_keywords`;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $files = File::whereNotNull('full_text')->where('full_text', '!=', '')->get();
        $totalFiles = $files->count();
        if ($totalFiles === 0) {
            echo "No files found.\n";
            return;
        }

        $startTime = microtime(true);
        $processedCount = 0;

        foreach ($files as $file) {
            // Calculate progress details
            $processedSoFar = $processedCount;

            if ($processedSoFar > 0) {
                $elapsed = microtime(true) - $startTime;
                $avgTimePerFile = $elapsed / $processedSoFar;
                $remaining = $totalFiles - $processedSoFar;
                $estimatedTimeRemaining = $remaining * $avgTimePerFile;
            } else {
                $estimatedTimeRemaining = 0;
            }

            echo "Processing file "
                . ($processedCount + 1) . " / $totalFiles ... ";
            echo "Estimated time remaining: "
                . gmdate("H:i:s", $estimatedTimeRemaining) . "\n";
            try {

                $text = $file->full_text;
                if (!$text) {
                    Log::warning('File with ID ' . $file->id . ' has no full_text.');
                    continue;
                }

                $peopleList = $this->openAIService->extractPeople($text);
                $keywordsList = $this->openAIService->extractKeywords($text);

                // Process the list of people
                foreach ($peopleList as $cleanedName) {
                    $cleanedName = strtolower(trim($cleanedName));
                    if (!empty($cleanedName) && strlen($cleanedName) <= 20) {
                        echo "Found Person: {$cleanedName}\n";
                        $person = Person::firstOrCreate(['name' => $cleanedName]);
                        $file->people()->attach($person->id);
                    }
                }

                // Process the list of keywords
                foreach ($keywordsList as $cleanedKeyword) {
                    $cleanedKeyword = strtolower(trim($cleanedKeyword));
                    if (!empty($cleanedKeyword) && strlen($cleanedKeyword) <= 20) {
                        echo "Found Keyword: {$cleanedKeyword}\n";
                        $keywordModel = Keyword::firstOrCreate(['word' => $cleanedKeyword]);
                        $file->keywords()->attach($keywordModel->id);
                    }
                }

            } catch (\Exception $e) {
                Log::error('Error processing File ID ' . $file->id, ['message' => $e->getMessage()]);
            }

            $processedCount++;
        }
    }



}

