<?php 
$mailto = 'yus.online@gmail.com, pshipovalov@gmail.com';
$mailadmin = 'admin@site.ru';
$MONTH = './month/';
include 'header.php';
$link=mysqli_connect("localhost", "cs26881_site", "1", "cs26881_site");

	if (!$link) {
		die('ERROR' . mysql_error());
	}

$datadir = '/home/c/cs26881/site/public_html/data/';


delTempRecords();
//------------------------------------------------OPEN CREDIT CSV

$fn = 'credit.csv';
$path = $datadir.$fn;
//$wait = 120;

file_put_contents($path, fopen("http://shalashi.ddns.net:8080/cards.csv", 'r'));

if (file_exists($path)) {
if (filesize($path)>0){
$file = fopen($path, 'r');
while (($line = fgetcsv($file, 0, ';', '\n')) !== FALSE) {
  //$line is an array of the csv elements
  
  $o = 0;
  if ($line[17] == 'Россия') $o = -1;
  if ($line[16] == 'Россия'){
	  break;
	  echo 'old';
  }
  if ($line[19] == 'Россия'){
	  break;
	  echo 'old';
  }

  $id = $line[0];
  
  $date = substr($line[$o+6], 0, 10);
  $date = strtr ($date, array (',' => '-'));
  
  $date = strtotime($date);
  $date = date('Y-m-d',$date);
  
  $time = substr($line[$o+6], -8);
  $card_number = $line[1];
  
 // $datetime1 = $datetime1.' '.$line[3];
  //$datetime1 = strtotime($datetime1);
  //$now = date('Y-m-d H:i:s');
 // $now = strtotime($now);
  //$diff = round(($now - $datetime1) / 60,2);
  
  //if($diff < $wait){
	//  continue;
  //}
  
  //echo $diff.'<br>';
  
  //$name = $line[$off+6];
  $name = $line[$o+17];
  //$state = $line[$off+7];
  $state = $line[$o+18];
  //$adress = $line[$off+9];
  $adress = $line[$o+20];
  //$type = $line[$off+10];
  $type = $line[$o+5];
  //$liters = $line[$off+11];
  $liters = $line[$o+10];
  $liters = str_replace(',', '.', $liters);
  
  //$disc = $line[$off+14];
  //$disc = str_replace(',', '.', $disc);

  //$disc = abs($disc);
  
  //$disc_ = $line[$off+23];
  //$disc_ = str_replace(',', '.', $disc_);
  //$disc_ = round($disc_, 2, PHP_ROUND_HALF_UP);
  //$disc_ = abs($disc_);
  
  //$diff_ = $disc - $disc_;
  //$diff_ = abs($diff_);

  $tmp = 0;
  
  if ($line[$o+8] == 'Запрос'){
	//echo 'no';
	//echo '<br>';
	$tmp = 1;
  } 
  else{
	//echo 'yes';
	//echo '<br>';
  } 
  
  //$action = $line[$off+15];
  $action = $line[$o+9];
  //$azs = $line[$off+18];
  $azs = $line[$o+23];
  //$amount = $line[$off+24];
  $amount = $line[$o+21];
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
echo $query;
echo '<br>';
//$result = mysqli_query($link, $query);
}
fclose($file);
}
else echo "<br>file $fn empty";

} else {
    echo "<br>file $fn not found";
}



//------------------------------------------------COUNT NEW ROWS, UPDATE ACCOUNTS AND CREDIT TABLES

$query = mysqli_query($link, "SELECT * FROM credit WHERE counted = '0';");

if (mysqli_num_rows($query)==0) echo "<br>credit: no new records";
	
while ($row = mysqli_fetch_assoc($query)){
	$clid = '';
	$res = mysqli_query($link, "SELECT * FROM cards WHERE number = '$row[card_number]' LIMIT 1");
        $res = mysqli_fetch_assoc($res);
        $clid = $res['id'];
        $clabout = $res['about'];
	if (empty($clid)) echo "<br>credit! card ".$row[card_number]." has no owner";
	else {
		$res = mysqli_query($link, "UPDATE accounts SET balance = (balance - $row[amount]) WHERE id = '$clid'");
		$res = mysqli_query($link, "UPDATE credit SET client_id = '$clid', client_about = '$clabout' WHERE id = '$row[id]'");
	echo "<br>credit: new record ".$clid.", amount ".$row[amount]."";
	}
        $res = mysqli_query($link, "UPDATE credit SET counted = '1' WHERE id = '$row[id]'");
}

//----------------------------------------------->
//----------------------------------------------->
//------------------------------------------------OPEN DEBIT CSV
$fn = 'debit.csv';
$path = $datadir.$fn;

if (file_exists($path)) {
if (filesize($path)>0){
$file = fopen($path, 'r');
while (($line = fgetcsv($file, 0, ';', '\n')) !== FALSE) {
  //$line is an array of the csv elements
  $date = substr($line[2], 0, 10);
  $date = strtr ($date, array (',' => '-'));
  $date = strtotime($date);
  $date = date('Y-m-d',$date);
  $amount = str_replace(',', '.', $line[6]);
//UPDATE DEBIT TABLE
$query = <<<eof
  INSERT INTO debit
  (payment_number, id, date, time, name, inn, amount, client_id)
  VALUES ('$line[0]', '$line[1]', '$date', '$line[3]', '$line[4]', '$line[5]', '$amount', '$line[7]')
eof;
$result = mysqli_query($link, $query);
}
fclose($file);
}
else echo "<br>file $fn empty";
} else {
    echo "<br>file $fn not found";
}


//------------------------------------------------COUNT NEW ROWS, UPDATE ACCOUNTS AND DEBIT TABLES

$query = mysqli_query($link, "SELECT * FROM debit WHERE counted = '0';");
if (mysqli_num_rows($query)==0) { echo "<br>debit: no new records."; }

while ($row = mysqli_fetch_assoc($query)){
	
	$res = mysqli_query($link, "SELECT * FROM accounts WHERE id = '$row[client_id]';");
	
	if (mysqli_num_rows($res)==0) {
		echo "<br>debit: no client record with id ".$row['client_id'].", amount not calculated";
	}
	else{
		$res = mysqli_query($link, "UPDATE accounts SET balance = (balance + $row[amount]) WHERE id = '$row[client_id]'");
		//$res = mysqli_query($link, "UPDATE debit SET counted = '1' WHERE id = '$row[id]'");
		echo "<br>debit: new record ".$row[client_id].", amount ".$row[amount]."";
	}
        $res = mysqli_query($link, "UPDATE debit SET counted = '1' WHERE id = '$row[id]'");
}

//----------------------------------------------->
//CHECK BALANCE LIMIT

$query = mysqli_query($link, "SELECT * FROM accounts WHERE balance < war;");
if (mysqli_num_rows($query)==0) {
        
	echo "<br>limit: ok.";
}
else {
	echo "<br>";
	while ($row = mysqli_fetch_assoc($query)){
            if ($row['balance'] < $row['lim'])
            {
                $text = 'Клиент '.$row['name'].' (id '.$row['id'].') достиг порога блокировки! Текущий баланс = '.$row['balance'].', лимит = '.$row['lim'];
            }
            else
            {
                $text = 'Клиент '.$row['name'].' (id '.$row['id'].') приближается к отключению! Текущий баланс = '.$row['balance'].', лимит = '.$row['lim'];
            }
	$to      = $mailto;
	$subject = 'Превышение по лимитам';
	$headers = 'From: ' . $mailadmin . "\r\n" .
    'Reply-To: '.$row['login'] . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
	$message = $text;
        echo $text;
        mail($to, $subject, $message, $headers);
	}
}

//----------------------------------------------->
//----------------------------------------------->

//example code
$query = <<<eof
    LOAD DATA LOCAL INFILE '$fileName'
    INTO TABLE credit CHARACTER SET utf8
    FIELDS TERMINATED BY  ';' LINES TERMINATED BY  '\n'
	(@dummy, id, @date, time, card_number,@dummy, @dummy, @dummy, @dummy, adress, type, liters, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, amount)
	SET date = STR_TO_DATE(LEFT(@date, 10), '%d,%m,%Y');
eof;
?>