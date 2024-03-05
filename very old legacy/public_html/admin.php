<?php
session_start();

//echo  $_SESSION['admin'];
if(!isset($_SESSION['admin'])){
//header("Location: index.php");
echo '<script>window.location = "index.php";</script>';
exit;
}
include 'connectDB.php';

include 'header.php';
$balance = 0;
$month_sum = 0;

$query = mysqli_query($link, "SELECT balance FROM accounts");
		while ($row = mysqli_fetch_assoc($query))
	   {
			$balance += $row['balance'];
	   }

echo "<a class='btn btn-default' href='adminClients.php' role='button'>Новый клиент</a>";
echo "<a class='btn btn-default' href='update.php?man=true' role='button'>Обновить таблицы</a>";
echo "
<form action='admin.php' method='post' enctype='multipart/form-data'>
  <div class='form-group'>
    <label for='userfile'>Транзакционный отчет</label>
    <input type='file' id='userfile' name='userfile[]'>
    <p class='help-block'>Только файлы CSV</p>
  </div>
  <div class='form-group'>
    <label for='userfile1'>1С отчет</label>
    <input type='file' id='userfile1' name='userfile[]'>
    <p class='help-block'>Только файлы CSV</p>
  </div>
		
		<input class='btn btn-default' type='submit' value='Отправить' name ='submit'>
		</form>


";

$query = mysqli_query($link, "SELECT * FROM accounts");
echo "<form action='admin.php' method='post' enctype='multipart/form-data'>";
echo "<div class='table-responsive'>";
	echo "<table class='table'><tr><td><b>№</b></td><td><b>Наим.</b></td><td><b>Прим.</b></td><td><b>Расход</b></td><td><b>Баланс</b></td><td><b>Лимит1</b></td><td><b>Лимит2</b></td></tr>";
		echo "<caption>Клиенты</caption>";
		while ($row = mysqli_fetch_assoc($query))
	   {	
                        $squery = mysqli_query($link, "SELECT SUM(liters) AS month FROM credit WHERE YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW()) AND client_id = '$row[id]'");
                        $month = mysqli_fetch_assoc($squery);
                        //print_r($squery);
                        $month = $month['month'];
						$month = round($month, 2);
						$month_sum += $month;
						
						if($row['balance'] >= 0) $plus_sum += $row['balance'];
						else $minus_sum += $row['balance'];
						
			echo "<tr>";
			echo "<td><a href='admin.php?clid=".$row['id']."'>".$row['id']."</a></td>";
			echo "<td>".$row['name']."</td><td>".$row['about']."</td><td>".$month."</td>";
			echo "<td><a href='admin.php?msg=".$row['id']."'>".$row['balance']."</a></td>";
			echo "<td>".$row['war']."</td><td>".$row['lim']."</td>";
			echo "<td><a href='adminClients.php?action=open&id=".$row['id']."' title='Редактирование'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a></td>";
			echo "<td><a href='adminCards.php?action=open&id=".$row['id']."' title='Управление картами'><span class='glyphicon glyphicon-credit-card' aria-hidden='true'></span></a></td>";
                        echo "<td><input type='file' name='adminfile[".$row['id']."]'></td>";
			echo "</tr>";
	   }
	   echo "<tr><td></td><td></td><td></td><td><b>".$month_sum."</b></td><td><b>".$balance."</b></td><td></td><td></td><td></td><td><input class='btn btn-default' type='submit' value='Отправить' name ='submit'></td><td></td></tr>";
	   echo "<tr><td></td><td></td><td></td><td><b></b></td><td><b>".$minus_sum."</b></td><td></td><td></td><td></td><td></td><td></td></tr>";
	   echo "<tr><td></td><td></td><td></td><td><b></b></td><td><b>".$plus_sum."</b></td><td></td><td></td><td></td><td></td><td></td></tr>";
	echo "</table>";
echo "</div>";
echo "</form>";

if(isset($_GET['msg'])) {
	$id = $_GET['msg'];
	$name = getAccInfo($id, 'name');
	$msg  = $name.'. ';
	$msg .= getStatus($id, 1);
	$msg .= getBalance($id);
	//echo $msg;
	sendMsg($id, $msg);
	//echo "<script>window.location.href = 'admin.php?echo=Сообщение отправлено';</script>";
}

if(isset($_POST["submit"])) {
	$uploaddir = './data/';
	
	if (!empty($_FILES['userfile']['name'][0])){
		
		$tmpFilePath = $_FILES['userfile']['tmp_name'][0];
		$newFilePath = $uploaddir."credit.csv";
		
		if (move_uploaded_file($tmpFilePath, $newFilePath)) {
			echo "<br>credit: Файл успешно загружен.";
		} else {
			echo "<br>credit: Файл не загружен! Повторите попытку.";
		}
	}
	if (!empty($_FILES['userfile']['name'][1])){
		
		$tmpFilePath = $_FILES['userfile']['tmp_name'][1];
		$newFilePath = $uploaddir."debit.csv";
		
		if (move_uploaded_file($tmpFilePath, $newFilePath)) {
			echo "<br>debit: Файл успешно загружен.";
		} else {
			echo "<br>debit: Файл не загружен! Повторите попытку.";
		}
	}

        foreach($_FILES['adminfile']['name'] as $key=>$value){
            if(!empty($value)){
                $ext = pathinfo($value, PATHINFO_EXTENSION);
                $ext = strtolower($ext);
                $tmp = $_FILES['adminfile']['tmp_name'][$key];
                $new = $MONTH.$key.'.'.$ext;

                    if (move_uploaded_file($tmp, $new)) {
                            echo "<div class='alert alert-success' role='alert'><strong>OK!</strong>".$key."</div>";
                            mysqli_query($link, "UPDATE accounts SET fpath='$new' WHERE id='$key' ");
                    } else {
                            echo "<div class='alert alert-danger' role='alert'><strong>FAIL!</strong>".$key."</div>";
                    }

                //echo $ext;
                //echo $value;
                //echo $key;
            }
        }
}

if(isset($_GET['clid'])) {
	$clid = $_GET['clid'];
	if(isset($_GET['action'])){
		if($_GET['action'] == 'clear'){
			clearRecord('debit', $_GET['recid']);
		}
		elseif($_GET['action'] == 'delete'){
			deleteRecord('debit', 'id', $_GET['recid']);
		}
		else{
			echo 'Что-то пошло не так! Срочно звоним админу!';
			break;
		}
		echo "<script>window.location.href = 'admin.php?clid=$clid';</script>";
	}
	
	$sql = "SELECT * FROM debit WHERE client_id = '$clid' ORDER BY date DESC";
	$res = mysqli_query($link, $sql);
	echo "<div class='table-responsive'>";
		echo "<table class='table'><tr><td><b>№</b></td><td><b>Наим.</b></td><td><b>Дата</b></td><td><b>Сумма</b></td><td></td><td></td></tr>";
			echo "<caption>Платежи</caption>";
			while ($row = mysqli_fetch_assoc($res))
		   {	
				$recid = $row['id'];
				echo "<tr>";
				echo "<td>".$recid."</td><td>".$row['name']."</td><td>".$row['date']."</td><td>".$row['amount']."</td>";
				echo "<td><a href=admin.php?clid=".$clid."&action=clear&recid=".$recid.">Удалить и пересчитать</a></td>";
				echo "<td><a href=admin.php?clid=".$clid."&action=delete&recid=".$recid.">Удалить</a></td>";
				echo "</tr>";
		   }
		   echo "<td></td><td></td><td></td><td></td>";
		echo "</table>";
	echo "</div>";
}

?>