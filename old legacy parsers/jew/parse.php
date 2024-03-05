<?php

$link=mysqli_connect("localhost", "cs26881_terra", "1", "cs26881_terra");

	if (!$link) {
		die('ERROR' . mysql_error());
	}

	ignore_user_abort(true);
ini_set('display_errors',1);
error_reporting(E_ALL);

//подключение библиотеки
include_once('simple_html_dom.php');

//ссылка на товар
//$url = 'http://www.bnsjewelry.com/product_p/14ktdcp1001.htm';
//parse($url);

function parse($url){

//echo $url;
$html = file_get_html($url);
//echo $html;
$code = $html->find('.product_code');
$code = $code[0]->innertext;

$name = $html->find('span[itemprop=name]');
$name = $name[0]->innertext;

$stock = $html->find('meta[itemprop=availability]',0)->content;

$price = $html->find('span[itemprop=price]',0)->innertext;

$title = $html->find('title',0)->innertext;

$desc = $html->find('meta[name=description]',0)->content;

$keyw = $html->find('meta[name=keywords]',0)->content;

$pdesc = $html->find('span[itemprop=description]',0)->find('span');
foreach($pdesc as $val) echo $val->innertext;

$img = $html->find('img.vCSS_img_product_photo',0)->src;

$tree = $html->find('td.vCSS_breadcrumb_td',0)->find('b');
$tree = $tree[0]->find('a');

echo "<br>";
$p1 = $tree[1]->innertext;
if($p1) $path = $p1.'/';
else echo 'BAD PATH!';

echo "<br>";
$p2 = $tree[2]->innertext;
if($p2) $path = $p1.$p2.'/';

saveImg($img,$path,$code);

//foreach($tree as $val) echo $val->innertext;

echo "<br>";
echo $code;
echo "<br>";
echo $name;
echo "<br>";
echo $stock;
echo "<br>";
echo $price;
echo "<br>";
echo $title;
echo "<br>";
echo $desc;
echo "<br>";
echo $keyw;
echo "<br>";

}


$myFile = "sitemap.txt";
$lines = file($myFile);//file in to an array
echo count($lines); //line 2
$num = count($lines);
$i = $_GET['last'];
$j=0;
for(;$i<=$num;){

	$url = chop($lines[$i]);
	//echo gettype($url);
	parse($url);
	echo "<script>window.location = 'parse.php?last=".++$i."';</script>";
}

function saveImg($url, $path, $name){

	$ext = pathinfo($url, PATHINFO_EXTENSION);
	//$path = realpath(dirname(__FILE__)).'/'.$path;
	//можно генерить имя на свой вкус
	//$fname = substr(str_shuffle(MD5(microtime())), 0, 10).'.'.$ext;
	//$new_path = $path.$fname;
	
	if (!is_dir($path)) {
	// dir doesn't exist, make it
	  mkdir($path, 0755, true);
	}
	
	if(file_put_contents($path.'.'.$ext, file_get_contents($url))){
		return $fname;
	}
	else return FALSE;
}

//$map = file_get_html('http://www.bnsjewelry.com/sitemap.xml');
//$map = $map->find('loc');
//foreach($map as $val){
//	echo "<br>";
//	echo $val->innertext;
//} 
?>