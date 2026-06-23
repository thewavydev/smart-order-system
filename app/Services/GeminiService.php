<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    /**
     * -------------------------------------------------------------
     * Parse natural language order into structured cart format
     * -------------------------------------------------------------
     * Expected output:
     * {
     *   "intent": "order",
     *   "items": [
     *      { "product": "tomatoes", "quantity": 2 }
     *   ]
     * }
     */
    public function parseOrder(string $message): ?array
    {
        // ---------------------------------------------------------
        // SYSTEM PROMPT (STRICT JSON OUTPUT ONLY)
        // ---------------------------------------------------------
        $prompt = "
You are an order extraction AI for a WhatsApp ordering system.

Extract products and quantities from the user message.

RULES:
- Return ONLY valid JSON
- No explanations
- No markdown
- No backticks
- If no order exists, return {\"intent\":\"none\"}

OUTPUT FORMAT:
{
  \"intent\": \"order\",
  \"items\": [
    {
      \"product\": \"string\",
      \"quantity\": number
    }
  ]
}

EXAMPLES:

User: I want 2 tomatoes and 1 onion
Output:
{
  \"intent\": \"order\",
  \"items\": [
    { \"product\": \"tomatoes\", \"quantity\": 2 },
    { \"product\": \"onion\", \"quantity\": 1 }
  ]
}

User: hello
Output:
{
  \"intent\": \"none\"
}

MESSAGE:
{$message}
";

        // ---------------------------------------------------------
        // CALL GEMINI API
        // ---------------------------------------------------------
        $response = Http::timeout(15)->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key='
            . env('GEMINI_API_KEY'),
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]
        );

        $data = $response->json();

        // ---------------------------------------------------------
        // SAFE EXTRACTION
        // ---------------------------------------------------------
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            return null;
        }

        // ---------------------------------------------------------
        // CLEAN GEMINI OUTPUT
        // ---------------------------------------------------------
        $text = str_replace(['```json', '```'], '', $text);
        $text = trim($text);

        // ---------------------------------------------------------
        // DECODE JSON SAFELY
        // ---------------------------------------------------------
        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        // ---------------------------------------------------------
        // VALIDATE STRUCTURE
        // ---------------------------------------------------------
        if (!is_array($decoded)) {
            return null;
        }

        if (!isset($decoded['intent'])) {
            $decoded['intent'] = 'none';
        }

        if ($decoded['intent'] !== 'order') {
            return $decoded;
        }

        // Ensure items exist
        if (!isset($decoded['items']) || !is_array($decoded['items'])) {
            $decoded['items'] = [];
        }

        // ---------------------------------------------------------
        // NORMALIZE ITEMS (important for matching products)
        // ---------------------------------------------------------
        $decoded['items'] = array_map(function ($item) {
            return [
                'product' => strtolower(trim($item['product'] ?? '')),
                'quantity' => (int) ($item['quantity'] ?? 1),
            ];
        }, $decoded['items']);

        return $decoded;
    }
}