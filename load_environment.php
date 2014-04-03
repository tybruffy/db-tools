<?php
if(file_exists(__DIR__ . "/production_env.php")) {
	require_once(__DIR__ . "/production_env.php");
} elseif(file_exists(__DIR__ . "/staging_env.php")) {
	require_once(__DIR__ . "/staging_env.php");
} elseif(is_file(__DIR__ . "/development_env.php")) {
	require_once(__DIR__ . "/development_env.php");
} elseif(is_file("../../environment/production_env.php")) { // For deployment via Capistrano
	require_once("../../environment/production_env.php"); // For deployment via Capistrano
} elseif(is_file("../../environment/staging_env.php")) { // For deployment via Capistrano
	require_once("../../environment/staging_env.php"); // For deployment via Capistrano
} elseif(is_file("../../environment/development_env.php")) { // For deployment via Capistrano
	require_once("../../environment/development_env.php"); // For deployment via Capistrano
} else {
	die("Sorry! We're busy making this site better. We'll be back online shortly. (No environment could be loaded.)");
}	
?>