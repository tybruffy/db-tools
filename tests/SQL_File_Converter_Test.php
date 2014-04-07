<?php 
require_once('cli.php');
require_once('sql_file_converter.php');

/**
 * Replace a URL with another URL, or a group of URLs with another group of URLs
 *  in a SQL file.
 */
Class SQL_File_Converter_Test extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		$this->converter = new SQL_File_Converter();
	}
	
	function __construct($input, $output) {
		$this->file_check($_ENV["db_dir"].$_ENV[$input]['sql']);		

		$this->input   = $_ENV[$input];
		$this->output  = $_ENV[$output];
		$this->content = file_get_contents( $_ENV["db_dir"].$this->input['sql'] );
		
		$this->message("Reading contents of {$this->input[sql]}");
		$this->do_replacement($this->input, $this->output);
	}


}