<?php

/**
 * This class acts as a parent class for all command line operations.
 */
Class CLI {

	/**
	 * Checks for a file, kills execution if not found.
	 *
	 * Checks for an important file and halts execution of the rest of the script
	 * if not found.  Used to detect the presence of non include files, in this
	 * case the SQL input file.
	 * 
	 * @param  string $file The filepath to check for.
	 * @return boolean       True if file exists.
	 */
	public function file_check( $file ) {
		if (!file_exists($file)) {
			die($this->message("{$file} does not exist.  Please create the database dump before running this script.", "error"));
		}
		return true;
	}

	/**
	 * Outputs a message to the command line.
	 *
	 * Useful to display information on the command line.  Changes colors of messages
	 * based on the $type variable.  Success messages are green, info messages are
	 * light blue, and error messages are red.
	 * 
	 * @param  string  $message The message to output
	 * @param  string  $type    The type of message.  Available options are success, info and error.
	 * @param  boolean $echo    Whether or not to echo the message or return the string content.
	 * @return string           The message string if $echo is set to false.
	 */
	public function message($message, $type = "", $echo = true) {
		switch ($type) {
			case "success":
				$color = "\033[0;32m";
				break;
			case "info":
				$color = "\033[1;34m";
				break;
			case "error":
				$color = "\033[0;31m";
				break;
			default:
				break;
		}

		$output = $color.$message."\033[0m\n";

		if ($echo) {
			echo $output;	
		} else {
			return $output;
		}
	}

	/**
	 * Logs data to a log file.
	 *
	 * Useful for storing extra data about an operation. Can be used to output
	 * data foe debugging, as well as to save data about a particular report that
	 * may not need the user's immediate attention.
	 * 
	 * @param  string  $message  The message to log.
	 * @param  boolean $var      Whether or not the message is a variable to debug.
	 * @param  string  $filename The name of the file to write to.
	 */
	public function log($message, $var = false, $filename = 'debug.log') {
		$log = $var ? "$message\n".print_r($var, true)."\n\n" : "$message\n\n";
		file_put_contents( 'debug.log', $log, FILE_APPEND );		
	}	

}