<?php
// Local DB
$_ENV["DB_USER"]      = "db_user_name";
$_ENV["DB_PASSWORD"]  = "db_password";
$_ENV["DB_NAME"]      = "db_name";
$_ENV["TABLE_PREFIX"] = "wp_";
$_ENV["DB_HOST"]      = "db_host";
$_ENV["WP_DEBUG"]     = true;

// Dev
$_ENV["dev"]["url"] = 'dev.example.com';
$_ENV["dev"]["sql"] = 'db_dump_dev.sql';

// Staging
$_ENV["staging"]["url"] = 'staging.example.com';
$_ENV["staging"]["sql"] = 'db_dump_staging.sql';

// Live
$_ENV["live"]["url"] = 'example.com';
$_ENV["live"]["sql"] = 'db_dump_live.sql';