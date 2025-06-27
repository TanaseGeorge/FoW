<?php
$parola = 'parola'; // parola dorită
$hash = password_hash($parola, PASSWORD_DEFAULT);
echo $hash;
?>