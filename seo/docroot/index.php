<?php

$home = getenv('HOME', true);
if(!is_string($home)) {
	trigger_error("Can't get 'HOME' environment", E_USER_ERROR);
	exit(255);
}
$file = "{$home}/.config/v3bd3w/seo.php";
require_once($file);


set_include_path(__DIR__.'/../include');
require_once('class/DB.php');
require_once('class/Action.php');
require_once('class/Data.php');

function main()
{//{{{//
	$request_method = @strval($_SERVER["REQUEST_METHOD"]);
	if($request_method != "POST") {
		if (defined('DEBUG') && DEBUG) var_dump(['$request_method' => $request_method]);
		trigger_error("Unsupported request method", E_USER_WARNING);
		return(false);
	}
	
	DB::open(CONFIG["database"]["host"], CONFIG["database"]["user"], CONFIG["database"]["password"], CONFIG["database"]["name"]);
	
	$action = @strval($_GET["action"]);
	switch($action) {
		case('save_titles'):
			$return = Action::save_titles();
			if(!$return) {
				trigger_error("Action 'save_titles' failed", E_USER_WARNING);
				return(false);
			}
			break;
		default:
			if (defined('DEBUG') && DEBUG) var_dump(['$action' => $action]);
			trigger_error("Unsupported action", E_USER_WARNING);
			return(false);
	}
	
	return(true);
}//}}}//

$return = main();
if($return !== true) {
	trigger_error("Calling 'main' returned an error", E_USER_ERROR);
	exit(255);
}

exit(0);
