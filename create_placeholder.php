<?php
/**
 * Create Placeholder Image Script
 * 
 * This script creates a placeholder image for the meme generator
 */

// Set the image dimensions
$width = 600;
$height = 400;

// Create a blank image
$image = imagecreatetruecolor($width, $height);

// Define colors
$bg_color = imagecolorallocate($image, 240, 240, 240);
$text_color = imagecolorallocate($image, 80, 80, 80);
$border_color = imagecolorallocate($image, 200, 200, 200);

// Fill the background
imagefill($image, 0, 0, $bg_color);

// Draw a border
imagerectangle($image, 0, 0, $width - 1, $height - 1, $border_color);

// Add text
$text = "Select a template";
$font_size = 5; // Built-in font size (1-5)
$text_width = imagefontwidth($font_size) * strlen($text);
$text_height = imagefontheight($font_size);
$text_x = ($width - $text_width) / 2;
$text_y = ($height - $text_height) / 2;

// Add text to the image
imagestring($image, $font_size, $text_x, $text_y, $text, $text_color);

// Add a second line of text
$text2 = "MemeNews";
$text2_width = imagefontwidth($font_size) * strlen($text2);
$text2_x = ($width - $text2_width) / 2;
$text2_y = $text_y + $text_height + 10;

// Add second text to the image
imagestring($image, $font_size, $text2_x, $text2_y, $text2, $text_color);

// Set the content type header
header('Content-Type: image/jpeg');

// Save the image to a file
$target_file = __DIR__ . '/img/placeholder.jpg';
imagejpeg($image, $target_file, 90); // 90 is the image quality (0-100)

// Free up memory
imagedestroy($image);

echo "Placeholder image created at: $target_file";
?> 