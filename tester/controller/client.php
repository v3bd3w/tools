<?php
define('CONFIG', [
	"server" => [
		"ip" => '127.0.0.1',
		"port" => '8181',
	],
]);

function send(string $data)
{
	$ip = CONFIG["server"]["ip"];
	$port = CONFIG["server"]["port"];
	$socket = "tcp://{$ip}:{$port}";
	$error = [
		"number" => 0, 
		"string" => '',
	];
	$return = stream_socket_client($socket, $error["number"], $error["string"]);
	if(!is_resource($return)) {//{{{//
		tirigger_error("Stream: {$error['string']}", E_USER_WARNING);
		if (defined('DEBUG') && DEBUG) var_dump($socket);
		trigger_error("The 'stream_socket_client' returned an error", E_USER_WARNING);
		return(false);
	}//}}}//
	$socket = $return;


	$return = fwrite($socket, $data);
	if(!is_int($return)) {//{{{//
		if (defined('DEBUG') && DEBUG) var_dump($socket, $data);
		trigger_error("The 'fwrite' returned an error", E_USER_WARNING);
		return(false);	
	}//}}}//
	
	fclose($socket);
	
	return(true);
}

send("XA!");

