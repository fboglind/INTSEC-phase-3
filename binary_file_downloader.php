<?php
header('Content-Type: text/plain');
$file = '/passoire/my_own_cryptographic_algorithm';
if (file_exists($file)) {
    $content = file_get_contents($file);
    echo base64_encode($content);
}
?>
