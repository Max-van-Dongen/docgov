<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Keyword;
use App\Models\Person;
use App\Services\PdfService;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'pdf' => 'required|file|mimes:pdf|max:20480',
        ]);

        try {
            $file = $request->file('pdf');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            $text = $this->pdfService->extractText($file->getPathname());
            $summary = $this->openAIService->summarizeText($text);
            $title = $this->openAIService->generateTitle($text);
            $peopleList = $this->openAIService->extractPeople($text);
            $keywordsList = $this->openAIService->extractKeywords($text);

            // Save File Record
            $fileRecord = File::create([
                'location' => $filePath,
                'summary' => $summary,
                'title' => $title,
            ]);

            // Process People
            foreach ($peopleList as $peopleText) {
                // Split by line breaks and process each line
                $names = preg_split('/\R/', trim($peopleText), -1, PREG_SPLIT_NO_EMPTY);

                foreach ($names as $name) {
                    // Remove numbering like "1. ", "2. ", etc.
                    $cleanedName = preg_replace('/^\d+\.\s*/', '', trim($name));

                    // Only add if the name is not empty
                    if (!empty($cleanedName)) {
                        $person = Person::firstOrCreate(['name' => $cleanedName]);
                        $fileRecord->people()->attach($person->id);
                    }
                }
            }

            // Process Keywords
            foreach ($keywordsList as $keywordsText) {
                // Split by line breaks and process each line
                $keywords = preg_split('/\R/', trim($keywordsText), -1, PREG_SPLIT_NO_EMPTY);

                foreach ($keywords as $keyword) {
                    // Remove numbering like "1. ", "2. ", etc.
                    $cleanedKeyword = preg_replace('/^\d+\.\s*/', '', trim($keyword));

                    // Only add if the keyword is not empty
                    if (!empty($cleanedKeyword)) {
                        $keywordModel = Keyword::firstOrCreate(['word' => $cleanedKeyword]);
                        $fileRecord->keywords()->attach($keywordModel->id);
                    }
                }
            }

            return response()->json([
                'message' => 'File processed successfully!',
                'data' => $fileRecord,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}

