<?php
class School{
	public $name;
	public $url;
	public function __construct($n,$u){
		$this->set($n,$u);
	}
	public function set($n,$u){
		$this->name=$n;
		$this->url=$u;
	}
}

class Mataphp{
	private $cookie_file_name=".mata-php.api.cookie.txt";
	private function curlget($url,$usecookie=false,$postdata=""){
		$referer=parse_url($url);
		if($referer){
			$referer=$referer["scheme"]."://".$referer["host"];
		} else {
			throw new Exception("Mataphp:curlget:invalid_url, an invalid url was passed");
		}
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt($ch,CURLOPT_TIMEOUT,60);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		if($usecookie)curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie_file_name);
		curl_setopt($ch,CURLOPT_REFERER,$referer);
		if($postdata!=""){
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch,CURLOPT_POST,1);
		}
		$f=fopen("/Users/Tom/Sites/Site/utilscurllog.txt","a"); ##DEBUG
		fwrite($f,"url=$url\nusecookie=$usecookie\npostdata=$postdata\nreferer=$referer\n"); ##DEBUG
		$result=curl_exec($ch);
		curl_close($ch);
		fwrite($f,"result=$result\n\n"); ##DEBUG
		fclose($f); ##DEBUG
		return $result;
	}
	private function encodeURIComponent($str){
		$revert=array('%21'=>'!','%2A'=>'*','%27'=>"'",'%28'=>'(','%29'=>')');
		return strtr(rawurlencode($str),$revert);
	}
	public static function getSchools($filter){
		$result=self::curlget("https://schoolkiezer.magister.net/home/query?filter=".self::encodeURIComponent($filter));
		if($result[0]=="<")throw new Exception("Mataphp:getSchools:invalid_server_response, server returned invalid response");
		$result=json_decode($result);
		foreach($result as &$item)$item=new School($item->Licentie,$item->Url);
		return $result;
	}
}
?>
