<?php
//
//namespace App\Services;
//
//use OpenAI\Laravel\Facades\OpenAI;
//
//class OpenAIService
//{
//    public function summarizeText($text)
//    {
//        $response = OpenAI::chat()->create([
//            'model' => 'gpt-4o-mini',
//            'messages' => [
//                ['role' => 'system', 'content' => 'You are a helpful assistant that summarizes documents in Dutch.'],
//                ['role' => 'user', 'content' => $text],
//            ],
//        ]);
//
//        return $response->choices[0]->message->content;
//    }
//
//    public function generateTitle($text)
//    {
//        $response = OpenAI::chat()->create([
//            'model' => 'gpt-4o-mini',
//            'messages' => [
//                ['role' => 'system', 'content' => 'You are a helpful assistant that generates concise titles for documents in Dutch.'],
//                ['role' => 'user', 'content' => "Generate a short title for this content: {$text}"],
//            ],
//        ]);
//
//        return $response->choices[0]->message->content;
//    }
//    public function generateShortDescription($text)
//    {
//        $response = OpenAI::chat()->create([
//            'model' => 'gpt-4o-mini',
//            'messages' => [
//                ['role' => 'system', 'content' => 'You are a helpful assistant that generates concise descriptions for documents in Dutch. The description should be 1-2 sentences long.'],
//                ['role' => 'user', 'content' => "Generate a short description for this content: {$text}"],
//            ],
//        ]);
//
//        return $response->choices[0]->message->content;
//    }
//
//    public function extractPeople($text)
//    {
//        $response = OpenAI::chat()->create([
//            'model' => 'gpt-4o-mini',
//            'messages' => [
//                ['role' => 'system', 'content' => 'Extract the names of people mentioned in this text. Provide a list of names.
//                Give it in a list like:
//                1.
//                2.
//                3. '],
//                ['role' => 'user', 'content' => $text],
//            ],
//        ]);
//
//        return array_map('trim', explode(',', $response->choices[0]->message->content));
//    }
//
//    public function extractKeywords($text)
//    {
//        $response = OpenAI::chat()->create([
//            'model' => 'gpt-4o-mini',
//            'messages' => [
//                ['role' => 'system', 'content' => 'Extract the most relevant keywords from this text for search optimization. Provide a list of keywords.
//                Give it in a list like:
//                1.
//                2.
//                3. '],
//                ['role' => 'user', 'content' => $text],
//            ],
//        ]);
//
//        return array_map('trim', explode(',', $response->choices[0]->message->content));
//    }
//
//}


namespace App\Services;

use Illuminate\Support\Facades\Http;

// Laravel's HTTP client

class OpenAIService
{
    private $baseUrl = 'http://llm.prsonal.nl'; // Replace with the actual base URL of the alternative API
    private $apiKey = 'your-api-key'; // Replace with your actual API key for the alternative service
    private $apiModel = 'llama-3.2-3b-instruct';

    private function sendRequest($payload)
    {
        set_time_limit(120);
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/v1/chat/completions", $payload);

        if ($response->failed()) {
            throw new \Exception('API request failed: ' . $response->body());
        }

        return $response->json();
    }

    public function summarizeText($text)
    {
        $payload = [
            'model' => $this->apiModel,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that summarizes documents in Dutch.'],
                ['role' => 'user', 'content' => $text],
            ],
        ];

        $response = $this->sendRequest($payload);
//        dd($response);
        return $response['choices'][0]['message']['content'];
    }

    public function generateTitle($text)
    {
        $payload = [
            'model' => $this->apiModel,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that generates concise titles for documents in Dutch. Do not add any extra info or sentences, only generate the title.'],
                ['role' => 'user', 'content' => "Generate a short title for this content: {$text}"],
            ],
        ];

        $response = $this->sendRequest($payload);

        return $response['choices'][0]['message']['content'];
    }

    public function generateShortDescription($text)
    {
        $payload = [
            'model' => $this->apiModel,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that generates concise descriptions for documents in Dutch. The description should be 1-2 sentences long. Do not add any extra info or sentences, only generate the description.'],
                ['role' => 'user', 'content' => "Generate a short description for this content: {$text}"],
            ],
        ];

        $response = $this->sendRequest($payload);

        return $response['choices'][0]['message']['content'];
    }

    public function extractPeople($text)
    {
        $payload = [
            'model' => $this->apiModel,
            'messages' => [
                ['role' => 'system', 'content' => 'Extract the names of people mentioned in this text. Provide a list of names.\n                Give it in a list like:\n                1.\n                2.\n                3. If no names are found, simply return NONAME'],
                ['role' => 'user', 'content' => $text],
            ],
        ];

        $response = $this->sendRequest($payload);

        return array_map('trim', explode(',', $response['choices'][0]['message']['content']));
    }

    public function extractKeywords($text)
    {
        $payload = [
            'model' => $this->apiModel,
            'messages' => [
                ['role' => 'system', 'content' => 'Extract the most relevant keywords from this text for search optimization. Provide a list of keywords.\n                Give it in a list like:\n                1.\n                2.\n                3. If no keywords are found, simply return NOKEYWORDS. \n try to stick to general topics, don\'t go into specifics'],
                ['role' => 'user', 'content' => $text],
            ],
        ];

        $response = $this->sendRequest($payload);

        return array_map('trim', explode(',', $response['choices'][0]['message']['content']));
    }
}
