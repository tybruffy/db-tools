<?php 
require_once(dirname(__DIR__).'/classes/sql_file_url_replacer.php');

/**
 * Replace a URL with another URL, or a group of URLs with another group of URLs
 *  in a SQL file.
 */
Class SQL_File_URL_Replacer_Test extends PHPUnit_Framework_TestCase {
	

	function setUp() {
		$this->text     = file_get_contents(__DIR__."/fixtures/input.sql");
		$this->input    = "input.com";
		$this->output   = "output.com";

		// MUST actually pass input/output here so that other attributes get set.
		$this->replacer = new SQL_File_URL_Replacer("", $this->input, $this->output);
	}


	function get_method( $name ) {
		$method = new ReflectionMethod('SQL_File_URL_Replacer', $name);
		$method->setAccessible(TRUE);
		return $method;
	}


	function test_String_Replacement() {
		// Set up our data
		$string     = "This URL appears in a string http://";
		$ref_string = $string.$this->input;
		$count      = 0;

		// Get our private method and make it accessible
		$replace = $this->get_method("replace_string");
		
		// Replace the object text.
		$array = $replace->invokeArgs( $this->replacer, array( &$ref_string, null, &$count ));
		
		$this->assertEquals($string.$this->output, $ref_string);
	}


	function test_Serialized_Replacement() {
		// Set up our data
		$string = "This URL appears in a string http://";
		$array1 = array(
			"key1" => "http://$this->input",
			"key2" => $string . $this->input,
		);

		$array2 = array(
			"key1" => "http://$this->output",
			"key2" => $string . $this->output,
		);	

		$serialized = serialize($array1);
		$expected   = serialize($array2);
		$expected   = mysql_escape_string($expected);

		// Get our private method and make it accessible
		$replace = $this->get_method("find_serialized");
		
		// Replace the serialized text.
		$converted = $replace->invokeArgs( $this->replacer, array($serialized) );

		// Check that they equal what we expect
		$this->assertEquals( $expected, $converted );
	}


	function test_Object_Replacement() {
		// Set up our data
		$obj         = new stdClass();
		$string      = "This URL appears in a string http://";
		$obj->param1 = "http://$this->input";
		$obj->param2 = $string . $this->input;

		// Get our private method and make it accessible
		$replace = $this->get_method("replace_object");
		
		// Replace the object text.
		$array = $replace->invokeArgs( $this->replacer, array($obj) );

		// Check that they equal what we expect
		$this->assertEquals( "http://$this->output", $array["param1"]);
		$this->assertEquals( $string.$this->output, $array["param2"] );
	}


}