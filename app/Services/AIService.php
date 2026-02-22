<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $apiKey;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        // Reverting to gemini-flash-latest as it was confirmed working locally; explicit 1.5-flash returned 404
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$this->apiKey}";
        
        if (empty($this->apiKey)) {
            Log::warning('AIService: Gemini API key is missing from configuration.');
        }
    }

    /**
     * Generate content using Gemini API.
     * @throws \App\Exceptions\AIException
     */
    public function generateContent(string $prompt): ?string
    {
        Log::info('AIService: Attempting to generate content.');
        
        try {
            if (empty($this->apiKey)) {
                Log::error('AIService: Gemini API key is not set.');
                throw \App\Exceptions\AIException::generalError('Gemini API key is not set.');
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                
                if ($text) {
                    Log::info('AIService: Content generated successfully.');
                } else {
                    Log::warning('AIService: API returned successful response but no text content.', ['response' => $data]);
                }
                
                return $text;
            }

            if ($response->status() === 429) {
                Log::warning('AIService: Gemini API quota exceeded.', [
                    'body' => $response->body(),
                ]);
                throw \App\Exceptions\AIException::quotaExceeded();
            }

            Log::error('AIService: Gemini API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw \App\Exceptions\AIException::generalError('Gemini API request failed with status: ' . $response->status());

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('AIService: Connection error', ['message' => $e->getMessage()]);
            throw \App\Exceptions\AIException::connectionError('Failed to connect to AI service. Please check your internet connection.');
        } catch (\App\Exceptions\AIException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('AIService: Exception occurred', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw \App\Exceptions\AIException::generalError('An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
