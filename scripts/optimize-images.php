<?php
// Rebuild responsive assets from the original files; requires PHP GD + WebP.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (!function_exists('imagewebp')) {
    fwrite(STDERR, "PHP GD with WebP support is required.\n");
    exit(1);
}
$root = dirname(__DIR__) . '/assets/img/';
$outputs = [
    'header1' => [640, 960, 1536],
    'header2' => [640, 960, 1536],
    'bungatangan' => [640, 960],
    'logoWF' => [96, 180],
];
foreach ($outputs as $name => $widths) {
    $source = imagecreatefrompng($root . $name . '.png');
    foreach ($widths as $width) {
        $targetWidth = min($width, imagesx($source));
        $height = (int) round(imagesy($source) * $targetWidth / imagesx($source));
        $output = imagecreatetruecolor($targetWidth, $height);
        imagealphablending($output, false);
        imagesavealpha($output, true);
        imagefill($output, 0, 0, imagecolorallocatealpha($output, 0, 0, 0, 127));
        imagecopyresampled($output, $source, 0, 0, 0, 0, $targetWidth, $height, imagesx($source), imagesy($source));
        $file = $root . $name . '-' . $width . '.webp';
        if (!imagewebp($output, $file, $name === 'logoWF' ? 88 : 78)) {
            throw new RuntimeException('Could not write ' . $file);
        }
        printf("%s: %s bytes (%dx%d)\n", basename($file), number_format(filesize($file)), $targetWidth, $height);
        imagedestroy($output);
    }
    if (in_array($name, ['header1', 'header2'], true)) {
        // Art direction: retain the same centered cover crop on tall mobile screens.
        $cropHeight = imagesy($source);
        $cropWidth = (int) round($cropHeight * .8);
        $mobile = imagecreatetruecolor(640, 800);
        imagecopyresampled($mobile, $source, 0, 0, (int) ((imagesx($source) - $cropWidth) / 2), 0, 640, 800, $cropWidth, $cropHeight);
        imagewebp($mobile, $root . $name . '-mobile.webp', 78);
        printf("%s-mobile.webp: %s bytes\n", $name, number_format(filesize($root . $name . '-mobile.webp')));
        imagedestroy($mobile);
    }
    imagedestroy($source);
}
