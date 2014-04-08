<?php

require_once('cli.php');

/**
 * This object performs the actual URL Replacement that the SQL_File_Converter
 * initiates.  This file actually interfaces with the SQL text and performs
 * the replacement, then returns the modified text to the SQL_File_Converter
 * object.
 *
 * Note: This Class makes heavy use of passing by reference.  This is due to 
 * necessity based on the limitations of the array_walk_recursive function.
 *
 * @todo Make this more testable.
 */
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

	/**
	 * Sets all the necessary object properties for a search and replacement.
	 * 
	 * @param string $text    The string to search through.
	 * @param string $search  The string to search for.
	 * @param string $replace The string to replace the old string.
	 */
	function __construct($text, $search, $replace) {
		$this->input       = $text;
		$this->search      = $search;
		$this->replace     = $replace;
		$this->char_diff   = strlen( $replace ) - strlen( $search ); 
		$this->regex_url   = preg_quote($search);
		$this->input_regex = '/\/\/' . $this->regex_url . '/';
		$this->array_regex = '/a:[\d]+:\{.*\/\/'.$this->regex_url.'.*;\}+/';
	}

	/**
	 * This function initializes the URL replacement process and then outputs the success
	 * or failure messages.
	 */
	public function replace() {
		echo "Replacing \033[1;34m{$this->search}\033[0m with \033[1;34m{$this->replace}\033[0m \n";
		$this->output = $this->find_serialized( $this->input );
		$this->replace_string( $this->output, null, $this->string_count );
		$this->output_messages();
	}

	/**
	 * Displays output messages for a set of replacements.
	 *
	 * Outputs messages describing the number and type of string replacements made.
	 */
	public function output_messages() {		
		$this->message("{$this->array_count["instances"]} replacements made in {$this->array_count["total"]} serialized objects/arrays.", "info");
		$this->message("{$this->string_count} general string replacements made.", "info");	

		if ($this->log_count >= 1 ) {
			$this->message("{$this->log_count} errors written to debug.log.", "error");
		}
	}

	/**
	 * Returns the output text.
	 * 
	 * @return string The modified search text with the initial URL replaced.
	 */
	public function get_output() {
		return $this->output;
	}

	/**
	 * Finds and replaces URLs that appear to be in serialized strings.  
	 *
	 * Takes a match found by the preg_replace_callback() call in the find_serialized() method
	 * and unserializes the array.  Then it searches unserialized array for instances of the 
	 * URL and replaces them with the new URL.  Then re-serializes the array and makes it
	 * safe for mysql before returning it to the preg_replace_callback() call, which then moves
	 * on to the next match found.  If it is unable to unserialize the array for some reason
	 * it will add a message to the debug log which details the string it was unable to serialize.
	 * 
	 * @param  array $matches An array containing the serialized array.
	 * @return string         The serialized string with the URL replaced.
	 */
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

	/**
	 * Searches and replcaes the input string for URLs which are part of serialized arrays.
	 *
	 * Searches the input string for URLs which are part of serialized arrays.  
	 * It then uses it's callback function to replace the URL in the serialized string
	 * with the new URL.
	 *
	 * @param  string $text The string to search through.
	 * 
	 * @return string The searched text with the serialized array updated.
	 */
	private function find_serialized( $text ) {
		return preg_replace_callback( $this->array_regex, array($this, "replace_serialized"), $text);
	}

	/**
	 * Replaces all instances of the URL with the new URL in a given string.
	 * 
	 * @param  string  $haystack The string to search through.
	 * @param  integer $index    The index of the value, used to determine if it was an array or not.
	 * @param  integer $counter  An integer representing the current number of replacements made.
	 */
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

	/**
	 * Replaces the URL if found in a serialized object.
	 * 
	 * @param  object $obj The object that contains the URL as a property.
	 *
	 * @return array       The replaced data as an array.
	 *
	 * @todo FIgure out why this is returning as an array.
	 */
	private function replace_object( $obj ) {
		$array = get_object_vars($obj);
		array_walk_recursive($array, array($this, 'replace_string'), $this->array_count["instances"]);
		return $array;
	}

}
