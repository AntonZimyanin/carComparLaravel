<?php


function getAllCarBrands(): mixed
{
    $path = './brand-items-id-name-slug.json';
    $json = file_get_contents($path);
    return json_decode($json, true);
}


$arr = getAllCarBrands();
// $init_char = 'a';
// $letter_arr = array_filter($arr, function($brand) use ($init_char){
//     return $brand['slug'][0] == $init_char;
// });

// print_r($letter_arr);

function findIdBySlug($slug, $carData)
{
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

    return null; // Slug not found
}

// Example usage:
$givenSlug = 'exeed'; // Replace with the actual slug you have
$id = findIdBySlug($givenSlug, $arr);

if ($id !== null) {
    echo "The id for slug '{$givenSlug}' is {$id}.";
} else {
    echo "Slug '{$givenSlug}' not found.";
}
