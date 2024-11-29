<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class OpenAIService
{
    public function summarizeText($text)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that summarizes documents in Dutch.'],
                ['role' => 'user', 'content' => $text],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    public function generateTitle($text)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that generates concise titles for documents in Dutch.'],
                ['role' => 'user', 'content' => "Generate a short title for this content: {$text}"],
            ],
        ]);

        return $response->choices[0]->message->content;
    }
    public function generateShortDescription($text)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that generates concise descriptions for documents in Dutch. The description should be 1-2 sentences long.'],
                ['role' => 'user', 'content' => "Generate a short description for this content: {$text}"],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    public function extractPeople($text)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Extract the names of people mentioned in this text. Provide a list of names.'],
                ['role' => 'user', 'content' => $text],
            ],
        ]);

        return array_map('trim', explode(',', $response->choices[0]->message->content));
    }

    public function extractKeywords($text)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Extract the most relevant keywords from this text for search optimization. Provide a list of keywords.'],
                ['role' => 'user', 'content' => $text],
            ],
        ]);

        return array_map('trim', explode(',', $response->choices[0]->message->content));
    }

}
