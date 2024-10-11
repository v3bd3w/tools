<?php

class Data
{
	static $TABLE = [
		"host" => 'seo.host',
		"title" => 'seo.title',
	];
	
	// Incoming data
	
	static $list = []; // ['host', 'title', 'url']
	
	static function set_list(array $titles)
	{//{{{//
		$result = [];
		foreach($titles as $title) {
			if(@is_string($title["title"]) != true) {
				if (defined('DEBUG') && DEBUG) @var_dump(['$title["title"]' => $title["title"]]);
				trigger_error("Incorrect string 'title[title]'", E_USER_WARNING);
				return(false);
			}
			
			if(@is_string($title["url"]) != true) {
				if (defined('DEBUG') && DEBUG) @var_dump(['$title["url"]' => $title["url"]]);
				trigger_error("Incorrect string 'title[url]'", E_USER_WARNING);
				return(false);
			}
			
			$return = parse_url($title["url"], PHP_URL_HOST);
			if(!is_string($return)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$url' => $url]);
				trigger_error("Can't parse 'host' from 'url'", E_USER_WARNING);
				return(false);
			}
			$host = $return;
			
			array_push($result, [
				"host" => $host,
				"title" => $title["title"],
				"url" => $title["url"],
			]);
		}
		
		self::$list = $result;
		return(true);
	}//}}}//
	
	// Database
	
	static function create_tables()
	{//{{{
		$DB = new DB;
		$TABLE = self::$TABLE;
		$queries = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `{$TABLE["host"]}`;
CREATE TABLE `{$TABLE["host"]}` (
	`id` INT AUTO_INCREMENT KEY
	,`host` TEXT
);

DROP TABLE IF EXISTS `{$TABLE["title"]}`;
CREATE TABLE `{$TABLE["title"]}` (
	`id` INT AUTO_INCREMENT KEY
	,`host_id` INT
	,`title` TEXT
	,`url` TEXT
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($queries);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		return(true);
	}//}}}
	
	static function insert_host(string $host)
	{//{{{//
		$DB = new DB;
		
		$table = self::$TABLE["host"];
		$_ = [
			"host" => $DB->escape($host),
		];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$table}` (
	`host`
) VALUES (
	'{$_["host"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't insert host", E_USER_WARNING);
			return(false);
		}
		
		$result = [
			"id" => $DB->id(),
			"host" => $host,
		];
		return($result);
	}//}}}//
	
	static function select_host(string $host)
	{//{{{//
		$DB = new DB;
		
		$_ = [
			"host" => $DB->escape($host),
		];
		$table = self::$TABLE["host"];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$table}` WHERE `host`='{$_["host"]}' LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		$array = $return;
		
		if(empty($array)) return(NULL);
		
		$result = $array[0];
		return($result);
	}//}}}//
	
	static function insert_title(int $host_id, string $title, string $url)
	{//{{{//
		$DB = new DB;
		
		$table = self::$TABLE["title"];
		$_ = [
			"host_id" => $DB->int($host_id),
			"title" => $DB->escape($title),
			"url" => $DB->escape($url),
		];
		$sql =
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `{$table}` (
	`host_id`, `title`, `url`
) VALUES (
	{$_["host_id"]}, '{$_["title"]}', '{$_["url"]}'
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		$result = [
			"id" => $id,
			"host_id" => $host_id,
			"title" => $title,
			"url" => $url,
		];
		return($result);
	}//}}}//
	
	static function select_title($url)
	{//{{{//
		$DB = new DB;
		
		$table = self::$TABLE["title"];
		$_ = [
			"url" => $DB->escape($url)
		];
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `{$table}` WHERE `url`='{$_["url"]}' LIMIT 1;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!is_array($return)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		$array = $return;
		
		if(empty($array)) return(NULL);
		
		$result = $array[0];
		return($result);
	}//}}}//
	
}
