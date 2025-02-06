<?php



class Pexels_API
{
    private $apiKey;
    private $apiEndpoint;

    /**
     * Constructor om API-configuratie in te stellen.
     */
    public function __construct(
        string $apiKey,
        string $apiEndpoint = 'https://api.pexels.com/v1/search'
    ) {
        $this->apiKey = $apiKey;
        $this->apiEndpoint = $apiEndpoint;
    }

    /**
     * Haal foto's op via de Pexels API.
     *
     * @param string $query Zoekterm (bijv. "bouw").
     * @param int $perPage Aantal resultaten per pagina.
     * @param int $page Welke pagina van resultaten op te halen.
     * @return array Resultaten van de API.
     * @throws Exception Bij een fout in de API-aanroep.
     */
    public function fetchPhotos(string $query, int $perPage = 15, int $page = 1): array
    {
        // Stel de headers en query-parameters samen
        $headers = [
            'Authorization: ' . $this->apiKey,
        ];
        $url = $this->apiEndpoint . '?' . http_build_query([
            'query' => $query,
            'per_page' => $perPage,
            'page' => $page,
        ]);

        // API-aanroep uitvoeren
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decode error: ' . json_last_error_msg());
        }

        $photos = $decodedResponse['photos'] ?? [];
        
        // Transform and return the full photo data
        return array_map(function($photo) {
            return [
                'id' => $photo['id'],
                'photographer' => $photo['photographer'],
                'url' => $photo['url'],
                'src' => [
                    'original' => $photo['src']['original'],
                    'large2x' => $photo['src']['large2x'],
                    'large' => $photo['src']['large'],
                    'medium' => $photo['src']['medium'],
                    'small' => $photo['src']['small'],
                    'portrait' => $photo['src']['portrait'],
                    'landscape' => $photo['src']['landscape'],
                    'tiny' => $photo['src']['tiny']
                ]
            ];
        }, $photos);
    }
}
