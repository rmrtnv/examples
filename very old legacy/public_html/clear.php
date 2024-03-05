<?php
include 'connectDB.php';
include 'header.php';
$id = $_GET['id'];
echo "clear records $id";
clear($id);

?>