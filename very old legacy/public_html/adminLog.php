<?php
header('Content-Type: text/html; charset=windows-1251');
echo "<a href='admin.php'>�����</a>";
include 'connectDB.php';

session_start();
if(!$_SESSION['admin']){
//header("Location: index.php");
echo '<script>window.location = "index.php";</script>';
exit;
}

$from = '';
$till = '';
if (empty($_GET['from'])) $from = date('Y-m-01');
else $from = $_GET['from'];
if (empty($_GET['till'])) $till = date('Y-m-d');
else $till = $_GET['till'];
?>
<form method='GET' action='adminLog.php'>
<input type='hidden' name='id' value="<?php echo $_GET['id']; ?>">
<input type='hidden' name='number' value="<?php echo $_GET['number']; ?>">
<input type="date" name="from" value="<?php echo $from; ?>" />
<input type="date" name="till" value="<?php echo $till; ?>" />
<input type='submit' value='������������'>
</form>

<?php
	$fp = fopen('clientOne.csv', 'wb');
	if (empty($_GET['number'])){
		$query = mysqli_query($link, "SELECT * FROM credit WHERE client_id = '$_GET[id]' AND date BETWEEN '".$from."' AND '".$till."';");
	}
	else {
		$query = mysqli_query($link, "SELECT * FROM credit WHERE card_number='$_GET[number]' AND client_id = '$_GET[id]' AND date BETWEEN '".$from."' AND '".$till."';");
	}
	if (mysqli_num_rows($query)==0) { echo "���������� ������� ���� ��������� ��������."; }
	else {
	echo "<table><tr><td><b>����</b></td><td><b>�����</b></td><td><b>� �����</b></td><td><b>����</b></td><td><b>�����</b></td><td><b>�����</b></td><td><b>�������</b></td><td><b>�����</b></td></tr>";
	while ($row = mysqli_fetch_assoc($query)){ 
		echo "<tr>";
		echo "<td>".$row['date']."</td><td>".$row['time']."</td><td>".$row['card_number']."</td><td>".$row['amount']."</td><td>".$row['liters']."</td><td>".$row['amount']."</td><td>".$row['type']."</td><td>".$row['adress']."</td>";
		echo "</tr>";
		fputcsv($fp, $row,';');
	}
	fclose($fp);
	echo "</table>";
	echo "<a href=clientOne.csv>������� ���� (csv)</a>";
	}
?>
