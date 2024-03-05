<?php
include 'connectDB.php';
include 'header.php';
session_start();
if(!$_SESSION['client']){
//header("Location: index.php");
echo '<script>window.location = "index.php";</script>';
exit;
}

include 'clientHeader.php';

require_once 'Classes/PHPExcel.php';
require_once 'Classes/PHPExcel/Writer/Excel2007.php';

$fn = $_GET['id'];


$from = '';
$till = '';
if (empty($_GET['from'])) $from = date('Y-m-01');
else $from = $_GET['from'];
if (empty($_GET['till'])) $till = date('Y-m-d');
else $till = $_GET['till'];
?>
<form method='GET' action='clientLog.php'>
<input type='hidden' name='action' value="<?php echo $_GET['action']; ?>">
<input type='hidden' name='id' value="<?php echo $_GET['id']; ?>">
<input type='hidden' name='number' value="<?php echo $_GET['number']; ?>">

<br>
<div class="row">
	<div class="col-xs-8 col-md-8 col-sm-12">
	<table class="table table-condensed table-responsive" >
	<tbody>
	 <tr>
	 <td style='border-top:0;'>Период</td>
	 <td style='border-top:0;'>
	 <div class="col-lg-12">
		<input type="date" name="from" value="<?php echo $from; ?>" />
		<input type="date" name="till" value="<?php echo $till; ?>" />
	 </div>
	 </td>
	 </tr>
	 
	 <tr>
	 <td style='border-top:0;'>Номер топливной карты</td>
	 <td style='border-top:0;'>
        <div class="col-md-12 col-sm-4">
            <select data-placeholder="Выберите номер" name="numbers[]" class="chosen-select" multiple tabindex="4">
              <option value=""></option>
			  <?php
			  foreach(getNumbers($_GET['id']) as $val){
				  $str = '<option ';
				  if(isset($_GET['numbers'])){
					  foreach($_GET['numbers'] as $num){
						  if($val == $num){
							  $str = $str.'selected ';
							  break;
						  } 
					  }
				  }
				  $str = $str.'value="'.$val.'">'.$val.'</option>';
				  echo $str;
			  }
			  ?>
            </select>

		</div>
	 </td>
	 </tr>
	 
	 <tr>
	 <td style='border-top:0;'>Примечание</td>
	 <td style='border-top:0;'>
        <div class="col-lg-6">
            <select name="vehid" class="form-control">
			  <option value=""></option>
			  <?php
			  foreach(getVehicleId($_GET['id']) as $val){
				  $str = '<option ';
				  if(isset($_GET['vehid'])){
					  if($_GET['vehid'] == $val) $str = $str.'selected ';
				  }
				  $str = $str.'value="'.$val.'">'.$val.'</option>';
				  echo $str;
			  }
			  ?>
            </select>

		</div>
	 </td>
	 </tr>
	 
	 <tr>
	 <td style='border-top:0;'>Товар или услуга</td>
	 <td style='border-top:0;'>
        <div class="col-lg-6">
            <select name="gooid" class="form-control">
			  <option value=""></option>
			  <?php
			  $goods = array(
			  '92' => 'АИ 92 ЭКТО',
			  '95' => 'АИ 95 ЕВРО',
			  '98' => 'АИ 98 ЕВРО',
			  'dt' => 'Дизельное топливо',
			  'gas' => 'Газ сжиженный'
			  );
			  foreach($goods as $key => $val){
				  $str = '<option ';
				  if(isset($_GET['gooid'])){
					  if($_GET['gooid'] == $key) $str = $str.'selected ';
				  }
				  $str = $str.'value="'.$key.'">'.$val.'</option>';
				  echo $str;
			  }
			  ?>
            </select>

		</div>
	 </td>
	 </tr>
	</tbody>
	</table>
	</div>
	
	<div class="col-xs-4 col-md-4 col-sm-6">
	<table class="table table-condensed table-responsive" >
	<tbody>
	 
	<?php
		$path = mysqli_query($link, "SELECT fpath FROM accounts WHERE id='$id'");
		$path = mysqli_fetch_assoc($path);
		$path = $path['fpath'];
		//echo $path;
		//$path = $MONTH.$id.'.xls';
		if (file_exists($path)){
			//echo "<button type='button' class='btn btn-default btn-default'>";
			//echo "<a href='".$path."'>";
			echo "<tr><td style='border-top:0;'><a class='btn btn-default' href='".$path."'>Скачать бухгалтерский отчет</a></td></tr>";
			//echo "<span class='glyphicon glyphicon-download' aria-hidden='true'></span> Скачать бухгалтерский отчет";
			//echo "<a class='btn btn-default' aria-hidden='true'></a> Скачать бухгалтерский отчет";
			//echo "</a>";
			//echo "</button>";
			//echo "<hr>";
		}
		
		echo "<tr><td style='border-top:0;'><a class='btn btn-default' href=tmp/".$fn.".xlsx>Скачать транзакционный отчет</a></td></tr>";
	?>
	
	</tbody>
	</table>
	</div>
</div>
<br>
<button type='submit' class="btn btn-default">Сформировать</button>

</form>

<?php
	
	$sql = "SELECT id ,date, time, card_number, client_about, azs, name, liters, ROUND(amount/liters, 2) AS price, amount, type, state, adress, action ";
	$sql .= "FROM credit WHERE client_id = '$_GET[id]' AND date BETWEEN '".$from."' AND '".$till."'";
	
	if (!empty($_GET['gooid'])){
		$i = $_GET['gooid'];
		$g = $goods[$_GET['gooid']];
		//echo $g;
		$sql .= " AND type LIKE '%$g%'";
	} 

	if (!empty($_GET['vehid'])){
		$a = $_GET['vehid'];
		//echo $g;
		$sql .= " AND client_about LIKE '$a'";
	} 
	
	if (!empty($_GET['numbers'])){
		$arr = join("','", $_GET['numbers']);
		//echo $arr;
		$sql .= " AND card_number IN('".$arr."')";
	}
	
	$sql .= " ORDER BY id DESC";
	//echo $sql;
	$query = mysqli_query($link, $sql);
	if (mysqli_num_rows($query)==0) {
		echo "Попробуйте изменить параметры запроса.";
	}
	else {
		//fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
		echo "<div class='table-responsive'>";
		echo "<table class='table'><tr><td><b>ID</b></td><td><b>Дата и время</b></td><td><b>№ карты</b></td><td><b>Закреплена за</b></td><td><b>Номер АЗС</b></td><td><b>Поставщик</b></td><td><b>Регион</b></td><td><b>Местоположение</b></td><td><b>Товар / услуга</b></td><td><b>Кол-во</b></td><td><b>Цена по стеле</b></td><td><b>Сумма по стеле</b></td><td><b>Тип</b></td></tr>";
		echo "<caption>Транзакции</caption>";
		while ($row = mysqli_fetch_assoc($query)){
                        $dt = $row['date'].' '.$row['time'];
                        $dt = strtotime($dt);
                        $dt = date('d.m.Y H:i:s', $dt);
			echo "<tr>";
			echo "<td>".$row['id']."</td><td>".$dt."</td><td>".$row['card_number']."</td><td>".$row['client_about']."</td><td>".$row['azs']."</td><td>".$row['name']."</td><td>".$row['state']."</td><td>".$row['adress']."</td><td>".$row['type']."</td><td>".$row['liters']."</td><td>".$row['price']."</td><td>".$row['amount']."</td><td>".$row['action']."</td>";
			echo "</tr>";
			$sum_lit += $row['liters'];
			$sum_amount += $row['amount'];
			//fputcsv($fp, $row,';');
		}
		echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>".$sum_lit."</td><td></td><td>".$sum_amount."</td><td></td></tr>";
		echo "</table>";
		echo "</div>";
		
		//Write to csv encoded
		
		//mysqli_set_charset($link, "cp1251");
		$query = mysqli_query($link, $sql);
		
		//$fp = fopen('tmp/'.$fn.'.csv', 'wb');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
		$objPHPExcel->getActiveSheet()->setTitle('Transactions');
		$objPHPExcel->setActiveSheetIndex(0);

		//fprintf($fp, chr(255) . chr(254)); utf16LE
		//fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); BOM
                
                
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'ID');
                $objPHPExcel->getActiveSheet()->SetCellValue('B1', 'Дата и время');
                $objPHPExcel->getActiveSheet()->SetCellValue('C1', '№ карты');
                $objPHPExcel->getActiveSheet()->SetCellValue('D1', 'Закреплена за');
                $objPHPExcel->getActiveSheet()->SetCellValue('E1', 'Номер АЗС');
                $objPHPExcel->getActiveSheet()->SetCellValue('F1', 'Поставщик');
                $objPHPExcel->getActiveSheet()->SetCellValue('G1', 'Регион');
                $objPHPExcel->getActiveSheet()->SetCellValue('H1', 'Местоположение');
                $objPHPExcel->getActiveSheet()->SetCellValue('I1', 'Товар / услуга');
                $objPHPExcel->getActiveSheet()->SetCellValue('J1', 'Кол-во');
                $objPHPExcel->getActiveSheet()->SetCellValue('K1', 'Ценна по стеле');
                $objPHPExcel->getActiveSheet()->SetCellValue('L1', 'Сумма по стеле');
                $objPHPExcel->getActiveSheet()->SetCellValue('M1', 'Тип');
                
                
		$i = 2;
		while ($row = mysqli_fetch_assoc($query)){
			//$row['liters'] = strtr($row['liters'], array (',' => '.'));
			//$row['card_number'] = '"'.$row['card_number'].'"';
			//array_walk($row, 'encodeCSV');
			//fputcsv($fp, $row, ';');
			    $dt = $row['date'].' '.$row['time'];
                $dt = strtotime($dt);
                $dt = date('d.m.Y H:i:s', $dt);
                
				$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$i, $row['id'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $dt);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$i, $row['card_number'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $row['client_about']);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $row['azs']);
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $row['name']);
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, $row['state']);
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, $row['adress']);
                $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, $row['type']);
                $objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, $row['liters']);
                $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->SetCellValue('K'.$i, $row['price']);
                $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()->setFormatCode('#,##0.00');
                $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, $row['amount']);
                $objPHPExcel->getActiveSheet()->SetCellValue('M'.$i, $row['action']);
			$i++;
		}
		
		// Auto size columns for each worksheet
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

			$objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

			$sheet = $objPHPExcel->getActiveSheet();
			$cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(true);
			/** @var PHPExcel_Cell $cell */
			foreach ($cellIterator as $cell) {
				$sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
			}
		}
		
		//fclose($fp);
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('tmp/'.$fn.'.xlsx');
		echo "<a class='btn btn-default' href=tmp/".$fn.".xlsx>Скачать транзакционный отчет</a>";
	}


	
	function encodeCSV($value, $key){
		mb_convert_encoding($value, 'UTF-16LE', 'UTF-8');
		//$value = iconv('UTF-8', 'UTF-16LE', $value);
	}
	
	function getNumbers($id){
		global $link;
		$sql = "SELECT DISTINCT number FROM cards WHERE id = $id";
		$query = mysqli_query($link, $sql);
		$result = array();
		while($row = mysqli_fetch_assoc($query)) array_push($result, $row['number']);
		return $result;
	}
	
	function getVehicleId($id){
		global $link;
		$sql = "SELECT DISTINCT about FROM cards WHERE id = $id";
		$query = mysqli_query($link, $sql);
		$result = array();
		while($row = mysqli_fetch_assoc($query)){
			if($row['about'] !== '') array_push($result, $row['about']);
		}
		return $result;
	}
	
?>