#!/usr/bin/php
<?php
if ($argc <= 2 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
?>
usage: ./sql_convert.sh [input] [output] [env]

Usage requires at least an input and output name:
   input   Array containing input data. Array keys are:
              sql: The filename to read from
              url: The url, or indexed array of URLs to replace
   output  Array containing output data. Array keys are:
              sql: The filename to write to
              url: The url, or indexed array of URLs to replace with 
   env     The file containing the input/output arrays. (Optional)
           By default looks for ../load_environment.php
<?php
} else {
	$env_file = $argv[3] ? $argv[3] : '../load_environment.php';

	if ( ! file_exists($env_file) ) {
		echo "\033[0;31mFile {$env_file} does not exist.\n";
	} else {
		require_once($env_file);
		require_once("classes/sql_file_converter.php");
		new SQL_File_Converter($argv[1], $argv[2]);
	}
}