<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TextRazorService
{
    private $apiKey;
    private $httpClient;

    // Liste des catégories autorisées avec mots-clés associés
    private $allowedCategories = [
        "Fruits" => ["fruit", "citrus", "berry", "apple", "banana", "grape"],
        "Légumes" => ["vegetable", "carrot", "tomato", "pepper", "broccoli", "cabbage"],
        "Fruits Secs" => ["nut", "almond", "hazelnut", "walnut", "cashew"]
    ];

    public function __construct(string $apiKey, HttpClientInterface $httpClient)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient;
    }

    public function analyzeText(string $text): array
    {
        $response = $this->httpClient->request('POST', 'https://api.textrazor.com/', [
            'headers' => [
                'X-TextRazor-Key' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'extractors' => 'entities,topics',
                'text' => $text,
            ],
        ]);

        if ($response->getStatusCode() === 200) {
            return $response->toArray();
        }

        throw new \Exception('TextRazor API error: ' . $response->getContent(false));
    }

    public function extractProductsAndCategories(array $response): array
    {
        $products = [];
        $categories = [];

        // Extract entities
        foreach ($response['response']['entities'] ?? [] as $entity) {
            $freebaseTypes = $entity['freebaseTypes'] ?? [];
            $name = $entity['entityId'] ?? $entity['matchedText'];
            $entityType = strtolower($name); // Convertir en minuscule pour correspondance

            // Products: Specific items or brands
            if (in_array('/common/product', $freebaseTypes) || in_array('/organization/organization', $freebaseTypes)) {
                $products[] = $name;
            } 
            // Categories: Vérifier contre la liste des catégories autorisées
            else {
                $matchedCategory = null;
                foreach ($this->allowedCategories as $category => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (strpos($entityType, $keyword) !== false) {
                            $matchedCategory = $category;
                            break 2; // Sortir des deux boucles une fois la catégorie trouvée
                        }
                    }
                }
                // Ajouter la catégorie correspondante ou "Autre" si aucune correspondance
                $categories[] = $matchedCategory ?? "Autre";
            }
        }

        // Topics: Ajouter des sujets comme catégories si pertinents
        foreach ($response['response']['topics'] ?? [] as $topic) {
            if ($topic['score'] > 0.7) {
                $topicLabel = strtolower($topic['label']);
                $matchedCategory = null;
                foreach ($this->allowedCategories as $category => $keywords) {
                    foreach ($keywords as $keyword) {
                        if (strpos($topicLabel, $keyword) !== false) {
                            $matchedCategory = $category;
                            break 2;
                        }
                    }
                }
                $categories[] = $matchedCategory ?? "Autre";
            }
        }

        return [
            'products' => array_unique($products),
            'categories' => array_unique($categories)
        ];
    }
}