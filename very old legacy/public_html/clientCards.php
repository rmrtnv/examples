<?php

include 'connectDB.php';
include 'header.php';
session_start();
if(!$_SESSION['client']){
//header("Location: index.php");
echo '<script>window.location = "index.php";</script>';
exit;
}

echo "
<nav>
  <ul class='pager'>
    <li class='previous'><a href='client.php?action=cards&id=".$_GET['id']."'><span aria-hidden='true'>&larr;</span> Назад</a></li>
  </ul>
</nav>
";

$about = '';

if ($_GET['action'] == 'update') {
	$query = mysqli_query($link, "UPDATE cards SET about='$_GET[about]' WHERE number='$_GET[number]'");
}

if ($_GET['action'] == 'fuel') {
	global $GoodGroupMap;
	$fmap = array(
	0 => '92',
	1 => '95',
	2 => 'dt',
	3 => '98',
	4 => 'gas',
	5 => 'tovary'
	);

	$fuel = array();
	$vol = array();
	$selvol = array();
	$time = array();
	
	$text = array();

	for($i=0; $i<=5; $i++){
		
		if ($_POST['vol'][$i]) array_push($vol, $_POST['vol'][$i]);
		else array_push($vol, '0');
		
		if ($_POST['selvol'][$i]) array_push($selvol, $_POST['selvol'][$i]);
		else array_push($selvol, '0');

		if ($_POST['time'][$i]) array_push($time, $_POST['time'][$i]);
		else array_push($time, '0');

		if(in_array($fmap[$i], $_POST['checkboxes'])){
			array_push($fuel, '1');
			$str = $fmap[$i].'('.$vol[$i].' '.$selvol[$i].' '.$time[$i].')';
			array_push($text, $str);
		} 
		else array_push($fuel, '0');
	}
	
	$fuel = implode(',', $fuel);
	$vol = implode(',', $vol);
	$selvol = implode(',', $selvol);
	$time = implode(',', $time);
	
	$query = mysqli_query($link, "UPDATE cards SET fuel='$fuel', vol='$vol', selvol='$selvol', time='$time' WHERE number='$_GET[number]'");
	
	$text = implode('; ', $text);
//IF TEXT NO EMPTY
//DELIMITER ;
//В ОТЧЕТНОСТЬ кнопку по всем, показывать номер текущей карты
	$to      = $mailto;
	$subject = 'Запрос на обновление лимитов';
	$message = $_GET['id'].', №'.$_GET[number].' заданы лимиты: '.$text;
	$headers = 'From: ' . $mailadmin . "\r\n" .
    'Reply-To: ' . $mailadmin . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
        //echo $_POST['text'];
	mail($to, $subject, $message, $headers);
	
	echo "<br><br><i>Параметры обновлены.</i>";

}

	$query = mysqli_query($link, "SELECT * FROM cards WHERE number ='".$_GET['number']."'");
	while ($row = mysqli_fetch_assoc($query)){ 
		$about = $row['about'];
        $fuel = explode(',', $row['fuel']);
		$vol = explode(',', $row['vol']);
		$selvol = explode(',', $row['selvol']);
		$time = explode(',', $row['time']);
	}

echo "
<form method='GET' action='clientCards.php'>
Закреплена за:<br>
<input name='about' value='".$about."'>
<input type='hidden' name='action' value='update'>
<input type='hidden' name='number' value='".$_GET['number']."'>
<input type='hidden' name='id' value='".$_GET['id']."'>
<input type='submit' value='Обновить'>
</form>
";

?>

<form action="clientCards.php?action=fuel&id=<?php echo $_GET['id']; ?>&number=<?php echo $_GET['number']; ?>" method="POST" role="form" class="form-horizontal">
<fieldset>


<!-- Form Name -->
Суточные лимит:<br>

<!-- Multiple Checkboxes -->
<div class="form-group">

	<div class="row">
	
	  <div class="col-md-2">
	  
		<div class="checkbox">
			<label>
			  <input type="checkbox" name="checkboxes[]" value="92" <?php if($fuel[0]) echo 'checked' ?> >
			  92
			</label>
		</div>
		
		<input name="vol[]" type="text" class="form-control input-md" <?php if($vol[0]) echo 'value="'.$vol[0].'"'; ?>>
		<select name="selvol[]" class="form-control">
		  <option value="rub" <?php if($selvol[0] == 'rub') echo 'selected="selected"'; ?>>рублей</option>
		  <option value="lit" <?php if($selvol[0] == 'lit') echo 'selected="selected"'; ?>>литров</option>
		</select>
		<select name="time[]" class="form-control">
		  <option value="day" <?php if($time[0] == 'day') echo 'selected="selected"'; ?>>день</option>
		  <option value="week" <?php if($time[0] == 'week') echo 'selected="selected"'; ?>>неделя</option>
		  <option value="month" <?php if($time[0] == 'month') echo 'selected="selected"'; ?>>месяц</option>
		</select>
		
	  </div>
	  
		<div class="col-md-2">
		  <div class="checkbox">
			<label>
			  <input type="checkbox" name="checkboxes[]" value="95" <?php if($fuel[1]) echo 'checked' ?> >
			  95
			</label>
			</div>
			<input name="vol[]" type="text" class="form-control input-md" <?php if($vol[1]) echo 'value="'.$vol[1].'"'; ?>>
			<select name="selvol[]" class="form-control">
			  <option value="rub" <?php if($selvol[1] == 'rub') echo 'selected="selected"'; ?>>рублей</option>
			  <option value="lit" <?php if($selvol[1] == 'lit') echo 'selected="selected"'; ?>>литров</option>
			</select>
			<select name="time[]" class="form-control">
			  <option value="day" <?php if($time[1] == 'day') echo 'selected="selected"'; ?>>день</option>
			  <option value="week" <?php if($time[1] == 'week') echo 'selected="selected"'; ?>>неделя</option>
			  <option value="month" <?php if($time[1] == 'month') echo 'selected="selected"'; ?>>месяц</option>
			</select>
		</div>
		
	<div class="col-md-2">
	  <div class="checkbox">
		<label>
		  <input type="checkbox" name="checkboxes[]" value="dt" <?php if($fuel[2]) echo 'checked' ?> >
		  ДТ
		</label>
		</div>
		<input name="vol[]" type="text" class="form-control input-md" <?php if($vol[2]) echo 'value="'.$vol[2].'"'; ?>>
		<select name="selvol[]" class="form-control">
		  <option value="rub" <?php if($selvol[2] == 'rub') echo 'selected="selected"'; ?>>рублей</option>
		  <option value="lit" <?php if($selvol[2] == 'lit') echo 'selected="selected"'; ?>>литров</option>
		</select>
		<select name="time[]" class="form-control">
		  <option value="day" <?php if($time[2] == 'day') echo 'selected="selected"'; ?>>день</option>
		  <option value="week" <?php if($time[2] == 'week') echo 'selected="selected"'; ?>>неделя</option>
		  <option value="month" <?php if($time[2] == 'month') echo 'selected="selected"'; ?>>месяц</option>
		</select>		
	</div>
	
	<div class="col-md-2">
	  <div class="checkbox">
		<label>
		  <input type="checkbox" name="checkboxes[]" value="98" <?php if($fuel[3]) echo 'checked' ?> >
		  98
		</label>
		</div>
		<input name="vol[]" type="text" class="form-control input-md" <?php if($vol[3]) echo 'value="'.$vol[3].'"'; ?>>
		<select name="selvol[]" class="form-control">
		  <option value="rub" <?php if($selvol[3] == 'rub') echo 'selected="selected"'; ?>>рублей</option>
		  <option value="lit" <?php if($selvol[3] == 'lit') echo 'selected="selected"'; ?>>литров</option>
		</select>
		<select name="time[]" class="form-control">
		  <option value="day" <?php if($time[3] == 'day') echo 'selected="selected"'; ?>>день</option>
		  <option value="week" <?php if($time[3] == 'week') echo 'selected="selected"'; ?>>неделя</option>
		  <option value="month" <?php if($time[3] == 'month') echo 'selected="selected"'; ?>>месяц</option>
		</select>
	</div>
	
	<div class="col-md-2">
	  <div class="checkbox">
		<label>
		  <input type="checkbox" name="checkboxes[]" value="gas" <?php if($fuel[4]) echo 'checked' ?> >
		  Газ
		</label>
		</div>
		<input name="vol[]" type="text" class="form-control input-md" <?php if($vol[4]) echo 'value="'.$vol[4].'"'; ?>>
		<select name="selvol[]" class="form-control">
		  <option value="rub" <?php if($selvol[4] == 'rub') echo 'selected="selected"'; ?>>рублей</option>
		  <option value="lit" <?php if($selvol[4] == 'lit') echo 'selected="selected"'; ?>>литров</option>
		</select>
		<select name="time[]" class="form-control">
		  <option value="day" <?php if($time[4] == 'day') echo 'selected="selected"'; ?>>день</option>
		  <option value="week" <?php if($time[4] == 'week') echo 'selected="selected"'; ?>>неделя</option>
		  <option value="month" <?php if($time[4] == 'month') echo 'selected="selected"'; ?>>месяц</option>
		</select>
	</div>
	
	<div class="col-md-2">
	  <div class="checkbox">
		<label>
		  <input type="checkbox" name="checkboxes[]" value="tovary" <?php if($fuel[5]) echo 'checked' ?> >
		  Товары
		</label>
		</div>
		<input name="vol[]" type="text" class="form-control input-md" <?php if($vol[5]) echo 'value="'.$vol[6].'"'; ?>>
		<select name="selvol[]" class="form-control">
		  <option value="rub" <?php if($selvol[5] == 'rub') echo 'selected="selected"'; ?>>рублей</option>
		  <option value="lit" <?php if($selvol[5] == 'lit') echo 'selected="selected"'; ?>>литров</option>
		</select>
		<select name="time[]" class="form-control">
		  <option value="day" <?php if($time[5] == 'day') echo 'selected="selected"'; ?>>день</option>
		  <option value="week" <?php if($time[5] == 'week') echo 'selected="selected"'; ?>>неделя</option>
		  <option value="month" <?php if($time[5] == 'month') echo 'selected="selected"'; ?>>месяц</option>
		</select>
	</div>
	
</div>

<hr>
<!-- Button -->
<div class="form-group">
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" class="btn btn-primary">Сохранить и отправить</button>
  </div>
</div>

</fieldset>
</form>
