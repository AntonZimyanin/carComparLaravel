<?php

namespace App\Telegram\Parser\AvBy;

use App\Telegram\Enum\AvByCarProperty;

use DefStudio\Telegraph\Models\TelegraphChat;
use DOMDocument;
use DOMXPath;


//TODO:
// - add av by api fot this class and change access to av by field
// - review the loop 114

class AvByParser
{
    private string $url;
    private string $xApiKey = 'y5b3b55fdce273d03ec1d22';


    private function getAllCarBrands(): mixed
    {
        $path = base_path('brand-items-id-name-slug.json');
        $json = file_get_contents($path);
        return json_decode($json, true);
    }

    private function findIdBySlug($slug)
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
        $brandId = $this->findIdBySlugModel($brandSlug);

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


    private function findIdBySlugModel($slug)
    {
        $carData = $this->getModels($slug);
        for ($i = 0; $i < count($carData); $i++) {
            if ($carData[$i]['slug'] === $slug) {
                return $carData[$i]['id'];
            }
        }
        return null;
    }

    public function __construct()
    {
        $this->url = "https://cars.av.by/filter";
    }
    //https://cars.av.by/audi/a2
    //https://cars.av.by/filter?brands[0][brand]=1444&brands[0][model]=1451&price_usd[min]=1&price_usd[max]=111111
    public function set(
        AvByCarProperty $p
    ): void {
        $brand_id = $this->findIdBySlug($p->car_brand);

        if ($p->car_brand) {
            $this->url .= "?brands[0][brand]=" . $brand_id;
        }
        if ($p->car_model_id) {
            $this->url .= "&brands[0][model]=" . $p->car_model_id;
        }
        if ($p->car_price_low > 0) {
            $this->url .= "&price_usd[min]=" . $p->car_price_low;
        }
        if ($p->car_price_high) {
            $this->url .= "&price_usd[max]=" . $p->car_price_high;
        }
    }

    public function parse(TelegraphChat $chat): void
    {
        //        $url = "https://cars.av.by/filter?brands[0][brand]=6&brands[0][model]=5812&brands[0][generation]=4316&price_usd[max]=20000";

        $handle = curl_init($this->url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($handle);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();

        $doc->loadHTML($html);

        $xpath = new DOMXPath($doc);
        $data = $xpath->evaluate("//script[@id='__NEXT_DATA__']");
        $extractedData = [];

        foreach ($data as $node) {
            $textContent = htmlspecialchars_decode($node->textContent);

            $jsonData = json_decode(trim($textContent), true, 512, JSON_UNESCAPED_UNICODE);
            if (json_last_error() === JSON_ERROR_NONE) {
                $extractedData[] = $jsonData;
            }
        }
        $i = 1;
        if (empty($extractedData)) {
            $chat->message("Ничего не найдено2")->send();
            return;
        }
        if ($extractedData[0]['props']['initialState']['filter']['main']['adverts'] == 0) {
            $chat->message("Ничего не найдено1\n {$this->url}")->send();
            return;
        }
        $sellerName = '';
        $locationName = '';
        $brand = '';
        $model = '';
        $generation = '';
        $year = '';
        $publicUrl = '';
        $price = '';
        $photoUrl = '';
        foreach ($extractedData[0]['props']['initialState']['filter']['main']['adverts'] as $fields) {
            foreach ($fields as $key => $val) {
                if ($key == 'sellerName') {
                    $sellerName = $val;
                }
                if ($key == 'locationName') {
                    $locationName = $val;
                }
                if ($key == 'properties') {
                    $brand = $val[0]['value'];
                    $model = $val[1]['value'];
                    $generation = $val[2]['value'];
                    $year = $val[3]['value'];
                }
                if ($key == 'publicUrl') {
                    $publicUrl = $val;
                }
                if ($key == 'price') {
                    $price = $val["usd"]["amount"];
                }

                if ($key == 'photos') {
                    $photoUrl = $val[0]['medium']['url'];
                }
            }

            $chat->photo($photoUrl)->message(
                "
Продавец: {$sellerName}
Город: {$locationName}
Brand: {$brand}
Model: {$model}
Generation: {$generation}
Year: {$year}
Price: {$price}
Public Url: {$publicUrl}"
            )->send();
        }
    }
}
