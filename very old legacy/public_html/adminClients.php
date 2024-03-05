<?php
include 'connectDB.php';
include 'header.php';
session_start();
if(!$_SESSION['admin']){
//header("Location: index.php");
echo '<script>window.location = "index.php";</script>';
exit;
}

$sys_id = "";
$id = "";
$login = "";
$password = "";
$name = "";
$inn = "";
$about = "";
$war = '0.0';
$lim = '0.0';
$date = "";
$kpp = "";
$contact = "";
$phone = "";


$action = 'insert';

if ($_GET['action'] == 'update') {
	$query = mysqli_query($link, "UPDATE accounts SET login='$_GET[login]', password='$_GET[password]', name='$_GET[name]', inn='$_GET[inn]', kpp='$_GET[kpp]', contact='$_GET[contact]', phone='$_GET[phone]', date='$_GET[date]', about='$_GET[about]', war='$_GET[war]', lim='$_GET[lim]', id='$_GET[id]' WHERE sys_id='$_GET[sys_id]'");
	if ($query === false) {
    // And error has occured while executing
    // the SQL query
	}
	$_GET['action'] = 'open';
}

if ($_GET['action'] == 'insert') {
	$query = mysqli_query($link, "INSERT INTO accounts (id, login, password, name, inn, kpp, contact, phone, date, about, war, lim) VALUES ('$_GET[id]', '$_GET[login]', '$_GET[password]', '$_GET[name]', '$_GET[inn]', '$_GET[kpp]', '$_GET[contact]', '$_GET[phone]', '$_GET[date]', '$_GET[about]', '$_GET[war]', '$_GET[lim]')");
	echo "Запись добавлена";
}

if ($_GET['action'] == 'open') {
	$query = mysqli_query($link, "SELECT * FROM accounts WHERE id ='".$_GET['id']."'");
	while ($row = mysqli_fetch_assoc($query)){
                $sys_id = $row['sys_id'];
		$id = $row['id'];
		$login = $row['login'];
		$password = $row['password'];
		$name = $row['name'];
		$inn = $row['inn'];
		$about = $row['about'];
                $war = $row['war'];
		$lim = $row['lim'];
                $kpp = $row['kpp'];
                $contact = $row['contact'];
                //if (empty($row['date'])) $date = date('Y-m-d');
                $date = $row['date'];
                $phone = $row['phone'];
                $action = 'update';
	}
}

echo "
<nav>
  <ul class='pager'>
    <li class='previous'><a href='admin.php'><span aria-hidden='true'>&larr;</span> Назад</a></li>
  </ul>
</nav>
";

echo "
<form method='GET' action='adminClients.php'>
№ договора:<br>
<input name='id' value='".$id."'><br>
Наименование:<br>
<input name='name' value='".$name."'><br>
ИНН:<br>
<input name='inn' value='".$inn."'><br>
КПП:<br>
<input name='kpp' value='".$kpp."'><br>
Контакт:<br>
<input name='contact' value='".$contact."'><br>
Телефон:<br>
<input name='phone' value='".$phone."'><br>
Логин(email):<br>
<input name='login' value='".$login."'><br>
Пароль:<br>
<input name='password' value='".$password."'><br>
Лимит1(приближение):<br>
<input name='war' value='".$war."'><br>
Лимит2(блокировка):<br>
<input name='lim' value='".$lim."'><br>
Дата договора:<br>
<input type='date' name='date' value='".$date."'><br>
Примечание:<br>
<textarea rows='10' cols='45' name='about'>".$about."</textarea><br><br>
<input type='hidden' name='action' value='".$action."'>
<input type='hidden' name='sys_id' value='".$sys_id."'>
<input class='btn btn-default' type='submit' value='Добавить / Редактировать'>
</form>
";

?>