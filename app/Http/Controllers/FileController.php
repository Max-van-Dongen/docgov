<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Keyword;
use App\Models\Person;
use App\Services\PdfService;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use JBBCode\Parser;

class FileController extends Controller
{
    protected $pdfService;
    protected $openAIService;

    public function __construct(PdfService $pdfService, OpenAIService $openAIService)
    {
        $this->pdfService = $pdfService;
        $this->openAIService = $openAIService;
    }

    public function uploadAndProcess(Request $request)
    {
        $request->validate([
            'pdf.*' => 'required|file|mimes:pdf|max:20480000000', // Allow multiple files
        ]);

        try {
            $uploadedFiles = $request->file('pdf');
            $processedFiles = [];

            foreach ($uploadedFiles as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads', $fileName, 'public');

                $text = $this->pdfService->extractText($file->getPathname());

                $text = $this->truncateTextToTokenLimit($text, 50000);

                $summary = $this->openAIService->summarizeText($text);
                $title = $this->openAIService->generateTitle($text);
                $desc = $this->openAIService->generateShortDescription($text);
                $peopleList = $this->openAIService->extractPeople($text);
                $keywordsList = $this->openAIService->extractKeywords($text);

                // Save File Record
                $fileRecord = File::create([
                    'location' => $filePath,
                    'summary' => $summary,
                    'title' => $title,
                    'short_desc' => $desc,
                ]);

                // Process People
                foreach ($peopleList as $peopleText) {
                    $names = preg_split('/\R/', trim($peopleText), -1, PREG_SPLIT_NO_EMPTY);

                    foreach ($names as $name) {
                        $cleanedName = preg_replace('/^\d+\.\s*/', '', trim($name));
                        $cleanedName = strtolower($cleanedName);
                        if (!empty($cleanedName)) {
                            $person = Person::firstOrCreate(['name' => $cleanedName]);
                            $fileRecord->people()->attach($person->id);
                        }
                    }
                }

                // Process Keywords
                foreach ($keywordsList as $keywordsText) {
                    $keywords = preg_split('/\R/', trim($keywordsText), -1, PREG_SPLIT_NO_EMPTY);

                    foreach ($keywords as $keyword) {
                        $cleanedKeyword = preg_replace('/^\d+\.\s*/', '', trim($keyword));
                        $cleanedKeyword = strtolower($cleanedKeyword);
                        if (!empty($cleanedKeyword)) {
                            $keywordModel = Keyword::firstOrCreate(['word' => $cleanedKeyword]);
                            $fileRecord->keywords()->attach($keywordModel->id);
                        }
                    }
                }

                $processedFiles[] = $fileRecord;
            }

            return response()->json([
                'message' => 'Files processed successfully!',
                'data' => $processedFiles,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
            ->orderBy('created_at', 'desc')
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
                'created_at' => $file->created_at,
            ];
        });

        // Pass results to the view
        return view('results', compact('results'));
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
}

