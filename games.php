<?php

// class that will store all needed info from games to display in the 
// plugin. All info will be loaded from the game developer definitions 
// included within the game folder
class game {
	
	public $name; // the name of the game
	public $description; // the description of the game
	public $category; // the game category
	public $logo; // the game logo to be displayed
	public $author; // the author of the game
	public $file_path; // the path of the main game file
	
}

// enable log
error_reporting(E_ALL ^ E_NOTICE);

// gets course id by url param (get)
$course_id = filter_input(INPUT_GET, 'id');

// needed for global functions and vars
require_once __DIR__ . '/../../config.php';
require_login();

// stores user id to register player
$user_id = $USER->id;

// gets context of the course using its id
$context = context_course::instance($course_id);

// check if user is capable of managing (editing teacher or manager)
if (has_capability('block/games:manage', $context))
{
	// creates a session for the user
	session_start();
	// stores user capability
	$_SESSION['capability'] = "manage";
}
// if not, check if is a student
else if (has_capability('block/games:attempt', $context))
{
	// creates a session for the user
	session_start();
	// stores user capability
	$_SESSION['capability'] = "attempt";
}
// else user does not have permission to play
else
{
	echo ("<script>alert('You need to login at least as a student in order to play the games!'); window.close(); </script>");
	return;
}

// stores in the session the current course id the user is in
$_SESSION['cid'] = $course_id;
// goes ahead with the plugin
init_cfg($user_id);
main();
final_cfg();

function init_cfg($userid)
{
	// creates db config file with info about user db config
	// and adds info to path config file by instanciating cfg
	require_once 'cfg.php';
	$cfg_inst = cfg::Instance();
	
	// gets course id by url param (get)
	$course_id = filter_input(INPUT_GET, 'id');
	// instance of dbhelper to help db access
	require_once __DIR__ . '/dbhelper.php';
	$dbhelper = new dbHelper();
	
	// gets course name by its id
	$course_name = $dbhelper->db_get_course_name($course_id);
	
	// register the player if is not registered yet
	$dbhelper->db_register_player($userid, $course_id);
	
	// path to moodle course view
	$tags = parse_ini_file(__DIR__ . "/config.ini"); 
	$course_path = $tags['wwwroot']. '/course/view.php?id=' . $course_id;

	// HTML
	echo('
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
			<link type="text/css" href="css/styles.css" rel="stylesheet" media="all" />
			<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
			<script src="js/jquery.quicksand.js" type="text/javascript"></script>
			<script src="js/jquery.easing.js" type="text/javascript"></script>
			<script src="js/script.js" type="text/javascript"></script>
			<script src="js/jquery.prettyPhoto.js" type="text/javascript"></script> 
			<link href="css/prettyPhoto.css" rel="stylesheet" type="text/css" />
		</head>
		<body>	
			<div class="wrapper">
			<div class="portfolio-content">	
			<h1 class="title-page">Learning Games</h1>
			<h1><a href = '.$course_path.' title = "Go back to course view">'.$course_name.'</a></h1>
			<br>
			<ul class="portfolio-categ filter">
				<li>categories:</li>
				  <li class="all"><a href="#">All</a></li>
				  <li class="board"><a href="#" title="Board Games">Board Games</a></li>
				  <li class="quiz"><a href="#" title="Quiz Games">Quiz Games</a></li>
				  <li class="action"><a href="#" title="Action Games">Action Games</a></li>
				  <li class="rpg"><a href="#" title="RPG Games">RPG Games</a></li>
				  <li class="other"><a href="#" title="Other Games">Other Games</a></li>
			</ul>
        
			<ul class="portfolio-area">	
		');
}

function main()
{	
	// games folder
	$path_games = __DIR__ . "/learning_games/";
	
	$tags = parse_ini_file(__DIR__ . "/config.ini"); 
	$wwwroot = $tags['wwwroot'];
	
	// games external
	$path_games_ext = $wwwroot . "/blocks/games/learning_games/";
	
	// gets all games dirs
	$dirs = scandir($path_games);
		
	// initialize the array of games
	$games = array();

	// iterates in dirs
	foreach($dirs as $dir)
	{
		// ignores unwanted dirs that are not games
		if($dir != '.' && $dir != '..')
		{
			// it's a game!
			// gets all the info defined by the game developer
			// on defaults defined by the plugin
			
			// creates a new game object to be added to the array of games
			$game = new Game();
			// we already have the info about the logo, by the defaults
			// logo is a .gif file on the game directory named logo.gif
			$game->logo = $path_games_ext . $dir . '/logo.gif';
			
			// path to the description file of the game, containing all
			// info that we`ll be loading on our data structure
			$description_file = $path_games . $dir . '/description.php';
			
			// include the game description file to read its vars
			include $description_file;

			// stores the remaining info of the game on the data structure
			$game->name = $name;
			$game->description = $description;
			$game->category = $category;
			$game->author = $author;
			$game->file_path = $dir . '/' . $file_name;
			
			$games[] = $game;
		}
	}
	
	// for each game, display its logo and info
	// and make a link to the game main source file
	$i = 0;
	foreach($games as $g)
	{
		echo('
			<li class="portfolio-item2" data-id="id-'.$i.'" data-type='.$g->category.'>	
				<div>
					<span class="image-block">
						<a href="learning_games/'.$g->file_path.'"><img id = "gamelogo'.$i.'" img src='.$g->logo.' width="225" height="140" alt='.$g->name.'></a>
					</span>
					<div class="home-portfolio-text">
						<h2 class="post-title-portfolio"><a href="learning_games/'.$g->file_path.'" rel="bookmark" >'.$g->name.'</a></h2>
					</div>
				</div>
				<p class="post-subtitle-portfolio">'.charlimit($g->description, 50).'								
					<a href = "javascript:void(0)" class="tooltip">
						Read More
						<span>
							<strong>'.$g->name.'</strong><br />
							'.$g->description.' <br>
							Author: '.$g->author.'
						</span>
					</a>			
				</p>
			</li>

			');
	
		$i++;
	}
}

function final_cfg()
{
	// HTML
	echo('    
			<div class="column-clear"></div>
			</ul><!--end portfolio-area -->
			</div><!--end portfolio-content -->
 	
			</div><!-- end wrapper -->  
		</body>
	</html>
	');
}

// limit chars of a string
function charlimit($string, $limit) {
	return substr($string, 0, $limit) . (strlen($string) > $limit ? "..." : '');
}
