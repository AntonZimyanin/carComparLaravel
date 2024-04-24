<?php

use App\Telegram\Enum\AvByCarProperty;

class AvByParser
{

    private $url;
    public function __construct()
    {
        $this->url = "https://cars.av.by/filter";
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


    public function parse($chat)
    {
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
        file_put_contents('myfile.json', json_encode($extractedData));
        $i = 1;
        foreach ($extractedData[0]['props']['initialState']['filter']['main']['adverts'] as $fields) {
            foreach ($fields as $key => $val) {

                if ($key == 'sellerName') {
                    echo $i . " $val" . PHP_EOL;
                    $i++;
                }
                if ($key == 'locationName') {
                    echo 'locationName' . " $val"  . PHP_EOL;
                }
                if ($key == 'properties') {
                    echo "Brand " . $val[0]['value']  . PHP_EOL;
                    echo "Model " . $val[1]['value']  . PHP_EOL;
                    echo "generation " . $val[2]['value']  . PHP_EOL;
                    echo "year " . $val[2]['value']  . PHP_EOL;

                    echo "year " . $val[2]['value']  . PHP_EOL;
                }
                if ($key == 'publicUrl') {
                    echo 'publicUrl' . " $val"  . PHP_EOL;
                }


                if ($key == 'price') {
                    echo 'price';
                    print_r($val["usd"]);
                    echo   '' . PHP_EOL;
                }

                if ($key == 'photos') {
                    echo 'photos';
                    print_r($val);
                    echo   '' . PHP_EOL;
                }
            }
        }
    }
}
