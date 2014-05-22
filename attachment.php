<?php
	define("_MATA_PHP_API_COOKIE_BASE_NAME",".mata-php.api.cookie.");
	$url = $_GET["url"];
	$cookie_id = $_GET["id"];
	$header = array('Content-Type: multipart/form-data');
	$referer=parse_url($url);
	if($referer){
		$referer=$referer["scheme"]."://".$referer["host"];
	} else {
		throw new \Exception("Mataphp:curlget:invalid_url, an invalid url was passed");
	}
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
	curl_setopt($ch,CURLOPT_TIMEOUT,60);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	if($cookie_id&&$cookie_id!=""){
		curl_setopt($ch,CURLOPT_COOKIEJAR,_MATA_PHP_API_COOKIE_BASE_NAME.$cookie_id);
		curl_setopt($ch,CURLOPT_COOKIEFILE,_MATA_PHP_API_COOKIE_BASE_NAME.$cookie_id);
	}
	curl_setopt($ch,CURLOPT_REFERER,$referer);
	$file=curl_exec($ch);
	curl_close($ch);

$file_array = explode("\n\r", $file, 2);
$header_array = explode("\n", $file_array[0]);
foreach($header_array as $header_value) {
	$header_pieces = explode(':', $header_value);
	if(count($header_pieces) == 2) {
		$headers[$header_pieces[0]] = trim($header_pieces[1]);
	}
}
header('Cache-Control: public'); 
header('Content-type: '. $headers['Content-Type']);
header('Content-Disposition: ' . $headers['Content-Disposition']);
header('Content-Length: '.strlen($file_array[1]));
echo substr($file_array[1], 1);
?>
