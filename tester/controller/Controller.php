<?php

class Controller
{
	var $key = "";
	var $url = "";
	var $user_agent = "\x80";
	var $server_process = NULL;
	var $server_pipes = [];

	function __construct(string $key, string $url)
	{//{{{//
		$this->key = $key;
		$this->url = $url;
	}//}}}///

	function send(array $post_data, array &$server_data, array &$http_response)
	{//{{{//
		$data = $post_data;
		$data["key"] = $this->key;
		
		$r = $this->encode($data);
		if(!$r) return w("Can't encode controller post data");
		
		$r = $this->server_create();
		if(!$r) return w("Can't create controller tcp server");
		
		$response = [];
		$r = $this->http_post($url, $data, $response);
		if(!$r) return w("Can't http post controller data");
		
		$data = '';
		$r = $this->sever_data_get($data);
		if(!$r) return w("Can't get controller tcp server data");
		
		$r = $this->decode($data);
		if(!$r) return w("Can't decode controller server data");
		
		$key = @strval($data["key"]);
		$r = $this->compare($this->key, $key);
		if(!$r) return w("Incorrect server data key");
		
		$http_response = $response;
		$server_data = $data["result"];
		return(true);
	}//}}}//

	function encode(&$data)
	{//{{{//
		$return = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if(!is_string($return)) {//{{{//
			$error_msg = json_last_error_msg();
			trigger_error("JSON {$error_msg}", E_USER_WARNING);
			if (defined('DEBUG') && DEBUG) var_dump($data);
			trigger_error("The 'json_encode' returned an error", E_USER_WARNING);
			return(false);
		}//}}}//
		$data = $return;
		
		return(true);
	}//}}}//

	function server_create()
	{//{{{//
		$command = __DIR__.'/server';
		$descriptorspec = [
			['pipe', 'r'],
			['pipe', 'w'],
			['pipe', 'w'],
		];
		$pipes = &$this->server_pipes;
		$cwd = __DIR__;
		$env = NULL;
		$return = proc_open($command, $descriptorspec, $pipes, $cwd, $env);
		if(!is_resource($return)) {//{{{//
			trigger_error("The 'proc_open' returned an error", E_USER_WARNING);
			return(false);
		}//}}}//
		$this->server_process = $return;
		
		return(true);
	}//}}}//

	function server_destroy()
	{//{{{//
		$process = &$this->server_process;
		$stdout = &$this->server_pipes[1];
		$stderr = &$this->server_pipes[2];
	
		$cd = 50;
		while(true) {
			$return = proc_get_status($process);
			if(!is_array($return)) {//{{{//
				trigger_error("The 'proc_get_status' returned an error", E_USER_WARNING);
				return(false);
			}//}}}//
			$status = $return;
		
			if(!$status["running"]) break;
			
			usleep(100000);
			$cd--;
			if($cd == 0) {
				proc_terminate($process, 9);
				$cd = 50;
			}
		}
		
		if($status["exitcode"] !== 0) {
			$err = '';
			while(true) {
				$return = feof($stderr);
				if($return) break;
				
				$return = fread($stderr, 2048);
				if(!is_string($return)) {//{{{//
					trigger_error("The 'fread' returned an error", E_USER_WARNING);
					return(false);
				}//}}}//
				$err .= $return;
			}
			trigger_error("Server: {$err}", E_USER_WARNING);
			trigger_error("The 'server' returned an error", E_USER_WARNING);
			$result = false;
		}
		else {
			$out = '';
			while(true) {
				$return = feof($stdout);
				if($return) break;
				
				$return = fread($stdout, 2048);
				if(!is_string($return)) {//{{{//
					trigger_error("The 'fread' returned an error", E_USER_WARNING);
					return(false);
				}//}}}//
				$out .= $return;
			}
			$result = $out;
		}
		
		fclose($this->server_pipes[0]);
		fclose($stdout);
		fclose($stderr);
		proc_close($process);
		
		return($result);
	}//}}}//
	

}

