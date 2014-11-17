<?php
namespace mataphp;
define("_MATA_PHP_API_COOKIE_BASE_NAME",".mata-php.api.cookie.");

//if we're running on a terminal, let's check for stray cookie files.
/*
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
*/

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

class Attachment{
	public $title,$type,$url;
	public function __construct($ti,$ty,$ur){$this->set($ti,$ty,$ur);}
	public function set($ti,$ty,$ur){
		$this->title=$ti;
		$this->type=$ty;
		$this->url=$ur;
	}
}

class AssignmentList{
	public $title,$id,$subject,$description,$lastDate,$submittedDate;
	public function __construct($ti,$i,$sub,$desc,$lD,$sD){$this->set($ti,$i,$sub,$desc,$lD,$sD);}
	public function set($ti,$i,$sub,$desc,$lD,$sD){
		$this->title = $ti;
		$this->id = $i;
		$this->subject = $sub;
		$this->description = $desc;
		$this->lastDate = $lD;
		$this->submittedDate = $sD;
	}	
}

class Assignment{
	public $title,$id,$subject,$description,$teacherName,$lastDate,$submittedDate,$mark,$attachments,$submittedAssignments;
	public function __construct($ti,$i,$sub,$desc,$tN,$lD,$sD,$m,$at,$sbAs){$this->set($ti,$i,$sub,$desc,$tN,$lD,$sD,$m,$at,$sbAs);}
	public function set($ti,$i,$sub,$desc,$tN,$lD,$sD,$m,$at,$sbAs){
		$this->title = $ti;
		$this->id = $i;
		$this->subject = $sub;
		$this->description = $desc;
		$this->teacherName = $tN;
		$this->lastDate = $lD;
		$this->submittedDate = $sD;
		$this->mark = $m;
		$this->attachments = $at;
		$this->submittedAssignments = $sbAs;
	}
}

class SubmittedAssignment{
	public $date,$mark,$noteStudent,$noteTeacher,$attachments,$feedbackAttachments;
	public function __construct($d,$m,$nS,$nT,$at,$fbAt){$this->set($d,$m,$nS,$nT,$at,$fbAt);}
	public function set($d,$m,$nS,$nT,$at,$fbAt){
		$this->date = $d;
		$this->mark = $m;
		$this->noteStudent = $nS;
		$this->noteTeacher = $nT;
		$this->attachments = $at;
		$this->feedbackAttachments = $fbAt;
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
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		//curl_setopt($ch, CURLOPT_SSLVERSION, 3);
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
				if($attachmentItem["Uri"] === NULL || $attachmentItem["Uri"] == ""){$attachmentUrl='/studiewijzers/'.$studyguideId.'/onderdelen/'.$items["Id"].'/bijlagen/'.$attachmentItem["Id"];}elseif((strpos($attachmentItem["Uri"],'YoutubePlayer.aspx?youtubeid=')!== false) || (strpos($attachmentItem["Uri"],'youtube.com/watch?v=')!== false)) {$type=4;$youtubeId= explode("=", $attachmentItem["Uri"]);$attachmentUrl='https://www.youtube.com/watch?v='.$youtubeId[1];}elseif(strpos($attachmentItem["Uri"],'/api/leerlingen/')!== false){$type=1;$attachmentUrl=$attachmentUrl='/studiewijzers/'.$getId[0].'/onderdelen/'.$items["Id"].'/bijlagen/'.$attachmentItem["Id"];}else{$type=3;$attachmentUrl=$attachmentItem["Uri"];}
				$attachmentList[]=new Attachment($attachmentItem["Naam"],$type,$attachmentUrl);
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
	public static function getAssignmentList($session){
		$result=self::curlget('https://'.$session->school->url.'/api/leerlingen/'.$session->userId.'/opdrachten/status/openstaand?$skip=0&$top=20&$orderby=InleverenVoor%20DESC',$session->sessionId);
		$result=json_decode($result,true);
		$list = array();
		foreach($result["Items"] as $item){
			$lastDate=date_create($item["InleverenVoor"],timezone_open("UTC"));
			date_timezone_set($lastDate,timezone_open(date_default_timezone_get()));
			$submittedDate=date_create($item["IngeleverdOp"],timezone_open("UTC"));
			date_timezone_set($submittedDate,timezone_open(date_default_timezone_get()));
			$list[]=new AssignmentList($item["Titel"],$item["Id"],$item["Vak"],$item["Omschrijving"],$lastDate,$submittedDate);
		}
		return $list;
	}
	public static function getAssignment($session,$assignmentId){
		$result=self::curlget("https://".$session->school->url."/api/leerlingen/".$session->userId."/opdrachten/".$assignmentId,$session->sessionId);
		$result=json_decode($result,true);
		$lastDate=date_create($result["InleverenVoor"],timezone_open("UTC"));
		date_timezone_set($lastDate,timezone_open(date_default_timezone_get()));
		$submittedDate=date_create($result["IngeleverdOp"],timezone_open("UTC"));
		date_timezone_set($submittedDate,timezone_open(date_default_timezone_get()));
		$teachersName = $result["Docenten"][0]["Voornamen"].' '.$result["Docenten"][0]["Tussenvoegsel"].' '.$result["Docenten"][0]["Achternaam"];
		$attachmentList =  array();
		foreach($result["Bijlagen"] as $attachmentItem){ //Look for attachments
			$type = 1; //$type==1 = Just a normal attachment, $type == 2 = An assignment, $type == 3 = A website, $type == 4 = A Youtube Video.
			$attachmentUrl = '/opdrachten/bijlagen/'.$attachmentItem["Id"];
			$attachmentList[]=new Attachment($attachmentItem["Naam"],$type,$attachmentUrl);
		}
		$submittedAssignmentList=array();
		foreach($result["VersieNavigatieItems"] as $submittedAssignmentItem){ //Look for attachments
			$submittedAttachmentResult=self::curlget("https://".$session->school->url.$submittedAssignmentItem["Ref"]["Self"],$session->sessionId);
			$submittedAttachmentResult=json_decode($submittedAttachmentResult,true); //Let's decode it
			$submittedAttachmentList=array();
			foreach($submittedAttachmentResult["LeerlingBijlagen"] as $submittedAttachmentItem){ //Look for attachments
				$type = 1; //$type==1 = Just a normal attachment, I think you can only upload just a normal attachment.
				$attachmentUrl = '/opdrachten/bijlagen/Ingeleverd/'.$submittedAttachmentItem["Id"];
				$submittedAttachmentList[]=new Attachment($submittedAttachmentItem["Naam"],$type,$attachmentUrl);
			}
			$feedbackAttachmentList=array();
			foreach($submittedAttachmentResult["FeedbackBijlagen"] as $feedbackAttachmentItem){ //Look for attachments
				$type = 1; //$type==1 = Just a normal attachment, I think you can only upload just a normal attachment.
				$attachmentUrl = '/opdrachten/bijlagen/Ingeleverd/'.$feedbackAttachmentItem["Id"]; //THIS NEED TO BE CHANGED, AS MATA DOESNT WORK WITH FEEDBACKS!!
				$feedbackAttachmentList[]=new Attachment($feedbackAttachmentItem["Naam"],$type,$attachmentUrl);
			}
			$submittedAttachmentDate=date_create($submittedAttachmentResult["IngeleverdOp"],timezone_open("UTC"));
			date_timezone_set($submittedAttachmentDate,timezone_open(date_default_timezone_get()));
			$submittedAssignmentList[]=new SubmittedAssignment($submittedAttachmentDate,$submittedAttachmentResult["Beoordeling"],$submittedAttachmentResult["LeerlingOpmerking"],$submittedAttachmentResult["DocentOpmerking"],$submittedAttachmentList,$feedbackAttachmentList);
		}
		return new Assignment($result["Titel"],$result["Id"],$result["Vak"],$result["Omschrijving"],$teachersName,$lastDate,$submittedDate,$result["Beoordeling"],$attachmentList,$submittedAssignmentList);
	}
}

function getSchools($filter){return Mataphp::getSchools($filter);}
function login($school,$username,$password){return Mataphp::login($school,$username,$password);}
function getHomework($session,$fromdate,$todate){return Mataphp::getHomework($session,$fromdate,$todate);}
function getStudyGuideList($session){return Mataphp::getStudyGuideList($session);}
function getStudyGuideContent($session,$studyguideId){return Mataphp::getStudyGuideContent($session,$studyguideId);}
function getAssignment($session, $assignmentId){return Mataphp::getAssignment($session, $assignmentId);}
function getAssignmentList($session){return Mataphp::getAssignmentList($session);}
?>
