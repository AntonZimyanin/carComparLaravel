<?php


function getAllCarBrands(): mixed
{
    $path = './brand-items-id-name-slug.json';
    $json = file_get_contents($path);
    return json_decode($json, true);
}


$arr = getAllCarBrands();
$init_char = 'a';
$letter_arr = array_filter($arr, function($brand) use ($init_char){ 
    return $brand['slug'][0] == $init_char;
});

// print_r($letter_arr);


foreach ($letter_arr as $el) {
   echo $el['name'] . PHP_EOL;
}