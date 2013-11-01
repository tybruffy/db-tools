<?php

require_once('cli.php');

Class DB_URL_Replacer extends CLI {
	
	private $from;
	private $to;

	private $char_diff;
	private $regex_url;
	private $array_regex;
	private $from_regex;
	private $string_count = 0;
	private $log_count    = 0;
	private $array_count  = array(
		"total"     => 0,
		"instances" => 0,
	);

	private $input;
	private $output;

	function __construct($from, $to) {
		$this->file_check($_ENV[$from]['sql']);
		$this->set_vars($_ENV[$from], $_ENV[$to]);
	}

	public function set_vars($from, $to) {
		$this->from        = $from;
		$this->to          = $to;
		$this->char_diff   = strlen( $this->to['url'] ) - strlen( $this->from['url'] ); 
		$this->regex_url   = preg_quote($this->from['url']);
		$this->from_regex  = '/\/\/' . $this->regex_url . '/';
		$this->array_regex = '/a:[\d]+:\{.*\/\/'.$this->regex_url.'.*;\}+/';
		$this->input       = file_get_contents( $this->from['sql'] );

		$this->message("Reading contents of {$this->from[sql]}");
	}

	public function replace() {
		echo "Replacing \033[1;34m{$this->from[url]}\033[0m with \033[1;34m{$this->to[url]}\033[0m \n";
		$this->output = $this->find_serialized();
		$this->replace_string( $this->output, null, $this->string_count );
		$this->save();
	}

	private function replace_serialized( $matches ){
		$serial      = stripcslashes($matches[0]);
		$unserialzed = unserialize( $serial );

		if (is_array($unserialzed)) {
			$this->array_count["total"]++;
			array_walk_recursive($unserialzed, array($this, 'replace_string'), $this->array_count["instances"]);
			$replaced = serialize($unserialzed);
			return mysql_escape_string($replaced);
		} else {
			$this->log_count++;
			$this->log("Failed to unserialize the following string", $matches[0]);
			return;
		}
	}

	private function find_serialized() {
		return preg_replace_callback( $this->array_regex, array($this, "replace_serialized"), $this->input);
	}

	private function replace_string( &$haystack, $index = null, &$counter ) {
		if (is_object($haystack)) {
			$haystack = $this->replace_object($haystack);
		} else {
			$haystack = preg_replace( $this->from_regex, '//' . $this->to['url'], $haystack, -1, $counter );
			if (!is_null($index) && $counter) {
				$this->array_count["instances"]++;
			}
		}
	}

	private function replace_object( $obj ) {
		$array = get_object_vars($obj);
		array_walk_recursive($array, array($this, 'replace_string'), $this->array_count["instances"]);
		return $array;
	}

	private function save() {
		echo "Writing modified SQL file to \033[1;34m{$this->to[sql]}\033[0m.\n";
		$write_status = file_put_contents( $this->to['sql'], $this->output );
		$this->save_messages( $write_status );
	}

	private function save_messages( $status ) {
		if( $status !== false ) {
			$this->message("{$this->array_count["instances"]} replacements made in {$this->array_count["total"]} serialized objects/arrays.", "info");
			$this->message("{$this->string_count} general string replacements made.", "info");
			$this->message("Successfully modified {$this->to[sql]}.", "success");
		} else {
			$this->message("Could not write to $to[sql]", "error");
		}

		if ($this->log_count >= 1 ) {
			$this->message("{$this->log_count} errors written to debug.log.", "error");
		}
	}

}






if (!$argv[1] || !$argv[2]) {
	die("\033[0;31mPlease supply 2 arguments.  Your request should be made in the format: php -f db_url_update.php from_site to_site\033[0m.\n");
} else {
	if ($argv[3] == 'test')  {
		require_once('test.php');
	} else {
		require_once('../load_environment.php');
	}
	$replacer = new DB_URL_Replacer($argv[1], $argv[2]);
	$replacer->replace();
}