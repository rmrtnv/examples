<?php
include 'connectDB.php';
include 'header.php';

session_start();
if(!$_SESSION['admin']){
//header("Location: index.php");
echo '<script>window.location = "index.php";</script>';
exit;
}

echo "
<nav>
  <ul class='pager'>
    <li class='previous'><a href='admin.php'><span aria-hidden='true'>&larr;</span> Назад</a></li>
  </ul>
</nav>
";

echo "
<form method='GET' action='adminCards.php'>
Номер карты:<br>
<input name='number'>
<input type='hidden' name='action' value='add'>
<input type='hidden' name='id' value='".$_GET['id']."'>
<input type='submit' value='Добавить'>
</form>
";

if ($_GET['action'] == 'delete') {
	$query = mysqli_query($link, "DELETE FROM cards WHERE number='$_GET[number]'");
}

if ($_GET['action'] == 'add') {
	$query = mysqli_query($link, "INSERT INTO cards (number, id) VALUES ('$_GET[number]', '$_GET[id]')");
}

$query = mysqli_query($link, "SELECT * FROM cards WHERE id ='".$_GET['id']."'");
echo "<table class='table'><tr><td><b>Номер карты</b></td><td><b>Примечание клиента</b></td></tr>";
echo "<caption>Топливные карты</caption>";
while ($row = mysqli_fetch_assoc($query)){ 
	echo "<tr>";
	echo "<td>".$row['number']."</td><td>".$row['about']."</td><td><a href='adminCards.php?action=delete&number=$row[number]&id=$_GET[id]'>Удалить</a></td>";
	echo "</tr>";
}
echo "</table>";
?>