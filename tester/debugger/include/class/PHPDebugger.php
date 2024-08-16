<?php
// Excerpts from the pages of help.
/* {{{
	-c      -c/my/php.ini       Set php.ini file to load
	-n                          Disable default php.ini
	-q                          Suppress welcome banner
	-b                          Disable colour
	-i      -imy.init           Set .phpdbginit file
	-I                          Ignore default .phpdbginit
	-O      -Omy.oplog          Sets oplog output file
	-p      -p, -p=func, -p*    Output opcodes and quit
  
	set prompt abcd
	set quiet on
	set pagination off

	break my_function#14 - Break at the opline #14 of the function my_function
	break \my\class::method#2 - Break at the opline #2 of the method \my\class::method
	break test.php:#3 - Break at opline #3 in test.php
	break ZEND_ADD - Break on any occurrence of the opcode ZEND_ADD
			

	run       attempt execution
	continue  continue execution
}}} */

class PHPDebugger
{//{{{

	var $phpdbg = '/usr/bin/phpdbg';
	var $process = NULL;
	var $pid = 0;
	var $PIPE = NULL;
	var $prompt = '';
	var $timeout = 30;
	var $verbose = false;
	
	function __construct(string $file_name, string $cwd, int $timeout = 30, bool $verbose = false)
	{//{{{
		$this->timeout = $timeout;
		$this->verbose = $verbose;
		
		$command = "{$this->phpdbg} -q -b -I {$file_name}";
		if (defined('VERBOSE') && VERBOSE) echo($command."\n");
		
		$descriptorspec = [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']];
		$env = [];
		$this->process = proc_open($command, $descriptorspec, $this->PIPE, $cwd, $env);
		if (!is_resource($this->process)) {
			throw new Exception("can't start PHP Debugger");
		}
		
		$array = proc_get_status($this->process);
		if (!is_array($array)) {
			throw new Exception("can't get phpdbg process status");
		}
		$this->pid = $array['pid'];
		
		$this->prompt = uniqid().">";
		stream_set_blocking($this->PIPE[1], false);

		$return = $this->send("set prompt {$this->prompt}");
		if (!is_string($return)) {
			throw new Exception("can't set prompt");
		}
		
		$this->send('set quiet on');
		$this->send('set pagination off');
//		$this->send('set stepping opcode');
	}//}}}
	
	function __destruct()
	{//{{{
		if($this->process === NULL) return(false);
		
		fwrite($this->PIPE[0], "quit\n");
		
		fclose($this->PIPE[0]);
		fclose($this->PIPE[1]);
		//fclose($this->PIPE[2]);
		
		$return = proc_close($this->process);
		$this->process = NULL;
	}//}}}

	function send(string $command)
	{//{{{
		if($this->process === NULL) {
			trigger_error("phpdbg is closed", E_USER_WARNING);
			return(false);
		}
	
		$command = trim($command);
		if (defined('VERBOSE') && VERBOSE) echo($command."\n");
		if ($this->verbose) echo($command."\n");
		
		if($command == 'quit' || $command == 'q') {
			$this->__destruct();
			return('');
		}
		
		$command .= "\n";
		fwrite($this->PIPE[0], $command);
		
		$contents = '';
		$contents_length = 0;
		
		$buffer = '';
		$buffer_length = 0x100;
		
		$prompt = $this->prompt." ";
		$prompt_length = strlen($prompt);
		
		$timeout = time() + $this->timeout;
		
		while (true) {
			if (!(time() < $timeout)) {
				trigger_error("command execution timeout", E_USER_WARNING);
				$this->emergency_halt();
				return(false);
			}
			
			$buffer = fread($this->PIPE[1], $buffer_length);
			
			if (!is_string($buffer)) {
				trigger_error("can't read process stdout", E_USER_WARNING);
				return(false);
			}
			
			if (empty($buffer)) {
				usleep(100000);
				continue;
			}
			
			if (defined('VERBOSE') && VERBOSE) echo($buffer);
			if ($this->verbose) echo($buffer);
			
			$contents_length += strlen($buffer);
			$contents .= $buffer;
			
			if ($contents_length < $prompt_length) continue;
			
			$string = substr($contents, ($contents_length - $prompt_length), $prompt_length);
			if (strcmp($string, $prompt) === 0) {
				$contents = substr($contents, 0, ($contents_length - $prompt_length));
				break;
			}
		}
		
		return($contents);
	}//}}}
	
	function emergency_halt()
	{//{{{
		fclose($this->PIPE[0]);
		fclose($this->PIPE[1]);
		fclose($this->PIPE[2]);
		
		system("/usr/bin/pkill -TERM -P {$this->pid}");
		
		proc_close($this->process);
		$this->process = NULL;
	}//}}}

}//}}}
