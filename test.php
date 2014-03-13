<?php

	// $_ENV["input"]["url"]  = "jakedevelopment.com";
	// $_ENV["output"]["url"] = "dev.jakedevelopment.com";

	$_ENV["db_dir"] = __DIR__."/db/";

	$_ENV["input"]["sql"]  = "input.sql";
	$_ENV["output"]["sql"] = "output.sql";

	$_ENV["input"]["url"] = array(
		"jakedevelopment.com",
		"skilf.com",
		"vdevcorp.com",
		"kenneallycompany.com",
	);

	$_ENV["output"]["url"] = array(
		"dev.jakedevelopment.com",
		"dev.skilf.com",
		"dev.vdevcorp.com",
		"dev.kenneallycompany.com",
	);