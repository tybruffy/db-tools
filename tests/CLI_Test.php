<?php 

Class CLI_Test extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		mkdir("/tmp/");
	}

	protected function tearDown() {
		rmdir("/tmp/");
	}	

	public function test_file_check() {
		$filename = "tmp/file_test_check.tmp";
		
		file_put_contents($filename, "This file was created by a test and can be safely deleted.");

		$this->assertTrue( $cli->file_check($filename); );
		$this->assertFalse( $cli->file_check("/path/to/fake.file") );
	}


}