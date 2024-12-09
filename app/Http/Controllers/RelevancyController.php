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

                // Calculate the title relevancy score
                $commonWords = array_intersect($titleWords1, $titleWords2);
                $totalWords = count(array_unique(array_merge($titleWords1, $titleWords2)));

                if ($totalWords === 0) {
                    continue;
                }

                $titleRelevancyScore = (count($commonWords) / $totalWords) * 100;

                // Calculate the date relevancy score
                $date1 = strtotime($relevant_file_id->original_date);
                $date2 = strtotime($relevant_to_file_id->original_date);
                $dateDifference = abs($date1 - $date2) / (60 * 60 * 24); // Difference in days

                // Use an exponential decay for date proximity
                $dateRelevancyScore = exp(-$dateDifference / 30) * 100; // Decay factor of 30 days

                // Combine title and date relevancy scores
                $combinedRelevancyScore = 0.7 * $titleRelevancyScore + 0.3 * $dateRelevancyScore; // Weighted combination

                if ($combinedRelevancyScore < 10) { // Skip low-relevancy pairs
                    continue;
                }

                // Store the relevancy score in the database
                DB::table('file_relevancy')->updateOrInsert(
                    ['relevant_file_id' => $relevant_file_id->id, 'relevant_to_file_id' => $relevant_to_file_id->id],
                    [
                        'relevancy' => min(round($combinedRelevancyScore, 2), 100), // Clamp score to 100
                        'matched_words' => json_encode($commonWords),
                        'date_difference_days' => $dateDifference,
                    ]
                );
            }
        }

        return response()->json(['message' => 'Relevancy scores calculated successfully.']);
    }



    private function extractWords($title)
    {
        // List of common words (stop words) to ignore
        $stopWords = [
            'en', 'aan', 'van', 'het', 'de', 'een', 'op', 'in', 'met', 'door', 'voor',
            'uit', 'over', 'onder', 'naar', 'bij', 'te', 'of', 'maar', 'om', 'tot', 'als',
//            "Vragen", "Kamervragen"
        ];

        // Convert title to lowercase, remove special characters, and split into words
        $words = array_filter(
            preg_split('/\s+/', strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $title)))
        );

        // Filter out stop words
        return array_diff($words, $stopWords);
    }

}
