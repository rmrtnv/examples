<?php

include 'connectDB.php';
include 'header.php';

session_start();
if(!$_SESSION['client']){
	echo '<script>window.location = "index.php";</script>';
	exit;
}

include 'clientHeader.php';

if (!isset($_GET['action'])){

?>
 <div class="row">

  <div class="col-xs-6 col-sm-4">

	<table class="table table-condensed table-responsive">
		<tbody>
			<tr>
				<td>
					Наименование
				</td>
				<td>
					<?php echo $name; ?>
				</td>
			</tr>
			<tr>
				<td>
					ИНН
				</td>
				<td>
					<?php echo $inn; ?>
				</td>
			</tr>
			<tr>
				<td>
					КПП
				</td>
				<td>
					<?php echo $kpp; ?>
				</td>
			</tr>
			<tr>
				<td>
					№ телефона
				</td>
				<td>
					<?php echo $phone; ?>
				</td>
			</tr>
			<tr>
				<td>
					Логин
				</td>
				<td>
					<?php echo $login; ?>
				</td>
			</tr>
		</tbody>
	</table>
	</div>

  <div class="col-xs-12 col-md-8">

<?php
	
	$query = mysqli_query($link, "SELECT * FROM debit WHERE client_id='$_GET[id]' AND YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())");
	$num_rows = mysqli_num_rows($query);
	if ($num_rows > 0){
		echo "<div class='table-responsive'>";
		echo "<table class='table'><tr><td><b>Дата</b></td><td><b>Время</b></td><td><b>№ платежа</b></td><td><b>Сумма</b></td></tr>";
		echo "<caption>Платежи за текущий месяц</caption>";
		while ($row = mysqli_fetch_assoc($query))
		{
			echo "<tr>";
			echo "<td>".$row['date']."</td>";
			echo "<td>".$row['time']."</td>";
			echo "<td>".$row['payment_number']."</td>";
			echo "<td>".$row['amount']."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";		
	}
	else{
		//echo '<p class="text-muted">Платежи отсутствуют</p>';
	}
echo '</div></div>';
}


if ($_GET['action'] == 'cards') {
	
	//echo "<a class='btn btn-default' href='clientLog.php?action=open&id=".$_GET['id']."'>Отчет по всем</a>";

	$query = mysqli_query($link, "SELECT * FROM cards WHERE id='$_GET[id]'");
	echo "<div class='table-responsive'>";
	echo "<table class='table'><tr><td><b>Номер карты</b></td><td><b>АИ92</b></td><td><b>АИ95</b></td><td><b>АИ98</b></td><td><b>ДТ</b></td><td><b>Газ</b></td><td><b>Товары</b></td><td><b>Примечание</b></td></tr>";
	//echo "<caption>Топливные карты</caption>";
	while ($row = mysqli_fetch_assoc($query))
	{
		$lim = getLimits($row['number']);
		echo "<tr>";
		echo "<td>".$row['number']."</td>";
		echo "<td>".$lim[0]."</td>";
		echo "<td>".$lim[1]."</td>";
		echo "<td>".$lim[3]."</td>";
		echo "<td>".$lim[2]."</td>";
		echo "<td>".$lim[4]."</td>";
		echo "<td>".$lim[5]."</td>";
		echo "<td>".$row['about']."</td>";
		echo "<td><a href='clientCards.php?action=open&number=".$row['number']."&id=".$_GET['id']."'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>";
		echo "<td><a href='clientLog.php?action=open&numbers%5B%5D=".$row['number']."&id=".$_GET['id']."'><span class='glyphicon glyphicon-file' aria-hidden='true'></span></a></td>";
		echo "<td><a href='client.php?action=block&number=".$row['number']."&id=".$_GET['id']."'><span class='glyphicon glyphicon-ban-circle' aria-hidden='true'></span></a></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</div>";

}

//echo "<tr><td></td><td></td><td></td><td><a href='clientLog.php?action=open&id=".$_GET['id']."'>Отчет по всем</a></td></tr>";

if ($_GET['action'] == 'block') {
	$to      = $mailto;
	$subject = 'Запрос на блокирование карты';
	$message = 'Клиент '.$name.' отправил запрос на блокирование карты №'.$_GET['number'].'';
	$headers = 'From: admin@magcard.ru' . "\r\n" .
    'Reply-To: ' . $login . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);
	
	echo "<br><br><i>Запрос на блокирование карты №<b>".$_GET['number']."</b> отправлен.</i>";
}

if ($_GET['action'] == 'help') {
	$to      = $mailto;
	$subject = 'Запрос на поддержку от '.$name;
	$message = $_POST['text'];
	$headers = 'From: admin@magcard.ru' . "\r\n" .
    'Reply-To: ' . $login . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
        //echo $_POST['text'];
	mail($to, $subject, $message, $headers);
	
	echo "<br><br><i>Ваше сообщение отправлено администратору.</i>";
}

echo "
<script type='text/javascript'>
jQuery(document).ready(function(){
    jQuery('#hideshow').on('click', function(event) {        
         jQuery('#fuelCards').toggle('show');
    });
});
</script>
";

function getLimits($number){
	global $link;
	
	$result = array();
	$error = "<br>$number !bad<br>";
	
	$goods = array(
	0 => '92',
	1 => '95',
	2 => 'Диз',
	3 => '98',
	4 => 'Газ',
	5 => 'товары'
	);
	
	$sql = "SELECT * FROM cards WHERE number = '$number'";
	$query = mysqli_query($link, $sql);

	while($row = mysqli_fetch_assoc($query)){
		
		$fuel = $row['fuel'];
		
		if($fuel){
			$vol = explode(',', $row['vol']);
			$type = explode(',', $row['selvol']);
			$time = explode(',', $row['time']);
			$fuel = explode(',', $row['fuel']);
			foreach ($goods as $key => $value){
				if($fuel[$key]){
					
					$volume = $vol[$key];
					if(!$volume){
						array_push($result, 'х');
						continue;
					} 

					$sql = "SELECT SUM(";
					
					if($type[$key]){
						if($type[$key] == 'rub'){
							$sql .= 'amount';
							$_type = 'руб';
						} 
						if($type[$key] == 'lit'){
							$sql .= 'liters';
							$_type = 'лит';
						} 
					}
					else{
						//echo $error;
						continue;
					}
					
					
					if ($time[$key]){
						$sql .= ") AS sum FROM credit WHERE card_number = '$number' AND date BETWEEN ";
						
						if($time[$key] == 'day'){
							$start = date('Y-m-d', strtotime('now'));
							$left = 'день';
						} 
						
						if($time[$key] == 'week'){
							$start = date('Y-m-d', strtotime('monday this week'));
							$left = 'неделя';
						} 
						
						if($time[$key] == 'month'){
							$start = date('Y-m-d', strtotime('first day of this month'));
							$left = 'месяц';
						} 
						
						if($sql) $sql .= "'$start'";
						else{
							//echo "DB format error @ $number";
							continue;
						} 
						
						$end = date('Y-m-d', strtotime('now'));
						$sql .= " AND '$end' ";
					}
					else{
						//echo $error;
						continue;
					}
					
					$sql .= "AND type LIKE '%$value%'";
					
					//echo '<br>';
					//echo date("Y-m-d h:i");
					
					//echo $sql;
					$query = mysqli_query($link, $sql);
					$row = mysqli_fetch_assoc($query);
					//print_r($row);
					//echo $row['sum'];
					if($value == 'Диз') $value = 'ДТ';
					
					
					$sum = (int)$row['sum'];
					$sum = (int)$volume - $sum;
					
					//array_push($result, "<font color='brown'>$sum</font> из <font color='slateblue'>$volume</font><br> $_type/$left");
					array_push($result, "<b>$sum</b> из $volume<br>$_type/$left");
					continue;
					//$result .= "($value: <b>$sum</b>/$volume $_type/$left)";
					//$result .= "<font color='grey'>(<font color='#656565'><b>$value</b></font>: <font color='brown'>$sum</font>/<font color='slateblue'>$volume</font> $_type/$left)</font>";
				}
			array_push($result, '');
			}
		return $result;
		}

	}
}


?>
