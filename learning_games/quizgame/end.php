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
$username = htmlentities($db->db_get_user_name($userid),  ENT_COMPAT,'ISO-8859-1', true);
$courseid = $db->get_courseID();
$coursename = $db->db_get_course_name($courseid);
$playerid = $db->db_get_playerID($userid, $courseid);

// path to games game menu
$game_menu = $tags['wwwroot']. 'blocks/games/learning_games/quizgame/quiz.php';

// path to single player game
$single_game = $tags['wwwroot']. 'blocks/games/learning_games/quizgame/game.php';

// path to multi player menu
$single_game = $tags['wwwroot']. 'blocks/games/learning_games/quizgame/multiplayer.php';

// is it multiplayer?
$multiplayer = $_POST["multiplayer"];

// score
$score = $_POST["score"];

// if it is multiplayer,
if($multiplayer == "true") {
	
	// what is the id of the other player?
	if (!empty($_POST["contestant"])) {
		$contestant = $_POST["contestant"];
	}

	// what is the name of the other player?
	if (!empty($_POST["cname"])) {
		$cname = $_POST["cname"];
		$cname = htmlentities($cname,  ENT_COMPAT,'ISO-8859-1', true);
	}

	// gets the seed, needed for multiplayer challenge
	if (!empty($_POST["seed"]))
		$seed = $_POST["seed"];

	/* gets game config to challenge other with same config */
	// questions category id
	if (!empty($_POST["questions_category"]))	
		$category = $_POST["questions_category"];

	// number of rounds
	if (!empty($_POST["n_rounds"]))
		$n_rounds = $_POST["n_rounds"];

	// time to answer
	if (!empty($_POST["round_time"]))
		$time_limit = $_POST["round_time"];

	// right question value
	if (!empty($_POST["correct_value"]))
		$correct_value = $_POST["correct_value"];

	// wrong question value;
	if (!empty($_POST["wrong_value"]))
		$wrong_value = $_POST["wrong_value"];
	
	// jump question value;
	if (!empty($_POST["jump_value"]))
		$jump_value = $_POST["jump_value"];

	// time limit reach value
	if (!empty($_POST["time_value"]))
		$time_value = $_POST["time_value"];
}

// if it is a accepted challenge
if (!empty($_POST["acceptedchallenge"])) {
	$acceptedchallenge = $_POST["acceptedchallenge"];
	
	// rank info to send notification in case
	// one of the players rank up in this match
	$ranks = $db->get_ranks();
}

// if it is practice, for answers feedback purposes
if (!empty($_POST["practice"]) && $_POST["practice"] == "true") {
	
	$practice = $_POST["practice"];
	
	// question seed for feedback generation
	$seed = $_POST["seed"];
	
	// answer seed for feedback generation
	// a_seed is aligned with seed (0:0, 1:1, etc...)
	// seed is the question seed 
	$a_seed = $_POST["a_seed"];
	
	// questions category to be able to retrieve on feedback
	$questions_category = $_POST["questions_category"];

}



echo('
<!DOCTYPE html>
<html>
	<div id = "content">

		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

			<script src="js/load.js" type="text/javascript"></script>
			<script src="js/end.js" type="text/javascript"></script>
			
		</head>

		<body>

			<div id = "centered25">

			<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
				<div align = "center">
					<img src = "images/quiz_completed.png">
				</div>
				<br><br><br><br><br><br><br>
				<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
				<div align = "center">
					<img src = "images/score.png"><br>
				</div>
				<div align = "center">
					<h1><b><span id="endscore"><gamecolor> '.$score.'</gamecolor></span></b></h1>
');

if($practice == "true") {
	echo 
	('
		<div id = "seed" value = "'.$seed.'" style="display: none;"></div>
		<div id = "a_seed" value = "'.$a_seed.'" style="display: none;"></div>
		<div id = "questions_category" value = "'.$questions_category.'" style="display: none;"></div>
		<span id = "feedback">Answers Feedback</span>
	');
}
else
	echo("<br>");
	
echo('
				</div>
				<br>
				
				<div align="center"><listfont>
');

if($multiplayer == "false" && $acceptedchallenge = "false")
	echo('
			<br><br><div align="center"><button id = "single" class="sexybutton sexysimple sexyorange sexyxxxl">Play Again</button></div>
		');

// multiplayer - creates a notification for the contestant
// with game seed, score and players ids so that the contestant
// can play the same game and send back notifications (whos the winner...)		
if ($multiplayer == "true"){
	
	$text = "nulltext";
	$cn = htmlentities($db->db_get_player_name($contestant),  ENT_COMPAT,'ISO-8859-1', true);
	
	$result = $db->db_register_notification("challenge", $playerid, $contestant, $text, $score, $seed, 
											$category, $n_rounds, $time_limit, $correct_value, 
											$wrong_value, $jump_value, $time_value);
	
	if($result)
		echo("Player ".$cn." was challenged!") ;
	else
		echo("Could not challenge player ".$cn.". Try again later.") ;
	
	echo('<br><br><div align="center"><button id = "multi" class="sexybutton sexysimple sexyorange sexyxxxl">Challenge Other</button></div>');
}
else if ($acceptedchallenge == "true") {
		
	// gets info from challenger notification
	$challenger = $_POST["challenger"];
	$cscore = $_POST["cscore"];
	$nid = $_POST["nid"];
	//gets challenger name
	$cname = htmlentities($db->db_get_player_name($challenger),  ENT_COMPAT,'ISO-8859-1', true);
	
	// info to notify rank ups
	// current player wins
	$player_record = $db->db_get_player_record($playerid);
	$player_wins = $player_record['wins'];
	// challenger player wins
	$c_record = $db->db_get_player_record($challenger);
	$c_wins = $c_record['wins'];
	
	// sets the winner, loser and do the respective operations
	
	// texts hardcoded
	$wintext = "Congratulations! You have won the challenge against ";
	$losetext = "That's unfortunate. You have lost the challenge against ";
	$tie = "You have tied with ";
	$tiewintext = ". Don't worry, our best algorithms have decided you were the winner!";
	$tielosetext = ". What a shame, destiny has chosen you as the loser of this duel.";
	
	$ctext; // challenger text with result information
	$ptext; // player text with result information
		
	// final status, win or defeat
	$cstatus; // status of challenger
	$pstatus; // status of player
	
	if($cscore > $score) { // challenger is the winner, current player is the loser
		$winner = $challenger;
		$ctext = $wintext . $username . '. You scored ' . $cscore . ' while ' . $username . ' scored ' . $score;
		$ptext = $losetext . $cname . '. You scored ' . $score . ' while ' . $cname . ' scored ' . $cscore;
		$cstatus = "win";
		$pstatus = "defeat";
	}
	else if ($cscore < $score) { // current player is the winner
		$winner = $playerid;
		$ctext = $losetext . $username . '. You scored ' . $cscore . ' while ' . $username . ' scored ' . $score;
		$ptext = $wintext . $cname . '. You scored ' . $score . ' while ' . $cname . ' scored ' . $cscore;
		$cstatus = "defeat";
		$pstatus = "win";
	}
	else { // tie - BEWARE! AMAZING ALGORITHM TO DECIDE THE WINNER LIES AHEAD!
	
		// randomize a winner
		$rand = rand(0,100);
		
		// if its even, winner is the current player
		// else, winner is the challenger
		if($rand % 2 == 0) {
			$ctext = $tie . $username . $tielosetext;
			$ptext = $tie . $cname . $tiewintext;
			$cstatus = "defeat";
			$pstatus = "win";	
		}
		else {
			$ctext = $tie . $username . $tiewintext;
			$ptext = $tie . $cname  . $tielosetext;
			$cstatus = "win";
			$pstatus = "defeat";
		}
		
		$ctext = $ctext . ' You both scored ' . $cscore;
		$ptext = $ptext . ' You both scored ' . $score;
	}
	
	// $ptext is going to be shown in html to current player
	echo($ptext) ;
	echo('<br><br><div align="center"><button id = "multi" class="sexybutton sexysimple sexyorange sexyxxxl">Challenge Other</button></div>');
	// $ctext is going to be send as a message notification to the challenger
	$db->db_register_notification("message", $playerid, $challenger, $ctext);
	
	// status recording
	$db->db_update_player_record($challenger, $cscore, $cstatus);
	$db->db_update_player_record($playerid, $score, $pstatus);
	
	// send notification to the winner in case of rank up
	$ranktext = "You have a new rank! You are now a ";
	if($cstatus == "win" && $db->has_player_ranked($c_wins + 1))
		$db->db_register_notification("achievement", $playerid, $challenger, $ranktext.$db->get_player_rank($c_wins + 1)['rankname']);
	if($pstatus == "win" && $db->has_player_ranked($player_wins + 1))
		$db->db_register_notification("achievement", $playerid, $playerid, $ranktext.$db->get_player_rank($player_wins + 1)['rankname']);
	
	// challenge notification delete
	$db->db_delete_notification($nid);
}
				
echo('			<br>
					<div align="center"><button id = "menu" class="sexybutton sexysimple sexyorange sexyxxxl">Main Menu</button></div>
				</listfont></div>
			<br>
			</div>

		</body>
	</div>
</html>
');