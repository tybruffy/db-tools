<?php

require_once('cli.php');
require_once('test.php');

Class DB_URL_Searcher extends CLI {
	
	private $file;
	private $search;
	private $regex;
	private $matches;

	private $counts;
	
	private $input;

	function __construct($file, $search) {
		$this->file_check($_ENV[$file]['sql']);
		$this->set_vars($_ENV[$file]['sql'], $search);
	}

	function set_vars($file, $search) {
		$this->file   = $file;
		$this->search = $search;
		$this->input  = file_get_contents( $file );
		$this->regex  = '/([^\"\'\s,]*)?'. preg_quote($this->search) . '/';
	}

	function search() {
		preg_match_all($this->regex, $this->input, $this->matches);
		$this->sort($this->matches);
	}

	private function sort($matches) {
		$this->counts = array_count_values($matches[1]);
		print_r($this->counts);
	}

}


$searcher = new DB_URL_Searcher($argv[1], $argv[2]);
$searcher->search();

