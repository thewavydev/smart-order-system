<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
        public function parseOrder(string $message): ?array
        {
            $prompt = "
                Extract products and quantities from the customer message.

                Return ONLY valid JSON.

                 Example:

                {
                \"intent\":\"order\",
                \"items\":[
                    {
                    \"product\":\"tomatoes\",
                    \"quantity\":2
                    }
                ]
                }

                Message:
                {$message}
                ";
            $response = Http::timeout(15)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'),
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ]
                ]
            );
            $data = $response->json();
            $text =$data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$text) {
                return null;
            }

            $text = str_replace(
                ['```json', '```'], '',$text
            );

            return json_decode(
                trim($text),
                true
            );
        }
}