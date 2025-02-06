<?php

use Includes\Logger;

class ChatGPT_API
{
    private $apiKey;
    private $apiEndpoint;
    private $defaultModel;
    private $defaultMaxTokens;
    private $defaultTemperature;

    /**
     * Constructor to initialize API settings.
     */
    public function __construct(
        string $apiKey,
        string $apiEndpoint = 'https://api.openai.com/v1/chat/completions',
        string $defaultModel = 'gpt-3.5-turbo',
        int $defaultMaxTokens = 150,
        float $defaultTemperature = 0.3,
        string $apiLogFile = null // <-- Toegevoegd
    ) {
        $this->apiKey = $apiKey;
        $this->apiEndpoint = $apiEndpoint;
        $this->defaultModel = $defaultModel;
        $this->defaultMaxTokens = $defaultMaxTokens;
        $this->defaultTemperature = $defaultTemperature;


    }

    /**
     * Generates a chat completion using the ChatGPT API.
     *
     * @param array $userMessages The conversation messages.
     * @return string The response from the ChatGPT API.
     * @throws \Exception If the API response is invalid.
     */
    public function generateChat(array $userMessages): string
    {
        Logger::info('generateChat called', ['userMessages' => $userMessages]);

        // Voeg een specifieke system message toe voor domeinnamen
        $systemMessage = [
            'role' => 'system',
            'content' => "Je bent een expert in het genereren van unieke en professionele domeinnamen. " .
                        "Wees creatief maar zakelijk. " .
                        "Gebruik nooit generieke termen zoals 'online', 'web', 'nl' (behalve als extensie). " .
                        "Focus op de essentie van het bedrijf."
        ];

        // Combineer system message met gebruikersberichten
        $messages = array_merge([$systemMessage], $userMessages);

        Logger::debug('System + user messages combined', ['messages' => $messages]);

        // Stel de payload samen
        $payload = [
            'model'       => $this->defaultModel,
            'messages'    => $messages,
            'max_tokens'  => $this->defaultMaxTokens,
            'temperature' => $this->defaultTemperature,
        ];

        Logger::debug('Payload for ChatGPT request', ['payload' => $payload]);

        // Verstuur de aanvraag
        $response = $this->makeRequest($payload);

        // Controleer op geldige respons
        if (isset($response['choices'][0]['message']['content'])) {
            $content = trim($response['choices'][0]['message']['content']);
            Logger::info('Valid response received from ChatGPT', ['content' => $content]);
            return $content;
        }

        // Als we hier komen, is er iets misgegaan
        Logger::error('Invalid response from ChatGPT API', ['response' => $response]);
        throw new \Exception('Invalid response from ChatGPT API: ' . json_encode($response));
    }

    /**
     * Makes a request to the ChatGPT API.
     *
     * @param array $payload The request payload.
     * @return array The decoded JSON response.
     * @throws \Exception If an error occurs during the request.
     */
    private function makeRequest(array $payload): array
    {
        Logger::info('Starting ChatGPT API request');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->apiEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            Logger::error('cURL error occurred', ['error' => $error]);
            throw new \Exception('Curl error: ' . $error);
        }

        if ($response === false) {
            Logger::error('Empty response from API');
            throw new \Exception('Empty response from API');
        }

        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $jsonError = json_last_error_msg();
            Logger::error('JSON decode error', ['error' => $jsonError, 'response' => $response]);
            throw new \Exception('JSON decode error: ' . $jsonError);
        }

        Logger::debug('Raw response from ChatGPT', ['response' => $decodedResponse]);
        return $decodedResponse;
    }

    
}
