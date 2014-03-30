<?php
/*
Made by tkon99
special thanks to:
-tomsmeding (help with the code)
-stipmonster (magister rooster API)
--> github: https://github.com/tkon99/MATA-PHP
DEPRECATED INDEX NOT USED IN NEW VERSION!
ONLY HERE FOR API REFERENCE (E.G. URL'S)
*/

//error_reporting(0);

require "utils.php";

$this_file_name = basename(__FILE__);

function writeHomework(){
	global $urlname,$userId;
	if(date("N")==6){ //if it's saturday
		$monday=strtotime("Monday");
		$friday=strtotime("Friday");
	} else {
		$monday=strtotime("Monday this week");
		$friday=strtotime("Friday this week");
	}
	$result=curlget("https://$urlname/api/leerlingen/$userId/huiswerk/huiswerk?van=".date("Y-m-d",$monday)."T00:00&tot=".date("Y-m-d",$friday)."T23:59&groupBy=Dag",true);
	echo "<pre>";
	var_dump($result);
	ob_flush();
	echo "</pre>\n";
	echo "<h1>Huiswerk:</h1>\n";

	$days=array(1=>"Maandag",2=>"Dinsdag",3=>"Woensdag",4=>"Donderdag",5=>"Vrijdag");
	//var_dump($result["Items"]["1"]["Items"][0]);
	foreach($days as $daynum=>$dayname){
		echo "<b>$dayname</b><ul>";
		foreach($result as $items){
			foreach($items as $uur){
				foreach($uur as $item){
					foreach($item as $les){
						$datum = date('N',strtotime(array_shift(explode('T',$les["Datum"]))));
						if($datum == "$daynum"){
							$outline=array();
							if($les["Lesuur"]!=0)$outline[]=$les["Lesuur"];
							if($les["VakAfkortingen"]!=NULL)$outline[]=$les["VakAfkortingen"];
							switch($les["InfoType"]){
							case 1:$outline[]="<img src='https://mata-sgtongerlo.magister.net/Content/img/icon-huiswerk.png' width='32px' height='auto'> ".decode($les["Inhoud"]);break;
							case 3:$outline[]="<img src='https://mata-sgtongerlo.magister.net/Content/img/blue-tentamen.png' width='32px' height='auto'> ".decode($les["Inhoud"]);break;
							case 4:$outline[]="<img src='https://mata-sgtongerlo.magister.net/Content/img/blue-schriftelijk.png' width='32px' height='auto'> ".decode($les["Inhoud"]);break;
							default:$outline[]="<b>Onbekend(".$les["InfoType"].")</b>: ".decode($les["Inhoud"]);
							}
							echo "<li>".implode(" - ",$outline)."</li>\n";
							//echo "<li>".($les["Lesuur"]==0?NULL:$les["Lesuur"])." - ".$les["VakAfkortingen"]." - ".$les['Inhoud']."</li>";
						}
					}
				}
			}
		}
		echo "</ul>\n";
	}
}

function writeSchedules(){
	global $urlname,$userId;
	echo "<h1>Studiewijzers</h1>\n";
	$result=curlget("https://$urlname/api/leerlingen/$userId/studiewijzers?\$skip=0&\$top=50",true);
	$result=json_decode($result,true);
	echo "<ul>\n";
	foreach($result as $items){
		foreach($items as $item){
			$url=$this_file_name;
			$url.="?studie=";
			$url.=$item["Id"];
			$title=$item["Titel"];
			if(!empty($title)){
				echo("<li><a href=\"$url\">$title</a></li>\n");
			}
		}
	}
	echo "</ul>";
}

function writeLoginform(){
	readfile("loginform.html");
}

function doLogin(){
	global $username,$password,$urlname,$userId;
	echo "Mata-site is: ".$urlname."<br>";
	$result=curlget("https://$urlname/api/sessie",true,"Gebruikersnaam=".$username."&Wachtwoord=".$password);

	$result=json_decode($result,true);
	$naam=$result["Naam"];
	$userId=$result["GebruikersId"];
	$session=$result["SessieId"];
	$msg=$result["Message"];

	echo("Welkom, $naam<br>\nUw gebruikersId is: $userId<br>\nUw sessie is: $session<br>\nBericht: $msg\n");
	ob_flush();
}


if(!array_key_exists("username",$_POST)){
	writeLoginform();
	die();
}
$username=$_POST["username"]; //TODO make this more secure and shit!!!
$password=$_POST["password"];
$urlname=$_POST["schoolName"];
writeHTMLhead();
doLogin();
writeHomework();
writeSchedules();
writeHTMLfoot();
?>
