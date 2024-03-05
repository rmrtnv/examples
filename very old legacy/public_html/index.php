<?php
//ini_set ( 'display_errors', 'On' );
//ob_start();
session_start();
$err = array();
include 'connectDB.php';	
if ($_POST['login']) {
	 
	//$pass = md5($_POST['password']);
	//echo $pass;
    $query = mysqli_query($link, "SELECT * FROM accounts WHERE login='".$_POST['login']."' AND password='".$_POST['password']."'");
    if(mysqli_num_rows($query) > 0)
    {
		while ($row = mysqli_fetch_assoc($query))
	   {
		  if ($row['name'] == 'admin'){
			  $_SESSION['admin'] = $_POST['login'];
			  //echo  $_SESSION['admin'];
			  //header('Location: admin.php');
			  echo '<script>window.location = "admin.php";</script>';
			  exit;
		  } 
		  else {
			  $_SESSION['client'] = 'client';
			  //header("Location: client.php?id=".$row['id']."");
			  echo '<script>window.location = "client.php?id='.$row['id'].'";</script>';
			  echo '<script>window.location = "admin.php";</script>';

			  exit;
		  }
	   }
    }
	else echo "Пользователь не найден";

}
else {
	
include 'header.php';
	
echo "
<form method='POST'>
Логин:<br>
<input name='login'><br>
Пароль:<br>
<input name='password' type='password'><br><br>
<input type='submit' value='Войти'>
</form>
";

}

        foreach($err AS $error)
        {
            print $error."<br>";
        }

?>