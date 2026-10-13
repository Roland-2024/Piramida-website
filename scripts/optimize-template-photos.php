<?php

// Originals and dimensions remain intact; dashboard image keys do not change.
foreach (require __DIR__.'/../config/template_photos.php' as $path) {
    $source = __DIR__.'/../public/'.$path;
    $destination = substr($source, 0, strrpos($source, '.')).'.webp';
    $image = imagecreatefromstring(file_get_contents($source));
    if (! $image) {
        throw new RuntimeException('Could not decode '.$path);
    }
    imagepalettetotruecolor($image);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    if (! imagewebp($image, $destination, 82)) {
        throw new RuntimeException('Could not encode '.$path);
    }
    imagedestroy($image);
    if (array_slice(getimagesize($source), 0, 2) !== array_slice(getimagesize($destination), 0, 2)
        || filesize($destination) >= filesize($source)) {
        throw new RuntimeException('Copy must retain dimensions and be smaller: '.$path);
    }
    echo $path.': '.filesize($source).' -> '.filesize($destination)." bytes\n";
}
