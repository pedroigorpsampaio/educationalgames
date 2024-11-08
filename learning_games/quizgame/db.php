<?php

// Enable error logging: 
error_reporting(E_ALL ^ E_NOTICE);

// db config
$config = parse_ini_file('../../db/dbconfig.ini'); 
// db helper include
// path to the db helper file
$tags = parse_ini_file(__DIR__ . "/../../config.ini"); 
$root = $tags['root'];
$dbhelper_file = $root . '/blocks/games/dbhelper.php';
require_once ($dbhelper_file);

// class responsable for handling db methods and operations (quiz game)
class db {
	
	// The database connection 
    protected $connection;
	// dbhelper object
	protected $dbhelper;

	// sets the dpHelper (initializes it)
	// if isn`t set
	function set_dbhelper()
	{
		if(!isset($this->dbhelper))
			$this->dbhelper = new dbHelper();
	}
	
	/**
     * Connect to the database provided in dbconfig.ini
     * 
     * @return bool false on failure / mysqli MySQLi object instance on success
     */
	public function db_connect()
	{
		$this->set_dbhelper();
		
		// Try and connect to the database
        if(!isset($this->connection)) {
            // Load configuration as an array. Use the actual location of your configuration file
            $config = $GLOBALS['config']; 
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
	
	// creates the table that will hold all players records
	// of the game, later to be registered by db_register_player_record
	public function db_create_table_players_records() 
	{
		// connects to the db if not connected
		$connection = $this -> db_connect();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// query the table to check if it already exists
		$query = "SELECT * FROM `".$prefix."games_players_records`";
		$result = $this->db_query($query);
		
		// if table do not exist yet, creates the table
		if(empty($result)) {
			$query = "CREATE TABLE `".$prefix."games_players_records` (
					  id int AUTO_INCREMENT,
					  playerid int NOT NULL,
					  wins int NOT NULL,
					  defeats int NOT NULL,
					  scoresum int NOT NULL,
					  PRIMARY KEY  (id),
					  CONSTRAINT fk_playerid FOREIGN KEY (playerid)
					  REFERENCES `".$prefix."games_players`(id)
					  ON DELETE CASCADE
					  )";
			$result = $this->db_query($query);
			
			// error
			if($result === false) {
				$error = $this->db_error();
				// Handle error - inform administrator, log to file, show error page, etc.
				echo 'error: could not query the database (db_create_table_players_records())<br>Error? '.$error;
				return false;
			}
		}
	}
	
	// register a player records in the db
	// if player records isn`t registered in the db yet.
	public function db_register_player_record($playerid)
	{
		// connects to the db if not connected
		$connection = $this -> db_connect();
		
		// creates players table if does not exist yet
		$this->db_create_table_players_records();
		
		// gets the prefix of moodle database tables
		$config = $GLOBALS['config'];
		$prefix = $config['prefix'];
		
		// query the table to check if player already exists
		$query = "SELECT * FROM `".$prefix."games_players_records` WHERE `playerid` = ".$playerid."";
		$result = $this->db_select($query);

		// if player records isnt registered yet, registers the player records
		if(!$result) {
			$query = "INSERT INTO `".$prefix."games_players_records` (playerid, wins, defeats, scoresum)
						VALUES (".$playerid.", 0, 0, 0)";
			$result = $this->db_query($query);
			
			// error
			if($result === false) {
				$error = $this->db_error();
				// Handle error - inform administrator, log to file, show error page, etc.
				echo 'error: could not query the database (db_register_player_record() 2)<br>Error? '.$error;
				return false;
			}
		}
	}
	
	// updates player record with score and a status (win or defeat)
	public function db_update_player_record($playerid, $score, $status)
	{
		// connects to the db if not connected
		$connection = $this -> db_connect();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// if player wins, update wins and score
		// else, update defeats and score
		if($status == "win") {
			$query = "UPDATE `".$prefix."games_players_records` 
				SET `wins` = `wins` + 1, `scoresum` = `scoresum` + ".$score."
				WHERE `playerid` = ".$playerid."";
		}
		else {
			$query = "UPDATE `".$prefix."games_players_records` 
				SET `defeats` = `defeats` + 1, `scoresum` = `scoresum` + ".$score."
				WHERE `playerid` = ".$playerid."";			
		}

		$result = $this->db_query($query);
		
		// error
		if($result === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_update_player_record())<br>Error? '.$error;
			return false;
		}
	}
	
	// gets all player record info from a player (receives the player id)
	public function db_get_player_record($playerid)
	{
		// Connect to the database
		$connection = $this->db_connect();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// query the db
		$result = $this->db_select("SELECT * FROM `".$prefix."games_players_records` WHERE `playerid` = ".$playerid."");

		// error
		if($result === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_get_player_record())<br>Error? '.$error;
			return false;
		}
					
		// stores info retrieve
		$record = array();
		$record['wins'] = $result[0]['wins'];
		$record['defeats'] = $result[0]['defeats'];
		$record['scoresum'] = $result[0]['scoresum'];
		
		// returns in a array all info from player record
		return $record;
	}
	
	// gets all players records from this course from database
	// order by $order, if $order isn`t empty
	// order can be "wins, "defeats", "ratio" or "scoresum"
	public function db_get_records($order, $courseid)
	{
		// Connect to the database
		$connection = $this->db_connect();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// adjust order to represent ratio of wins:defeats
		if($order == "ratio") 
			$order = "CAST(wins AS DECIMAL) / CASE WHEN defeats = 0 THEN 1 ELSE defeats END";
		
		// query the db on the desired order
		if(!empty($order)) {
			$result = $this->db_select("SELECT recs.*, CAST(wins AS DECIMAL) / CASE WHEN defeats = 0 THEN 1 ELSE defeats END as ratio
										FROM `".$prefix."games_players_records` AS recs, `".$prefix."games_players` AS players
										WHERE recs.playerid = players.id AND players.courseid = ".$courseid."
										ORDER BY ".$order." DESC");
		}
		else {
			$result = $this->db_select("SELECT recs.*, CAST(wins AS DECIMAL) / defeats as ratio
							FROM `".$prefix."games_players_records` AS recs");
		}

		// error
		if($result === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_get_records())<br>Error? '.$error;
			return false;
		}
		
		// the array of records that will be returned by this function
		$recs = array();
		
		// loop through ordered records registered in the database to store in the array
		foreach($result as $record)
		{
			// array that will store notifications
			$rec = array();
			// stores known informations
			$rec['playerid'] = $record['playerid'];
			$rec['wins'] = $record['wins'];
			$rec['defeats'] = $record['defeats'];
			$rec['scoresum'] = $record['scoresum'];
			$rec['ratio'] = $record['ratio'];
			
			// adds the record, with all its information, to the array of records
			$recs[] = $rec;
		}
		
		// now we have all the info needed. return the array
		return $recs;
	}
	
	// creates the table that will hold all notifications of the game
	// of the game, later to be registered by db_register_notification
	public function db_create_table_notifications() 
	{
		// connects to the db if not connected
		$connection = $this -> db_connect();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// query the table to check if it already exists
		$query = "SELECT * FROM `".$prefix."games_notifications`";
		$result = $this->db_query($query);

		// if table do not exist yet, creates the table
		if(empty($result)) {
			$query = "CREATE TABLE `".$prefix."games_notifications` (
					  id int AUTO_INCREMENT,
					  type VARCHAR(255) NOT NULL,
					  sender int NOT NULL,
					  sendee int NOT NULL,
					  text VARCHAR(255),
					  score int,
					  seed VARCHAR(255),
					  questions_category int,
					  n_rounds int,
					  round_time int,
					  correct_value int,
					  wrong_value int,
					  jump_value int,
					  time_value int,
					  timestamp TIMESTAMP(6),
					  PRIMARY KEY  (id),
					  CONSTRAINT fk_sender FOREIGN KEY (sender)
					  REFERENCES `".$prefix."games_players`(id)
					  ON DELETE CASCADE,
					  CONSTRAINT fk_sendee FOREIGN KEY (sendee)
					  REFERENCES `".$prefix."games_players`(id)
					  ON DELETE CASCADE
					  )";
			$result = $this->db_query($query);
			
			// error
			if($result === false) {
				$error = $this->db_error();
				// Handle error - inform administrator, log to file, show error page, etc.
				echo 'error: could not query the database (db_create_table_notifications())<br>Error? '.$error;
				return false;
			}
		}
	}
	
	// registers a notification in the db.
	// if table notification doesnt exist yet, creates it.
	/***
		$type: 'message' -normal messages
			   'challenge' -challenge proposal message
		$sender: sender of the notification
		$sendee: recipient of the notication
		$text: text of the notification
		
		the rest is information for the challenge 
		and is only necessary when notification is
		of type 'challenge'
	***/
	public function db_register_notification($type, $sender, $sendee, $text , $score, $seed, 
											$questions_category, $n_rounds, $round_time, 
											$correct_value, $wrong_value, $jump_value, $time_value)
	{
		// connects to the db if not connected
		$connection = $this -> db_connect();
		
		// creates notifications table if does not exist yet
		$this->db_create_table_notifications();
		
		// gets the prefix of moodle database tables
		$config = $GLOBALS['config'];
		$prefix = $config['prefix'];

		// if type equals challenge, must store also score and seed
		if($type == "challenge") {
			$query = 'INSERT INTO `'.$prefix.'games_notifications` (type, sender, sendee, text , score, seed,
																	questions_category, n_rounds, round_time,
																	correct_value, wrong_value, jump_value, time_value)
						VALUES ("'.$type.'", '.$sender.', '.$sendee.', "'.$text.'", '.$score.', "'.$seed.'",
								'.$questions_category.', '.$n_rounds.', '.$round_time.', '.$correct_value.',
								'.$wrong_value.', '.$jump_value.', '.$time_value.')';
		}
		else {
			$query = 'INSERT INTO `'.$prefix.'games_notifications` (type, sender, sendee, text, score, seed,
																	questions_category, n_rounds, round_time,
																	correct_value, wrong_value, jump_value, time_value)
						VALUES ("'.$type.'", '.$sender.', '.$sendee.', "'.$text.'", 0, "0", 0, 0, 0, 0, 0, 0, 0)';
		}
		
		$result = $this->db_query($query);
		
		// error
		if($result === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_register_notification())<br>Error? '.$error;
			return false;
		}
		
		return true;
	}
	
	// checks if player has notifications and
	// return how many notifications there is
	public function db_check_notifications($sendee) 
	{
		// Connect to the database
		$connection = $this->db_connect();
		
		// creates notifications table if does not exist yet
		$this->db_create_table_notifications();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// query the db
		$result = $this->db_select("SELECT COUNT(id) as ntqtd FROM `".$prefix."games_notifications`  WHERE `sendee` = ".$sendee."");
		
		// returns the number of notifications user has
		return $result[0]["ntqtd"];
	}
	
	// gets all notifications of a player (sendee)
	public function db_get_notifications($sendee)
	{
		// Connect to the database
		$connection = $this->db_connect();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// query the db
		$result = $this->db_select("SELECT * FROM `".$prefix."games_notifications` WHERE `sendee` = ".$sendee." ORDER BY timestamp DESC");

		// error
		if($result === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_get_notifications())<br>Error? '.$error;
			return false;
		}
		
		// the array of notifications that will be returned by this function
		$notes = array();
		
		// loop through notifications registered in the database to store in the array
		foreach($result as $notification)
		{
			// array that will store notifications
			$note = array();
			// stores known informations
			$note['id'] = $notification['id'];
			$note['type'] = $notification['type'];
			$note['sender'] = $notification['sender'];
			$note['sendee'] = $notification['sendee'];
			$note['text'] = $notification['text'];
			$note['score'] = $notification['score'];
			$note['seed'] = $notification['seed'];
			$note['questions_category'] = $notification['questions_category'];
			$note['n_rounds'] = $notification['n_rounds'];
			$note['round_time'] = $notification['round_time'];
			$note['correct_value'] = $notification['correct_value'];
			$note['wrong_value'] = $notification['wrong_value'];
			$note['jump_value'] = $notification['jump_value'];
			$note['time_value'] = $notification['time_value'];
			
			// adds the notification, with all its information, to the array of notifications
			$notes[] = $note;
		}
		
		// now we have all the info needed. return the array
		return $notes;
	}
	
	// deletes a notification in the db, receiving its id
	public function db_delete_notification($nid) {
		// connects to the db if not connected
		$connection = $this -> db_connect();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// query to delete a notification by its id
		// from notifications table
		$query = "DELETE FROM `".$prefix."games_notifications` 		
					WHERE `id` = ".$nid."";

		// query the db
		$result = $this->db_query($query);
		
		// error
		if($result === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_delete_notification())<br>Error? '.$error;
			return false;
		}
	}
	
	// creates course configuration table
	// if does not exist yet
	public function db_create_table_configurations() 
	{
		// connects to the db if not connected
		$connection = $this -> db_connect();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// query the table to check if it already exists
		$query = "SELECT * FROM `".$prefix."games_configurations`";
		$result = $this->db_query($query);
		
		// if table do not exist yet, creates the table
		if(empty($result)) {
			$query = "CREATE TABLE `".$prefix."games_configurations` (
					  id int AUTO_INCREMENT,
					  courseid bigint(10) NOT NULL,
					  n_rounds int NOT NULL,
					  time_limit int NOT NULL,
					  category int NOT NULL,
					  PRIMARY KEY  (id),
					  CONSTRAINT fk_configcid FOREIGN KEY (courseid)
					  REFERENCES `".$prefix."course`(id)
					  ON DELETE CASCADE
					  )";
			$result = $this->db_query($query);
			
			// error
			if($result === false) {
				$error = $this->db_error();
				// Handle error - inform administrator, log to file, show error page, etc.
				echo 'error: could not query the database (db_create_table_configurations())<br>Error? '.$error;
				return false;
			}
		}
	}
	
	// saves a course config in the db
	// receives course id $courseid
	public function db_save_course_config($courseid, $n_rounds, $time_limit, $category) {
		
		// connects to the db if not connected
		$connection = $this -> db_connect();
		
		// creates configuration table if does not exist yet
		$this->db_create_table_configurations();
				
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// query the table to check if course config already exists
		$query = "SELECT * FROM `".$prefix."games_configurations` WHERE `courseid` = ".$courseid."";
		$result = $this->db_select($query);
		
		// gets category id by name;
		$query = "SELECT * FROM `".$prefix."question_categories` WHERE `name` = '".$category."'";
		$rescat = $this->db_select($query);
		$catid =  $rescat[0]['id'];
		
		// if it exists, updates row
		if(!empty($result)) {
			$query = "UPDATE `".$prefix."games_configurations` 
				SET `n_rounds` = ".$n_rounds.", `time_limit` = ".$time_limit.", `category` = ".$catid."
				WHERE `courseid` = ".$courseid."";
		} // else, insert new row
		else {
			$query = "INSERT INTO `".$prefix."games_configurations` (courseid, n_rounds, time_limit, category)
									VALUES (".$courseid.", ".$n_rounds.", ".$time_limit.", ".$catid.")";	
		}
	
		$result = $this->db_query($query);
		
		// error
		if($result === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			echo 'error: could not query the database (db_save_course_config())<br>Error? '.$error;
			return false;
		}
		
		return true;
	}
	
	// retrieves a course config in the db
	// receives course id $courseid
	public function db_get_course_config($courseid) {
		
		// Connect to the database
		$connection = $this->db_connect();
		
		$config = $GLOBALS['config']; 
		// gets the prefix of moodle database tables
		$prefix = $config['prefix'];
		
		// query the db
		$result = $this->db_select("SELECT * FROM `".$prefix."games_configurations` WHERE `courseid` = ".$courseid."");

		// error
		if($result === false) {
			$error = $this->db_error();
			// Handle error - inform administrator, log to file, show error page, etc.
			error_log('error: could not query the database (db_get_course_config())<br>Error? '.$error);
			return $result;
		}
					
		// stores info retrieve
		$config = array();
		$config['courseid'] = $result[0]['courseid'];
		$config['n_rounds'] = $result[0]['n_rounds'];
		$config['n_rounds'] = $result[0]['n_rounds'];
		$config['time_limit'] = $result[0]['time_limit'];
		$config['category'] = $result[0]['category'];
		
		// returns in a array all info from player record
		return $config;
	}
	
	/** DP HELPER PLUGIN EXISTING METHODS CALLS **/
	
	// returns the player id of a respective
	// user id and course id (a plugin player)
	public function db_get_playerID($userid, $courseid) 
	{
		$this->set_dbhelper();
		return $this->dbhelper->db_get_playerID($userid, $courseid);
	}
	
	// returns an array of players of a course
	// with all info needed for the games 
	// require the class players in /obj
	public function db_get_players($courseid)
	{
		$this->set_dbhelper();
		return $this->dbhelper->db_get_players($courseid);
	}
	
	// returns an array of multichoice questions 
	// with all info needed for the games
	// require the class of multi choice questions
	public function get_multichoice_questions($category)
	{
		$this->set_dbhelper();
		return $this->dbhelper->get_multichoice_questions($category);
	}
	
	// returns the list of categories in the question bank
	public function db_get_categories()  
	{
		$this->set_dbhelper();
		return $this->dbhelper->db_get_categories();
	}
	
	// receives user id and returns user name
	public function db_get_user_name($userid)
	{
		$this->set_dbhelper();
		return $this->dbhelper->db_get_user_name($userid);
	}
	
	// receives user player id and returns user name
	public function db_get_player_name($playerid)
	{
		$this->set_dbhelper();
		return $this->dbhelper->db_get_player_name($playerid);
	}
	
	// receives course id and returns course name
	public function db_get_course_name($courseid)
	{
		$this->set_dbhelper();
		return $this->dbhelper->db_get_course_name($courseid);
	}
	
	// returns the id, on the db, of the current logged user 
	public function get_userID()
	{
		$this->set_dbhelper();
		return $this->dbhelper->get_userID();
	}
	
	// returns the id, on the db, of the current course user is in
	public function get_courseID()
	{
		$this->set_dbhelper();
		return $this->dbhelper->get_courseID();
	}
	
	// gets current user capability
	public function get_user_capability()
	{
		$this->set_dbhelper();
		return $this->dbhelper->get_user_capability();
	}
	
	// receives a category id and return its name
	public function db_get_category_name($category) 
	{
		$this->set_dbhelper();
		return $this->dbhelper->db_get_category_name($category);
	}
	
	/** end of DBHELPER methods composing **/
	
	// gets current games ranks and returns in array
	/* array[$i]['rankname'] => name of the rank $i
	   array[$i]['rankwins'] => the number of wins to achieve rank $i	
	   array[$i]['imagepath'] => the path to the image of rank $i (relative path)
	*/
	public function get_ranks()
	{	
		// parse the rank info array from ini
		$ini_array = parse_ini_file("rank.ini");
		$rank_wins = $ini_array['rank'];
		$rank_names = array_keys($rank_wins);
		
		/* The names of a '$i' rank => $rank_names[$i] */
		/* The number of wins to achieve a '$i' rank => $rank_wins[$rank_names[$i]] */
		/* The path of the image of a '$i' rank => 'folder:images/ranks/'($i+1).png */

		// the new array object that will be returned with all rank info
		$ranks[] = array();
		
		// loops through the array obtainer from ini file and stores
		// in the one that will be returned with the described structure
		for($i = 0 ; $i < count($rank_names); $i++) {
			$ranks[$i]['rankname'] = $rank_names[$i];
			$ranks[$i]['rankwins'] = $rank_wins[$rank_names[$i]];
			$ranks[$i]['imagepath'] = "images/ranks/".($i+1).".png";
		}
		
		// returns the now fulfilled array
		return $ranks;
	}
	
	// returns player rank within an object
	// with all necessary information. 
	// recieves the number of wins a player has
	/* rank['rankname'] => name of the rank
	   rank['rankwins'] => the number of wins to achieve rank
	   rank['imagepath'] => the path to the image of rank (relative path)
	*/
	public function get_player_rank($player_wins) 
	{
		// loads the game ranks
		$ranks = $this->get_ranks();
		
		// the rank that will be returned in the end of this method_exists
		$rank;
		
		// only search for a players rank if player wins
		// is not higher or equal than higher rank
		// if it is higher or equal, than player rank is equal to higher rank
		if($player_wins >= $ranks[count($ranks)-1]['rankwins'])
		{
			$rank = $ranks[count($ranks)-1];
			return $rank;
		}
		
		// finds the first rank that number of wins is bigger than player wins
		// considering that rank ini has all ranks in crescent order
		for($i = 0; $i < count($ranks) ; $i++)
		{
			// found the first rank higher than player wins
			if($ranks[$i]['rankwins'] > $player_wins)
			{
				// player rank
				$rank = $ranks[$i-1];
				break;
			}
		}
		
		// returns player rank
		return $rank;
	}
	
	// returns how many wins is necessary for a player to achieve next rank
	// recieves a player current number of wins and returns the next rank wins 
	function get_wins_next_rank($player_wins)
	{
		// load the game ranks
		$ranks = $this->get_ranks();
		
		// if player is on last rank, returns a string representing max rank
		if($player_wins >= $ranks[count($ranks)-1]['rankwins'])
			return "maxedrank";
		
		// if there is still higher ranks for a player, returns the n wins for the next rank
		
		// finds the first rank that number of wins is bigger than player wins
		// considering that rank ini has all ranks in crescent order
		// and returns it
		for($i = 0; $i < count($ranks) ; $i++)
		{
			// found the first rank higher than player wins
			if($ranks[$i]['rankwins'] > $player_wins)
			{
				// returns n of wins for the next rank
				return $ranks[$i]['rankwins'];
			}
		}
		
	}
	
	// returns a bool representing if player
	// has just achieved a higher rank
	// recieves the current number of wins of a player
	function has_player_ranked($player_wins) 
	{
		// loads the game ranks
		$ranks = $this->get_ranks();
		
		// search ranks for a rank that has exactly
		// the same wins of player wins. If it is found,
		// than it menas that the player has just achieved that rank
		for($i = 0; $i < count($ranks) ; $i++)
			if($ranks[$i]['rankwins'] == $player_wins)
				return true;
			
		return false;
	}
	
	// gets current game options and returns in array
	/* options[0] => first option of number of rounds
	   options[1] => second option of number of rounds
	   options[2] => third option of number of rounds
	   options[3] => first option of round time limit
	   options[4] => second option of round time limit
	   options[5] => third option of round time limit
	*/
	public function get_options()
	{	
		// parse the options info array from ini
		$ini_array = parse_ini_file("options.ini");
		$rounds = $ini_array['rounds'];
		$time = $ini_array['time'];

		// the new array object that will be returned with all options info
		$options[] = array();
		
		// loops through the rounds array obtained from ini file and stores
		// in the one that will be returned with the described structure
		for($i = 0 ; $i < count($rounds); $i++) {
			$options[$i] = $rounds["op".($i+1)];
		}
		
		// loops through the time array obtained from ini file and stores
		// in the one that will be returned with the described structure
		for($i = 0; $i < count($time); $i++) {
			$options[$i + count($rounds)] = $time["op".($i+1)];
		}
		
		// returns the now fulfilled array
		return $options;
	}
	
}

/* response to ajax solicitations */

if(isset($_POST['action']) && !empty($_POST['action'])) {
	
	$db = new db();
	
    $action = $_POST['action'];
	
    switch($action) {
        case 'notedelete' : $db->db_delete_notification($_POST['nid']) ; break;
		case 'checknote' : echo $db->db_check_notifications($_POST['sendee']); break;
		case 'saveconfig' : echo ($db->db_save_course_config($_POST['cid'], $_POST['n_rounds'], $_POST['time_limit'], $_POST['category'])); break;
		default: echo "Unknown action" ; break; 
    }
	
	if (!empty($_POST['actionext']))
	{
		$actionext = $_POST['actionext'];
		
		switch($actionext) {
			case 'sendrefusal' : 
				$userid = $db->get_userID();
				$username = $db->db_get_user_name($userid);
				$text = buildRefusalMessage($username);
				$db->db_register_notification("message", $userid, $_POST['sendee'], $text);
				echo("ajax solicitations");
				break;
			default: echo "Unknown action" ; break; 
		}
	}
}

function buildRefusalMessage($refuser) {
	
	$db = new db();
	
	return "Player ".htmlentities($refuser,  ENT_COMPAT,'ISO-8859-1', true)." has refused your challenge.";
}