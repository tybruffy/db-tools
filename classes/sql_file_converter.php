<?php 
require_once('cli.php');
require_once('sql_file_url_replacer.php');

/**
 * Replace a URL with another URL, or a group of URLs with another group of URLs
 *  in a SQL file.
 */
Class SQL_File_Converter extends CLI {

	/**
	 * Creates the Converter object and initiates a replacement.
	 *
	 * This sets up some default properties and then preapres and runs the file 
	 * conversion.
	 * 
	 * @param  array $input  The array of data associated with the input file/urls.
	 * @param  array $output The array of data associated with the output file/urls.
	 */
	function __construct($input, $output) {
		$this->directory = dirname(__DIR__).$_ENV["db_dir"];
		
		$this->file_check($this->directory.$_ENV[$input]['sql']);

		$this->input   = $_ENV[$input];
		$this->output  = $_ENV[$output];
		$this->content = file_get_contents( $this->directory.$this->input['sql'] );
		
		$this->message("Reading contents of {$this->input[sql]}");
	}

	/**
	 * Runs the logic for determining what type of replacement to make.
	 *
	 * Simply calls the replace_url() method for simple conversions, or alternately
	 * calls the replcae_urls() method for array to array conversions.  It will also
	 * stop execution if it notices that the input data type is not the same as the
	 * output data type.
	 * 
	 * @param  array $input  The array of data associated with the input file/urls.
	 * @param  array $output The array of data associated with the output file/urls.
	 */
	function convert() {		
		if ( is_array($this->input["url"]) && is_array($this->output["url"]) ) {
			$this->replcae_urls($this->input["url"], $this->output["url"]);
		} elseif ( is_string($this->input["url"]) && is_string($this->output["url"]) ) {
			$this->replace_url($this->input["url"], $this->output["url"]);
		} else {
			$message = sprintf(
				'Please make sure that the URL properties of your input and output match.  The URL property of your input array is %s and the output array is %s.  Please make sure they are both arrays or both strings.',
				gettype($this->input["url"]),
				gettype($this->output["url"])
			);
			$message = $this->message($message, "error", false);
			die($message);
		}
		$this->save();
	}

	/**
	 * Creates a new SQL_File_URL_Replacer and passes in the search terms.
	 *
	 * Passes the string to search, the searched for URL and the replacement URL
	 * to the SQL_File_URL_Replacer object.  Then it calls the replace() method
	 * on that object, and finally sets the content property to be the output from
	 * the replacer object.  The content property getting set here also benefits the
	 * replcae_urls() method because each time it calls this method, it is able to 
	 * pass in the updated content property easily.
	 * 
	 * @param  string $search  The URL to be replaced.
	 * @param  string $replace The URL to replace the old URL with.
	 */
	function replace_url($search, $replace) {
		$replacer = new SQL_File_URL_Replacer($this->content, $search, $replace);
		$replacer->replace();
		$this->content = $replacer->get_output();
	}

	/**
	 * Takes an array of URLs and passes them to the replace_url function.
	 * @param  array $inputs  The array of input URLs
	 * @param  array $outputs The array of new URLs
	 */
	function replcae_urls($inputs, $outputs) {
		foreach($inputs as $index => $url) {
			$this->replace_url($inputs[$index], $outputs[$index]);
		}
	}

	/**
	 * Saves the content to the sql file specified in the output array.
	 */
	private function save() {
		echo "Writing modified SQL file to \033[1;34m{$this->output[sql]}\033[0m.\n";
		$write_status = file_put_contents( $this->directory.$this->output['sql'], $this->content );
		$this->save_messages( $write_status );
	}

	/**
	 * Writes a message to the user based on the status of the file save.
	 * @param  boolean $status True if file saved successfully, false otherwise.
	 */
	private function save_messages( $status ) {
		if( $status !== false ) {
			$this->message("Successfully modified {$this->output[sql]}.", "success");
		} else {
			$this->message("Could not write to {$this->output[sql]}", "error");
		}
	}

}