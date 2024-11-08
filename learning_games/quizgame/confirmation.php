<?php
 
// Enable error logging: 
error_reporting(E_ALL ^ E_NOTICE);

// db file
$db_file = 'db.php';
			
// include the db file 
include_once $db_file;
// new db class 
$db = new db();
// connects to the db 
$db->db_connect();

// gets info needed
$userid = $db->get_userID();
$courseid = $db->get_courseID();
$coursename = $db->db_get_course_name($courseid);
$playerid = $db->db_get_playerID($userid, $courseid);

// notification type
$type = $_POST["type"];

// in case of challenge type, challenge id

$contestant = $_POST["contestant"];

// get contestant name
$cname = htmlentities($db->db_get_player_name($contestant),  ENT_COMPAT,'ISO-8859-1', true);

// notification id
if(!empty($_POST["nid"]))
	$nid = $_POST["nid"];

// initial html
echo('
<!DOCTYPE html>
<html>

	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			
		<div id = "content" >

			<script src="js/confirmation.js" type="text/javascript"></script>
			
			<div id = "type" value = "'.$type.'" style="display: none;"></div>
	
		</head>
	<body>
');

echo (' 
		<div id = "centered25">
		
			<br><br><br><br><br><br><br><br><br>
			<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div> ');
// challenge sending confirmation
if($type == "challenge")	
{	

	echo (' <div id = "contestant" value = "'.$contestant.'" style="display: none;"></div>
			<div id = "cname" value = "'.$cname.'" style="display: none;"></div>
			<div align = "center">
			<h1><challengefont>Challenge player '.$cname.'?</challengefont></h1></div>
				<br>
				<div align = "center">
					<button id = "confirmyes" class="sexybutton sexysimple sexyorange sexyxxxl">&nbspYes&nbsp</button>
					&nbsp&nbsp
					<button id = "confirmno" class="sexybutton sexysimple sexyorange sexyxxxl">&nbsp&nbspNo&nbsp&nbsp</button>
				</div>
			</div>
		');
}
// challenge accepting confirmation
else if ($type == "notechallenge") 
{
	$score = intval($_POST["score"]);
	$challenger = intval($_POST["challenger"]);
	$seed = $_POST["seed"];
	$c_name = htmlentities($db->db_get_player_name($challenger),  ENT_COMPAT,'ISO-8859-1', true);
	$questions_category = $_POST["questions_category"];
	$n_rounds = $_POST["n_rounds"];
	$round_time = $_POST["round_time"];
	$correct_value = $_POST["correct_value"];
	$wrong_value = $_POST["wrong_value"];
	$jump_value = $_POST["jump_value"];
	$time_value = $_POST["time_value"];
	
	echo (' <div id = "nid" value = "'.$nid.'" style="display: none;"></div>
			<div id = "score" value = "'.$score.'" style="display: none;"></div>
			<div id = "challenger" value = "'.$challenger.'" style="display: none;"></div>
			<div id = "seed" value = "'.$seed.'" style="display: none;"></div>
			<div id = "questions_category" value = "'.$questions_category.'" style="display: none;"></div>
			<div id = "n_rounds" value = "'.$n_rounds.'" style="display: none;"></div>
			<div id = "round_time" value = "'.$round_time.'" style="display: none;"></div>
			<div id = "correct_value" value = "'.$correct_value.'" style="display: none;"></div>
			<div id = "wrong_value" value = "'.$wrong_value.'" style="display: none;"></div>
			<div id = "jump_value" value = "'.$jump_value.'" style="display: none;"></div>
			<div id = "time_value" value = "'.$time_value.'" style="display: none;"></div>
			<div align = "center"><h1><challengefont>Accept challenge from '.$c_name.'?</challengefont></h1></div>
				<br>
				<div align = "center">
					<button id = "confirmyes" class="sexybutton sexysimple sexyorange sexyxxxl">&nbspAccept&nbsp</button>
					&nbsp&nbsp
					<button id = "confirmno" class="sexybutton sexysimple sexyorange sexyxxxl">&nbspRefuse&nbsp</button>
				</div>
			</div>
			');
}
else if ($type == "notemessage")
{
	// the message text
	if(!empty($_POST["text"]))
		$text = $_POST["text"];
	
		echo (' <div id = "nid" value = "'.$nid.'" style="display: none;"></div>
				<div align = "center"><h1><challengefont>'.$text.'<br>Delete this notification?</challengefont></h1></div>
				<br>
				<div align = "center">
					<button id = "confirmyes" class="sexybutton sexysimple sexyorange sexyxxxl">&nbspDelete&nbsp</button>
					&nbsp&nbsp
					<button id = "confirmno" class="sexybutton sexysimple sexyorange sexyxxxl">&nbspReturn&nbsp</button>
				</div>
			</div>
			');
}


// final html
echo('
		</div>
	</body>
</html>
');