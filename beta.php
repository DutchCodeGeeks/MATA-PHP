<?php
//beta no api interface yet, just variables for now
//if you make it you can commit!
$username="gebruikersnaam"; 
$password="wachtwoord"; 
?>
<style>
body{
	font-family: Arial;
}
code{
	font-size: 10px;
}
</style>
<?php
error_reporting(0);
$login="https://mata-sgtongerlo.magister.net/api/sessie";
$cookie="cookie.txt"; 

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
?>
<br><hr>
<?php
$huiswerk="https://mata-sgtongerlo.magister.net/api/leerlingen/$id/huiswerk/huiswerk";
$ref = "https://mata-sgtongerlo.magister.net/";
$cookie="cookie.txt"; 
$strCookie = "SESSION_ID=$session";

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
<code>
<?php
var_dump($result);
?>
</code>
<?php
echo '<br><hr><br><br>';
//var_dump($result["Items"]["1"]["Items"][0]);
echo "Maandag:";
foreach($result as $items){
	foreach($items as $uur){
		foreach($uur as $item){
			foreach($item as $les){
				$datum = $les["Datum"];
				$datum = array_shift(explode('T', $datum));
				$datum = strtotime($datum);
				$datum = date('N', $datum);
				if($datum == "1"){
				echo "<br>";
				echo ($les["Lesuur"].' - '.$les["VakAfkortingen"].' - '.$les['Inhoud']);
				}
			}
		}
	}
}
echo "<br><br>";
echo "Dinsdag:";
foreach($result as $items){
	foreach($items as $uur){
		foreach($uur as $item){
			foreach($item as $les){
				$datum = $les["Datum"];
				$datum = array_shift(explode('T', $datum));
				$datum = strtotime($datum);
				$datum = date('N', $datum);
				if($datum == "2"){
				echo "<br>";
				echo ($les["Lesuur"].' - '.$les["VakAfkortingen"].' - '.$les['Inhoud']);
				}
			}
		}
	}
}
echo "<br><br>";
echo "Woensdag:";
foreach($result as $items){
	foreach($items as $uur){
		foreach($uur as $item){
			foreach($item as $les){
				$datum = $les["Datum"];
				$datum = array_shift(explode('T', $datum));
				$datum = strtotime($datum);
				$datum = date('N', $datum);
				if($datum == "3"){
				echo "<br>";
				echo ($les["Lesuur"].' - '.$les["VakAfkortingen"].' - '.$les['Inhoud']);
				}
			}
		}
	}
}
echo "<br><br>";
echo "Donderdag:";
foreach($result as $items){
	foreach($items as $uur){
		foreach($uur as $item){
			foreach($item as $les){
				$datum = $les["Datum"];
				$datum = array_shift(explode('T', $datum));
				$datum = strtotime($datum);
				$datum = date('N', $datum);
				if($datum == "4"){
				echo "<br>";
				echo ($les["Lesuur"].' - '.$les["VakAfkortingen"].' - '.$les['Inhoud']);
				}
			}
		}
	}
}
echo "<br><br>";
echo "Vrijdag:";
foreach($result as $items){
	foreach($items as $uur){
		foreach($uur as $item){
			foreach($item as $les){
				$datum = $les["Datum"];
				$datum = array_shift(explode('T', $datum));
				$datum = strtotime($datum);
				$datum = date('N', $datum);
				if($datum == "5"){
				echo "<br>";
				echo ($les["Lesuur"].' - '.$les["VakAfkortingen"].' - '.$les['Inhoud']);
				}
			}
		}
	}
}
//echo($result["Items"]["0"]["Items"][0]["Lesuur"]);

curl_close($ch);
?>
