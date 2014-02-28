<?php
function curlget($url,$usecookie=false,$postdata=""){
	if($referer=parse_url($url)){
		$referer=$referer["scheme"]."://".$referer["host"];
	} else {
		throw new Exception("Invalid url given in curlget(\"$url\",$usecookie,\"$postdata\")");
	}
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
	curl_setopt($ch,CURLOPT_TIMEOUT,60);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	if($usecookie)curl_setopt($ch,CURLOPT_COOKIEJAR,".cookie.txt");
	curl_setopt($ch,CURLOPT_REFERER,$referer);
	if($postdata!=""){
		curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);
		curl_setopt($ch,CURLOPT_POST,1);
	}
	$f=fopen("/Users/Tom/Sites/Site/utilscurllog.txt","a");
	fwrite($f,"url=$url\nusecookie=$usecookie\npostdata=$postdata\nreferer=$referer\n");
	$result=curl_exec($ch);
	curl_close($ch);
	fwrite($f,"result=$result\n\n");
	fclose($f);
	return $result;
}

function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}

function decode($string){
	$string = utf8_decode($string);
	$string = preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);
	$string = preg_replace('/([[:lower:]])([[:upper:]])/', '$1 $2', $string);
	$string = str_replace('  ', ' ', $string);
	return $string;
}

function writeHTMLhead(){
	echo <<<'EOT'
<!DOCTYPE html>
<html>
<head>
<title>Mata-beta</title>
<style>
body{
	font-family:Arial;
}
pre{
	font-size:10px;
}
</style>
</head>
<body>
EOT;
}

function writeHTMLfoot(){
	echo <<<'EOT'
</body>
</html>
EOT;
}
?>