<?php
ignore_user_abort(true);
ini_set('display_errors',1);
error_reporting(E_ALL);

//подключение библиотеки
include_once('simple_html_dom.php');

//ссылка на товар
//$url = 'https://www.walmart.com/ip/GEORGE-WOMENS-CLASSIC-MID-HEELED-PUMP-DRESS-SHOE/36276861';

//функция получения информации о доставке
function get_d_info($offer){
	echo '<hr>';
	//инициализируем curl
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, 'https://www.walmart.com/terra-firma/fulfillment/'.$offer.'/shipping');
	//сохранение результата запроса
	$result = curl_exec($ch);
	curl_close($ch);
	//декодирование из формата json
	$data = json_decode($result);
	foreach($data->payload->shippingOptions as $option){
		echo $option->shipMethod;
		echo ' : ';
		echo $option->fulfillmentPrice->price;
		echo '<br>';
	}
}

//основная функция, получение вариантов, наличия, цены
function parse($url){

$E = TRUE;
//сохраниение страницы с товаром в переменную
$html = file_get_html($url);
$html = $html->find('.center');
$html = $html[0];
//находим в коде первый код товара для запроса
$scripts = $html->find('script');
    foreach($scripts as $s) {
        if(strpos($s->innertext, 'Append CC') !== false) {
            $script = $s;
        }
    }

$start = strpos($script->innertext, 'productIds');
$start += 14;
$end = 12;

$prodcode = substr($script->innertext, $start, $end);

//полученный код используется для генерации url
$url = 'https://www.walmart.com/terra-firma/item/'.$prodcode.'?selected=true&wl13=';

//полученный url возвращает json с детальной информацией по товару
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result);
//находим все варианты товара
$array = get_object_vars($data->payload->products);
//перебираем, вытаскиваем искомые поля
foreach($array as $el){
	$offer = $el->offers[0];
	echo '<br>';
	echo $el->variants->size;
	echo '<br>';
	echo $el->variants->actual_color;
	echo '<br>';
	echo $data->payload->offers->$offer->pricesInfo->priceMap->CURRENT->price;
	echo '<br>';
	echo $data->payload->offers->$offer->productAvailability->availabilityStatus;
	echo '<hr>';
}
//переменная offer содержит код из последнего варианта товара, по которому получем подробности доставки
//оставил только последний, так как перебор всех существенно увеличивает время выполнения скрипта, а результы во всех просмотренных мною случаях одинаковы
get_d_info($offer);
}

echo 'Текущая версия PHP: ' . phpversion();

$url = $_GET['ip'];
$url = 'https://www.walmart.com/ip/'.$url;
parse($url);
?>