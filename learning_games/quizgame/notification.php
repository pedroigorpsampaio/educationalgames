<?php

// db file
$db_file = 'db.php';
			
// include the db file 
include_once $db_file;
// new db class 
$db = new db();
// connects to the db 
$db->db_connect();

// gets info of this current player
$userid = $db->get_userID();
$username = $db->db_get_user_name($userid);
$courseid = $db->get_courseID();
$coursename = $db->db_get_course_name($courseid);
$sendee = $db->db_get_playerID($userid, $courseid);

// array of notifications to this current player
$notifications = $db->db_get_notifications($sendee);

// html
echo('
<!DOCTYPE html>
<html>

	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			
		<div id = "content" >
			
			<script src="js/notification.js" type="text/javascript"></script>
			<script src="js/load.js" type="text/javascript"></script>
			
		</head>

		<body>
			
			<div id = "centered25">
				<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
				<status align = "center" style = "text-align: center;">Notifications</gamecolor></status>
				<br>

');

if(count($notifications) == 0)
{
	echo ('<br>	<div align = "center">
				<h1><listfont><b>No new notifications.</b></listfont></h1>
			</div>');
}

foreach($notifications as $note)
{
	$nid = $note['id'];
	$sender = $note['sender'];
	$sname = htmlentities($db->db_get_player_name($sender),  ENT_COMPAT,'ISO-8859-1', true);
	$type = $note['type'];
	$text = $note['text'];
	
	// open a div with all infos 
	echo('				<div id = '.$nid.' class = "notifications" >');
	echo "\r\n";
	
	// gets all info for challenge notes
	if($type == "challenge") {
		// display info
		$dtype = "Challenge";
		$c_record = $db->db_get_player_record($sender);
		$c_wins = $c_record['wins'];
		$c_defeats = $c_record['defeats'];
		$c_scoresum = $c_record['scoresum'];

		// challengee rank info
		$c_rank = $db->get_player_rank($c_wins);
		$c_rank_name = $c_rank['rankname'];
		$c_rank_image = $c_rank['imagepath'];
		
		// necessary info for the duel
		$c_score = $note['score'];
		$c_seed = $note['seed'];
		$questions_category = $note['questions_category'];
		$n_rounds = $note['n_rounds'];
		$round_time = $note['round_time'];
		$correct_value = $note['correct_value'];
		$wrong_value = $note['wrong_value'];
		$jump_value = $note['jump_value'];
		$time_value = $note['time_value'];		

		// notification info
		echo('					<listfont2><b><span class = "unselectable" id = '.$nid.' >
									<a href = "javascript:void(0)" id = '.$nid.' class="tooltip2" style = "position: relative; display: inline; ">
										<img id = '.$nid.' class = "rankimg" src = "'.$c_rank_image.'" width = 40 height = 40>
										<span id = '.$nid.' class = "clickable" style = "position: absolute; width = 100px ;padding-right: 12px; top: -550%; left: -3.2em">
										<div id = '.$nid.'><strong><div id = '.$nid.'>'.$c_rank_name.'</div></strong></div></span>
									</a>
									&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'.$sname.'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
									'.$c_wins.' Wins&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
									'.$c_defeats.' Defeats&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
									Overall: '.$c_scoresum.' points
									<span id = '.$nid.' class = "type">'.$dtype.'</span>
									<div id = "type'.$nid.'" value = "'.$type.'" style="display: none;"></div>
									<div id = "sender'.$nid.'" value = "'.$sender.'" style="display: none;"></div>
									<div id = "score'.$nid.'" value = "'.$c_score.'" style="display: none;"></div>
									<div id = "seed'.$nid.'" value = "'.$c_seed.'" style="display: none;"></div>
									<div id = "questions_category'.$nid.'" value = "'.$questions_category.'" style="display: none;"></div>
									<div id = "n_rounds'.$nid.'" value = "'.$n_rounds.'" style="display: none;"></div>
									<div id = "round_time'.$nid.'" value = "'.$round_time.'" style="display: none;"></div>
									<div id = "correct_value'.$nid.'" value = "'.$correct_value.'" style="display: none;"></div>
									<div id = "wrong_value'.$nid.'" value = "'.$wrong_value.'" style="display: none;"></div>
									<div id = "jump_value'.$nid.'" value = "'.$jump_value.'" style="display: none;"></div>
									<div id = "time_value'.$nid.'" value = "'.$time_value.'" style="display: none;"></div>
								</b></listfont2>');
	} 
	else if($type == "message") {
		// notification info
		echo('					<listfont2><b><span class = "unselectable" id = '.$nid.' >You have a message from '.$sname.'
										<span id = '.$nid.' class = "type">Message</span>
										<div id = "type'.$nid.'" value = "'.$type.'" style="display: none;"></div>
										<div id = "text'.$nid.'" value = "'.$text.'" style="display: none;"></div>
								</b></listfont2>');
	}
	else if($type == "achievement") {
		echo('					<listfont2><b><span class = "unselectable" id = '.$nid.' >You have a new achievement!
										<span id = '.$nid.' class = "type">Achievement</span>
										<div id = "type'.$nid.'" value = "'.$type.'" style="display: none;"></div>
										<div id = "text'.$nid.'" value = "'.$text.'" style="display: none;"></div>
								</b></listfont2>');		
	}
	
	echo "\r\n";
	// close div 
	echo('				</div>');
	echo "\r\n";
	
}

// end html
echo('
					</br></br>	
					
					<div align = "right">
						<button id = "menu" class="sexybutton sexysimple sexyorange sexyxxxl">Main Menu</button>
					</div>
					<br>
				</div>
			
			</div>

		</body>
	</div>
	
</html>
');