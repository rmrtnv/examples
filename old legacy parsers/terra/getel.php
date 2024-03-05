<?php

include_once('simple_html_dom.php');
include 'db.php';

$HOST = 'http://www.terracorp.ru';
$MAX = 20;
$COL = 5;
$i = 0;

$type = '';
$stype = '';


//смотрим, есть ли задачи
$result = mysqli_query($link, "SELECT * FROM jobs");
$num_rows = mysqli_num_rows($result);

//если да, переходим к выполнению
if ($num_rows > 0) {
    echo "run";
    while($row = mysqli_fetch_assoc($result)){
		if($row['col']){
			$i += $COL;
			echo 'is collection<br>';
		}
		else{
			$i += 1;
			echo 'is element<br>';
		}
		if ($i <= $MAX){
			echo 'go go<br>';
			$type = $row['type'];
			$stype = $row['stype'];
			if($row['col']) parseCollection($row['url']);
			else parseElement($row['url'],0);
			deleteJob($row['id']);
			//unset($result[])
		}
		echo '<script>window.location = "getel.php";</script>';
	}
//если нет, предлагаем создать
} else {
    echo "ALL JOBS DONE<br>";
	if ($_GET['do'] == 'jobs'){
		createJobs();
		echo '<a href=getel.php>RUN</a>';
	}
	else {
		echo '<a href=getel.php?do=jobs>CREATE JOBS</a>';
	}
}


//------------------------------------------------FUNCTIONS.
//создание задач
function createJobs(){
	//LINKS WITH COLLECTIONS
	//массив ссылок на страницы с коллекциями
	$links = array(
	'http://www.terracorp.ru/keramicheskaya-plitka/plitka-dlya-vannoy?show=all',
	'http://www.terracorp.ru/keramicheskaya-plitka/plitka-dlya-kuhni?show=all',
	'http://www.terracorp.ru/keramicheskaya-plitka/plitka-dlya-pola?show=all',
	'http://www.terracorp.ru/keramicheskaya-plitka/plitka-dlya-basseina',
	'http://www.terracorp.ru/keramicheskaya-plitka/plitka-dlya-gostinoy',
	'http://www.terracorp.ru/keramicheskiy-granit/plitka-dlya-vannoy?show=all',
	'http://www.terracorp.ru/keramicheskiy-granit/plitka-dlya-kuhni?show=all',
	'http://www.terracorp.ru/keramicheskiy-granit/plitka-dlya-ulitsyi?show=all',
	'http://www.terracorp.ru/keramicheskiy-granit/plitka-dlya-pola?show=all',
	'http://www.terracorp.ru/keramicheskiy-granit/plitka-dlya-fasada?show=all',
	'http://www.terracorp.ru/mozaika/dlya-kuchni',
	'http://www.terracorp.ru/mozaika/dlya-vannoi',
	'http://www.terracorp.ru/klinker/dlya-kuhni',
	'http://www.terracorp.ru/klinker/dlya-ulitsy?show=all',
	'http://www.terracorp.ru/klinker/dlya-pola?show=all',
	'http://www.terracorp.ru/klinker/dlya-basseina',
	);
	foreach($links as $link){
		$list = getList($link,1);
		foreach($list as $url)
		{
			addJob(1, $url);
		}
		echo $link."<br>";
	}

	//LINKS WITHOUT COLLECTIONS
	//элементы без коллекций
	$links = array(
	'http://www.terracorp.ru/keramicheskiy-granit/tech?show=all',
	'http://www.terracorp.ru/keramicheskiy-granit/stupeni?show=all',
	'http://www.terracorp.ru/mozaika/steklyannaya?show=all',
	'http://www.terracorp.ru/mozaika/kamennaya?show=all',
	'http://www.terracorp.ru/mozaika/keramicheskaya?show=all',
	'http://www.terracorp.ru/mozaika/kombinirovannaya?show=all',
	'http://www.terracorp.ru/klinker/stupeni/prohodnie?show=all',
	'http://www.terracorp.ru/klinker/stupeni/uglovie?show=all',
	'http://www.terracorp.ru/klinker/stupeni/podstupenok?show=all',
	'http://www.terracorp.ru/klinker/stupeni/torrelo?show=all',
	'http://www.terracorp.ru/naturalniy-kamen/granit',
	'http://www.terracorp.ru/naturalniy-kamen/mramor',
	'http://www.terracorp.ru/naturalniy-kamen/travertin',
	'http://www.terracorp.ru/naturalniy-kamen/pliti?show=all',
	'http://www.terracorp.ru/naturalniy-kamen/stupeni?show=all',
	'http://www.terracorp.ru/naturalniy-kamen/podstupenok?show=all',
	'http://www.terracorp.ru/profili/dlya-oblicovok/dlya-uglov-vneshnie/polukrug?show=all',
	'http://www.terracorp.ru/profili/dlya-oblicovok/dlya-uglov-vnutrinnie?show=all',
	'http://www.terracorp.ru/profili/dlya-oblicovok/zashitnie-nakladnie-ugli?show=all',
	'http://www.terracorp.ru/profili/dlya-oblicovok/decorativnie-vstavki/s-perforaciey?show=all',
	'http://www.terracorp.ru/profili/dlya-oblicovok/decorativnie-vstavki/bez-perforacii?show=all',
	'http://www.terracorp.ru/profili/dlya-polov/plintusi/l-obraznie?show=all',
	'http://www.terracorp.ru/profili/dlya-polov/plintusi/s-kabel-kanalom?show=all',
	'http://www.terracorp.ru/profili/dlya-polov/plintusi/s-zashelkivayushemsya-krepezem'
	);
	
	foreach($links as $link){
		$list = getList($link,0);
		foreach($list as $url)
		{
			addJob(0, $url);
		}
		echo $link."<br>";
	}
	
	echo 'job list create ok<br>';
}

//добавить задачу
function addJob($col, $url){
    $url = mysql_real_escape_string($url);
//    echo $col.":".$url."<br>";
    global $link;
	global $type;
	global $stype;
    mysqli_query($link, "INSERT INTO jobs (col,url,type,stype) VALUES('$col','$url','$type','$stype')");
}

//удалить
function deleteJob($id){
	global $link;
	$sql = "DELETE FROM jobs WHERE id = $id ";
	mysqli_query($link, $sql);
	echo 'job delete ok';
}

//получить список элементов
//параметры: ссылка, коллекция (да, нет)
function getList($url, $col){

global $HOST;
global $type;
global $stype;

$result = array();

  $input = @file_get_html($url) or die("Could not open link: $url");
  
  $bread = $input->find('div[id=breadcrumbs]'); 
  $bread = $bread[0];
  $bread = $bread->find('text');
  $type = trim($bread[4]);
  $stype = trim($bread[7]);
  $type = mb_strtolower($type, 'UTF-8');
  $stype = mb_strtolower($stype, 'UTF-8');

  $input = $input->find('.rounded_block');
  $input = $input[0];
  $input = $input->find('.row');
  $input = $input[0];
  $input = $input->find('a');

  foreach ($input as $value) {
      $url = $value->href;
	  if(!$col) array_push($result, $url);
	  else{
		  if (strpos($url, 'katalog') == false)
		  {
			  array_push($result, $url);
		  }
	  }

 
}
   return $result;   
}

//сохранение изображений
function saveImg($url){
	global $HOST;
	$ext = pathinfo($url, PATHINFO_EXTENSION);
	$path = realpath(dirname(__FILE__)).'/img/';
	//можно генерить имя на свой вкус
	$fname = substr(str_shuffle(MD5(microtime())), 0, 10).'.'.$ext;
	$new_path = $path.$fname;
	if(file_put_contents($new_path, file_get_contents($HOST.$url))){
		return $fname;
	}
	else return FALSE;
}

//парсит коллекцию, запускает парсинг элементов
//передает элементам id только что созданной коллекции
function parseCollection($url){

global $HOST;
global $link;
global $type;
global $stype;

$txt = ' ';

$url = $HOST.$url;

$input = @file_get_html($url) or die("Could not open link: $url");


$html = $input->find('.container');
$html = $html[4];

$img = $html->find('a');
$img = $img[0]->href;
$img = saveImg($img);
$img = mysql_real_escape_string($img);
echo $img;

$header = $html->find('h1');
$header = $header[0]->innertext;
$header = mysql_real_escape_string($header);

$text = $html->find('.rounded_block');
$text = $text[1]->find('div');
$text = $text[0]->find('p');

for ($i=1; $i<=count($text); $i++){
	$txt .= '<p>'.$text[$i]->plaintext.'</p>';
} 
$txt = str_replace('<p></p>','',$txt);
$txt = mysql_real_escape_string($txt);
echo '<br>@@'.$txt;


$sql = "INSERT INTO col (type,stype,name,img,text) VALUES('$type','$stype','$header','$img','$txt')";
echo $sql;
echo '<br>';
mysqli_query($link, $sql);

$col_id = mysqli_insert_id($link);
echo $col_id;

$input = $input->find('div[id=sostav]'); 

$input = $input[1]->find('.pad60');

foreach($input[1]->find('a') as $element) {
       $url = $element->href;
	   echo $url;
       parseElement($url, $col_id);
}
}

//парсим элемент
function parseElement($url, $col_id){
	global $HOST;
	global $link;
	global $type;
	global $stype;
		$url = $HOST.$url;
        $input = @file_get_html($url) or die("Could not open link: $url");
        $arr = array(
            'price' => 'Цена',
            'manufacturer' => 'Производитель',
            'collection' => 'Коллекция',
            'country' => 'Страна',
            'nomenclature' => 'Номенклатура',
            'article' => 'Артикул',
            'size' => 'Размер',
            'points' => 'Единицы измерения',
            'surface' => 'Поверхность',
            'mixedtones' => 'Смешанные оттенки',
            'gage' => 'Толщина'
        );
		
		$res = array(
            'price' => "''",
            'manufacturer' => "''",
            'collection' => "''",
            'country' => "''",
            'nomenclature' => "''",
            'article' => "''",
            'size' => "''",
            'points' => "''",
            'surface' => "''",
            'mixedtones' => "''",
            'gage' => "''"
        );
        
        $html = $input->find('div[id=item_page]');
		if (!count($html)) return FALSE;
        $html = $html[0];
        $pic = $html->find('a[id=bigFoto_a]');
        $pic = $pic[0]->href;
		$pic = saveImg($pic);
		$pic = mysql_real_escape_string($pic);
        echo $pic;
        echo '<br>';
		
        $name = $html->find('h1');
        $name = $name[0]->plaintext;
		$name = mysql_real_escape_string($name);
		
		//получаем размер из названия
		$size = explode(' ', $name);
		$size = array_reverse($size);
		$size = $size[0];
		$size = ltrim($size, '|');
		if (strpos($size, 'x')){
			$size = "'".$size."'";
			$res['size'] = $size;
			echo 'size:'.$size;
		}
		
        $text = $html->find('text');
        $text = array_reverse($text);
        $data = NULL;
        foreach ($text as $key => $value){
            foreach($arr as $k => $v){
                if (strpos($value, $v)){
                    echo $k.' : ';
                    echo $data;
                    echo '<br>';
					$data = mysql_real_escape_string($data);
					$res[$k] = "'".$data."'";
                }
            }
        $data = $value;
        }
	$res = join(',', $res);
	echo $res;
    $sql = "INSERT INTO main (col_id,type,stype,img,name,price,manufacturer,collection,country,nomenclature,article,size,points,surface,mixedtones,gage) VALUES($col_id,'$type','$stype','$pic','$name',$res);";
	echo $sql;
	echo '<br>';
	mysqli_query($link, $sql);
}
       
