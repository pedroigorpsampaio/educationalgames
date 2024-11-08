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
$username = $db->db_get_user_name($userid);
$courseid = $db->get_courseID();
$coursename = $db->db_get_course_name($courseid);
$playerid = $db->db_get_playerID($userid, $courseid);

// current player record info
$player_record = $db->db_get_player_record($playerid);
$player_wins = $player_record['wins'];
$player_defeats = $player_record['defeats'];
$player_scoresum = $player_record['scoresum'];

// gets rank information array 
$ranks = $db->get_ranks();
$player_rank = $db->get_player_rank($player_wins);
$player_rank_name = $player_rank['rankname'];
$player_rank_wins = $player_rank['rankwins'];
$player_rank_image = $player_rank['imagepath'];
$player_rank_next = $db->get_wins_next_rank($player_wins);
$player_progress = round((($player_wins - $player_rank_wins) / ($player_rank_next - $player_rank_wins)) * 100);

// html

echo('
<!DOCTYPE html>
<html>

	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			
		<div id = "content" >
		
			<script src="js/load.js" type="text/javascript"></script>
			
		</head>

			<body>
				
				<div id = "centered25">
					<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
					<status align = "center" style = "text-align: center;">Achievements</gamecolor></status>
					<br>
					
					<div id = "rank" align = "center">
						<br><br><br>
						<br><h1><rankfont><b>Rank</h1></b></rankfont>
						<img src = "'.$player_rank_image.'" width = 110 height = 110>
						<br><h1><b><rankfont>'.$player_rank_name.'</h1></rankfont></b>
						<br> <defaultwhite>Rank Progress <br>
						<progress min="0" max="100" value="'.$player_progress.'" align = "left""></progress>
						<div class="after">'.$player_progress.'%</div></defaultwhite>
						<div id = "progwins" align = "center"> '.$player_wins.' / '.$player_rank_next.' wins </div>
					</div>
					
					<br></br></br>	

					<div align = "right">
						<button id = "menu" class="sexybutton sexysimple sexyorange sexyxxxl">Main Menu</button>
					</div>
					
				</div>
			</body>
</html>

');
