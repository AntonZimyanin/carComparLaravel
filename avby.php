<?php



$nextPage = true;
$url = "https://cars.av.by/filter?brands[0][brand]=6&brands[0][model]=5812&brands[0][generation]=4316&price_usd[max]=20000";
$pageNumber = 1;
echo "\n\n Page number $pageNumber \n\n";

while ($nextPage) {

    $handle = curl_init($url);
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
                echo "generation ". $val[2]['value']  . PHP_EOL;
                echo "year ". $val[2]['value']  . PHP_EOL;
                
                echo "year ". $val[2]['value']  . PHP_EOL;

            }
            if ($key == 'publicUrl') {
                echo 'publicUrl' . " $val"  . PHP_EOL;
            }


            if ($key == 'price') {
                echo 'price'; 
                print_r($val["usd"]);
                echo   ''. PHP_EOL;
            }

            if ($key == 'photos') {
                echo 'photos'; 
                print_r($val);
                echo   ''. PHP_EOL;
            }
        }
    }


    $nextPage = false;

//    $nextPageData = $xpath->query("//*[contains(text(), 'На странице')]");
//    $extractedNextPageData = [];
//    foreach ($nextPageData as $node) {
//        $extractedNextPageData[] = $node->nodeValue;
//    }
//
//    $comparNumberOfCars = explode(' ', $extractedNextPageData[0]);
//    if ($comparNumberOfCars[2] == $comparNumberOfCars[5]) {
//        $nextPage = false;
//    }
//
//    $pageNumber++;
//    $url = $url . '?page=' . $pageNumber;
//
//    echo "\n\n Page number $pageNumber \n\n";
//    if ($pageNumber == 5) {
//        $nextPage = false;
//    }
}
