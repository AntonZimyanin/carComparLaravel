<?php

namespace App\Telegram\Parser\AvBy;

use App\Telegram\Api\AvBy\AvByApi;
use App\Telegram\Enum\AvByCarProperty;

use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Redis;


//TODO:
// - add av by api fot this class and change access to av by field +
// - review the loop 114

class AvByParser
{
    private string $xApiKey;
    private string $url;
    private AvByApi $avByApi;
    public function __construct(AvByApi $avByApi)
    {
        $this->url = "https://cars.av.by/filter";
        $this->xApiKey = 'y5b3b55fdce273d03ec1d22';
        $this->avByApi = $avByApi;
    }

    //https://cars.av.by/audi/a2
    //https://cars.av.by/filter?brands[0][brand]=1444&brands[0][model]=1451&price_usd[min]=1&price_usd[max]=111111
    public function set(
        AvByCarProperty $p
    ): void {
        $brandId = $this->avByApi->findBrandIdBySlug($p->carBrand);

        if ($p->carBrand) {
            $this->url .= "?brands[0][brand]=" . $brandId;
        }
        if ($p->carModelId) {
            $this->url .= "&brands[0][model]=" . $p->carModelId;
        }
        if ($p->carPriceLow > 0) {
            $this->url .= "&price_usd[min]=" . $p->carPriceLow;
        }
        if ($p->carPriceHigh > 0) {
            $this->url .= "&price_usd[max]=" . $p->carPriceHigh;
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
        $i = 0;
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

                    foreach ($val as $item) {
                        if ( $item['name'] == "year") {
                            $year = $item['value'];
                        }
                    }
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
            Redis::hSet("car:$i",
                "sellername", $sellerName,
                "locationname", $locationName,
                "brand", $brand,
                "model", $model,
                "generation", $generation,
                "year", $year,
                "publicurl", $publicUrl,
                "price", $price,
                "photourl", $photoUrl
            );
            $i++;
        }
        $chat->message("Поиск завершен")->send();
    }
}
