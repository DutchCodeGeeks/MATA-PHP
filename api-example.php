<?php
require "api.php";

//Running in the terminal? Enable this code:

/*
echo "Search for school: ";
$filter=trim(fgets(STDIN));/
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
*/

//Otherwise, just use this to search for schools:
$filter='XXXXX'; //<------- REPLACE XXXXX with your school's name. 
$schools=mataphp\getSchools($filter);
if(count($schools)==1){
  $school=$schools[0]; //Use the first school.
  var_dump($school);
} else {
  	var_dump($schools); //So if there are multiple schools found, then you can do some fancy stuff here
  	$school=$schools[0]; //But for just this example we will use the first school, you can edit this of course
}

//Or even more easier, if you just want to use one school, then just enable this code and disable the search code above:
/*
	class School {
	  public $url;
	  public function __construct($getUrl){
	    $this->url = $getUrl;
	  }
	}

	$school = new School('mata-XXXXX.magister.net'); //<-----If you know the Mata URL, just replace the XXXXX with the right school URL. Found on: https://schoolkiezer.magister.net/#/nieuwekeuze
*/

$username="XXXXX";//<---Here your username. Disable this if you running this on the terminal.
$password="XXXXX";//<---Here your password. Disable this if you running this on the terminal.
$session=mataphp\login($school,$username,$password);
echo"Session: "; var_dump($session);
echo"<br><br>Homework: "; var_dump(mataphp\getHomework($session,new DateTime("now"),new DateTime("now +7 day")));
echo"<br><br>StudyGuide list: "; var_dump(mataphp\getStudyGuideList($session)); //Get All Study Guides
echo"<br><br>Assignment list: "; var_dump(mataphp\getAssignmentList($session)); //Get All Assignments

//Study Guide Content example:
/*
	$studyGuideId=1398; //!!!*** IMPORTANT TO MAKE IT WORK: Change this Id to a correct StudyGuide Id. You could call one from the StudyGuidesList
	$studyGuide = (mataphp\getStudyGuideContent($session,$studyGuideId));
	foreach($studyGuide->content as $items){
		echo("<div><h3>".$items->title."</h3>\n");
		echo"<p>".$items->content."</p>";
		foreach ($items->attachments as $attachmentItem) {
			if($attachmentItem->type == 1){echo('Attachment <a href="attachment.php?schoolurl='.$session->school->url.'&studentid='.$session->userId.'&url='.$attachmentItem->url.'&sessionid='.$session->sessionId.'">'.$attachmentItem->title."</a><br>\n");
			}elseif($attachmentItem->type == 2){echo('Assignment: <a href="'.$attachmentItem->url.'" target="_blank">'.$attachmentItem->title."</a><br>\n");
			}elseif($attachmentItem->type == 3){echo('URL Link: <a href="'.$attachmentItem->url.'" target="_blank">'.$attachmentItem->title."</a><br>\n");
			}else{
				echo('YouTube video: <a href="'.$attachmentItem->url.'" target="_blank">'.$attachmentItem->title."</a><br>\n");
			}
		}
		echo"</div>\n";
	}

*/
//Assignment example:
/*
	$assignmentId=749; //!!!*** IMPORTANT TO MAKE IT WORK: Change this Id to a correct Assignment Id. You could call one from the AssignmentList
	$assignment = (mataphp\getAssignment($session,$assignmentId));
	echo("<div><h2>".$assignment->title." - ".$assignment->subject." - ".$assignment->teacherName."</h2>\n");
	echo"<p>".$assignment->description."</p>";
	echo"<p>Uiterste inleverdatum: ".$assignment->lastDate->format('d-m-Y H:i')."</p>";
	echo"<p>Ingeleverd op: ".$assignment->submittedDate->format('d-m-Y H:i')."</p>";
	echo"<p>Beoordeling (Onbekend of dit wel werkt): ".$assignment->mark."</p>";
	echo"<h3>Bijlagen:</h3>";
	foreach ($assignment->attachments as $attachmentItem) {
		echo('Attachment <a href="attachment.php?schoolurl='.$session->school->url.'&studentid='.$session->userId.'&url='.$attachmentItem->url.'&sessionid='.$session->sessionId.'">'.$attachmentItem->title."</a><br>\n");
	}
	echo"<h3>Ingeleverde opdracht(en):</h3>";
	foreach ($assignment->submittedAssignments as $submittedAssignmentItem){
	echo"<h4>Ingeleverde opdracht:</h4>";
		foreach ($submittedAssignmentItem->attachments as $attachmentItem) {
			echo('Attachment <a href="attachment.php?schoolurl='.$session->school->url.'&studentid='.$session->userId.'&url='.$attachmentItem->url.'&sessionid='.$session->sessionId.'">'.$attachmentItem->title."</a><br>\n");
		}
		echo"<p>Ingeleverd op: ".$submittedAssignmentItem->date->format('d-m-Y H:i')."</p>";
		echo"<p>Opmerking leerling:	".$submittedAssignmentItem->noteStudent."</p>";	
		echo"<h4>Feedback: (MATA'S BIJLAGEN WERKEN NIET)</h4>";
		echo"<p>Opmerking docent: (MATA GEEFT DIT OOK NIET WEER): ".$submittedAssignmentItem->noteTeacher."</p>";
		foreach ($submittedAssignmentItem->feedbackAttachments as $attachmentItem) {
			echo('Attachment <a href="attachment.php?schoolurl='.$session->school->url.'&studentid='.$session->userId.'&url='.$attachmentItem->url.'&sessionid='.$session->sessionId.'">'.$attachmentItem->title."</a><br>\n");
		}
	echo"<p>Beoordeling (Onbekend of dit wel werkt): ".$submittedAssignmentItem->mark."</p>";

	}
	echo"</div>\n";
*/
?>
