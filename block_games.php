<?php

class block_games extends block_base {
	
    public function init() {
        $this->title = get_string('games', 'block_games');
		// changes current working directory to the plugin directory
		chdir(__DIR__);
    }

	public function get_content() {
		if ($this->content !== null) {
		  return $this->content;
		}

		$this->content         =  new stdClass;
		$this->content->text   = file_get_contents(getcwd() . "/block_index.html");
		$this->content->footer = get_string('games:blockfooter', 'block_games');
	 
		return $this->content;
	}
  
}