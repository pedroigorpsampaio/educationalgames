<?php

// db file
$db_file = 'db.php';
			
// include the db file 
include_once $db_file;
// new db class 
$db = new db();
// connects to the db 
$db->db_connect();

// questions category id
$category = $_POST["questions_category"];

// get_multichoice_questions() that returns an array of multichoice question objects
// require multichoice question class (once, avoiding redeclaration)
$tags = parse_ini_file(__DIR__ . "/../../config.ini"); 
$root = $tags['root'];
require_once $root . '/blocks/games/obj/multichoice_question.php';
$mc_questions = $db->get_multichoice_questions($category);

// size of questions
$mc_size = count($mc_questions);

// gets seeds (question seed and answer seed)
$seed = $_POST["seed"];
$a_seed = $_POST["a_seed"];

/** Question Parser **/

// split seed into array of strings (separator '.')
$qid = explode(".", $seed);

// array that will store the game selected questions
$questions[] = array();

// array of converted question texts
$questions_texts[] = array();

// loops through questions id (obtained from seed) and stores in questions array
for($i = 0; $i < count($qid); $i++) {
	$questions[$i] = $mc_questions[$qid[$i]];
	$questions_texts[$i] = htmlentities($questions[$i]->text,  ENT_COMPAT,'ISO-8859-1', true);
}

/** Answer Parser **/

// split a_seed into array of strings (separator '.')
$aid = explode(".", $a_seed);

//for($i = 0; $i < count($aid); $i++)
//echo ("<script>alert('".parse_answer_status($aid[$i])."');</script>");

// parses answer seed for answer id
function parse_answer_id($aid) {
	if($aid == "jump" || $aid == "timeout")
		return $aid;
	else
		return $aid[1];
}

// parses answer seed for answer status
// (incorrect, correct, action(jump or timeout))
function parse_answer_status($aid) {
	if($aid == "jump" || $aid == "timeout")
		return "action";
	else if($aid[0] == "T")
		return "correct";
	else
		return "incorrect";
}

/** html **/
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
				<div align = "center">
					<button id = "menu" class="sexybutton sexysimple sexyorange sexyxxxl">Main Menu</button>
				</div>
				<div style ="visibility: hidden;"> <img src = "images/banner.png" width = "800" height = "1"></div>
				
');

// loops through questions selected (size of questions selected == rounds == size of player answers/actions)
for($i = 0; $i < count($qid); $i++) {
	echo('
				<div align = "center">
					<holder>
						<question><h3><b><quest>Question</quest><br>'.$questions_texts[$i].'</b></h3></question>
					</holder>
		');
	
	$parsed_id = parse_answer_id($aid[$i]);
	$parsed_status = parse_answer_status($aid[$i]);
	
	if($parsed_id == "jump")
		$a_echo_txt = '<actionfeed>You have jumped this question!</actionfeed>';
	else if($parsed_id == "timeout")
		$a_echo_txt = '<actionfeed>This question was not answered before timeout!</actionfeed>';
	else if($parsed_status == "correct")
		$a_echo_txt = '<correctfeed>'.htmlentities($questions[$i]->answers[parse_answer_id($aid[$i])],  ENT_COMPAT,'ISO-8859-1', true).' - Correct!</correctfeed>';
	else
		$a_echo_txt = '<incorrectfeed>'.htmlentities($questions[$i]->answers[parse_answer_id($aid[$i])],  ENT_COMPAT,'ISO-8859-1', true).' - Incorrect!</incorrectfeed>';
					
	echo($a_echo_txt.'
						<br>
					</div>
		');
}

echo('			
			</div>

		</body>
	</div>
</html>
');





