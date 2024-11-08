<?php
  
/// DB TESTER
// Enable error logging: 
error_reporting(E_ALL ^ E_NOTICE);

// path to the db helper file
$db = 'db.php';
			
// include the db file 
include_once $db;
// new db class 
$db = new db();
// connects to the database provided in the constructor
$db->db_connect();

// test of ids (user and course)
echo '<br>###### IDs TEST ######';
// tests aux function get_userID() that returns the current logged user id on the db
$userid = $db->get_userID();
echo '<br>user id: '.$userid;
$username = $db->db_get_user_name($userid);
echo '<br>user name: '.$username;
// tests aux function get_courseID() that returns the current course id on the db
$courseid = $db->get_courseID();
echo '<br>course id: '.$courseid;
echo '<br>#######################<br>';
// tests aux function get_multichoice_questions() that returns an array of multichoice question objects
// require multichoice question class (once, avoiding redeclaration)
require_once $root . '/blocks/games/obj/multichoice_question.php';
$mc_questions = $db->get_multichoice_questions();

// debug $mc_questions returned from db
echo '<br> ###### MULTICHOICE QUESTIONS INFO ###### <br>';

foreach($mc_questions as $mc_question)
{
	echo '<br>-- Question: '.htmlentities($mc_question->name,  ENT_COMPAT,'ISO-8859-1', true);
	echo '<br>- Text: '.htmlentities($mc_question->text,  ENT_COMPAT,'ISO-8859-1', true);
	echo '<br>- Possible Answers: ';
	
	for($i = 0; $i < count($mc_question->answers); $i++)
	{
		echo '<br>'.($i+1).') '.htmlentities($mc_question->answers[$i],  ENT_COMPAT,'ISO-8859-1', true);
	}
	
	echo '<br>- Correct Answers: ';
	
	foreach($mc_question->correct_answers_idx as $correct_answer)
	{
		echo '<br>'.($correct_answer+1).') '.htmlentities($mc_question->answers[$correct_answer],  ENT_COMPAT,'ISO-8859-1', true);
	}
	echo '<br>';
}
echo '<br> ###########################################';
// end of $mc_questions debug

// tests aux function db_get_players() that returns an array of players objects
// require players class (once, avoiding redeclaration)

require_once $root . '/blocks/games/obj/player.php';
$players = $db->db_get_players(2);

// debug $players returned from db
echo '<br><br> ###### PLAYERS INFO ###### <br>';

foreach($players as $player)
{
	echo '<br>- Name: '.$player->name;
	echo '<br>- Player ID: '.$player->playerid;
	echo '<br>- User ID: '.$player->userid;
	echo '<br>- Course ID: '.$player->courseid;
	echo '<br>';
}
echo '<br> ###########################################';
// end of $players debug

/* ranks methods test */
$ranks = $db->get_ranks();

echo('<div id = "centered25">');
$testwins = 60;
if($db->has_player_ranked($testwins))
	echo("player has just ranked!!!<br>");
echo($testwins." wins :".$db->get_player_rank($testwins)['rankname']."<br>");
echo("<br>Next Rank: ".$db->get_wins_next_rank($testwins)." <br>");

for($i = 0 ; $i < count($ranks); $i++) {
	echo($ranks[$i]['rankname'].'=>'.$ranks[$i]['rankwins'].'<img src = '.$ranks[$i]['imagepath'].'></img><br>');
}

echo('</div>');


