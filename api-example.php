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
var_dump(mataphp\getStudyGuidesList($session)); //Get All Study Guides

//Study Guide Content example:
	$StudyGuideId=421; //!!!*** IMPORTANT TO MAKE IT WORK: Change this Id to a correct StudyGuide Id. You could call one from the StudyGuidesList
	$StudyGuideContent = (mataphp\getStudyGuideContent($session,$StudyGuideId));
	$StudyGuideContentResult=json_decode($StudyGuideContent,true);
	foreach($StudyGuideContentResult as $items){
		echo('<div><h1>'.$items->title.'</h1>'); 
		echo'<p>'.$items->content.'</p>';
		foreach ($items->attachments as $attachmentItem) {
			if($attachmentItem->type == 1){echo('---Attachment: <a href="'.$attachmentItem->url.'" target="_blank">'.$attachmentItem->title.'</a><br>');
			}elseif($attachmentItem->type == 2){echo('---Assignment Attachment: <a href="'.$attachmentItem->url.'" target="_blank">'.$attachmentItem->title.'</a><br>');
			}elseif($attachmentItem->type == 3){echo('---Attachment: <a href="'.$attachmentItem->url.'" target="_blank">'.$attachmentItem->title.'</a><br>');
			}else{echo('---Youtube Attachment: <a href="'.$attachmentItem->url.'" target="_blank">'.$attachmentItem->title.'</a><br>');
			}
		 }
		echo'</div>'; 
	}
				
?>
