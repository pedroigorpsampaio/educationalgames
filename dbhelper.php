<?php

// Enable error logging: 
error_reporting(E_ALL ^ E_NOTICE);

// path to the root
$tags = parse_ini_file(__DIR__ . "/config.ini"); 
$root = $tags['root'];

// error logging
ini_set('date.timezone', 'America/Sao_paulo');
ini_set("log_errors", 1);
ini_set("error_log", $root . '/blocks/games/log/db-error.log');

$dbhelper_file = $root . '/blocks/games/dbhelper.php';

// class responsable for handling db methods and operations
class dbHelper {
	
	// The database connection 
    protected $connection;
	
	
	/**
     * Connect to the database provided in dbconfig.ini
     * 
     * @return bool false on failure / mysqli MySQLi object instance on success
     */
	public function db_connect()
	{
		// Try and connect to the database
        if(!isset($this->connection)) {
            // Load configuration as an array. Use the actual location of your configuration file
            $config = parse_ini_file('db/dbconfig.ini'); 
            $this->connection = new mysqli($config['host'],$config['username'],$config['password'],$config['dbname']);
        }

        // If connection was not successful, handle the error
        if($this->connection === false) {
            // Handle error - notify administrator, log to a file, show an error screen, etc.
			echo 'Could not connect to the database. Check your database config file.';
            return  mysqli_connect_error(); 
        }
        return $this->connection;
	}
	
	/**
     * Query the database
     *
     * @param $query The query string
     * @return mixed The result of the mysqli::query() function
     */
    public function db_query($query) {
        // Connect to the database
        $connection = $this->db_connect();

        // Query the database
        $result = $connection -> query($query);

        return $result;
    }

    /**
     * Fetch rows from the database (SELECT query)
     *
     * @param $query The query string
     * @return bool False on failure / array Database rows on success
     */
    public function db_select($query) {
        $rows = array();
        $result = $this -> db_query($query);
        if($result === false) {
            return false;
        }
        while ($row = $result -> fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Fetch the last error from the database
     * 
     * @return string Database error message
     */
    public function db_error() {
        $connection = $this -> db_connect();
        return $connection -> error;
    }

    /**
     * Quote and escape value for use in a database query
     *
     * @param string $value The value to be quoted and escaped
     * @return string The quoted and escaped string
     */
    public function db_quote($value) {
        $connection = $this -> db_connect();
        return "'" . $connection -> real_escape_string($value) . "'";
    }
	
	
	// creates the table that will hold all players
	// of the plugin, later to be registered by db_register_player
	public function db_create_table_players() {
		
		// Connect to the database
		$connection = $this->db_connect();
		
		// gets the prefix of moodle database tables
		$config = parse_ini_file('db/dbconfig.ini'); 
		$prefix = $config['prefix'];
		
		// query the table to check if it already exists
		$query = "SELECT * FROM `".$prefix."games_players`";
		$result = $this->db_query($query);

		// if table do not exist yet, creates the table
		if(empty($result)) {
			$query = "CREATE TABLE `".$prefix."games_players` (
					  id int AUTO_INCREMENT,
					  userid bigint(10) NOT NULL,
					  courseid bigint(10) NOT NULL,
					  PRIMARY KEY  (id),
					  CONSTRAINT fk_uid FOREIGN KEY (userid)
					  REFERENCES `".$prefix."user`(id)
					  ON DELETE CASCADE,
					  CONSTRAINT fk_cid FOREIGN KEY (courseid)
					  REFERENCES `".$prefix."course`(id)
					  ON DELETE CASCADE
					  )";
			$result = $this->db_query($query);
			
			// error
			if($result === false) {
				$error = $this->db_error();
				// Handle error - inform administrator, log to file, show error page, etc.
				echo 'error: could not query the database (db_create_table_players())<br>Error? '.$error;
				return false;
			}
		}
	}
	
	// register a player (plugin user) in the db
	// if player isn`t registered in the db yet,
	// as soon as a user access the plugin.
	// a player is a user within a course, which
	// means that the same user will be considered 
	// a different player depending on the course
	public function db_register_player($userid, $courseid)
	{
		// Connect to the database
		$connection = $this->db_connect();
		
		// creates players table if does not exist yet
		$this->db_create_table_players();
		
		// gets the prefix of moodle database tables
		$config = parse_ini_file('db/dbconfig.ini'); 
		$prefix = $config['prefix'];
		
		// query the table to check if player already exists
		$query = "SELECT * FROM `".$prefix."games_players` WHERE `userid` = ".$userid." AND `courseid` = ".$courseid."";
		$result = $this->db_select($query);

		// if player isnt registered yet, registers the player
		if(!$result) {
			$query = "INSERT INTO `".$prefix."games_players` (userid, courseid)
						VALUES (".$userid.", ".$courseid.")";
			$result = $this->db_query($query);
			
			// error
			if($result === false) {
				$error = $this->db_error();
				// Handle error - inform administrator, log to file, show error page, etc.
				echo 'error: could not query the database (db_register_player())<br>Error? '.$error;
				return false;
			}
		}
	}
	
	// returns the player id of a respective
	// user id and course id (a plugin player)
	public function db_get_playerID($userid, $courseid) 
	{
		// Connect to the database
		$connection = $this->db_connect();

		// imports the players class (once, avoiding redeclaration)
		require_once 'obj/player.php';
		
		// selects id(playerid), from a {userid && courseid}
		$config = parse_ini_file('db/dbconfig.ini'); 
		$prefix = $config['prefix'];
		$rows_players = $this->db_select("SELECT `id` FROM `".$prefix."games_players` WHERE `userid` = ".$userid." AND `courseid` = ".$courseid."");

		// error
		if($rows_players === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_get_playerID())<br>Error? '.$error;
			return false;
		}
		
		return $rows_players[0]['id'];
	}
	
	// returns an array of players of a course
	// with all info needed for the games 
	// require the class players in /obj
	public function db_get_players($courseid)
	{
		// Connect to the database
		$connection = $this->db_connect();

		// imports the players class (once, avoiding redeclaration)
		require_once 'obj/player.php';
		
		// the array of players that will be returned by this function
		$players = array();
		
		// selects id(playerid), userid and courseid from all players in db
		// to store in the array of players
		$config = parse_ini_file('db/dbconfig.ini'); 
		$prefix = $config['prefix'];
		$rows_players = $this->db_select("SELECT `id`, `userid`,`courseid` FROM `".$prefix."games_players` WHERE `courseid` = ".$courseid."");

		// error
		if($rows_players === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_get_players())<br>Error? '.$error;
			return false;
		}
		
		// loop through players registered in the database to store in the array
		foreach($rows_players as $rows_player)
		{
			// creates a new player that
			// will be added to the array of players
			$player = new player();
			// stores known informations
			$player->playerid = $rows_player['id'];
			$player->userid = $rows_player['userid'];
			$player->courseid = $rows_player['courseid'];
			$player->name = $this->db_get_user_name($player->userid);
			
			// adds the player, with all its information, to the array of players
			$players[] = $player;
		}
		
		// now we have all the info needed. return the array
		return $players;
	}
	
	// returns an array of multichoice questions 
	// with all info needed for the games
	// require the class of multi choice questions
	public function get_multichoice_questions($category)
	{
		// Connect to the database
		$connection = $this->db_connect();

		// imports the multichoice question class (once, avoiding redeclaration)
		require_once 'obj/multichoice_question.php';
		
		// the array of multichoice questions that will be returned by this function
		$mc_questions = array();
		
		// selects id, name and question text from all multichoice questions
		// to store in the array of multichoice questions
		$config = parse_ini_file('db/dbconfig.ini'); 
		$prefix = $config['prefix'];
		if(empty($category))
			$rows_questions = $this->db_select("SELECT `id`, `name`,`questiontext` FROM `".$prefix."question` WHERE `qtype` = 'multichoice'");
		else
			$rows_questions = $this->db_select("SELECT `id`, `name`,`questiontext` 
												FROM `".$prefix."question` 
												WHERE `qtype` = 'multichoice' and `category` = ".$category."");

		// error
		if($rows_questions === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_get_multichoice_questions())<br>Error? '.$error;
			return false;
		}
		
		// loop through multichoice questions found in the question bank
		foreach($rows_questions as $row_question)
		{
			// creates a new multichoice question that
			// will be added to the array of mc questions
			$mc_question = new multichoice_question();
			// stores known informations
			$mc_question->name = $row_question['name'];
			$mc_question->text = strip_tags($row_question['questiontext']);

			// now that we have the question id
			// we will look for its answers and store it
			$mc_question->answers = array();
			$mc_question->correct_answers_idx = array();
			// we need another sql select now
			$config = parse_ini_file('db/dbconfig.ini'); 
			$prefix = $config['prefix'];
			$rows_answers = $this->db_select("SELECT `id`,`answer`,`fraction` FROM `".$prefix."question_answers` WHERE `question` = ".$row_question['id']."");
			
			// counter of correct answers
			$i = 0;
			// loop through each multichoice questions answers found in the bank
			foreach($rows_answers as $row_answer)
			{
				// adds answer to respective positions in the array of answers (starting from 0)
				$mc_question->answers[($row_answer['id'] - 1)%(count($rows_answers))] = strip_tags($row_answer['answer']);
				
				// if answer is the correct answer, stores the correct id of the array of answers in the correct answers array
				if(($row_answer['fraction'] - 1.0) >= 0)
				{
					$mc_question->correct_answers_idx[$i] = ($row_answer['id'] - 1)%(count($rows_answers));
					$i++;
				}
			}
			// adds the question, with all its information, to the array of questions
			$mc_questions[] = $mc_question;
		}
		// now we have all the info needed. return the array
		return $mc_questions;
	}
	
	// returns the list of categories in the question bank
	// categories ['category'] <=> category id
	public function db_get_categories() 
	{
		// Connect to the database
		$connection = $this->db_connect();
		
		// the array of categories questions that will be returned by this function
		$categories = array();

		// selects category id from distinct categories
		// to store in the array of categories
		$config = parse_ini_file('db/dbconfig.ini'); 
		$prefix = $config['prefix'];

		$categories = $this->db_select("SELECT DISTINCT `category` FROM `".$prefix."question`");

		// error
		if($rows_questions === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_get_categories())<br>Error? '.$error;
			return false;
		}

		return $categories;
	}
	
	// receives user id and returns user name
	public function db_get_user_name($userid)
	{
		// Connect to the database
		$connection = $this->db_connect();
		
		$config = parse_ini_file('db/dbconfig.ini'); 
		$prefix = $config['prefix'];
		$query = "SELECT `firstname`,`lastname` FROM `".$prefix."user` WHERE `id` = ".$userid;
		$row_cname = $this->db_select($query);
		
		// error
		if($row_cname === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_get_user_name())<br>Error? '.$error;
			return false;
		}
		
		return $row_cname[0]['firstname'] . " " . $row_cname[0]['lastname'];
	}
	
	// receives user player id and returns user name
	public function db_get_player_name($playerid)
	{
		// Connect to the database
		$connection = $this->db_connect();
		
		// selects id(playerid), from a {userid && courseid}
		$config = parse_ini_file('db/dbconfig.ini'); 
		$prefix = $config['prefix'];
		$rows_players = $this->db_select("SELECT `userid` FROM `".$prefix."games_players` WHERE `id` = ".$playerid."");

		
		// error
		if($rows_players === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			error_log('error: could not query the database (db_get_course_name()) Error? '.$error);
			return false;
		}
		
		$userid =  $rows_players[0]['userid'];
		
		return $this->db_get_user_name($userid);
	}
	
	// receives course id and returns course name
	public function db_get_course_name($courseid)
	{
		// Connect to the database
		$connection = $this->db_connect();
		
		$config = parse_ini_file('db/dbconfig.ini'); 
		$prefix = $config['prefix'];
		$query = "SELECT `fullname` FROM `".$prefix."course` WHERE `id` = ".$courseid;
		$row_cname = $this->db_select($query);
		
		// error
		if($row_cname === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_get_course_name())<br>Error? '.$error;
			return false;
		}
		
		return $row_cname[0]['fullname'];
	}
	
	// returns the id, on the db, of the current logged user 
	public function get_userID()
	{
		// moodle root dir
		$tags = parse_ini_file(__DIR__ . "/config.ini"); 
		$root = $tags['root'];
		
		// to be able to get user id
		require_once  $root . '/config.php';
		require_login();
		
		$userid = $USER->id;
		
		return $userid;
	}
	
	// returns the id, on the db, of the current course user is in
	public function get_courseID()
	{
		// gets from session cid, registered in the games module
		return $_SESSION['cid'];
	}
	
	// returns a string representing user capability
	// "attempt" / "manage"
	public function get_user_capability()
	{
		// gets user capability registered in session, by the games module
		return $_SESSION['capability'];
	}
	
	// receives a category id and return its name
	public function db_get_category_name($category) 
	{
		global $DB;
		$cat = $DB->get_record('question_categories', array("id" => $category));
		return $cat->name;
	}
	
	// debug
	public function db_print_info()
	{
		$config = parse_ini_file('db/dbconfig.ini'); 

		echo '##### DB INFO #####<br>';
		echo 'host: '.$config['host'].'<br>';
		echo 'username: '.$config['username'].'<br>';
		echo 'password: '.$config['password'].'<br>';
		echo 'dbname: '.$config['dbname'].'<br>';
		echo '###################<br>';
	}
}

