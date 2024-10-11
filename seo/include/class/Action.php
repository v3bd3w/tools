<?php

class Action
{
	static function save_titles()
	{//{{{//
		$file = "php://input";
		$return = file_get_contents($file);
		if(!is_string($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
			trigger_error("Can't get json contents", E_USER_WARNING);
			return(false);
		}
		$json = $return;
		
		$return = json_decode($json, true);
		if(!is_array($return)) {
			if (defined('DEBUG') && DEBUG) var_dump(['$json' => $json]);
			trigger_error("Can't decode json into 'titles' array", E_USER_WARNING);
			return(false);
		}
		$titles = $return;
		
		$return = Data::set_list($titles);
		if(!$return) {
			trigger_error("Can't set list of 'titles'", E_USER_WARNING);
			return(false);
		}
		
		//user_error(var_export(Data::$list, true));
		
		foreach(Data::$list as $item) {
			$return = self::save_list_item($item);
			if(!is_array($return)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$item' => $item]);
				trigger_error("Can't save 'list item'", E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
	}//}}}//
	
	static function save_list_item(array $item)
	{//{{{//
		$host = $item["host"];
		$title = $item["title"];
		$url = $item["url"];
		
		$return = Data::select_host($host);
		if($return === false) {
			if (defined('DEBUG') && DEBUG) var_dump(['$host' => $host]);
			trigger_error("Can't select host", E_USER_WARNING);
			return(false);
		}
		if($return === NULL) {
			$return = Data::insert_host($host);
			if(!is_array($return)) {
				if (defined('DEBUG') && DEBUG) var_dump(['$host' => $host]);
				trigger_error("Can't insert host", E_USER_WARNING);
				return(false);
			}
		}
		$host = $return;
		
		$host_id = $host["id"];
		$return = Data::select_title($url);
		if($return === false) {
			if (defined('DEBUG') && DEBUG) var_dump(['$url' => $url]);
			trigger_error("Can't select 'title'", E_USER_WARNING);
			return(false);
		}
		if($return === NULL) {
			$return = Data::insert_title($host_id, $title, $url);
			if(!is_array($return)) {
				if (defined('DEBUG') && DEBUG) var_dump([
					'$host_id' => $host_id
					,'$title' => $title
					,'$url' => $url
					]);
				trigger_error("Can't insert title", E_USER_WARNING);
				return(false);
			}
		}
		$item = $return;
		
		$item["host"] = $host["host"];
		
		return($item);
	}//}}}//
	
}
