<?php
/*
Made by tkon99
special thanks to:
-tomsmeding (help with the code)
-stipmonster (magister rooster API)
--> github: https://github.com/tkon99/MATA-PHP
*/
function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}

$this_file_name="mata-beta.php";
if(array_key_exists("getschoolname",$_GET)){
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, "https://schoolkiezer.magister.net/home/query?filter=".encodeURIComponent($_GET["name"]));
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
	curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_REFERER, "https://schoolkiezer.magister.net/home/query");
	$result=curl_exec($ch);
	if($result[0]=="<")$result='[{"Licentie":"Fout!","Url":"fail..."}]';
	die($result);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Mata-beta</title>
<style>
body{
	font-family: Arial;
}
pre{
	font-size: 10px;
}
</style>
<script>
<?php
echo "var this_file_name=\"$this_file_name\";";
?>
function updateSchoolNameSelect(){
	var namepart,xhr;
	namepart=document.getElementById("schoolNamePart").value;
	xhr=new XMLHttpRequest();
	xhr.onreadystatechange = function(){
		if (xhr.readyState == 4 && xhr.status == 200){
			var select,response,i,elem;
			select=document.getElementById("schoolNameSelect");
			if(xhr.responseText=="")response=[];
			else response=JSON&&JSON.parse(xhr.responseText)||eval(xhr.responseText); //Because we all love eval, right? Right?
			select.innerHTML="";
			for(i=0;i<response.length;i++){
				elem=document.createElement("option");
				elem.setAttribute("value",response[i]["Url"]);
				elem.innerHTML=response[i]["Licentie"];
				select.appendChild(elem);
			}
		}
	}
	xhr.open("GET",this_file_name+"?getschoolname=1&name="+encodeURIComponent(namepart),true);
	xhr.send();
}
</script>
</head>
<body>
<?php
if(!array_key_exists("username",$_POST)){
	?>
	<form action=<?php echo '"'.$this_file_name.'"';?> method="post">
	<input type="text" id="schoolNamePart" placeholder="Naam van de school" onkeypress="updateSchoolNameSelect();"> &rarr;
	<select id="schoolNameSelect" name="schoolName"><option>-- Typ een deel van de naam --</option></select><br>
	Gebruikersnaam: <input type="text" name="username" placeholder="Gebruikersnaam"><br>
	Wachtwoord: <input type="password" name="password" placeholder="Wachtwoord"><br>
	<input type="submit" value="Log in">
	</form>
	</body>
	</html>
	<?php
	die();
} else {
	$username=$_POST["username"]; //TODO make this more secure and shit!!!
	$password=$_POST["password"];
	$urlname=$_POST["schoolName"];
	echo "Mata-site is: ".$urlname."<br>";
}
?>
<?php
error_reporting(0);
$login="https://$urlname/api/sessie";
$cookie=".cookie.txt";

$postdata = "Gebruikersnaam=".$username."&Wachtwoord=".$password;



$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL, $login);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt ($ch, CURLOPT_REFERER, $login);

curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt ($ch, CURLOPT_POST, 1);
$result = curl_exec ($ch);


$result = json_decode($result, true);
$naam = $result["Naam"];
$id = $result["GebruikersId"];
$session = $result["SessieId"];
$msg = $result["Message"];

curl_close($ch);

echo("Welkom, $naam<br>Uw gebruikersId is: $id<br>Uw sessie is: $session<br>Bericht: $msg");
ob_flush();
?>
<br><hr>
<?php
if(date("N")==6){ //if it's saturday
	$monday=strtotime("Monday");
	$friday=strtotime("Friday");
} else {
	$monday=strtotime("Monday this week");
	$friday=strtotime("Friday this week");
}
$huiswerk="https://$urlname/api/leerlingen/$id/huiswerk/huiswerk?van=".date("Y-m-d",$monday)."T00:00&tot=".date("Y-m-d",$friday)."T23:59&groupBy=Dag";
$ref="https://$urlname/";
//$strCookie="SESSION_ID=$session";

$ch = curl_init();
curl_setopt ($ch, CURLOPT_URL, $huiswerk);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt ($ch, CURLOPT_REFERER, $ref);

$result = curl_exec ($ch);
$result = json_decode($result, true);
?>
<pre>
<?php
var_dump($result);
ob_flush();
?>
</pre>
<br><hr><br><br>
<?php
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
						case 1:$outline[]="<b>Huiswerk</b>: ".utf8_decode($les["Inhoud"]);break;
						case 3:$outline[]="<b>Tentamen</b>: ".utf8_decode($les["Inhoud"]);break;
						case 4:$outline[]="<b>SO</b>: ".utf8_decode($les["Inhoud"]);break;
						default:$outline[]="<b>Onbekend(".$les["InfoType"].")</b>: ".utf8_decode($les["Inhoud"]);
						}
						echo "<li>".implode(" - ",$outline)."</li>\n";
						//echo "<li>".($les["Lesuur"]==0?NULL:$les["Lesuur"])." - ".$les["VakAfkortingen"]." - ".$les['Inhoud']."</li>";
					}
				}
			}
		}
	}
	echo "</ul>";
}
//echo($result["Items"]["0"]["Items"][0]["Lesuur"]);

curl_close($ch);
?>
</body>
</html>
