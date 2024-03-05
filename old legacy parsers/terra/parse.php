<?php
set_time_limit(0); 
ignore_user_abort(true);
ini_set('max_execution_time', 0);
ini_set('display_errors',1);
error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);
header('Content-Type: text/html; charset=windows-1251');
//header('Content-Type: text/html; charset=utf-8');
include_once('simple_html_dom.php');

//echo parse('http://www.terracorp.ru/katalog/dural/t-cove/n005738');
//$N = 25;
//$i = 0;
//$list = file("short.txt");
////print_r($list);
//$less = fopen("short.txt", "w");
//$nogps = fopen("_nogps.txt", "a");
//$badurl = fopen("_badurl.txt", "a");
//$done = fopen("_done.txt", "a");
//if ($list) {
//	foreach($list as $line){
//		if ($i++ < $N) {
//			echo "<br>!".$line;
//			$res = parse($line);
//			if($res === 0) { 
//				fwrite($badurl, $line);
//				continue;
//			}
//			if ($res === 1){
//				fwrite($nogps, $line);
//				continue;
//			}
//			fwrite($done, $line);
//		}
//		else{
//			echo "<br>@".$line;
//			fwrite($less, $line);
//		}
//	}
//	echo '<script>window.location = "parse.php";</script>';
//}
//else echo "Done.";


function parse($url){
include 'db.php';
$E = TRUE;
//$url = preg_replace('/\s+/', '', $url);
//echo "<br>".$url;
//echo $url;
$html = file_get_html($url);

if (!strpos($html->outertext, 'surf,surfing,spot')) {
return 0;
}
if (strpos($html, 'GPS coordinates not set')) return 1;

//COORDINATES
$gps = $html->outertext;
$gps = explode(',', substr($gps, strpos($gps, 'new OpenLayers.Bounds(') + 22, 50));
$lon = $gps[0];
$lat = $gps[1];

if ($E) echo "<br><b>Coordinates:</b><br>";
if ($E) echo "Latitude: ".$lat;
if ($E) echo "<br>"; 
if ($E) echo "Longitude: ".$lon;
if ($E) echo "<br>"; 

//NAME
$name = $html->find('.wanna-item-title-title');
$name = mysql_real_escape_string($name[1]->plaintext);
if ($E) echo "<br><b>Name:</b> ";
if ($E) echo $name;

$level = $html->find('div[id=wanna-item-specific-2columns-left]'); 
$level = $level[0]->plaintext;
//if ($E) echo "<br>".$level;
//GET EXPERIENCE
$exp = getExp($level);
if ($E) echo "<br><br><b>Experience level index:</b> ".$exp;

//GET DIRECTION
$dir = getDirection($level);
if ($E) echo "<br><b>Direction index:</b> ".$dir;

//GET TYPE
$type = getWType($level);
if ($E) echo "<br><b>Type index:</b> ".$type;

//GET BOTTOM
$bottom = getWBottom($level);
if ($E) echo "<br><b>Bottom index:</b> ".$bottom;

//GET POWER
$power = getWPower($level);
if ($E) echo "<br><b>Wave Power:</b> ";
if ($E) print_r($power);

$right = $html->find('div[id=wanna-item-specific-2columns-right]');
$right = $right[0]->plaintext;
//if ($E) echo "<br>".$right;

//GET DANGER
$danger = getDanger($right);
if ($E) echo "<br><b>Danger:</b> ";
if ($E) print_r($danger);

$desc = $html->find('h3.wanna-item');
$d = array();
$desc = $desc[2];
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
if ($desc->plaintext !== '') array_push($d, $desc->plaintext);
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
if ($desc->plaintext !== '') array_push($d, $desc->plaintext);
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
$desc = $desc->next_sibling();
if ($desc->plaintext !== '') array_push($d, $desc->plaintext);
$d = implode('<br><br>', $d);
$d = mysql_real_escape_string($d);
if ($E) echo "<br><b>Description:</b><br> ";
if ($E) echo $d;

//GET KEYWORDS
$keywords = getKeywords($path);
if ($E) echo "<br><b>Keywords:</b> ";
if ($E) print_r($keywords);


$power = arr_to_str($power);
$danger = arr_to_str($danger);
$keywords = arr_to_str($keywords);
$keywords = rtrim($keywords);
$keywords = mysql_real_escape_string($keywords);

$query = <<<eof
  INSERT INTO main
  (lat, lon, name, surfer_level, wave_direction, wave_char, bottom_char, wave_power, dangers, keywords, spot_description)
  VALUES ('$lat', '$lon', '$name', '$exp', '$dir', '$type', '$bottom', '$power', '$danger', '$keywords', '$d')
eof;

$result = mysqli_query($link, $query);

return 'good';
}

function arr_to_str($arr){
	return implode(',', $arr);
}

function getKeywords($str){
	
	$arr = explode("/", $str);
	switch($arr[0]){
		case 'Australia_Pacific':
		$arr[0] = 'australia';
		array_push($arr, 'pacific');
		break;
	}
	if(strpos($arr[0], 'America')) array_push($arr, 'america');
	foreach($arr as $key=>$value){
		$arr[$key] = strtolower($value);
		$arr[$key] = str_replace('_', ' ', $arr[$key]);
	}
	return $arr;
}

function getDanger($str){
	$res = array();
	$arr = array(
	0 => 'Urchins',
	1 => 'Rips / undertow',
	2 => 'Rocks',
	3 => 'Man-made danger (buoys etc..)',
	4 => 'Private beach',
	5 => 'Localism',
	6 => 'Pollution',
	7 => 'Sharks',
	8 => 'Shark protected',
	9 => 'Nudist colony (France only!)',
	10 => 'Mines (Angola only !)'
	);
	foreach ($arr as $key=>$value){
		if (strpos($str, $value)) array_push($res, $key);
	}
	return $res;
}

function getWBottom($str){
	$arr = array(
	0 => 'Reef (coral,sharp rocks etc..) with sand',
	1 => 'Reef (coral, sharp rocks etc..)',
	2 => 'Boulders',
	3 => 'Flat rocks with sand',
	4 => 'Flat rocks',
	5 => 'Sandy with rock',
	6 => 'Sandy'
	);
	foreach ($arr as $key=>$value){
		if (strpos($str, $value)) return $key;
	}
}

function getWPower($str){
	$res = array();
	$arr = array(
	0 => 'Hollow',
	1 => 'Fast',
	2 => 'Powerful',
	3 => 'Ordinary',
	4 => 'Fun',
	5 => 'Powerless',
	6 => 'Ledgey',
	7 => 'Slab'
	);
	foreach ($arr as $key=>$value){
		if (strpos($str, $value)) array_push($res, $key);
	}
	return $res;
}

function getWType($str){
	$arr = array(
	0 => 'Beach-break',
	1 => 'Sand-bar',
	2 => 'Point-break',
	3 => 'Reef-coral',
	4 => 'Reef-rocky',
	5 => 'Reef-artificial',
	6 => 'Rivermouth',
	7 => 'breakwater/jetty'
	);
	foreach ($arr as $key=>$value){
		if (strpos($str, $value)) return $key;
	}
}

function getExp($str){
	$arr = array(0 => 'All surfers', 1 => 'Beginners wave', 2 => 'Experienced surfers', 3 => 'Pros or kamikaze only...');
	foreach ($arr as $key=>$value){
		if (strpos($str, $value)) return $key;
	}
}

function getDirection($str){
	$arr = array(0 => 'Right and left', 1 => 'Right', 2 => 'Left');
	foreach ($arr as $key=>$value){
		if (strpos($str, $value)) return $key;
	}
}

function DMStoDEC($deg,$min,$sec)
{

// Converts DMS ( Degrees / minutes / seconds ) 
// to decimal format longitude / latitude

    return $deg+((($min*60)+($sec))/3600);
}
?>