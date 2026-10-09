<?php

// Lossless copies retain exact dimensions for the floor-plan hit areas.
foreach (['kati-0', 'kati-1', 'kati-3', 'kati-4', 'outdoor'] as $name) {
    $source = __DIR__.'/../public/template/images/leasing/'.$name.'.png';
    $destination = substr($source, 0, -4).'.webp';
    $image = imagecreatefrompng($source);
    imagepalettetotruecolor($image);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    if (! imagewebp($image, $destination, IMG_WEBP_LOSSLESS)) {
        throw new RuntimeException('Could not encode '.$name);
    }
    if (array_slice(getimagesize($source), 0, 2) !== array_slice(getimagesize($destination), 0, 2)) {
        throw new RuntimeException('Dimensions changed for '.$name);
    }
    echo $name.': '.filesize($source).' -> '.filesize($destination)." bytes\n";
}
