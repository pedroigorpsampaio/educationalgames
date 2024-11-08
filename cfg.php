<?php

/* DO NOT EDIT */
/* loaded direct from the moodle config.php file */
/* since informations must match, do not edit */

final class cfg
{
    /**
     * Call this method to get singleton
     *
     * @return UserFactory
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new self();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instance it
     *
     */
    private function __construct()
    {
		global $CFG;
		
		// infos from moodle global $CFG
		$dbhost    = $CFG->dbhost;
		$dbname    = $CFG->dbname;
		$dbuser    = $CFG->dbuser;
		$dbpass    = $CFG->dbpass;
		$prefix    = $CFG->prefix;
		$wwwroot   = $CFG->wwwroot;
		
		// writes database infos on database config file
		$dbfile = fopen(__DIR__ . "/db/dbconfig.ini", "w");

		$dbhost_parse = str_replace(";", "';'", $dbhost);
		$dbuser_parse = str_replace(";", "';'", $dbuser);
		$dbpass_parse = str_replace(";", "';'", $dbpass);
		$dbname_parse = str_replace(";", "';'", $dbname);
		$prefix_parse = str_replace(";", "';'", $prefix);

		fwrite($dbfile, "[database]".PHP_EOL);
		fwrite($dbfile, "host = ".$dbhost_parse.PHP_EOL);
		fwrite($dbfile, "username = ".$dbuser_parse.PHP_EOL);
		fwrite($dbfile, "password = ".$dbpass_parse.PHP_EOL);
		fwrite($dbfile, "dbname = ".$dbname_parse.PHP_EOL);
		fwrite($dbfile, "prefix = ".$prefix_parse.PHP_EOL);

		fclose($dbfile);
		
		// writes path infos in config file
		$tags = parse_ini_file(__DIR__ . "/config.ini"); 
		$root = $tags['root'];
		
		$cfgfile = fopen(__DIR__ . "/config.ini", "w");
		
		fwrite($cfgfile, "; EDIT WITH YOUR MOODLE DIR ROOT".PHP_EOL);
		fwrite($cfgfile, "; ex1: C:/xampp/htdocs/moodle".PHP_EOL);
		fwrite($cfgfile, "; ex2: C:/xampp/htdocs".PHP_EOL);
		fwrite($cfgfile, "root = ".$root.PHP_EOL);
		fwrite($cfgfile, "; DO NOT EDIT FROM THIS POINT ON".PHP_EOL);
		fwrite($cfgfile, "wwwroot = ".$wwwroot.PHP_EOL);
		
		fclose($cfgfile);
    }

}
?>