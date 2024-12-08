<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelevancyController extends Controller
{
    public function calculateRelevancy()
    {
        // Retrieve all files from the database
        $files = File::all();
        ini_set('max_execution_time', 300);

        // Prepare a collection of words from file titles
        $fileWordMap = $files->mapWithKeys(function ($file) {
            return [$file->id => $this->extractWords($file->title)];
        });

        foreach ($files as $relevant_file_id) {
            foreach ($files as $relevant_to_file_id) {
                // Skip if comparing the same file
                if ($relevant_file_id->id === $relevant_to_file_id->id) {
                    continue;
                }

                $titleWords1 = $fileWordMap[$relevant_file_id->id];
                $titleWords2 = $fileWordMap[$relevant_to_file_id->id];

                // Calculate the relevancy score based on common words
                $commonWords = array_intersect($titleWords1, $titleWords2);
                $totalWords = count(array_unique(array_merge($titleWords1, $titleWords2)));

                if ($totalWords === 0) {
                    continue;
                }

                // Relevancy score as a percentage
                $relevancyScore = (count($commonWords) / $totalWords) * 100;

                if ($relevancyScore < 20) { // Skip low-relevancy pairs, e.g., less than 20%
                    continue;
                }

                // Store the relevancy score in the database
                DB::table('file_relevancy')->updateOrInsert(
                    ['relevant_file_id' => $relevant_file_id->id, 'relevant_to_file_id' => $relevant_to_file_id->id],
                    [
                        'relevancy' => min(round($relevancyScore, 2),100), // Round to 2 decimal places and clamp it to 100
                        'matched_words' => json_encode($commonWords),
                    ]
                );
            }
        }

        return response()->json(['message' => 'Relevancy scores calculated successfully.']);
    }


    private function extractWords($title)
    {
        // Convert title to lowercase, remove special characters, and split into words
        return array_filter(
            preg_split('/\s+/', strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $title)))
        );
    }
}
