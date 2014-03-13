<?php

require_once('cli.php');

Class SQL_File_URL_Replacer extends CLI {
	
	private $search;
	private $replace;

	private $char_diff;
	private $regex_url;
	private $array_regex;
	private $input_regex;
	private $string_count = 0;
	private $log_count    = 0;
	private $array_count  = array(
		"total"     => 0,
		"instances" => 0,
	);

	private $input;
	private $output;


	function __construct($text, $search, $replace) {
		$this->input = $text;
		$this->set_vars($search, $replace);
	}


	public function set_vars($search, $replace) {
		$this->search      = $search;
		$this->replace     = $replace;
		$this->char_diff   = strlen( $replace ) - strlen( $search ); 
		$this->regex_url   = preg_quote($search);
		$this->input_regex = '/\/\/' . $this->regex_url . '/';
		$this->array_regex = '/a:[\d]+:\{.*\/\/'.$this->regex_url.'.*;\}+/';
	}


	public function replace() {
		echo "Replacing \033[1;34m{$this->search}\033[0m with \033[1;34m{$this->replace}\033[0m \n";
		$this->output = $this->find_serialized();
		$this->replace_string( $this->output, null, $this->string_count );
		$this->output_messages();
	}

	public function output_messages() {		
		$this->message("{$this->array_count["instances"]} replacements made in {$this->array_count["total"]} serialized objects/arrays.", "info");
		$this->message("{$this->string_count} general string replacements made.", "info");	

		if ($this->log_count >= 1 ) {
			$this->message("{$this->log_count} errors written to debug.log.", "error");
		}
	}

	public function get_output() {
		return $this->output;
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
			$haystack = preg_replace( $this->input_regex, '//' . $this->replace, $haystack, -1, $counter );
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

}
