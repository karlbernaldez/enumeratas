<?php

namespace App\Libraries;

class ChatbotAIService
{
    private string $provider;
    private string $apiKey;
    private string $model;
    private bool $enabled;

    public function __construct()
    {
        $this->provider = strtolower((string) (env('CHATBOT_AI_PROVIDER') ?: 'groq'));
        $this->enabled = filter_var(env('CHATBOT_ENABLE_AI'), FILTER_VALIDATE_BOOLEAN);

        if ($this->provider === 'groq') {
            $this->apiKey = trim((string) env('GROQ_API_KEY'));
            $this->model = env('GROQ_MODEL') ?: 'llama-3.1-8b-instant';
        } else {
            $this->apiKey = trim((string) env('OPENAI_API_KEY'));
            $this->model = env('OPENAI_MODEL') ?: 'gpt-4o-mini';
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled && $this->apiKey !== '';
    }

    public function debugStatus(): array
    {
        return [
            'provider' => $this->provider,
            'enabled_value' => env('CHATBOT_ENABLE_AI'),
            'enabled_bool' => $this->enabled,
            'has_api_key' => $this->apiKey !== '',
            'api_key_prefix' => $this->apiKey !== '' ? substr($this->apiKey, 0, 4) : '',
            'model' => $this->model,
            'is_enabled' => $this->isEnabled(),
        ];
    }

    public function generateReply(string $userMessage, string $verifiedContext): ?string
    {
        if (! $this->isEnabled()) {
            log_message('error', 'Chatbot AI disabled or missing API key. Provider: ' . $this->provider);
            return null;
        }

        $userMessage = trim($userMessage);
        $verifiedContext = trim($verifiedContext);

        if ($userMessage === '' || $verifiedContext === '') {
            return null;
        }

        $systemInstruction = 'You are BIS Assistant for Barangay Bacolod, Bato, Camarines Sur. '
            . 'Answer only based on the provided barangay/BIS context. '
            . 'Keep answers short, helpful, and understandable. '
            . 'Support English and Filipino. '
            . 'Do not provide legal, medical, financial, political, or unrelated advice. '
            . 'Do not make barangay decisions or promise approval. '
            . 'If the answer is not in the context, say you do not have enough verified information and refer the user to the barangay office.';

        $payload = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemInstruction,
                ],
                [
                    'role' => 'user',
                    'content' => "Resident question:\n{$userMessage}\n\nVerified barangay/BIS context:\n{$verifiedContext}",
                ],
            ],
            'temperature' => 0.2,
            'max_tokens' => 250,
        ];

        $url = $this->provider === 'groq'
            ? 'https://api.groq.com/openai/v1/chat/completions'
            : 'https://api.openai.com/v1/chat/completions';

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            log_message('error', 'Chatbot AI curl error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode < 200 || $statusCode >= 300) {
            log_message('error', 'Chatbot AI API error. Status: ' . $statusCode . ' Response: ' . $response);
            return null;
        }

        $data = json_decode($response, true);

        $reply = $data['choices'][0]['message']['content'] ?? null;

        if (! is_string($reply) || trim($reply) === '') {
            log_message('error', 'Chatbot AI returned empty reply. Response: ' . $response);
            return null;
        }

        return trim($reply);
    }
}
