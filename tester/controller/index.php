<?php

$return = getenv('KEY');
if(!is_string($return)) {//{{{//
	//if (defined('DEBUG') && DEBUG) var_dump(['' => ]);
	trigger_error("Can't get 'KEY' environment", E_USER_ERROR);
	exit(255);
}//}}}//
define('KEY', $return);

//var_dump(["KEY" => md5(KEY)]);

require_once(__DIR__.'/Controller.php');

$url = 'http://localhost/index.php';
$Controller = new Controller(KEY, $url);

