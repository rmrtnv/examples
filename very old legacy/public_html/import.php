LOAD DATA LOCAL INFILE  'http://sa26704-29540.smrtp.ru/cards.csv' INTO TABLE credit CHARACTER SET utf8
FIELDS TERMINATED BY  ';' LINES TERMINATED BY  '\n'
(@dummy, id, @date, time, card_number,@dummy, @dummy, @dummy, @dummy, adress, type, liters, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, @dummy, amount)
SET date = STR_TO_DATE(LEFT(@date, 10), '%d,%m,%Y');


<?php

	$link = mysql_connect('mysql.hostinger.ru', 'u989286729_admin', 'xn#+]T`i$F0d;!8Yry');
	if (!$link) {
		die('?????? ??????????: ' . mysql_error());
	}
	mysql_select_db('u989286729_base');
$fileName = "cards.csv";
$query = <<<eof
    LOAD DATA LOCAL INFILE '$fileName'
     INTO TABLE credit
     FIELDS TERMINATED BY '|' OPTIONALLY ENCLOSED BY '"'
     LINES TERMINATED BY '\n'
	 IGNORE 1 ROWS;
eof;

$result = mysql_query($query) or die(mysql_error());
?>