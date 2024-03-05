<?php

$id = '';
$balance = '';
$login = "";
$password = "";
$name = "";
$inn = "";
$about = "";
$query = mysqli_query($link, "SELECT * FROM accounts WHERE id='$_GET[id]'");
while ($row = mysqli_fetch_assoc($query))
{
	$balance = $row['balance'];
	$name = $row['name'];
	$inn = $row['inn'];
	$id = $row['id'];
//Arrr
	$fin_state = $row['fin_state'];

	$login = $row['login'];
        $phone = $row['phone'];
        $kpp = $row['kpp'];
        $contact = $row['contact'];
}
$squery = mysqli_query($link, "SELECT SUM(liters) AS month FROM credit WHERE YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW()) AND client_id = '$id'");
$month = mysqli_fetch_assoc($squery);
//print_r($squery);
$month = $month['month'];
$month = round($month, 2);

//Arrr
$map = array(
	1 => 'Договор заблокирован ',
	2 => 'Рекомендуется пополнить баланс ',
	3 => 'Договор действующий ',
	);

//echo "№ договора: <b>".$id."</b><br>";
//echo "Баланс: <b>".$balance."</b><br>";
//echo "Расход лит. за месяц: <b>".$month."</b><br>";

?>

<div class="row">
  <div class="col-xs-6 col-sm-3"><img src="logo.png" width="145px"/></div>
  <div class="col-xs-6 col-sm-2">
	<p class="text-muted">№ договора</p>
	<h4><?php echo $id; ?></h4>
  </div>
  <div class="col-xs-6 col-sm-2">
	<p class="text-muted">Баланс</p>
	<h4><?php echo $balance; ?></h4>
	<p class="text-muted">рублей</p>
  </div>  <div class="col-xs-6 col-sm-2">
	<p class="text-muted">Расход за месяц</p>
	<h4><?php echo $month; ?></h4>
	<p class="text-muted">литров</p>
  </div> <div class="col-xs-6 col-sm-3">
	<p class="text-muted">Статус договора</p>
	<h4><?php echo $map[$fin_state]; ?></h4>
  </div>
</div>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="client.php?id=<?php echo $_GET['id']?>">Главная</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li <?php if($_GET['action'] == 'cards') echo "class='active'"; ?> id="cards"><a href="client.php?action=cards&id=<?php echo $_GET['id']?>">Топливные карты</a></li>
        <li <?php if($_GET['action'] == 'open') echo "class='open'"; ?> id="reports"><a href="clientLog.php?action=open&id=<?php echo $_GET['id']?>">Отчетность</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#" data-toggle="modal" data-target="#myModal">Поддержка</a></li>
		<li><a href="logout.php">Выход</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Сообщение администратору</h4>
      </div>
      <div class="modal-body">

          <form action="client.php?action=help&id=<?php echo $id; ?>" method="POST" role="form" class="form-horizontal">
            <fieldset>

            <!-- Form Name -->

            <!-- Textarea -->
            <div class="form-group">
              <div class="col-md-12">                     
                <textarea class="form-control" id="textarea" name="text"></textarea>
              </div>
            </div>
            <input type='hidden' name='name' value="<?php echo $name; ?>">
            <!-- Button -->
            <div class="form-group">
              <div class="col-md-6">
                <button id="singlebutton" name="singlebutton" class="btn btn-primary">Отправить</button>
              </div>
            </div>

            </fieldset>
          </form>
          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>
      </div>
    </div>

  </div>
</div>
