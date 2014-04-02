# DB Tools
A set of tools for replacing URLs in a SQL database dump.

## Basic Usage
Include data in an environment file for the DB tools to access.  By default,
the converter looks for a file up one level called "load_environment.php" to
load environment data. Passing a 3rd "test" param to the call will force
it to look for a file in the same directory called test.php.

Necessary data for the environment file can be seen below:

```php
$_ENV["input"] = array(
	"sql" => "input.sql",
	"url" => "example.com",
);

$_ENV["output"] = array(
	"sql" => "output.sql",
	"url" => "dev.example.com",
);	

$_ENV["db_dir"] = __DIR__."/db/";
```

The `db_dir` variable is optional, and can be passed to override the default read/write
location of the sql files.

After that, just call the following from your command line:
```bash
php -f sql_file_converter.php input output
```

## Advanced Usage
The script can also be used to replace multiple URLs within the same database. Do this
by setting the `url` property of your input/output arrays to an array of urls like so:

```php
$_ENV["input"] = array(
	"sql" => "input.sql",
	"url" => array(
		"example.com",
		"company.com",
		"business.com",
	),
);

$_ENV["output"] = array(
	"sql" => "output.sql",
	"url" => array(
		"dev.example.com",
		"dev.company.com",
		"dev.business.com",
	),
);	
```
```bash
php -f sql_file_converter.php input output
```