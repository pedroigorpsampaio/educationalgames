<?php
  
/// DB HELPER TESTER
// Enable error logging: 
error_reporting(E_ALL ^ E_NOTICE);

// path to the db helper file
$tags = parse_ini_file(__DIR__ . "/../../config.ini"); 
$root = $tags['root'];
$dbhelper_file = $root . '/blocks/games/dbhelper.php';
			
// include the dbhelper file 
include_once $dbhelper_file;
// new dbhelper class 
$db_helper = new dbHelper();
// debugs db connection info
$db_helper->db_print_info();
// connects to the database provided in the constructor
$db_helper->db_connect();

// test of ids (user and course)
echo '<br>###### IDs TEST ######';
// tests aux function get_userID() that returns the current logged user id on the db
$userid = $db_helper->get_userID();
echo '<br>user id: '.$userid;
$username = $db_helper->db_get_user_name($userid);
echo '<br>user name: '.$username;
// tests aux function get_courseID() that returns the current course id on the db
$courseid = $db_helper->get_courseID();
echo '<br>course id: '.$courseid;
echo '<br>#######################<br>';
// tests aux function get_multichoice_questions() that returns an array of multichoice question objects
// require multichoice question class (once, avoiding redeclaration)
require_once $root . '/blocks/games/obj/multichoice_question.php';
$mc_questions = $db_helper->get_multichoice_questions();

// debug $mc_questions returned from db_helper
echo '<br> ###### MULTICHOICE QUESTIONS INFO ###### <br>';

foreach($mc_questions as $mc_question)
{
	echo '<br>-- Question: '.$mc_question->name;
	echo '<br>- Text: '.$mc_question->text;
	echo '<br>- Possible Answers: ';
	
	for($i = 0; $i < count($mc_question->answers); $i++)
	{
		echo '<br>'.($i+1).') '.$mc_question->answers[$i];
	}
	
	echo '<br>- Correct Answers: ';
	
	foreach($mc_question->correct_answers_idx as $correct_answer)
	{
		echo '<br>'.($correct_answer+1).') '.$mc_question->answers[$correct_answer];
	}
	echo '<br>';
}
echo '<br> ###########################################';
// end of $mc_questions debug

// tests aux function db_get_players() that returns an array of players objects
// require players class (once, avoiding redeclaration)

require_once $root . '/blocks/games/obj/player.php';
$players = $db_helper->db_get_players(2);
/*$players = array();
$p->name = new player();
$p->name = 'teste';
$p->playerid = 2;
$p->userid = 2;
$p->courseid = 2;
$players[] = $p;*/

// debug $players returned from db_helper
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

