<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $instanceId;
    protected $token;
    protected $apiUrl;

    public function __construct()
    {
        $this->instanceId = config('services.whatsapp.instance_id');
        $this->token = config('services.whatsapp.token');
        $this->apiUrl = rtrim(config('services.whatsapp.api_url'), '/') . '/' . $this->instanceId;
    }

    /**
     * Send a text message via WhatsApp.
     */
    public function sendMessage($to, $message)
    {
        if (!$this->instanceId || !$this->token) {
            Log::warning('WhatsApp Service: Instance ID or Token missing.');
            return false;
        }

        try {
            $response = Http::post("{$this->apiUrl}/messages/chat", [
                'token' => $this->token,
                'to' => $to,
                'body' => $message,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a document/file via WhatsApp.
     */
    public function sendDocument($to, $filePath, $fileName, $caption = '')
    {
        if (!$this->instanceId || !$this->token) {
            Log::warning('WhatsApp Service: Instance ID or Token missing.');
            return false;
        }

        try {
            // Using absolute URL if available, or base64 if needed by the API
            // For UltraMsg, we can send a public URL or a base64 string
            // Here we assume the file is locally available and we send it via multipart if supported
            // or we use the specific API format for documents.
            
            $response = Http::post("{$this->apiUrl}/messages/document", [
                'token' => $this->token,
                'to' => $to,
                'filename' => $fileName,
                'document' => $filePath, // This usually needs to be a public URL or base64
                'caption' => $caption,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp Service Error (Document): ' . $e->getMessage());
            return false;
        }
    }
}
