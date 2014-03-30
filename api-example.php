<?php
require "api.php";
echo "Search for school: ";
$filter=trim(fgets(STDIN));
$schools=mataphp\getSchools($filter);
if(count($schools)==0)die("Could not find any schools matching that filter.\n");
if(count($schools)==1){
	$school=$schools[0];
} else {
	foreach($schools as $num=>$school){
		echo $num.") ".$school->name."\n";
	}
	echo "Which school? ";
	$choice=intval(trim(fgets(STDIN)));
	if($choice<0||$choice>count($schools))die("Invalid choice.\n");
	$school=$schools[$choice];
}
echo "Using school ".$school->name.".\n";
echo "Username: ";
$username=trim(fgets(STDIN));
echo "Password: ";
$password=trim(fgets(STDIN));
$session=mataphp\login($school,$username,$password);
var_dump($session);
var_dump(mataphp\getHomework($session,new DateTime("now"),new DateTime("now +7 day")));

var_dump(mataphp\getStudyGuidesList($session)); //Get All StudyGuides
//StudyGuides example:
$studyGuideId=421; //Change this Id to a correct StudyGuide Id
$StudyGuideContent = (mataphp\getStudyGuideContent($session,$studyGuideId));
$result=json_decode($data,true);
foreach($result["Onderdelen"]["Items"] as $items){
	echo('<h1> '.$items["Titel"].'</h1>'); //Title
	$oldDescription = $items["Omschrijving"];
	//Now, let's replace the obvious hyperlinks in the description! Example: <u>\n\\*HYPERLINK \"http://www.youtube.com/watch?v=A0VUsoeT9aM\"Draagbare energie</u> to: <a href="http://www.youtube.com/watch?v=A0VUsoeT9aM">Draagbare energie</a>
	$replacePattern = '/<u>[^"]*?"([^"]*?)"(.*?)<\/u>/'; //Search for these obvious hyperlinks
	$replaceWith = '<a href="\\1">\\2</a>'; //Change them to just HTML hyperlinks
	$newDescription = preg_replace($replacePattern, $replaceWith, $oldDescription); //Replace that shit
	echo($newDescription.'<br>'); //So now it outputs a new Description with just normal hyperlinks!
	$attachmentResult=(mataphp\getStudyGuideAttachments($session,$items["Ref"]["Self"])); //Call the Study Guide Attachments API with the link of the official MATA-API  Item of the StudyGuide
	$attachmentResult=json_decode($attachmentResult,true); //Let's decode it
	foreach($attachmentResult["Bronnen"] as $attachmentItem){ //Look for attachments
		if($attachmentItem["Uri"] === NULL){$attachmentUrl='https://'.$session->school->url.$attachmentItem["Ref"]["Self"];}else{$attachmentUrl=$attachmentItem["Uri"];}//Use the Self URL if it is a document or the Uri if it is a link to a website
		if($attachmentItem["BronSoort"] == 2){ //It is an assignment
			echo('---Assignment Attachment: <a href="'.$attachmentUrl.'">'.$attachmentItem["Naam"].'</a><br>');
		}else{ //It's just an website or real Attachment
			echo('---Attachment: <a href="'.$attachmentUrl.'">'.$attachmentItem["Naam"].'</a><br>'); //CHANGE INCLUDED YOUTUBE PLAYER, example: https://amadeus.swp.nl/5.6.19/YoutubePlayer.aspx?youtubeid=FPzy_9x006U
		}
	}
}
?>
