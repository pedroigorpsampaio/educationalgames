<?php

// Enable error logging: 
error_reporting(E_ALL ^ E_NOTICE);

$db_file = 'db.php';
// include the db file 
include_once $db_file;
// new db class (db class = db quiz game methods + db plugin methods)
$db = new db();

// infos
$userid = $db->get_userID();
$courseid = $db->get_courseID();
$coursename = $db->db_get_course_name($courseid);
$playerid = $db->db_get_playerID($userid, $courseid);

///////////////// configuration  /////////////////////
// global configurations
$tags = parse_ini_file(__DIR__ . "../config.ini"); 
// course configurations

// queries db only if necessary
if(empty($_POST["questions_category"]) || empty($_POST["n_rounds"]) || empty($_POST["round_time"]))
{
	$config = $db->db_get_course_config($courseid);
}

// questions category id
if (empty($_POST["questions_category"]))
	$category = $config['category'];
else
	$category = $_POST["questions_category"];

// number of rounds
if (empty($_POST["n_rounds"]))
	$n_rounds = $config['n_rounds'];
else
	$n_rounds = $_POST["n_rounds"];

// time to answer
if (empty($_POST["round_time"]))
	$time_limit = $config['time_limit'];
else
	$time_limit = $_POST["round_time"];

// right question value
if (empty($_POST["correct_value"]))
	$correct_value = $tags['correct_value'];
else
	$correct_value = $_POST["correct_value"];

// wrong question value;
if (empty($_POST["wrong_value"]))
	$wrong_value = $tags['wrong_value'];
else
	$wrong_value = $_POST["wrong_value"];

// jump question value;
if (empty($_POST["jump_value"]))
	$jump_value = $tags['jump_value'];
else
	$jump_value = $_POST["jump_value"];

// time limit reach value
if (empty($_POST["time_value"]))
	$time_value = $tags['time_value'];
else
	$time_value = $_POST["time_value"];

///////////////////////////////////////////////////////

// current round;
$round = $_POST["round"];

// is it multiplayer?
$multiplayer = $_POST["multiplayer"];

// current score
$score = $_POST["score"];

// is it last round?
$lastround = $_POST["lastround"];

if($n_rounds == $rounds)
	$lastround = true;
else
	$lastround = false;

// if it is multiplayer, what is the id of the other player?
if (!empty($_POST["contestant"])) {
	$contestant_id = $_POST["contestant"];
}

// if it is multiplayer, what is the name of the other player?
if (!empty($_POST["cname"])) {
	$cname = $_POST["cname"];
}

// if it is a accepted challenge
if (!empty($_POST["acceptedchallenge"])) {
	
	$acceptedchallenge = $_POST["acceptedchallenge"];
	
	if($acceptedchallenge == "true") {
		$challenger = $_POST["challenger"];
		$cscore = $_POST["cscore"];
		$nid = $_POST["nid"];
	}
}

// practice 
$practice = "false";
if(empty($acceptedchallenge) && $multiplayer == "false")
	$practice = "true"; 

// gets answers seed to be used in end log
if($practice == "true")
{
	// if there seed exists, gets from post
	// else, seed is a empty string
	if (!empty($_POST["a_seed"])) 
		$a_seed = $_POST["a_seed"];
	else
		$a_seed = "";
}

// get_multichoice_questions() that returns an array of multichoice question objects
// require multichoice question class (once, avoiding redeclaration)
require_once $root . '/blocks/games/obj/multichoice_question.php';
$mc_questions = $db->get_multichoice_questions($category);

$mc_size = count($mc_questions);

// the seed to the group of questions
// if it isn`t set on post, generates it
if (!empty($_POST["seed"])) {
	$seed = $_POST["seed"];
}
else {
	$seed = generate_seed($n_rounds, $mc_size);
}

// question and answers

// split seed into array of strings (separator '.')
$qid = explode(".", $seed);
	
$mc_question = $mc_questions[$qid[$round-1]];
$mc_name = htmlentities($mc_question->name,  ENT_COMPAT,'ISO-8859-1', true);
$mc_text = htmlentities($mc_question->text,  ENT_COMPAT,'ISO-8859-1', true);

// generates and returns a seed
function generate_seed($n_rounds, $n_questions) {
	
	$seed = '';	
	$used = array();

	if($n_rounds > $n_questions) {
		for($i = 0 ; $i < $n_rounds ; ) {
			$i++;
			if($i < $n_rounds)
				$seed = $seed . rand(0, $n_questions-1) . '.';
			else
				$seed = $seed . rand(0, $n_questions-1);
		}
	} 
	else {	
	
		for($i = 0; $i < $n_rounds ; ) {
			$rand_n = rand(0, $n_questions-1);
			
			if (!in_array($rand_n, $used)) {
				$i++;
				if($i < $n_rounds)
					$seed = $seed . (string)$rand_n . '.';
				else
				    $seed = $seed . (string)$rand_n;	
				
				$used[] = $rand_n;
			}
		}
	}
	return $seed;
}

echo('
<!DOCTYPE html>
<html>

	<head>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			
		<div id = "content" >
			
			<script src="js/timer.js" type="text/javascript"></script>
			<script src="js/actions.js" type="text/javascript"></script>
			
			<div>
				<h1>
					<div id="questionsfixed">
						<span><img src = "images/question.png" width = "205" height = "45"></span>
						<span id = "rounds"><b><gamecolor> '.$round.'/'.$n_rounds.'</gamecolor></b></span>
					<div>
					
					<div id = "timefixed">
						<span id = "time"><img src = "images/time.png" ></span>
						<b><gamecolor><span id="timer">'.$time_limit.'s</span></gamecolor></b>
					</div>
				</h1>
			</div>
			
			<div id = "questions_category" value = "'.$category.'" style="display: none;"></div>
			<div id = "n_rounds" value = "'.$n_rounds.'" style="display: none;"></div>
			<div id = "round_time" value = "'.$time_limit.'" style="display: none;"></div>
			<div id = "correct_value" value = "'.$correct_value.'" style="display: none;"></div>
			<div id = "wrong_value" value = "'.$wrong_value.'" style="display: none;"></div>
			<div id = "jump_value" value = "'.$jump_value.'" style="display: none;"></div>
			<div id = "time_value" value = "'.$time_value.'" style="display: none;"></div>
			<div id = "round" value = "'.$round.'" style="display: none;"></div>
			<div id = "multiplayer" value = "'.$multiplayer.'" style="display: none;"></div>
			<div id = "score" value = "'.$score.'" style="display: none;"></div>
			<div id = "contestant" value = "'.$contestant_id.'" style="display: none;"></div>
			<div id = "seed" value = "'.$seed.'" style="display: none;"></div>
			<div id = "lastround"  data-return = "'.$lastround.'" style="display: none;"></div>
			<div id = "cname" value = "'.$cname.'" style="display: none;"></div>
			<div id = "acceptedchallenge" value = "'.$acceptedchallenge.'" style="display: none;"></div>
			<div id = "challenger" value = "'.$challenger.'" style="display: none;"></div>
			<div id = "cscore" value = "'.$cscore.'" style="display: none;"></div>
			<div id = "nid" value = "'.$nid.'" style="display: none;"></div>
			<div id = "practice" value = "'.$practice.'" style="display: none;"></div>
			<div id = "a_seed" value = "'.$a_seed.'" style="display: none;"></div>

		</head>

		<body>
			
			<div id = "centered">
				<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
				<div align = "center">
				<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
					<holder>
						<question><h3><b><quest>Question</quest><br>'.$mc_text.'</b></h3></question><br><br><br>
					</holder>
					<div align = "left">
					<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
');

/***** constructs the answers	*****/		
	
// loop through question answers shuffling them
$usedAnswers = array ();
		
for($i = 0; $i < count($mc_question->answers); ){
	$rand_n = rand(0, count($mc_question->answers)-1);
	
	if (!in_array($rand_n, $usedAnswers)) {
		$i++;
		$usedAnswers[] = $rand_n;
	
		// gets answer text
		$answer_text = htmlentities($mc_question->answers[$rand_n],  ENT_COMPAT,'ISO-8859-1', true);
		
		// if answer is correct, tag holder as correct
		// else, tag as incorrect
		if(in_array($rand_n, $mc_question->correct_answers_idx))
			echo('				<holder class = "correct" id = "'.$rand_n.'">');
		else
			echo('				<holder class = "incorrect" id = "'.$rand_n.'">');
		// echo the answer text 
		echo('					<answer id = "'.$rand_n.'"><h3><b id = "'.$rand_n.'">'.$answer_text.'</b></h3></answer>');
		// close holder 
		echo('				</holder>');
	}
}

				
echo('			</div>
				</div>
				</br></br>	
				
				<div align="right">
					<button id = "jump" class="sexybutton sexysimple sexyorange sexyxxxl">Jump</button>
				</div>
			<br>
			</div>

		</body>
	</div>
</html>
');