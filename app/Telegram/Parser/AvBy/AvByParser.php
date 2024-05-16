<?php

namespace App\Telegram\Parser\AvBy;

use App\Telegram\Api\AvBy\AvByApi;
use App\Telegram\Enum\AvByCarProperty;

use DefStudio\Telegraph\Models\TelegraphChat;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

/*
URL example :

Without price:
https://cars.av.by/audi/a2

With price:
https://cars.av.by/filter?brands[0][brand]=1444&brands[0][model]=1451&price_usd[min]=1&price_usd[max]=111111
https://cars.av.by/filter?brands[0][brand]=6&brands[0][model]=5812&brands[0][generation]=4316&price_usd[max]=20000

*/

class AvByParser
{
    private string $xApiKey;
    private string $url;
    private int $brandId;
    private int $modelId;

    public function __construct(protected AvByApi $avByApi)
    {
        $this->url = "https://cars.av.by/filter";
        $this->xApiKey = 'y5b3b55fdce273d03ec1d22';
    }

    public function set(AvByCarProperty $p): void {
        $isFirstArg = true;

        if ($p->carBrand) {
            $this->brandId = $this->avByApi->findBrandIdBySlug($p->carBrand);
            $this->url .= "?brands[0][brand]=" . $this->brandId;
            $isFirstArg = false;
        }
        if ($p->carModelName) {
            $this->modelId = $this->avByApi->findModelIdBySlug($p->carModelName, $this->brandId);
            $this->url .= "&brands[0][model]=" . $this->modelId;
        }

        if ($p->carPriceLow > 0) {
            $this->url .= ($isFirstArg ? "?" : "&") . "price_usd[min]=" . $p->carPriceLow;
            $isFirstArg = false;
        }
        if ($p->carPriceHigh > 0) {
            $this->url .= ($isFirstArg ? "?" : "&") . "price_usd[max]=" . $p->carPriceHigh;
        }
    }

    public function parse(TelegraphChat $chat): void
    {
        $html = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux i686; rv:125.0) Gecko/20100101 Firefox/125.0'
        ])->get($this->url)->body();
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
        if (empty($extractedData) || empty($extractedData[0]['props']['initialState']['filter']['main']['adverts'])) {
            return;
        }

        $i = 0;
        foreach ($extractedData[0]['props']['initialState']['filter']['main']['adverts'] as $fields) {
            $carData = [
                'sellername' => '',
                'locationname' => '',
                'brand' => '',
                'model' => '',
                'generation' => '',
                'year' => '',
                'publicurl' => '',
                'price' => '',
            ];

            foreach ($fields as $key => $val) {
                if ($key == 'sellerName') {
                    $carData['sellername'] = $val;
                }
                if ($key == 'locationName') {
                    $carData['locationname'] = $val;
                }
                if ($key == 'properties') {
                    $carData['brand'] = $val[0]['value'];
                    $carData['model'] = $val[1]['value'];
                    $carData['generation'] = $val[2]['value'];

                    foreach ($val as $item) {
                        if ($item['name'] == "year") {
                            $carData['year'] = $item['value'];
                        }
                    }
                }
                if ($key == 'publicUrl') {
                    $carData['publicurl'] = $val;
                }
                if ($key == 'price') {
                    $carData['price'] = $val["usd"]["amount"];
                }
            }

            $brandLowCase = strtolower($carData['brand']);
            Redis::hSet("car:$brandLowCase:$i", ...$carData);
            $i++;
        }
        Redis::set("car_count", $i);
    }
}
