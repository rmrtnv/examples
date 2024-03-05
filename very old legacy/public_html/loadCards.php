<?php

$datadir = '/home/c/cs26881/site/public_html/data/';
$fn = 'credit.csv';
$path = $datadir.$fn;

file_put_contents($path, fopen("http://shalashi.ddns.net:8080/cards.csv", 'r'));

include 'autoUpdate.php';
?>