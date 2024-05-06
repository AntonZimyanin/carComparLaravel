<?php

namespace App\Telegram\Api\AvBy;

class AvByApi
{
    private string $xApiKey = 'eec1bf3e24da7039e1ab116';

    public function getAllCarBrands(): mixed
    {
        $path = base_path('brand-items-id-name-slug.json');
        $json = file_get_contents($path);
        return json_decode($json, true);
    }

    public function findBrandIdBySlug(string $slug)
    {
        $carData = $this->getAllCarBrands();

        $left = 0;
        $right = count($carData) - 1;

        while ($left <= $right) {
            $mid = $left + floor(($right - $left) / 2);
            $currentSlug = $carData[$mid]['slug'];

            if ($currentSlug === $slug) {
                return $carData[$mid]['id'];
            } elseif ($currentSlug < $slug) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }

        return null;
    }


    public function getModels($brandSlug)
    {
        $brandId = $this->findBrandIdBySlug($brandSlug);

        $url = "https://api.av.by/offer-types/cars/catalog/brand-items/$brandId/models";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Api-Key: ' . $this->xApiKey
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    public function getGenerations(string $slug, int $modelId)
    {
        $brandId = $this->findBrandIdBySlug($slug);
        $url = "https://api.av.by/offer-types/cars/catalog/brand-items/{$brandId}/models/{$modelId}/generations";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'X-Api-Key: ' . $this->xApiKey
        ]);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
