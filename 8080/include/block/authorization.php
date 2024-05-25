<?php
header("X-Frame-Options: DENY");

$user = getenv('WEB_SERVER_USER', true);
if(!is_string($user)) {
	trigger_error("Environment variable 'WEB_SERVER_USER' is not set", E_USER_ERROR);
	exit(255);
}

$password = getenv('WEB_SERVER_PASSWORD', true);
if(!is_string($password)) {
	trigger_error("Environment variable 'WEB_SERVER_PASSWORD' is not set", E_USER_ERROR);
	exit(255);
}

$authorization = function(string $user, string $password)
{
	$headers = apache_request_headers();
	$r = array_key_exists('Authorization', $headers);
	if ($r !== true) {
		return(false);
	}
	
	$s = $headers['Authorization'];
	if (preg_match('/^([^\s]+)\s+([^\s]+)$/', $s, $m) !== 1) {
		return(false);
	}
	$type = $m[1];
	$credentials = $m[2];
	
	if (strcmp($type, 'Basic') !== 0) {
		return(false);
	}
	
	$s = base64_encode("{$user}:{$password}");
	if (strcmp($credentials, $s) !== 0) {
		return(false);
	}
	
	return(true);
};

$return = $authorization($user, $password);
if ($return !== true) {
	http_response_code(401);
	header('WWW-Authenticate: Basic realm="Access to the php built-in web server", charset="UTF-8"');
	exit(0);
}

$return = base64_encode("{$user}:{$password}");
$return = md5($return);
define('CSRF_TOKEN', $return);

if(
	isset($_SERVER["REQUEST_METHOD"])
	&& is_string($_SERVER["REQUEST_METHOD"])
	&& $_SERVER["REQUEST_METHOD"] == 'POST'
) {
	if(!(
		isset($_POST['csrf_token'])
		&& is_string($_POST['csrf_token'])
		&& strcmp(CSRF_TOKEN, $_POST['csrf_token']) === 0
	)) {
		trigger_error("Incorrect or not passed 'csrf_token' in POST request", E_USER_ERROR);
		exit(255);
	}
}

unset($user, $password, $authorization, $return);

