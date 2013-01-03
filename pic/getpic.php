<?php

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    function echoHeaders() {
        header("Cache-Control: private, max-age=10800, pre-check=10800");
        header("Pragma: private");
        header("Expires: " . date(DATE_RFC822, strtotime("2 day")));
        header('Content-type: image/jpeg');
    }

    function resizeImage($originalImage, $toWidth, $toHeight) {
        $toWidth = intval($toWidth);
        $toHeight = intval($toHeight);

        list($width, $height) = getimagesize($originalImage); 

        if ((!$toWidth && !$toHeight) || ($toWidth >= $width && $toHeight >= $height)) {
            echoHeaders();
            echo file_get_contents($originalImage);
            exit;
        }

        $xscale = $toWidth ? $width / $toWidth : 0;
        $yscale = $toHeight ? $height / $toHeight : 0;

        if ($yscale > $xscale) { 
            $new_width = round($width / $yscale); 
            $new_height = round($toHeight); 
        } 
        else { 
            $new_width = round($toWidth); 
            $new_height = round($height / $xscale); 
        } 
        
        $imageResized = imagecreatetruecolor($new_width, $new_height); 
        $imageTmp = imagecreatefromjpeg($originalImage); 
        imagecopyresampled($imageResized, $imageTmp, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 

        echoHeaders();
        imagejpeg($imageResized, NULL, 90);
    }

    if (isset($_GET['q'])) {
        $q = explode('/', $_GET['q'], 3);
        $src = '../pics/p' . $q[0] . '.jpg';
        list($w, $h) = explode('x', $q[1]);

        if (!file_exists($src)) {
            header('HTTP/1.1 404 Not Found');
            exit;
        }

        resizeImage($src, $w, $h);
        exit;
    }

?>
