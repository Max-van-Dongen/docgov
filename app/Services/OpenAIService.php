<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    private $baseUrl = 'http://llm.prsonal.nl';
    private $apiKey = '';
    private $apiModel = 'llama-3.2-3b-instruct';
    private $bigApiModel = 'qwen2.5-coder-32b-instruct-128k';

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

    private function sendStreamingRequest($payload): void
    {
        set_time_limit(120);

        // Open a curl session for streaming
        $ch = curl_init("{$this->baseUrl}/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) {
            echo $data; // Stream the data chunk by chunk
            flush();
            return strlen($data);
        });

        curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);
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
                ['role' => 'system', 'content' => 'Extract the names of people mentioned in this text. Provide a list of names. If no names are found, return an empty array.'],
                ['role' => 'user', 'content' => $text],
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'people_response',
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'response' => [
                                'type' => 'array',
                                'items' => ['type' => 'string']
                            ]
                        ],
                        'required' => ['response']
                    ]
                ]
            ]
        ];

        $response = $this->sendRequest($payload);
        return json_decode($response['choices'][0]['message']['content'], true)['response'] ?? [];
    }

    public function generatePersonalisedText($text)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant that generates concise and personalized descriptions for documents in Dutch. Tailor the description for a man aged 20-30 from The Hague. Focus on what might interest him, making it relatable and engaging, and limit the response to 2-4 sentences.'],
                ['role' => 'user', 'content' => "Generate a personalized description for this content: {$text}"],
            ],
        ]);

        return $response->choices[0]->message->content;
    }

    public function extractKeywords($text)
    {
        $payload = [
            'model' => $this->apiModel,
            'messages' => [
                ['role' => 'system', 'content' => 'Extract the most relevant keywords from this text for search optimization. Provide a list of keywords. If no keywords are found, return an empty array. Try to stick to general topics.'],
                ['role' => 'user', 'content' => $text],
            ],
            'response_format' => [
                'type' => 'json_schema',
                'json_schema' => [
                    'name' => 'keywords_response',
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'response' => [
                                'type' => 'array',
                                'items' => ['type' => 'string']
                            ]
                        ],
                        'required' => ['response']
                    ]
                ]
            ]
        ];

        $response = $this->sendRequest($payload);
        return json_decode($response['choices'][0]['message']['content'], true)['response'] ?? [];
    }


    public function summarizeTextPersonality($text): void
    {
        // Get the session data
        $sessionData = session()->only([
            'name', 'age', 'location', 'interests',
            'profession', 'education', 'preferred_topics'
        ]);

        // Combine session data into a personalized context
        $personalizedContext = "User details:\n";
        foreach ($sessionData as $key => $value) {
            $personalizedContext .= ucfirst($key) . ": " . (is_array($value) ? implode(', ', $value) : $value) . "\n";
        }
        // Construct the payload

        $payload = [
            'model' => $this->bigApiModel,
            'stream' => true,
            'messages' => [
                ['role' => 'system', 'content' => 'Provide a personalized summary of the given text, considering the user\'s context provided. The summary should be concise and relevant to the user\'s interests, profession, and other details.'],
                ['role' => 'assistant', 'content' => $personalizedContext],
                ['role' => 'user', 'content' => $text],
            ],
        ];

        // Stream the response
        $this->sendStreamingRequest($payload);
    }

    public function summarizeTextGeneral($text): void
    {
        // Get the session data
        $sessionData = session()->only([
            'name', 'age', 'location', 'interests',
            'profession', 'education', 'preferred_topics'
        ]);

        // Combine session data into a personalized context
        $personalizedContext = "User details:\n";
        foreach ($sessionData as $key => $value) {
            $personalizedContext .= ucfirst($key) . ": " . (is_array($value) ? implode(', ', $value) : $value) . "\n";
        }

        // Construct the payload
        $payload = [
            'model' => $this->apiModel,
            'stream' => true,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Provide a personalized summary of the given text, considering the user\'s context provided. The summary should be concise and relevant to the user\'s interests, profession, and highlights the most important points of all the documents. Summarize all documents into one text, and don\'t add any personalized info to the summary'
                ],
                [
                    'role' => 'assistant',
                    'content' => $personalizedContext
                ],
                [
                    'role' => 'user',
                    'content' => $text
                ],
            ],
        ];

        // Stream the response (similar to summarizeTextPersonality)
        $this->sendStreamingRequest($payload);
    }
}
