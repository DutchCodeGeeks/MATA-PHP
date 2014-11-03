<?php
	define("_MATA_PHP_API_COOKIE_BASE_NAME",".mata-php.api.cookie.");

	$schoolUrl = $_GET["schoolurl"];
	$studentId = $_GET["studentid"];
	//Or you could use session cookies to get the session variables: and use this code: $session = $_SESSION['sessie']; $url = 'https://'.$session->school->url.'/api/leerlingen/'.$session->userId.$_GET["url"];
	$url = 'https://'.$schoolUrl.'/api/leerlingen/'.$studentId.$_GET["url"];
	$cookie_id = $_GET["sessionid"];

	$header = array('Content-Type: multipart/form-data');
	$referer=parse_url($url);
	if($referer){
		$referer=$referer["scheme"]."://".$referer["host"];
	} else {
		throw new \Exception("Mataphp:curlget:invalid_url, an invalid url was passed");
	}
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($ch, CURLOPT_SSLVERSION, 3);
	curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
	curl_setopt($ch,CURLOPT_TIMEOUT,60);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	if($cookie_id&&$cookie_id!=""){
		if (! file_exists(_MATA_PHP_API_COOKIE_BASE_NAME.$cookie_id) || ! is_writable(_MATA_PHP_API_COOKIE_BASE_NAME.$cookie_id)){
			throw new \Exception("Mataphp:curlget:no_cookie, can't read or find cookie file... Oeps, er ging iets mis. De koekiemonster had heeeeeel veel honger en heeft per ongeluk jouw cookie opgegeten. Onze informaticus Graaf Tel is bezig om het probleem te verhelpen.");
		}
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

	//ANDROID PROBLEMS: http://www.digiblog.de/2011/04/android-and-the-download-file-headers/ YOU COULD TRY THIS CODE BELOW, BUT IT ISN'T WORKING ON ANDROID YET!!! DISPLAYING PFD FILES IN THE BROWSER ONLY WORKS IN CHROME, WE NEED TO FIX THAT TOO!
	/*$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		$tmp_name = explode('=', $headers['Content-Disposition']);
	    if ($tmp_name[1]) $realfilename = trim($tmp_name[1],'";\'');
		if(stripos($ua,'android') !== false) {
			header("Content-Type: application/octet-stream");
			header('Content-Disposition: attachment; filename="'.$realfilename.'"');
		}else{
			header('Content-Type: '. $headers['Content-Type']);
			if($headers['Content-Type'] != 'application/pdf'){ //To display a PDF-file without downloading it in supported browsers
				header('Content-Disposition: ' . $headers['Content-Disposition']);
			}else{
				header('Content-Disposition: inline; filename="'.$realfilename.'"');
				//header('Content-Transfer-Encoding: binary');
	  			header('Accept-Ranges: bytes');
			}
		}
	*/
?>
