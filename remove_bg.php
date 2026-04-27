<?php
$srcFile = 'public/images/logo-balmon-lampung.jpg';
$destFile = 'public/images/logo-balmon-lampung-transparent.png';

$img = imagecreatefromjpeg($srcFile);
if (!$img) {
    die("Failed to load image");
}

$width = imagesx($img);
$height = imagesy($img);

// Create a new image with alpha channel
$png = imagecreatetruecolor($width, $height);
imagesavealpha($png, true);
$transparent = imagecolorallocatealpha($png, 0, 0, 0, 127);
imagefill($png, 0, 0, $transparent);

// Iterate through pixels and copy, replacing white-ish pixels with transparent
for ($x = 0; $x < $width; $x++) {
    for ($y = 0; $y < $height; $y++) {
        $rgb = imagecolorat($img, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        // If the pixel is very light (close to white), make it transparent
        // Threshold: 235
        if ($r > 240 && $g > 240 && $b > 240) {
            // transparent
            imagesetpixel($png, $x, $y, $transparent);
        } else {
            // copy the pixel
            $color = imagecolorallocatealpha($png, $r, $g, $b, 0);
            imagesetpixel($png, $x, $y, $color);
        }
    }
}

imagepng($png, $destFile);
imagedestroy($img);
imagedestroy($png);
echo "Success\n";
