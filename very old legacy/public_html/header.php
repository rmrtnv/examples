<?php
//, amelk81@mail.ru  yus.online@gmail.com, 
$mailto = 'amelk81@mail.ru, pshipovalov@gmail.com';
$mailadmin = 'admin@site.ru';
$MONTH = './month/';

	$GoodGroupMap = array(
	0 => 'FUEL92',
	1 => 'FUEL95',
	2 => 'dt',
	3 => '98',
	4 => 'FUELGAS',
	5 => 'tovary'
	);

?>

<head>
   <meta charset="utf-8">
   <title></title>  
    <!-- Bootstrap -->
	<link href="css/bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="style.css">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> -->
	<script src="js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src='js/bootstrap.min.js'></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- <script src="http://harvesthq.github.io/chosen/chosen.jquery.js"></script> -->
	<script src="js/chosen.jquery.js"></script>
	
    <script>
      $(function() {
        $('.chosen-select').chosen();
        $('.chosen-select-deselect').chosen({ allow_single_deselect: true });
      });
    </script>
	
  </head>
  
  <div class='container'>
  <!-- <a href="logout.php" style="position:fixed; right:1em; top:1em;">Выход</a> -->
  
<?php

function llog($line){
	$time = date("H:i:s");
	$line = $time.' '.$line."\r\n";
	echo $line;
	file_put_contents('/home/c/cs26881/site/public_html/logs/log_'.date("j.n.Y").'.txt', $line, FILE_APPEND);
}

function sendMsg($id, $msg){
	global $mailto;
	$to = getEmail($id);
	$subject = 'Система оповещения site.ru';
	$headers = "From: no-reply@site.ru \r\n" .
	"BCC: $mailto \r\n".
	"Content-Type:text/html;charset=utf-8 \r\n".
    "Reply-To: no-reply@site.ru \r\n" .
    'X-Mailer: PHP/' . phpversion();
        echo '('.$to.') '.$subject.' '.$msg;
		echo '<br>';
        mail($to, $subject, $msg, $headers);	
}


function getStatus($clid, $force){
	global $link;
	$map = array(
	1 => 'Договор заблокирован. ',
	2 => 'Рекомендуется пополнить баланс. ',
	3 => 'Договор действующий. ',
	);
	$sql = "SELECT * FROM accounts WHERE id = $clid;";
	$res = mysqli_query($link, $sql);
	while ($row = mysqli_fetch_assoc($res)){
		$cur_state = $row['fin_state'];
		if($row['balance'] < $row['lim']){
			$fin_state = 1;
		}
		elseif($row['balance'] < $row['war']){
			$fin_state = 2;
		}
		else{
			$fin_state = 3;
		}
	if($force){
		$sql = "UPDATE accounts SET fin_state = $fin_state WHERE id = '$clid'";
		//echo $sql;
		//echo '<br>';
		$res = mysqli_query($link, $sql);
		return $map[$fin_state];
	}
	elseif ($cur_state == $fin_state) return 0;
	else{
		$sql = "UPDATE accounts SET fin_state = $fin_state WHERE id = '$clid'";
		//echo $sql;
		//echo '<br>';
		$res = mysqli_query($link, $sql);
		return $map[$fin_state];
	}
	}
}

function getEmail($id){
	global $link;
	$sql = "SELECT login FROM accounts WHERE id = $id;";
	$res = mysqli_query($link, $sql);
	while ($row = mysqli_fetch_assoc($res)){
		return $row['login'];
	}
}

function getAccInfo($id, $field){
	global $link;
	$sql = "SELECT $field FROM accounts WHERE id = $id;";
	$res = mysqli_query($link, $sql);
	while ($row = mysqli_fetch_assoc($res)){
		return $row[$field];
	}
}

function clearRecord($table, $id){
	global $link;
	$s = 1;
	if($table == 'debit') $s = -1;
	$sql = "SELECT date, time, client_id, amount FROM $table WHERE id = '$id'";
	$query = mysqli_query($link, $sql);
	while($row = mysqli_fetch_assoc($query)){
		$amount = $row['amount'] * $s;
		$client_id = $row['client_id'];
		echo $amount;
		echo '<br>';
	}
	updateBalance($client_id, $amount);
	//$sql = "UPDATE accounts SET balance = (balance + $amount) WHERE id = '$id';";
	//$query = mysqli_query($link, $sql);
	deleteRecord($table, 'id', $id);
	
	$sql = "INSERT INTO log (tble, id, date, time) VALUES('$table', '$id', '$row[date]', '$row[time]')";
	$query = mysqli_query($link, $sql);
}

function getRecord($table, $key, $val){
	global $link;
	$sql = "SELECT * FROM $table WHERE $key = '$val';";
	//echo $sql;
	$query = mysqli_query($link, $sql);
	while($row = mysqli_fetch_assoc($query)){
		return $row;
	}
}

function deleteRecord($table, $key, $val){
	global $link;
	$sql = "DELETE FROM $table WHERE $key = '$val';";
	//echo $sql;
	$query = mysqli_query($link, $sql);
}

function getBalance($id){
	global $link;
	$sql = "SELECT balance FROM accounts WHERE id = '$id';";
	$query = mysqli_query($link, $sql);
	while($row = mysqli_fetch_assoc($query)){
		$balance = $row['balance'];
		return 'Баланс: '.$balance.' руб.';
	}
}

function updateBalance($client_id, $amount){
	global $link;
	$sql = "UPDATE accounts SET balance = (balance + $amount) WHERE id = '$client_id';";
	//echo $sql;
	$query = mysqli_query($link, $sql);
}

function delTempRecords(){
	global $link;
	$sql = "SELECT * FROM credit WHERE tmp = '1';";
	$query = mysqli_query($link, $sql);
	while($row = mysqli_fetch_assoc($query)){
		updateBalance($row['client_id'],$row['amount']);
		deleteRecord('credit', 'id', $row['id']);
	}
}



function refresh($id){
	global $link;
	$sql = "UPDATE accounts SET balance = '0.00' WHERE id = '$id';";
	$query = mysqli_query($link, $sql);
	$sql = "UPDATE credit SET counted = '0' WHERE client_id = '$id';";
	$query = mysqli_query($link, $sql);
	$sql = "UPDATE debit SET counted = '0' WHERE client_id = '$id';";
	$query = mysqli_query($link, $sql);
	
$query = mysqli_query($link, "SELECT * FROM credit WHERE counted = '0';");

if (mysqli_num_rows($query)==0) echo "<br>credit: новые записи отсутствуют";
	
while ($row = mysqli_fetch_assoc($query)){
	$clid = '';
	$res = mysqli_query($link, "SELECT * FROM cards WHERE number = '$row[card_number]' LIMIT 1");
        $res = mysqli_fetch_assoc($res);
        $clid = $res['id'];
        $clabout = $res['about'];
	if (empty($clid)){
		echo "<br>credit! карте №".$row[card_number]." не присвоен владелец, значения сумм не посчитаны";
		continue;
	}
	else {
		//mysqli_query($link, "UPDATE credit SET client_id = '$clid', client_about = '$clabout' WHERE id = '$row[id]';");
		$sql = "UPDATE accounts SET balance = (balance - $row[amount]) WHERE id = '$clid';";
		$res = mysqli_query($link, $sql);
		echo 'sql:'.$res.'<br>';
		echo $sql;
		echo '<br>';
		
	echo "<br>credit: новая запись по договору №".$clid.", сумма ".$row[amount]."";
	
	mysqli_query($link, "UPDATE credit SET counted = '1' WHERE id = '$row[id]'");
	//notify($clid);
	}
        
}
}

function clear($id){
	global $link;
	$sql = "UPDATE accounts SET balance = '0' WHERE id = '$id';";
	$query = mysqli_query($link, $sql);
	deleteRecord('credit', 'client_id', $id);
	deleteRecord('debit', 'client_id', $id);
}

function _notify($id){
	$day = 9;
	$night = 21;
	$now = date("H");
	if (($now < $day) || ($now > $night)) return;
	
	global $link;
	$sql = "SELECT * FROM accounts WHERE id = '$id'";
	$query = mysqli_query($link, $sql);
	while($row = mysqli_fetch_assoc($query)){
		 
	}
	
	//echo date("H");
	
}

function loadCSV($path){
	global $link;

if (file_exists($path)) {
if (filesize($path)>0){
$file = fopen($path, 'r');
while (($line = fgetcsv($file, 0, ';', '\n')) !== FALSE) {
  //$line is an array of the csv elements
  $tmp = 0;
  
  $o1 = 0;
  $o2 = 0;
  
  if($line[18] == 'Россия'){
	  if($line[12] == '0'){
		  $o1 = -1;
		  //echo '@18_@';
	  }
	  else $o2 = -1;
	  //echo '@18@';
  }
  if($line[17] == 'Россия'){
	  $o1 = -1;
	  $o2 = -1;
	  //echo '@17@';
  }

  //echo $o1;
  //echo $o2;
  
  $o2 = $o1 + $o2;
  

 
  $id = $line[0];
  $card_number = $line[1];
  
  $type = $line[$o1+5];
  
  $date = substr($line[$o1+6], 0, 10);
  $date = strtr ($date, array (',' => '-'));
  $date = strtotime($date);
  $date = date('Y-m-d',$date);
  
  $time = substr($line[$o1+6], -8);
  
  if ($line[$o1+8] == 'Запрос'){
	//echo 'no';
	//echo '<br>';
	$tmp = 1;
  } 
  else{
	//echo 'yes';
	//echo '<br>';
  }   

  $action = $line[$o1+9];
  
  $liters = $line[$o1+10];
  $liters = str_replace(',', '.', $liters);
  

  $name = $line[$o2+17];
  //$state = $line[$off+7];
  $state = $line[$o2+18];
  //$adress = $line[$off+9];
  $adress = $line[$o2+20];


  

  
  //$action = $line[$off+15];

  //$azs = $line[$off+18];
  $azs = $line[$o2+23];
  //$amount = $line[$off+24];
  $amount = $line[$o2+21];
  $amount = str_replace(',', '.', $amount);
  
  //$disc_am = $line[$off+25];
  //$disc_am = str_replace(',', '.', $disc_am);
  
  //$diff = (abs($amount)) - (abs($disc_am));
  //$diff = round(floatval($diff),2);
  //$disc = round(floatval($disc),2);
  //$diff -= $disc;
  //$diff = abs($diff);
  
  //$GoodGroupCode = $line[$off+30];
  
//UPDATE CREDIT TABLE
$query = <<<eof
  INSERT INTO credit
  (id, date, time, card_number, name, state, adress,  type, liters, action, azs, tmp, amount)
  VALUES ('$id', '$date', '$time', '$card_number', '$name', '$state', '$adress', '$type', '$liters', '$action', '$azs', '$tmp', '$amount')
eof;
//echo $query;
//echo '<br>';
$result = mysqli_query($link, $query);
}
fclose($file);
}
else echo "<br>file $fn empty";

} else {
    echo "<br>file $fn not found";
}
}

function loadXML(){
global $link;

$datadir = 'data/';

//------------------------------------------------OPEN CREDIT CSV

//$fn = 'credit.csv';
$fn = 'transactions.xml';
$path = $datadir.$fn;
//$wait = 30;

	if (file_exists($path)) {
	if (filesize($path)>0){
		
		$xml = simplexml_load_file($path, "SimpleXMLElement", LIBXML_NOCDATA);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		//print_r($array[node][0]);
	
	} else echo "<br>файл $fn пуст";
	}else echo "<br>файл $fn не найден ";

	foreach($array[node] as $el){
		echo $el[TransMatterName];
		echo '<br>';
		
		$id = $el[Id];
		$card_number = $el[CardNumber];
		$dt = $el[TransDate];
		  $date = substr($dt, 0, 10);
		  $date = strtr ($date, array (',' => '-'));
		  $date = strtotime($date);
		  $date = date('Y-m-d',$date);
		$time = substr($dt, -8);
		$name = $el[PartnerName];
		$state = $el[Region];
		$adress = $el[StreetAddress];
		$type = $el[GoodsName];
		$liters = $el[Quantity];
		  $liters = str_replace(',', '.', $liters);
		$action = $el[TransType];
		$azs = $el[OutletName];
		$amount = $el[StelaAmount];
		  $amount = str_replace(',', '.', $amount);
		$tmp = 0;
		if($el[RequestCategoryName] == 'Запрос') $tmp = 1;
		
$query = <<<eof
  INSERT INTO credit
  (id, date, time, card_number, name, state, adress,  type, liters, action, azs, tmp, amount)
  VALUES ('$id', '$date', '$time', '$card_number', '$name', '$state', '$adress', '$type', '$liters', '$action', '$azs', '$tmp', '$amount')
eof;
echo $query;
echo '<br>';
$result = mysqli_query($link, $query);
echo $result;

	}
}

function findDubles(){
	global $link;

$sql = <<<eof
select   MIN(id)
from     credit
group by date, time,
         amount
having   count(*) > 1
eof;


	$query = mysqli_query($link, $sql);
	while($res = mysqli_fetch_assoc($query)){
		//echo $row['id'];
		$id = $res['MIN(id)'];
		$row = getRecord('credit', 'id', $id);
		$date = $row['date'];
		$time = $row['time'];
		$counted = $row['counted'];
		$tmp = $row['tmp'];
			
		if($counted){
			//print_r($row);
			
			clearRecord('credit', $id);
			
			llog("clear duble $id : $date : $time : $counted : $tmp");
			echo "clear dublicate $id";
			echo '<br>';
		}
		else{
			//deleteRecord('credit', 'id', $id);
			
			llog("trying delete duble $id : $date : $time : $counted : $tmp");
			echo "delete dublicate $id";
			echo '<br>';
		} 
	}
}

?>