<?php 
require_once('cli.php');
require_once('sql_file_url_replacer.php');

/**
 * Replace a URL with another URL, or a group of URLs with another group of URLs
 *  in a SQL file.
 */
Class SQL_File_URL_Replacer_Test extends PHPUnit_Framework_TestCase {
	
	function setUp() {
		require("/fixtures/url_replacer_fixtures.php");
		$this->replacer = new SQL_File_URL_Replacer($text, $input, $output);
	}

	function Test_Object_Replacement() {
		// Set up our object
		$obj         = new stdClass();
		$string      = "This URL appears in a string";
		$obj->param1 = $this->input_url;
		$obj->param2 = $string.$this->input_url;

		// Replace the object text.
		$array = $this->replacer->replace_object( $obj );

		// Check that they equal what we expect
		$this->assertEquals($array["param1"], $this->output_url);
		$this->assertEquals($array["param2"], $string.$this->output_url);
	}


}