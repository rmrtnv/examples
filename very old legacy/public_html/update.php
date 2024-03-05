<?php
include 'connectDB.php';
include 'header.php';
echo "
<nav>
  <ul class='pager'>
    <li class='previous'><a href='admin.php'><span aria-hidden='true'>&larr;</span> Назад</a></li>
  </ul>
</nav>
";

$datadir = '/home/c/cs26881/site/public_html/data/';
$fn = 'credit.csv';
$path = $datadir.$fn;

	if(isset($_GET['man'])){
		echo 'man..';
	}
	else {
		file_put_contents($path, fopen("http://shalashi.ddns.net:8080/cards.csv", 'r'));
		echo 'dl..';
	}
	

	
//delTempRecords();

	//$datadir = 'data/';
	//$fn = 'credit.csv';
	//$path = $datadir.$fn;

loadCSV($path);

findDubles();

//------------------------------------------------COUNT NEW ROWS, UPDATE ACCOUNTS AND CREDIT TABLES

$credit_arr = array();
$debit_arr = array();

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
		mysqli_query($link, "UPDATE credit SET client_id = '$clid', client_about = '$clabout' WHERE id = '$row[id]';");
		$sql = "UPDATE accounts SET balance = (balance - $row[amount]) WHERE id = '$clid';";
		$res = mysqli_query($link, $sql);
		if($res){
			mysqli_query($link, "UPDATE credit SET counted = '1' WHERE id = '$row[id]'");
			echo "<br>credit: новая запись по договору №".$clid.", сумма ".$row[amount]."";
			$credit_arr[] = $clid;
			$credit_arr[] = $clid;
		} 
		else {
			echo 'Ошибка при попытке обновления баланса';
		}
		//echo 'sql:'.$res.'<br>';
		//echo $sql;
		//echo '<br>';
	//notify($clid);
	}
        
}
$credit_arr = array_unique($credit_arr);
//echo '@76<br>';
//print_r($credit_arr);


//echo '<168>';
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
else echo "<br>файл $fn пуст";
} else {
    echo "<br>файл $fn не найден";
}
//echo '<199>';

//------------------------------------------------COUNT NEW ROWS, UPDATE ACCOUNTS AND DEBIT TABLES

$query = mysqli_query($link, "SELECT * FROM debit WHERE counted = '0';");
if (mysqli_num_rows($query)==0) { echo "<br>debit: новые записи отсутствуют."; }

while ($row = mysqli_fetch_assoc($query)){
	
	$res = mysqli_query($link, "SELECT * FROM accounts WHERE id = '$row[client_id]';");
	
	if (mysqli_num_rows($res)==0) {
		echo "<br>debit: отсутствует запись о клиенте по договору №".$row['client_id'].", значения сумм не посчитаны";
	}
	else{
		$res = mysqli_query($link, "UPDATE accounts SET balance = (balance + $row[amount]) WHERE id = '$row[client_id]'");
		//$res = mysqli_query($link, "UPDATE debit SET counted = '1' WHERE id = '$row[id]'");
		echo "<br>debit: новая запись по договору №".$row[client_id].", сумма ".$row[amount]."";

		$res = mysqli_query($link, "UPDATE debit SET counted = '1' WHERE id = '$row[id]'");
		
		$msg  = getAccInfo($row['client_id'], 'name');
		$msg .= ". Зачислено $row[amount] руб. ";
		$msg .= getStatus($row['client_id'], 1);
		$msg .= getBalance($row['client_id']);
		sendMsg($row['client_id'], $msg);
		
		$debit_arr[] = $row['client_id'];
	}
        
}

$credit_arr = array_diff($credit_arr, $debit_arr);
//echo '@141<br>';
//print_r($credit_arr);


foreach($credit_arr as $id){
	$msg = getStatus($id, 0);
	if($msg){
		$pre  = getAccInfo($id, 'name');
		$msg  = $pre.'. '.$msg;
		$msg .= getBalance($id);
		sendMsg($id, $msg);
	}
}




//----------------------------------------------->
//CHECK BALANCE LIMIT


$query = mysqli_query($link, "SELECT * FROM accounts WHERE balance < war OR balance < lim;");
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
        //mail($to, $subject, $message, $headers);
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