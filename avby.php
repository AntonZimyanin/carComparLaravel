<?php

//
//
//
//$nextPage = true;
//$url = "https://cars.av.by/filter?brands[0][brand]=6&brands[0][model]=5812&brands[0][generation]=4316&price_usd[max]=20000";
//$pageNumber = 1;
//echo "\n\n Page number $pageNumber \n\n";
//
//while ($nextPage) {
//
//    $handle = curl_init($url);
//    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
//    $html = curl_exec($handle);
//    libxml_use_internal_errors(true);
//    $doc = new DOMDocument();
//
//    $doc->loadHTML($html);
//
//    $xpath = new DOMXPath($doc);
//    $data = $xpath->evaluate("//script[@id='__NEXT_DATA__']");
//    $extractedData = [];
//
//    foreach ($data as $node) {
//        $textContent = htmlspecialchars_decode($node->textContent);
//
//        $jsonData = json_decode(trim($textContent), true, 512, JSON_UNESCAPED_UNICODE);
//        if (json_last_error() === JSON_ERROR_NONE) {
//            $extractedData[] = $jsonData;
//        }
//    }
//    file_put_contents('myfile.json', json_encode($extractedData));
//    $i = 1;
//    foreach ($extractedData[0]['props']['initialState']['filter']['main']['adverts'] as $fields) {
//        foreach ($fields as $key => $val) {
//
//            if ($key == 'sellerName') {
//                echo $i . " $val" . PHP_EOL;
//                $i++;
//            }
//            if ($key == 'locationName') {
//                echo 'locationName' . " $val"  . PHP_EOL;
//            }
//            if ($key == 'properties') {
//                echo "Brand " . $val[0]['value']  . PHP_EOL;
//                echo "Model " . $val[1]['value']  . PHP_EOL;
//                echo "generation ". $val[2]['value']  . PHP_EOL;
//                echo "year ". $val[2]['value']  . PHP_EOL;
//
//                echo "year ". $val[3]['value']  . PHP_EOL;
//
//            }
//            if ($key == 'publicUrl') {
//                echo 'publicUrl' . " $val"  . PHP_EOL;
//            }
//
//
//            if ($key == 'price') {
//                echo 'price';
//                print_r($val["usd"]["amount"]);
//                echo   ''. PHP_EOL;
//            }
//
//            if ($key == 'photos') {
//                echo 'photos';
//                if (count($val) > 1){
//                    echo "photo url " . $val[0]['medium']['url'];
//                }
//
//                echo   ''.   PHP_EOL;
//            }
//        }
//    }
//
//
//    $nextPage = false;
//
////    $nextPageData = $xpath->query("//*[contains(text(), 'На странице')]");
////    $extractedNextPageData = [];
////    foreach ($nextPageData as $node) {
////        $extractedNextPageData[] = $node->nodeValue;
////    }
////
////    $comparNumberOfCars = explode(' ', $extractedNextPageData[0]);
////    if ($comparNumberOfCars[2] == $comparNumberOfCars[5]) {
////        $nextPage = false;
////    }
////
////    $pageNumber++;
////    $url = $url . '?page=' . $pageNumber;
////
////    echo "\n\n Page number $pageNumber \n\n";
////    if ($pageNumber == 5) {
////        $nextPage = false;
////    }
//}

namespace App\Telegram\Parser\AvBy;

use App\Telegram\Enum\AvByCarProperty;

use DOMDocument;
use DOMXPath;

class AvByParser
{
    private string $url;

    public function __construct()
    {
        $this->url = "https://cars.av.by/filter/";
    }
    //https://cars.av.by/filter?brands[0][brand]=1444&brands[0][model]=1451&price_usd[max]=111111
    //https://cars.av.by/filter?brands[0][brand]=1444&brands[0][model]=1451&price_usd[min]=1&price_usd[max]=111111
    public function set(AvByCarProperty $property): void
    {
        if ($property->car_brand) {
            $this->url .= "?brands[0][brand]=" . $property->car_brand;
        }
        if ($property->car_model) {
            $this->url .= "&brands[0][model]=" . $property->car_model;
        }
        if ($property->car_price_low) {
            $this->url .= "&price_usd[min]=" . $property->car_price_low;
        }
        if ($property->car_price_high) {
            $this->url .= "&price_usd[max]=" . $property->car_price_high;
        }
    }

    public function url_r()
    {
        return $this->url;
    }

    public function parse(): void
    {

        $url = "https://cars.av.by/filter?brands[0][brand]=6&brands[0][model]=5812&brands[0][generation]=4316&price_usd[max]=20000";
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
            $res =
                "
Продавец: {$sellerName}
Город: {$locationName}
Brand: {$brand}
Model: {$model}
Generation: {$generation}
Year: {$year}
Price: {$price}
Public Url: {$publicUrl}";
            echo "$res";

        }
    }
}


class AvByAPI
{
    private string $xApiKey = 'y5b3b55fdce273d03ec1d22';


    public function __construct()
    {

    }
    private function getAllCarBrands(): mixed
    {
        $path = 'brand-items-id-name-slug.json';
        $json = file_get_contents($path);
        return json_decode($json, true);
    }

    private function findIdBySlug(string $slug)
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
        $brandId = $this->findIdBySlug($brandSlug);

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
        $brandId = $this->findIdBySlug($slug);
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

$av = new AvByAPI();
$models = $av->getGenerations('audi', 10);
print_r($models);
