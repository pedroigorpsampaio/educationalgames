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

// array of players from current course
$tags = parse_ini_file(__DIR__ . "/../../config.ini"); 
$root = $tags['root'];
require_once $root . '/blocks/games/obj/player.php';
$players = $db->db_get_players($courseid);

// shuffles players array
shuffle($players);

echo('
<!DOCTYPE html>
<html>

	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			
		<div id = "content" >
			
			<script src="js/multiplayer.js" type="text/javascript"></script>
			<script src="js/load.js" type="text/javascript"></script>
			
			
		</head>

		<body>
			
			<div id = "centered25">

			<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>

');

/***** constructs the list of players to challenge	*****/	

echo ('<div id = "listops">');	

echo ('		<div align = "center">
				<status> 
					Challenge Other Players!
				</status><br>
				<challengefont><b>My Status<br> Wins: '.$player_wins.' 
					&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Defeats: '.$player_defeats.'
					&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Overall: '.$player_scoresum.' points</b></challengefont>
			</div><br>');

if(count($players) == 1)
{
	echo ('<br>	<div align = "center">
				<h1><listfont><b>No other players to challenge.</b></listfont></h1>
			</div>');
}

// loop through course players to get all infos
foreach($players as $challengee)
{
	$cid =  $challengee->playerid;
	
	// if challengee is the challenger, ignore
	if($cid == $playerid)
		continue;
	
	// gets all info from challengee
	$cname =  htmlentities($challengee->name,  ENT_COMPAT,'ISO-8859-1', true);
	$crecord = $db->db_get_player_record($cid);
	$cwins = $crecord['wins'];
	$cdefeats = $crecord['defeats'];
	$cscoresum = $crecord['scoresum'];
	
	// challengee rank info
	$c_rank = $db->get_player_rank($cwins);
	$c_rank_name = $c_rank['rankname'];
	$c_rank_image = $c_rank['imagepath'];
	
	// open a div with id = $playerid, challengee player
	echo('				<div id = '.$cid.' class = "challengee" >');
	echo "\r\n";
	// player challengee info
	echo('					<listfont><b><span class = "unselectable" id = '.$cid.' >
									<a href = "javascript:void(0)" id = '.$cid.' class="tooltip2" style = "position: relative; display: inline; ">
										<img id = '.$cid.' class = "rankimg" src = "'.$c_rank_image.'">
										<span id = '.$cid.' class = "clickable" style = "position: absolute; width = 100px ;padding-right: 12px; top: -550%; left: -3.2em">
										<div id = '.$cid.'><strong><div id = '.$cid.'>'.$c_rank_name.'</div></strong></div></span>
									</a>
									&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'.$cname.'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
									'.$cwins.' Wins&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
									'.$cdefeats.' Defeats&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
									<span id = '.$cid.' class = "overall">Overall: '.$cscoresum.' points</span>
							</b></listfont>');
	echo "\r\n";
	// close div 
	echo('				</div>');
	echo "\r\n";
}
			
echo('
					</br></br>	
					
					<div align = "right">
						<button id = "menu" class="sexybutton sexysimple sexyorange sexyxxxl">Main Menu</button>
					</div>
				</div>
				</br>
			
			</div>

		</body>
	</div>
	
</html>
');

