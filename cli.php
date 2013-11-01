<?php


Class CLI {

	public function file_check( $file ) {
		if (!file_exists($file)) {
			die($this->message("{$_ENV[$from]['sql']} does not exist.  Please create the database dump before running this script.", "error"));
		}
		return true;
	}

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

	public function log($message, $var = false, $filename = 'debug.log') {
		$log = $var ? "$message\n".print_r($var, true)."\n\n" : "$message\n\n";
		file_put_contents( 'debug.log', $log, FILE_APPEND );		
	}	

}