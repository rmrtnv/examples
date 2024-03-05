<?php
include 'connectDB.php';
include 'header.php';
$id = $_GET['id'];
echo "refresh $id";
refresh($id);

?>