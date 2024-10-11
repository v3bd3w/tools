<?php

// Usage example
/*
	Args::$description = "Program description";
	Args::add([
		"-a", "--A", NULL, "not required parameter",
		function () {
			define("A", true);
		}, false
	]);
	Args::add([
		"-b", "--B", NULL, "required parameter",
		function () {
			define("B", true);
		}, true
	]);
	Args::add([
		"-c", "--C", "STRING", "not required parameter with value",
		function ($string) {
			define("C", $string);
		}, false
	]);
	Args::add([
		"-d", "--D", "STRING", "required parameter with value",
		function ($string) {
			define("D", $string);
		}, true
	]);
	Args::apply();
*/

class Args 
{
	static $description = "";
	static $config = [];
	
	static function help()
	{
		$text = "";
		if (!empty(self::$description))
			$text .= "\n".self::$description."\n\n";
		$text .= "Usage: \n";
		foreach (self::$config as $config) {
			$text .= "  {$config[0]} {$config[1]}";
			if ($config[2] !== null) {
				$text .= " {$config[2]}\t{$config[3]}\n";
			} else {
				$text .= "\t{$config[3]}\n";
			}
		}
		echo $text."\n";
		return NULL;
	}
	
	static function apply()
	{
		self::add();
		global $argv;
		array_walk(self::$config, function(array $config, int $index, array $argv) {
			$c = count($argv);
			for ($i = 1; $i < $c; $i++) {
				if ($argv[$i] == $config[0] || $argv[$i] == $config[1]) {
					if ($config[2] !== null) {
						if (!isset($argv[($i+1)])) {
							trigger_error("\"{$config[2]}\" is not set for \"{$config[0]}\" in command line", E_USER_ERROR);
							exit(255);
						}
						self::$config[$index][4]($argv[$i+1]);
						return null;
					} else {
						self::$config[$index][4]();
						return null;
					}
				}
			}
			if (self::$config[$index][5]) {
				trigger_error("\"{$config[0]}\" is not set in command line", E_USER_ERROR);
				exit(255);
			}
		}, $argv);
	}
	
	static function add(array $config = [])
	{
		if (!empty($config)) {
			array_push(self::$config, $config);
			return null;
		}
		self::add([
			'-v', '--verbose', null, "Allow verbose messages to stderr", 
			function() {
				define('VERBOSE', true);
			}, false
		]);
		self::add([
			'-q', '--quiet', null, "Prevent output to stdout", 
			function() {
				define('QUIET', true);
				ob_start();
				register_shutdown_function(function () {
					ob_end_clean();
				} );
			}, false
		]);
		self::add([
			'-d', '--debug', null, "Run in debug mode", 
			function() {
				define('DEBUG', true);
			}, false
		]);
		array_unshift(self::$config, [
			'-h', '--help', null, "Show help text and exit",
			function() {
				self::help();
				exit(0);
			}, false
		]);
	}
}

