<?php

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

// gets current config
$config = $db->db_get_course_config($courseid);

// gets list of categories in question bank
$cats = $db->db_get_categories();

$currentcat = intval($config['category']);
$currentround = intval($config['n_rounds']);
$currenttime = intval($config['time_limit']);

// gets current game time and round options 
$options = $db->get_options();
$op1 = $options[0];
$op2 = $options[1];
$op3 = $options[2];
$op4 = $options[3];
$op5 = $options[4];
$op6 = $options[5];

// string to mark option as checked
$round7 = "";
$round15 = "";
$round30 = "";
$time30 = "";
$time60 = "";
$time120 = "";

if($currentround == $op1) 
	$round7 = "checked";
else if($currentround == $op2)
	$round15 = "checked";
else
	$round30 = "checked";

if($currenttime == $op4)
	$time30 = "checked";
else if($currenttime == $op5)
	$time60 = "checked";
else
	$time120 = "checked";

// html
echo ('
<!DOCTYPE html>
<html>

	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		
		<link rel="stylesheet" href="css/options.css">  
		
		<script src="js/options.js" type="text/javascript"></script>
		<script src="js/load.js" type="text/javascript"></script>
		
		<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
		
		<div id = "cid" value = "'.$courseid.'" style="display: none;"></div>
		<div id = "currentcat" value = "'.$currentcat.'" style="display: none;"></div>
		<div id = "currentround" value = "'.$currentround.'" style="display: none;"></div>
		<div id = "currenttime" value = "'.$currenttime.'" style="display: none;"></div>
		
		<listfont><div id = "saveinfo" align = "left"></div></listfont>
		
	</head>
	<body>
	
		<div id = "centered25">
		
			<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
		
			<div align = "center">
				<status> 
					Options of '.$coursename.'
				</status><br>
			</div>
			<challengefont>
			<span id = "info">
				Rounds: The number of rounds of the game<br>
				Time: The time for each round of the game<br>
				Category: Moodle question bank category id
			<span>
			</challengefont>
			<br><br><br><br>
	<div id = "divoptions">
		<radiofont>
				<div class = "options">
				<input type="radio" name="rounds" value = "'.$op1.'" id="radio1" class="radio" '.$round7.'/>
				<label for="radio1">'.$op1.'</label>
				</div>

				<div class = "options">
				<input type="radio" name="rounds" value = "'.$op2.'" id="radio2" class="radio" '.$round15.'/>
				<label for="radio2">'.$op2.'</label>
				</div>

				<div class = "options">	
				<input type="radio" name="rounds" value = "'.$op3.'" id="radio3" class="radio" '.$round30.'/>
				<label for="radio3">'.$op3.'</label>
				</div>
		
				<br><br><br><br>

				<div class = "options">	
				<input type="radio" name="time" value = "'.$op4.'" id="radio4" class="radio" '.$time30.'/>
				<label for="radio4">'.$op4.'s</label>
				</div>

				<div class = "options">	
				<input type="radio" name="time" value = "'.$op5.'" id="radio5" class="radio" '.$time60.'/>
				<label for="radio5">'.$op5.'s</label>
				</div>

				<div class = "options">	
				<input type="radio" name="time" value = "'.$op6.'" id="radio6" class="radio" '.$time120.'/>
				<label for="radio6">'.$op6.'s</label>
				</div>
		</radiofont>
	</div>
	
	<div align = "center" id = "btns">
			<button id = "save" class="sexybutton sexysimple sexyorange sexyxxxl">&nbspSave&nbsp</button>
			&nbsp&nbsp&nbsp
			<button id = "return" class="sexybutton sexysimple sexyorange sexyxxxl">Return</button>
	</div>
				<div id = "ddbox">
				<div class="wrapper-demo">
					<div id="dd" class="wrapper-dropdown-1" tabindex="1">
						<span id = "cat">'.htmlentities($db->db_get_category_name($currentcat),  ENT_COMPAT,'ISO-8859-1', true).'</span>
						<ul class="dropdown" tabindex="1">
');
// loops through categories to shown in dropdown box

foreach($cats as $cat) {
	echo ('<li><a href="#"><span id = "cat">'.htmlentities($db->db_get_category_name($cat['category']),  ENT_COMPAT,'ISO-8859-1', true).'</span></a></li>');
}

echo('
						</ul>
					</div>
				​</div>
			</div>
			
			<div align = "left" id = "divrounds">Rounds: </divrounds>
			<div align = "left" id = "divtime">Time: </divrounds>
			<div align = "left" id = "divcategory">Category: </divrounds>
			
			<br><br>
		</div>
	</body>
</html>
');