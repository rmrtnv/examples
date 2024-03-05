<?php
include 'connectDB.php';
include 'header.php';

$sql = "SELECT id FROM accounts";
$res = mysqli_query($link, $sql);
while ($row = mysqli_fetch_assoc($res)){
	$id = $row['id'];
	
	if($id == '1') continue;
	if($id == 'test') continue;
	
	$name = getAccInfo($id, 'name');
	$msg  = $name.'. ';
	$msg .= getStatus($id, 1);
	$msg .= getBalance($id);
	echo $msg;
	echo '<br>';
	sendMsg($id, $msg);
}
?>