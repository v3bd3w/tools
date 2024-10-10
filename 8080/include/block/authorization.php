<?php
$hash = getenv('WEB_SERVER_HASH', true);
if(!is_string($hash)) {
	trigger_error("Environment variable 'WEB_SERVER_HASH' is not set", E_USER_ERROR);
	exit(255);
}

$authorization = function(string $hash)
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
	
	$credentials_md5 = md5($credentials);
	
	if (strcmp($credentials_md5, $hash) !== 0) {
		return(false);
	}
	
	return(true);
};

$return = $authorization($hash);
if ($return !== true) {
	http_response_code(401);
	header('WWW-Authenticate: Basic realm="Access to the php built-in web server", charset="UTF-8"');
	exit(0);
}

unset($hash, $authorization, $return);

