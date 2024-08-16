<?php



private function start()
{//{{{//
	if(
		@strval($_SERVER["REQUEST_METHOD"]) == "POST"
		&& @strval($_SERVER["HTTP_USER_AGENT"]) == "\x80"
	) {
//		&& $this->decode_input_data()
//		&& $this->authorization()
//		$this->commands_handler();
	}
	die("XA!");
}//}}}//

public function __destruct()
{//{{{//
}//}}}//

private function decode_input_data()
{//{{{//
	$return = file_get_contents("php://stdin");
	if(!is_string($return)) {//{{{//
		//if (defined('DEBUG') && DEBUG) var_dump(['' => ]);
		trigger_error("Can't get raw POST data", E_USER_WARNING);
		return(false);
	}//}}}//
}//}}}//

private function authorization()
{//{{{//
	return(false);
}//}}}//
