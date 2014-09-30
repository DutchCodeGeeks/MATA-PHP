<?php
namespace mataphp;
define("_MATA_PHP_API_COOKIE_BASE_NAME",".mata-php.api.cookie.");

//if we're running on a terminal, let's check for stray cookie files.
if(posix_isatty(STDERR)){
	$files=glob(_MATA_PHP_API_COOKIE_BASE_NAME."*");
	if(count($files)>0){
		file_put_contents("php://stderr","There ".(count($files)==1?"is ":"are ").count($files)." cookie file".(count($files)==1?"":"s")." left astray:\n",FILE_APPEND);
		foreach($files as $file)echo "* ".$file."\n";
		file_put_contents("php://stderr","Should th".(count($files)==1?"is":"ese")." be deleted? (y/n) ",FILE_APPEND);
		if(trim(fgets(STDIN))=="y"){
			foreach($files as $file)unlink($file);
		}
	}
}

class School{
	public $name,$url;
	public function __construct($n,$u){$this->set($n,$u);}
	public function set($n,$u){
		$this->name=$n;
		$this->url=$u;
	}
}

class Session{
	public $school; //of type School
	public $userId,$sessionId;
	public $realName; //of user
	public function __construct($s,$uid,$sid,$n){$this->set($s,$uid,$sid,$n);}
	public function __destruct(){
		if(file_exists(_MATA_PHP_API_COOKIE_BASE_NAME.$this->sessionId))
			unlink(_MATA_PHP_API_COOKIE_BASE_NAME.$this->sessionId);
	}
	public function set($s,$uid,$sid,$n){
		$this->school=$s;
		$this->userId=$uid;
		$this->sessionId=$sid; //not the one returned from the login request, but an internal identifier!
		$this->realName=$n;
	}
}

class Subject{
	public $short,$long;
	public function __construct($s,$l){$this->set($s,$l);}
	public function set($s,$l){
		$short=$s;
		$long=$l;
	}
}

class Homework{
	public $date; //of type Date
	public $subject; //of type Subject
	public $description,$content,$hour,$type;
	public function __construct($da,$s,$de,$c,$h,$t){$this->set($da,$s,$de,$c,$h,$t);}
	public function set($da,$s,$de,$c,$h,$t){
		$this->date=$da;
		$this->subject=$s;
		$this->description=$de;
		$this->content=$c;
		$this->hour=$h;
		$this->type=$t;
	}
}

class StudyGuideList{
	public $title,$id,$startDate,$endDate,$subject;
	public function __construct($ti,$i,$sd,$ed,$su){$this->set($ti,$i,$sd,$ed,$su);}
	public function set($ti,$i,$sd,$ed,$su){
		$this->title=$ti;
		$this->id=$i;
		$this->startDate=$sd;
		$this->endDate=$ed;
		$this->subject=$su;
	}
}

class StudyGuide{
	public $id,$startDate,$endDate,$title,$subject,$archived,$content;
	public function __construct($i,$sd,$ed,$ti,$su,$arch,$cont){$this->set($i,$sd,$ed,$ti,$su,$arch,$cont);}
	public function set($i,$sd,$ed,$ti,$su,$arch,$cont){
		$this->id=$i;
		$this->startDate=$sd;
		$this->endDate=$ed;
		$this->title=$ti;
		$this->subject=$su;
		$this->archived=$arch;
		$this->content=$cont;
	}

}

class StudyGuideContent{
	public $title,$content,$attachments;
	public function __construct($ti,$co,$at){$this->set($ti,$co,$at);}
	public function set($ti,$co,$at){
		$this->title=$ti;
		$this->content=$co;
		$this->attachments=$at;
	}
}

class StudyGuideAttachments{
	public $title,$type,$url;
	public function __construct($ti,$ty,$ur){$this->set($ti,$ty,$ur);}
	public function set($ti,$ty,$ur){
		$this->title=$ti;
		$this->type=$ty;
		$this->url=$ur;
	}
}

class Mataphp{
	//Passing a non-empty $postdata implies a POST reques; otherwise, a GET request is issued.
	//Passing $cookie_id implies using cookies; the id allows the use of multiple sessions at the same time.
	private function curlget($url,$cookie_id="",$postdata=""){
		$referer=parse_url($url);
		if($referer){
			$referer=$referer["scheme"]."://".$referer["host"];
		} else {
			//throw new \Exception("Mataphp:curlget:invalid_url, an invalid url was passed");
		}
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt($ch,CURLOPT_TIMEOUT,60);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		if($cookie_id&&$cookie_id!=""){
			curl_setopt($ch,CURLOPT_COOKIEJAR,_MATA_PHP_API_COOKIE_BASE_NAME.$cookie_id);
			curl_setopt($ch,CURLOPT_COOKIEFILE,_MATA_PHP_API_COOKIE_BASE_NAME.$cookie_id);
		}
		curl_setopt($ch,CURLOPT_REFERER,$referer);
		if($postdata!=""){
			curl_setopt($ch,CURLOPT_POSTFIELDS,$postdata);
			curl_setopt($ch,CURLOPT_POST,1);
		}
		$result=curl_exec($ch);
		curl_close($ch);
		//unlink(_MATA_PHP_API_COOKIE_BASE_NAME.$cookie_id);
		return $result;
	}
	private function encodeURIComponent($str){
		$revert=array('%21'=>'!','%2A'=>'*','%27'=>"'",'%28'=>'(','%29'=>')');
		return strtr(rawurlencode($str),$revert);
	}
	private function convertInfoType($it){
		switch($it){
		case 1:return "huiswerk";
		case 3:return "tentamen";
		case 4:return "schriftelijk";
		default:return "unknown";
		}
	}
	public static function getSchools($filter){
		if(ctype_space($filter)||strlen($filter)<3)return array();
		$result=self::curlget("https://schoolkiezer.magister.net/home/query?filter=".self::encodeURIComponent($filter));
		if($result[0]=="<")throw new \Exception("Mataphp:getSchools:invalid_server_response, server returned invalid response");
		$result=json_decode($result);
		foreach($result as &$item)$item=new School($item->Licentie,$item->Url);
		return $result;
	}
	//pass dates of type Date
	public static function getHomework($session,$fromdate,$todate){
		$result=self::curlget("https://".$session->school->url."/api/leerlingen/".$session->userId."/huiswerk/huiswerk?van=".date_format($fromdate,"Y-m-d\TH:i:s")."&tot=".date_format($todate,"Y-m-d\TH:i:s"),$session->sessionId);
		$result=json_decode($result,true);
		$list=array();
		foreach($result["Items"] as $itemgroup){
			foreach($itemgroup["Items"] as $i){
				$date=date_create($i["Datum"],timezone_open("UTC"));
				date_timezone_set($date,timezone_open(date_default_timezone_get()));
				$list[]=new Homework(
					$date,
					new Subject($i["VakAfkortingen"],$i["VakOmschrijvingen"]),
					$i["Omschrijving"],
					$i["Inhoud"],
					$i["Lesuur"],
					self::convertInfoType($i["InfoType"]));
			}
		}
		return $list;
	}
	public static function getStudyGuideList($session){
		$result=self::curlget('https://'.$session->school->url.'/api/leerlingen/'.$session->userId.'/studiewijzers?$skip=0&$top=50',$session->sessionId);
		$result=json_decode($result,true);
		$list=array();
		foreach($result["Items"] as $items){
			$startDate=date_create($items["Van"],timezone_open("UTC"));
			date_timezone_set($startDate,timezone_open(date_default_timezone_get()));
			$endDate=date_create($items["TotEnMet"],timezone_open("UTC"));
			date_timezone_set($endDate,timezone_open(date_default_timezone_get()));
			$list[]=new StudyGuideList($items["Titel"],$items["Id"],$startDate,$endDate,$items["VakCodes"][0]);
		}
		return $list;
	}
	public static function getStudyGuideContent($session,$studyguideId){
		$result=self::curlget("https://".$session->school->url."/api/leerlingen/".$session->userId."/studiewijzers/".$studyguideId,$session->sessionId);
		$result=json_decode($result,true);
		$contentList=array();
		foreach($result["Onderdelen"]["Items"] as $items){
			//Now, let's replace the obvious hyperlinks in the content! Example: <u>\n\\*HYPERLINK \"http://www.youtube.com/watch?v=A0VUsoeT9aM\"Draagbare energie</u> to: <a href="http://www.youtube.com/watch?v=A0VUsoeT9aM">Draagbare energie</a>
			$replacePattern = '/<u>[^"]*?"([^"]*?)"(.*?)<\/u>/'; //Search for these obvious hyperlinks
			$replaceWith = '<a href="\\1">\\2</a>'; //Change them to just HTML hyperlinks
			$newContent = preg_replace($replacePattern, $replaceWith, $items["Omschrijving"]); //Replace that shit
			$attachmentResult=self::curlget("https://".$session->school->url.$items["Ref"]["Self"],$session->sessionId);
			$attachmentResult=json_decode($attachmentResult,true); //Let's decode it
			$attachmentList=array();
			foreach($attachmentResult["Bronnen"] as $attachmentItem){ //Look for attachments
				$type = $attachmentItem["BronSoort"]; //$type==1 = Just a normal attachment, $type == 2 = An assignment, $type == 3 = A website, $type == 4 = A Youtube Video.
				if($attachmentItem["Uri"] === NULL || $attachmentItem["Uri"] == ""){$attachmentUrl='https://'.$session->school->url.$attachmentItem["Ref"]["Self"];}elseif((strpos($attachmentItem["Uri"],'YoutubePlayer.aspx?youtubeid=')!== false) || (strpos($attachmentItem["Uri"],'youtube.com/watch?v=')!== false)) {$type=4;$youtubeId= explode("=", $attachmentItem["Uri"]);$attachmentUrl='https://www.youtube.com/watch?v='.$youtubeId[1];}elseif(strpos($attachmentItem["Uri"],'/api/leerlingen/')!== false){$type=1;$attachmentUrl=$attachmentItem["Uri"];}else{$type=3;$attachmentUrl=$attachmentItem["Uri"];}
				$attachmentList[]=new StudyGuideAttachments($attachmentItem["Naam"],$type,$attachmentUrl);
			}
			$contentList[]=new StudyGuideContent($items["Titel"],$newContent,$attachmentList);
		}
		$list=array();
		$list=new StudyGuide($result["Id"],$result["Van"],$result["TotEnMet"],$result["Titel"],$result["VakCodes"][0],$result["InLeerlingArchief"],$contentList);
		return $list;
	}
	public static function login($school,$username,$password){
		$sessionId=uniqid();
		$result=self::curlget("https://".$school->url."/api/sessie",$sessionId,"Gebruikersnaam=".$username."&Wachtwoord=".$password);
		$result=json_decode($result,true);
		if(!array_key_exists("GebruikersId",$result)||$result["Message"]!="Succesvol ingelogd.")return false;
		//var_dump($result); ##DEBUG
		return new Session($school,$result["GebruikersId"],$sessionId,$result["Naam"]);
	}
}

function getSchools($filter){return Mataphp::getSchools($filter);}
function login($school,$username,$password){return Mataphp::login($school,$username,$password);}
function getHomework($session,$fromdate,$todate){return Mataphp::getHomework($session,$fromdate,$todate);}
function getStudyGuideList($session){return Mataphp::getStudyGuideList($session);}
function getStudyGuideContent($session,$studyguideId){return Mataphp::getStudyGuideContent($session,$studyguideId);}
?>
