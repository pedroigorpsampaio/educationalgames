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

// current player record info
$player_record = $db->db_get_player_record($playerid);
$player_wins = $player_record['wins'];
$player_defeats = $player_record['defeats'];
$player_scoresum = $player_record['scoresum'];
// player ratio
if($player_defeats == 0)
	$player_ratio = $player_wins;
else
	$player_ratio = $player_wins * 1.0 / $player_defeats;

$player_ratio = number_format($player_ratio, 2, '.', '');

// arrays of ordered records from current course
$recwins = $db->db_get_records("wins", $courseid);
$recdefeats = $db->db_get_records("defeats", $courseid);
$recscore = $db->db_get_records("scoresum", $courseid);
$recratio = $db->db_get_records("ratio", $courseid);

$ordername = "wins";

// what is the desired ordenation?
if(empty($_POST["order"]))
	$recs = $recwins;
else {
	$ord = $_POST["order"];
	switch($ord) {
		case "wins": $recs = $recwins ; $ordername = "wins" ; break; 
		case "defeats": $recs = $recdefeats ; $ordername = "defeats" ; break; 
		case "scoresum": $recs = $recscore ; $ordername = "scoresum" ; break; 
		case "ratio": $recs =  $recratio  ; $ordername = "ratio" ; break; 
		default: $recs = $recwins ; $ordername = "wins" ; break; 
	}
}

// look for player pos in desired order
for($i = 0; $i < count($recs); $i++) {
	if($recs[$i]['playerid'] == $playerid) {
		$playerpos = $i+1; 
		break;
	}
}

// html
echo ('
<!DOCTYPE html>
<html>

	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		
		<link rel="stylesheet" href="css/ranking.css">  
		
		<script src="js/ranking.js" type="text/javascript"></script>
		<script src="js/load.js" type="text/javascript"></script>
		
		<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
		
		<div id = "currentorder" value = "'.$ordername.'" style="display: none;"></div>
		
	</head>
	<body>
	
		<div id = "centered25">
		
			<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
		
			<div align = "center">
				<status> 
					Ranking of '.$coursename.'
				</status><br>
				<challengefont><b>My Ranking<br> Wins: '.$player_wins.' 
					&nbsp&nbsp&nbsp&nbsp Defeats: '.$player_defeats.'
					&nbsp&nbsp&nbsp&nbsp Overall: '.$player_scoresum.' points
					&nbsp&nbsp&nbsp&nbsp Ratio: '.$player_ratio.'  
					&nbsp&nbsp&nbsp&nbsp Position: '.$playerpos.' 
					</b></challengefont>
			</div><br>
			<div align = "center">
				<font color="#ffffff">order by: </font>
				<font color="#6699FF">
					&nbsp&nbsp&nbsp
					<span class = "order" id = "wins">wins</span>
					&nbsp&nbsp&nbsp
					<span class = "order" id = "defeats">defeats</span>
					&nbsp&nbsp&nbsp
					<span class = "order" id = "scoresum">overall</span>
					&nbsp&nbsp&nbsp
					<span class = "order" id = "ratio">ratio</span>
				</font>
			</div>
			<br>
			<table id = "tranking" style="border-radius: 0.6em ; border: 2px solid black; border-spacing:0;" >
			<thead>
			  <tr id="firstrow">
				<th>Player</th>
				<th>Wins</th>
				<th>Defeats</th>
				<th>Overall</th>
				<th>Ratio W/D</th>
			  </tr>
			 </thead>
			 <tbody> 
');

// loops through all ordered player records 
foreach($recs as $rec) { 

	// gets display info
	$rname = htmlentities($db->db_get_player_name($rec['playerid']),  ENT_COMPAT,'ISO-8859-1', true);
	$rwins = $rec['wins'];
	$rdefeats = $rec['defeats'];
	$rscoresum = $rec['scoresum'];
	$rratio = number_format($rec['ratio'], 2, '.', '');
	
	echo('			 
				  <tr>
					<td>'.$rname.'</td>
					<td>'.$rwins.'</td>
					<td>'.$rdefeats.'</td>
					<td>'.$rscoresum.'</td>
					<td>'.$rratio.'</td>
				  </tr>

	');
}

echo('
			  </tbody>
			</table>

			<div align = "right">
				<button id = "menu" class="sexybutton sexysimple sexyorange sexyxxxl">Main Menu</button>
			</div>
			<br><br>
		</div>
	</body>
</html>
');