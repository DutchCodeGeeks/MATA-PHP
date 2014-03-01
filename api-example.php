<?php
require "api.php";
echo "Search for school: ";
$filter=trim(fgets(STDIN));
$schools=mataphp\getSchools($filter);
if(count($schools)==0)die("Could not find any schools matching that filter.\n");
foreach($schools as $num=>$school){
	echo $num.") ".$school->name."\n";
}
echo "Which school? ";
$choice=intval(trim(fgets(STDIN)));
if($choice<0||$choice>count($schools))die("Invalid choice.\n");
$school=$schools[$choice];
echo "Username: ";
$username=trim(fgets(STDIN));
echo "Password: ";
$password=trim(fgets(STDIN));
$session=mataphp\login($school,$username,$password);
var_dump($session);
?>
