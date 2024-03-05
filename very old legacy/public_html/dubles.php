<?php

include 'connectDB.php';
include 'header.php';



$sql = <<<eof
select   id, date, time,
         amount,
         count(*)
from     credit
group by date, time,
         amount
having   count(*) > 1
eof;

	if (!empty($_GET['id'])){
		$id = $_GET['id'];
		
		if (!empty($_GET['method'])){
			$id = $_GET['id'];
			clearRecord('credit', $id);
			echo 'clear record';
		}
		else{
			echo "delete record $id";
			deleteRecord('credit', 'id', $id);
		}

	echo '<br>';
	} 

	$query = mysqli_query($link, $sql);
	while($row = mysqli_fetch_assoc($query)){
		//echo $row['time'];
		$s = "SELECT * FROM `credit` WHERE time = '$row[time]'";
		$q = mysqli_query($link, $s);
			while($res = mysqli_fetch_assoc($q)){
				$balance = getBalance($res['client_id']);
				$print = $res['id'].' ('.$balance.' р.) '.$res['date'].' '.$res['time'].' '.$res['amount'].' '.$res['added'];
				$print .= ' <a href=dubles.php?id='.$res['id'].'&method=clear>Удалить и пересчитать баланс</a>';
				$print .= ' <a href=dubles.php?id='.$res['id'].'>Удалить</a>';
				echo $print;
				echo '<br>';
			}
		echo '<br>';
		echo '<hr>';
	}

	//else echo 'not';
?>