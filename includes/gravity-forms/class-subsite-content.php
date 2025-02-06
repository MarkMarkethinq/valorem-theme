<?php

class SubsiteContent {
    private $chatGptApi;
    private $pexelsApi;
    private $siteData;

    public function __construct(array $siteData) {
        $this->chatGptApi = new ChatGPT_API($_ENV['chatgpt_api_key']);
        $this->pexelsApi = new Pexels_API($_ENV['pexels_api_key']);
        $this->siteData = $siteData;
    }

    public function generateAllContent(): array {
        try {
            return array_merge(
                $this->generateStaticContent(),
                $this->generateAIContent(),
                $this->generateImages(),
                ['features' => $this->generateFeatures()]
            );
        } catch (\Exception $e) {
            \Includes\Logger::error('Error generating content', [
                'error' => $e->getMessage(),
                'site_data' => $this->siteData
            ]);
            return [];
        }
    }

    private function generateStaticContent(): array {
        return [
            'hero_title' => 'Welkom bij ' . $this->siteData['bedrijfsnaam'],
            'features_card_title' => 'Onze Diensten',
            'content_title' => 'Over ' . $this->siteData['bedrijfsnaam'],
            'bedrijfnaam' => $this->siteData['bedrijfsnaam'],
            'email' => $this->siteData['E-mailadres'],
            'straat' => $this->siteData['straat'] ?? '',
            'postcode' => $this->siteData['postcode'] ?? '',
            'telefoonnummer' => $this->siteData['telefoonnummer'] ?? ''
        ];
    }

    private function generateAIContent(): array {
        $contentMap = [
            'hero_description' => [
                'prompt' => $this->getHeroPrompt(),
                'fallback' => "Welkom bij {$this->siteData['bedrijfsnaam']}, uw betrouwbare partner voor vakkundig schilderwerk."
            ],
            'features_card_description' => [
                'prompt' => $this->getFeaturesPrompt(),
                'fallback' => "Wij bieden professionele schilderdiensten voor zowel binnen- als buitenwerk."
            ],
            'content_description' => [
                'prompt' => $this->getAboutPrompt(),
                'fallback' => "Als ervaren schildersbedrijf staan wij voor kwaliteit en vakmanschap."
            ]
        ];

        $aiContent = [];
        foreach ($contentMap as $field => $config) {
            try {
                $aiContent[$field] = $this->chatGptApi->generateChat([
                    ['role' => 'user', 'content' => $config['prompt']]
                ]);
                
                \Includes\Logger::info("AI content generated for $field", [
                    'field' => $field,
                    'company' => $this->siteData['bedrijfsnaam']
                ]);
            } catch (\Exception $e) {
                \Includes\Logger::error("Failed to generate AI content for $field", [
                    'error' => $e->getMessage()
                ]);
                $aiContent[$field] = $config['fallback'];
            }
        }

        return $aiContent;
    }

    private function generateImages(): array {
        $imageConfig = [
            'hero_image' => [
                'query' => 'professional painter at work',
                'orientation' => 'landscape'
            ],
            'image_left' => [
                'query' => 'house painting detail',
                'orientation' => 'portrait'
            ],
            'image_right' => [
                'query' => 'paint brushes and tools',
                'orientation' => 'portrait'
            ]
        ];

        $images = [];
        foreach ($imageConfig as $field => $config) {
            try {
                $photos = $this->pexelsApi->fetchPhotos($config['query'], 1);
                if (!empty($photos)) {
                    $images[$field] = $photos[0]['src'][$config['orientation']];
                    \Includes\Logger::info("Image fetched for $field");
                }
            } catch (\Exception $e) {
                \Includes\Logger::error("Failed to fetch image for $field", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $images;
    }

    private function generateFeatures(): array {
        try {
            $response = $this->chatGptApi->generateChat([
                ['role' => 'user', 'content' => $this->getFeaturesListPrompt()]
            ]);

            $features = $this->parseFeatures($response);

            \Includes\Logger::info('Features generated successfully', [
                'count' => count($features)
            ]);

            return $features;
        } catch (\Exception $e) {
            \Includes\Logger::error('Failed to generate features', [
                'error' => $e->getMessage()
            ]);

            return $this->getFallbackFeatures();
        }
    }

    private function getHeroPrompt(): string {
        return "Schrijf een korte, pakkende introductie (maximaal 2 zinnen) voor {$this->siteData['bedrijfsnaam']}, 
                een professioneel schildersbedrijf. 
                Focus op kwaliteit, ervaring en betrouwbaarheid.";
    }

    private function getFeaturesPrompt(): string {
        return "Beschrijf in 1 korte punt(10 woorden) de belangrijkste diensten van een schildersbedrijf zoals 
                {$this->siteData['bedrijfsnaam']}. 
                Gebruik zakelijke maar toegankelijke taal.";
    }

    private function getAboutPrompt(): string {
        return "Schrijf een professionele 'Over ons' tekst (ongeveer 150 woorden) voor {$this->siteData['bedrijfsnaam']}.
                Benadruk vakmanschap, klanttevredenheid en jarenlange ervaring in de schildersbranche.
                Gebruik een persoonlijke maar professionele toon.";
    }

    private function getFeaturesListPrompt(): string {
        return "Geef 4 concrete diensten/features voor {$this->siteData['bedrijfsnaam']}. 
                Format voor elk punt:
                Title: korte beschrijving (max 15 woorden).
                
                Voorbeeld format:
                Binnenschilderwerk: Vakkundige afwerking van wanden, plafonds en kozijnen
                
                Gebruik professionele maar begrijpelijke taal.";
    }

    private function parseFeatures(string $response): array {
        $features = [];
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($title, $description) = explode(':', $line, 2);
                $features[] = [
                    'title' => trim($title),
                    'description' => trim($description)
                ];
            }
        }

        return $features;
    }

    private function getFallbackFeatures(): array {
        return [
            [
                'title' => 'Binnenschilderwerk',
                'description' => 'Professionele afwerking van al uw binnenruimtes'
            ],
            [
                'title' => 'Buitenschilderwerk',
                'description' => 'Duurzame bescherming en verfraaiing van uw pand'
            ],
            [
                'title' => 'Kleuradvies',
                'description' => 'Deskundig advies voor de juiste kleurkeuze'
            ],
            [
                'title' => 'Onderhoud',
                'description' => 'Preventief en correctief schilderonderhoud'
            ]
        ];
    }
}