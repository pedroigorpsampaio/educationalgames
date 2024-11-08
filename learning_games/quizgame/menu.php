<?php

// Enable error logging: 
error_reporting(E_ALL ^ E_NOTICE);

$db_file = 'db.php';
// include the db file 
include_once $db_file;
// new db class (db class = db quiz game methods + db plugin methods)
$db = new db();

// gets info to be displayed in this menu
$userid = $db->get_userID();
$username = $db->db_get_user_name($userid);
$courseid = $db->get_courseID();
$coursename = $db->db_get_course_name($courseid);
$playerid = $db->db_get_playerID($userid, $courseid);

// creates entry for this player records, if it doesnt exist yet
$db->db_register_player_record($playerid);

$test = $db->db_get_course_config($courseid);

// creates a initial config for the game if it does not have yet
if(is_null($test['category']))
{
	$cats = $db->db_get_categories();
	
	if(empty($cats))
		$cat = 1;
	else
		$cat = $cats[0]['category'];
	
	$db->db_save_course_config($courseid, 15, 30, $cat);
}
	
// path to games plugin menu
$plugin_path = $tags['wwwroot']. '/blocks/games/games.php?id=' . $courseid;

echo('
<!DOCTYPE html>
<html>

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="content-type">
		
<div id = "content" >
        <title>Quiz Game</title>

		<script src="js/load.js" type="text/javascript"></script>
		<script src="js/notechecker.js" type="text/javascript"></script>
		
		<div id = "sendee" value = "'.$playerid.'" style="display: none;"></div>
		<div id = "nummessages" style="display: none;">0</div>
		
    </head>

    <body>
		
		<div id = "centered25">

			<p><img src = "images/banner.png" width = "800" height = "160"></p>
			
			<div align="center" >
				<h1>Quiz Game</h1>
				<h2>'.htmlentities($coursename,  ENT_COMPAT,'ISO-8859-1', true).'</h2>
				<p class="intro"> Hello '.htmlentities($username,  ENT_COMPAT,'ISO-8859-1', true).'!</p>
			</div>
			</br></br>
				
			<div id = "inner">
					
				<button id = "single" class="sexybutton sexysimple sexyorange sexyxxxl">Singleplayer</button><p>
				
				<div id = "inner"><button id = "multi" class="sexybutton sexysimple sexyorange sexyxxxl">Multiplayer</button><p></div>

			</div>
				
			</br></br></br>
					
			<div align="right" >
				');
				
// only visible to editing teacher and manager
// has capability = block/games: manager

// gets capability
$cap = $db->get_user_capability();

// only editing teachers and managers can see cog icon (options menu)
if ($cap == "manage")
{
	echo('		<a href = "javascript:void(0)" class="tooltip">
					<div id = "options" style = "display: inline;"><img id = "cog" src = "images/options.png" width = "56" height = "56">
					<span class = "clickable" style = "left: -30%;"><strong>Options</strong></span></div>
				</a>'
		);
}
				
//check if user has new notification to display stamp with notification qnt
echo('<a href = "javascript:void(0)" class="tooltip">
		<div id = "notification"><img class = "clickable" src = "images/notification.png" width = "64" height = "64">');

$nt_quantity = check_notification($playerid);

if($nt_quantity > 0) {
	
	$strqtd = strval($nt_quantity);
	$qtdlen = strlen($strqtd);
	// calculates line height of stamp
	$lheight = calc_leight($qtdlen);
	// calculates top offset of stamp
	$top = calc_top($qtdlen);
	// calculates left offset of stamp
	$left = calc_left($qtdlen);
	
	echo('
			<stamp style = "line-height: '.$lheight.'%; top: '.$top.'%; left: '.$left.'%;">
				<ntcolor>'.$nt_quantity.'</ntcolor>
			</stamp>
		');
}

echo('
					<span class = "clickable"><strong>Notifications</strong></span></div>
				</a>
				<a href = "javascript:void(0)" class="tooltip">
					<div id = "achievement" style = "display: inline;"><img class = "clickable" src = "images/achievement.png" width = "64" height = "64">
					<span class = "clickable"><strong><div id = "achievement">Achievements</div></strong></span></div>
				</a>
				<a href = "javascript:void(0)" class="tooltip">
					<div id = "ranking" style = "display: inline;"><img class = "clickable" src = "images/ranking.png" width = "64" height = "64">
					<span class = "clickable"><strong>Ranking</strong></span></div>
				</a>

			</div>
			

			<a href = '.$plugin_path.' class="tooltip2">
				<img class = "clickable" src = "images/exit.png" align = "left" width = "54" height = "54">
				<exit align = "left"><span class = "clickable"><strong>Exit</strong></span></exit>
			</a>
		</div>
		
    </body>
</div>
</html>
');

// check if user has new notifications
function check_notification($sendee)
{
	$db = $GLOBALS['db'];
	
	return $db->db_check_notifications($sendee);
}

// calculates line height of stamp
function calc_leight($qtdlen)
{
	if($qtdlen == 1)
		return 50;
	else
		return calc_leight($qtdlen - 1) + (50+($qtdlen*5));
}

// calculates top offset of stamp
function calc_top($qtdlen)
{
	if($qtdlen == 1)
		return -400;
	else
		return calc_top($qtdlen - 1) - 50;
}

// calculates top offset of stamp
function calc_left($qtdlen)
{
	if($qtdlen == 1)
		return 50;
	else
		return calc_left($qtdlen - 1) - 5;
}
