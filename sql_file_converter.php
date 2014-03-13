<?php 

require_once('cli.php');
require_once('sql_file_url_replacer.php');

Class SQL_File_Converter extends CLI {


	function __construct($input, $output) {
		$this->file_check($_ENV["db_dir"].$_ENV[$input]['sql']);		

		$this->input   = $_ENV[$input];
		$this->output  = $_ENV[$output];
		$this->content = file_get_contents( $_ENV["db_dir"].$this->input['sql'] );
		
		$this->message("Reading contents of {$this->input[sql]}");
		$this->do_replacement($this->input, $this->output);
	}


	function do_replacement($input, $output) {		
		if ( is_array($input["url"]) && is_array($output["url"]) ) {
			$this->replcae_urls($input["url"], $output["url"]);
		} elseif ( is_string($input["url"]) && is_string($output["url"]) ) {
			$this->replace_url($input["url"], $output["url"]);
		} else {
			$message = sprintf(
				'Please make sure that the URL properties of your input and output match.  The URL property of your input array is %s and the output array is %s.  Please make sure they are both arrays or both strings.',
				gettype($input["url"]),
				gettype($output["url"])
			);
			$this->message($message, "error");
			exit;
		}
		$this->save();
	}


	function replace_url($search, $replace) {
		$replacer = new SQL_File_URL_Replacer($this->content, $search, $replace);
		$replacer->replace();
		$this->content = $replacer->get_output();
	}


	function replcae_urls($inputs, $outputs) {
		foreach($inputs as $index => $url) {
			$this->replace_url($inputs[$index], $outputs[$index]);
		}
	}


	private function save() {
		echo "Writing modified SQL file to \033[1;34m{$this->output[sql]}\033[0m.\n";
		$write_status = file_put_contents( $_ENV["db_dir"].$this->output['sql'], $this->content );
		$this->save_messages( $write_status );
	}


	private function save_messages( $status ) {
		if( $status !== false ) {
			$this->message("Successfully modified {$this->output[sql]}.", "success");
		} else {
			$this->message("Could not write to {$this->output[sql]}", "error");
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
	new SQL_File_Converter($argv[1], $argv[2]);
}