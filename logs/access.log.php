<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$file_location = 'C:/Program Files/Adobe/Adobe Photoshop CC 2017/Required/PDFL/Resource/Fonts/Programs/Apache24/logs/access.log';
$lines = explode("\r\n", file_get_contents($file_location));
foreach ($lines as &$line) {
    echo($line . "<br>");
}
?>