<?php

/// Development messages initialization

if(true) // DEBUG, VERBOSE, QUIET
{//{{{
	if(isset($_GET["debug"])) {
		define('DEBUG', true);
	}
	if(isset($_GET["verbose"])) {
		define('VERBOSE', true);
	}
	if(isset($_GET["quiet"])) {
		define('QUIET', true);
	}

	if(defined('QUIET') && QUIET === true) {
		ini_set('error_reporting', 0);
		ini_set('display_errors', '0');
	}
	else {
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', '1');
		ini_set('html_errors', '0');
	}
}//}}}

if(true) // DEFAULT_HTML
{//{{{
	$string = 
////////////////////////////////////////////////////////////////////////////////
<<<'HEREDOC'
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>
	<body>
<pre>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////

	define('DEFAULT_HTML', $string);
	unset($string);

	ob_start(function($buffer) {
		$buffer_len = strlen($buffer);
		$default_html_len = strlen(DEFAULT_HTML);
		
		$default_html = '';
		if($buffer_len >= $default_html_len) {
			$default_html = substr($buffer, 0, $default_html_len);
		}
		
		if(strcmp(DEFAULT_HTML, $default_html) === 0) {
			$substr = substr($buffer, $default_html_len);
			$buffer = DEFAULT_HTML.htmlentities($substr);
			return($buffer);
		}
		else {
			$buffer = htmlentities($buffer);
			return($buffer);
		}
	});

	echo(DEFAULT_HTML);
}//}}}

/// Tool for generate standard markup

class HTML
{//{{{
	static $head =
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}
	static $title = "";
	static $styles = [];
	static $style = "";
	static $body = "";
	static $scripts = [];
	static $script = "";
	
	function __construct($path_to_font = NULL, $path_to_favicon = NULL)
	{//{{{
		if(!is_null($path_to_font) && is_string($path_to_font)) {
			$return = file_get_contents($path_to_font);
			if(!is_string($return)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$path_to_font' => $path_to_font]);
				throw new Exception("Can't get contents from font file");
			}
			else {
				$base64_font = base64_encode($return);
				self::$style .= 
<<<HEREDOC
@font-face {
	font-family: 'monospace';
	src: url(data:font/truetype;base64,{$base64_font});
}
HEREDOC;
			}
		}
		if(!is_null($path_to_favicon) && is_string($path_to_favicon)) {
			$return = file_get_contents($path_to_favicon);
			if(!is_string($return)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$path_to_favicon' => $path_to_favicon]);
				throw new Exception("Can't get contents from favicon file");
			}
			else {
				$base64_favicon = base64_encode($return);
				self::$head .= 
<<<HEREDOC
<link rel="icon" href="data:image/x-icon;base64,{$base64_favicon}">
HEREDOC;
			}
		}
	}//}}}
	
	function __destruct()
	{//{{{
		$ob_level = ob_get_level();
		if($ob_level > 0) {
			$ob = ob_get_contents();
			ob_end_clean();
		
			if(!( defined('QUIET') && QUIET === true )) {
				$buffer = &$ob;
				
				$buffer_len = strlen($buffer);
				$default_html_len = strlen(DEFAULT_HTML);
				
				$default_html = '';
				if($buffer_len >= $default_html_len) {
					$default_html = substr($buffer, 0, $default_html_len);
				}
				
				if(strcmp(DEFAULT_HTML, $default_html) === 0) {
					$substr = substr($buffer, $default_html_len);
					$buffer = $substr;
				}
	
				if(!empty($ob)) {
					$ob = strip_tags($ob);
					
					$body = '<dialog name="output"><pre>'. $ob .'</pre></dialog>';
					HTML::$body = $body.HTML::$body;
				
					$script = 
////////////////////////////////////////////////////////////////////////////////
<<<'HEREDOC'
window.addEventListener("load", function(event) {
	let dialog = document.querySelector("dialog[name='output']");
	dialog.showModal();
});

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
					HTML::$script = $script.HTML::$script;
				}
			}
		}
		$html = HTML::generate();
		echo($html);
	}//}}}
	
	static function generate_font_face(string $path_to_font) // string
	{//{{{
		$contents = file_get_contents($path_to_font);
		if(!is_string($contents)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path_to_font' => $path_to_font]);
			trigger_error("Can't get contents from font file", E_USER_WARNING);
			return(false);
		}
		$base64_font = base64_encode($contents);
		$style = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
@font-face {
	font-family: 'monospace';
	src: url(data:font/truetype;base64,{$base64_font});
}

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		return($style);
	}//}}}
	
	static function generate_font_link(string $path_to_favicon) // string
	{//{{{
		$contents = file_get_contents($path_to_favicon);
		if(!is_string($contents)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$path_to_favicon' => $path_to_favicon]);
			trigger_error("Can't get contents from favicon file", E_USER_WARNING);
			return(false);
		}
		$base64_favicon = base64_encode($contents);
		$head = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<link rel="icon" href="data:image/x-icon;base64,{$base64_favicon}">

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		return($head);
	}//}}}
	
	static function generate_stylesheets(array $styles) // string
	{//{{{
		$result = "";
		foreach($styles as $style) {
			if(!is_string($style)) continue;
			$result .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<link rel="stylesheet" href="{$style}" />

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		}
		return($result);
	}//}}}
	
	static function generate_scripts(array $scripts) // string
	{//{{{
		$result = "";
		foreach($scripts as $script) {
			if(!is_string($script)) continue;
			$result .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<script src="{$script}"></script>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		}
		return($result);
	}//}}}

	static function get_url_path() // string
	{//{{{
		if(!@is_string($_SERVER["REQUEST_URI"])) {
			if(defined('DEBUG') && DEBUG) @var_dump(['$_SERVER["REQUEST_URI"]' => $_SERVER["REQUEST_URI"]]);
			trigger_error('Incorrect $_SERVER["REQUEST_URI"]', E_USER_WARNING);
			return(false);
		}
		
		$url_path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
		if(!is_string($url_path)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$_SERVER["REQUEST_URI"]' => $_SERVER["REQUEST_URI"]]);
			trigger_error('Parse url failed from $_SERVER["REQUEST_URI"]', E_USER_WARNING);
			return(false);
		}
		
		return($url_path);
	}//}}}

	static function generate_csrf_input() // string
	{//{{{
		if(!( defined('CSRF_TOKEN') && is_string('CSRF_TOKEN') )) {
			trigger_error("Incorrect CSRF_TOKEN", E_USER_WARNING);
			return('');
		}
		
		$csrf_token = htmlentities(CSRF_TOKEN);
		$input = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<input name="csrf_token" value="{$csrf_token}" type="hidden" />

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		return($input);
	}//}}}

	static function generate()
	{//{{{
		$head = &self::$head;
		$title = &self::$title;
		$stylesheets = self::generate_stylesheets(self::$styles);
		$style = &self::$style;
		$body = &self::$body;
		$scripts = self::generate_scripts(self::$scripts);
		$script = &self::$script;
		$html = 
<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
{$head}
		<title>{$title}</title>
{$stylesheets}
		<style>
{$style}
		</style>
{$scripts}
		<script>
{$script}
		</script>
	</head>
	<body>
{$body}
	</body>
</html>
HEREDOC;
		return($html);
	}//}}}
	
}//}}}

/// Getting typed variables from an array

function array_get_bool(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	$result = boolval($array[$key]);
	return($result);
}//}}}

function array_get_int(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	$result = intval($array[$key]);
	return($result);
}//}}}

function array_get_float(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	$result = floatval($array[$key]);
	return($result);
}//}}}

function array_get_string(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	$result = strval($array[$key]);
	return($result);
}//}}}

function array_get_array(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	if(!is_array($array[$key])) {
		trigger_error("Value of array element with given key is not array", E_USER_WARNING);
		return(false);
	}
	
	return($array[$key]);
}//}}}

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

class Main
{//{{{
	function __construct()
	{//{{{
		$request_method = array_get_string('REQUEST_METHOD', $_SERVER);
		if(!is_string($request_method)) {
			trigger_error("Can't get http request method", E_USER_WARNING);
			return(false);
		}
		
		switch($request_method) {
			case('GET'):
				$return = $this->handle_get_request();
				if($return !== true) {
					trigger_error("Handle get request failed", E_USER_ERROR);
					exit(255);
				}
				
				$HTML = new HTML;
				exit(0);
				
			case('POST'):
				$return = $this->handle_post_request();
				if($return !== true) {
					trigger_error("Handle post request failed", E_USER_ERROR);
					exit(255);
				}
				exit(0);
				
			default:
				if(defined('DEBUG') && DEBUG) var_dump(['$request_method' => $request_method]);
				trigger_error("Unsupported request method", E_USER_ERROR);
				exit(255);
		}
	}//}}}
	
	function handle_get_request()
	{//{{{
		$page = @array_get_string('page', $_GET);
		if(!is_string($page)) {
			$page = '';
		}
		
		$Page = new Page();
		
		switch($page) {
			case(''):
				$return = $Page->index();
				if(!$return) {
					trigger_error("Can't create 'index' page", E_USER_WARNING);
					return(false);
				}
				return(true);
			
			case('empty'):
				$return = $Page->empty();
				if(!$return) {
					trigger_error("Can't create 'empty' page", E_USER_WARNING);
					return(false);
				}
				return(true);
				
				
			default:
				if(defined('DEBUG') && DEBUG) var_dump(['$page' => $page]);
				trigger_error("Unsupported 'page'", E_USER_WARNING);
				return(false);
		}
		return(false);
	}//}}}
	
	function handle_post_request()
	{//{{{
		$action = array_get_string('action', $_POST);
		if(!is_string($action)) {
			trigger_error("Can't get 'action' from POST request", E_USER_WARNING);
			return(false);
		}
	
		$Action = new Action();
		
		switch($action) {				
			case('phpdbg'):
				$return = $Action->phpdbg();
				if(!$return) {
					trigger_error("Can't perform phpdbg action", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('help'):
				$return = $Action->help();
				if(!$return) {
					trigger_error("Can't perform help action", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			default:
				if(defined('DEBUG') && DEBUG) var_dump(['$action' => $action]);
				trigger_error("Unsupported 'action'", E_USER_WARNING);
				return(false);
		}
		return(false);
	}//}}}
	
}//}}}

class Page
{//{{{

	var $styles = [
		'/share/style/grey.css'
	];
	var $style = '';
	
	var $scripts = [];
	var $script = '';
	
	static function required_in_iframe()
	{//{{{
		header("X-Frame-Options: SAMEORIGIN");
		
		$close_dialog_from_iframe = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
function windowOnKeyDown(event) 
{//{{{
	if(
		event.key == "Escape"
		&& event.altKey == false
		&& event.ctrlKey == false
		&& event.shiftKey == false
	) {
		if(window.parent.dialog.open) {
			event.preventDefault();
			window.parent.dialog.close();
		}
	}
	
}//}}}
window.addEventListener("keydown", windowOnKeyDown);

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}
		HTML::$script .= $close_dialog_from_iframe;
		
		return(true);
	}//}}}
	
	function __construct()
	{//{{{
		HTML::$styles = array_merge(HTML::$styles, $this->styles);
		HTML::$style .= $this->style;
		HTML::$scripts = array_merge(HTML::$scripts, $this->scripts);
		HTML::$script .= $this->script;
	}//}}}

	function index()
	{//{{{
		HTML::$style .= 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
body {
	padding: 0px;
}
input[name='file'] {
	width: calc(100% - 12px);
}
textarea[name='commands'] {
	position: absolute;
	width: calc(100% - 28px);
	height: calc(100% - 128px);
}
dialog {
	width: 100%;
	height: 100%;
	padding: 0px;
	margin: 20px;
	background: #000;
	border: solid 0px #000;
}
iframe {
	width: calc(100% - 4px);
	height: calc(100% - 8px);
	border: solid 2px #666;
	padding: 0px;
	margin: 0px;
}

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}
	
		HTML::$script .= 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
var dialog = null;
var form = null;

function windowOnKeyDown(event)
{//{{{
	if(event.key == "Escape" && event.altKey == false && event.ctrlKey == false && event.shiftKey == false) {
		if(!dialog.open) {
			event.preventDefault();
			dialog.showModal();
		}
	}	
	if(event.key == "Enter" && event.altKey == false && event.ctrlKey == true && event.shiftKey == false) {
		event.preventDefault();
		form.submit();
		dialog.showModal();
	}
}//}}}
window.addEventListener("keydown", windowOnKeyDown);

function windowOnLoad(event)
{//{{{
	dialog = document.querySelector("dialog");
	form = document.querySelector("form");
	form.addEventListener("submit", function(event) {
		dialog.showModal();
	});
}//}}}
window.addEventListener("load", windowOnLoad);

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

		$help = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
<pre>
</pre>
HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

		$csrf_input = HTML::generate_csrf_input();
		$url_path = htmlentities(HTML::get_url_path());
		
		$file = @array_get_string('file', $_GET);
		if(!is_string($file)) {
			$file = '';
		}
		$file = htmlentities($file);
		
		HTML::$body .= 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
<form action="{$url_path}" method="post" target="phpdbg">
	{$csrf_input}
	<input name="action" value="phpdbg" type="hidden" />
	
	<button name="action" value="phpdbg" type="submit">phpdbg</button>
	<button name="action" value="help" type="submit">help</button>
	<hr/>
	
	<input name="file" value="{$file}" type="text" /><br />
	<textarea name="commands">
break /var/www/html/index.php:2
run
#ev var_dump(\$variable);
#back
continue
quit
	</textarea><br />
</form>

<dialog><iframe name="phpdbg" src="{$url_path}?page=empty"></iframe></dialog>

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

		return(true);
	}//}}}

	function empty()
	{//{{{
		Page::required_in_iframe();
		return(true);
	}//}}}

}//}}}

class Action
{//{{{
	var $styles = [
		'/share/style/grey.css'
	];
	var $style = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

	var $scripts = [];
	var $script = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
function windowOnKeyDown(event) 
{//{{{
	if(
		event.key == "Escape"
		&& event.altKey == false
		&& event.ctrlKey == false
		&& event.shiftKey == false
	) {
		if(window.parent.phpdbg.dialog.open) {
			event.preventDefault();
			window.parent.phpdbg.dialog.close();
		}
	}
	
}//}}}
window.addEventListener("keydown", windowOnKeyDown);

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

	var $dblclick_regexp_selection = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
window.addEventListener("dblclick", function(event) // Double click selection with regular expression
{//{{{	
	let $string, $return;
	
	var $Selection = document.getSelection();
	if(!($Selection.type == "Range" && $Selection.rangeCount == 1)) {
		return(null);
	}

	var $Range = $Selection.getRangeAt(0);
	$Selection.removeRange($Range);
	
	var $RegExp = new RegExp("^[\-A-Za-z0-9~/\.&\$_:>]+$");
	
	while(true) {
		var $startOffset = $Range.startOffset - 1;
		if($startOffset >= 0) {
			$Range.setStart($Range.startContainer, $startOffset);
		}
		else {
			break;
		}
		
		$string = $Range.toString();
		$return = $RegExp.test($string);
		if($return == false) {
			$startOffset += 1;
			$Range.setStart($Range.startContainer, $startOffset);
			break;
		}
	}
	
	while(true) {
		$endOffset = $Range.endOffset + 1;
		if($endOffset < $Range.endContainer.length) {
			$Range.setEnd($Range.endContainer, $endOffset);
		}
		else {
			break;
		}
		
		$string = $Range.toString();
		$return = $RegExp.test($string);
		if($return == false) {
			$endOffset -= 1;
			$Range.setEnd($Range.endContainer, $endOffset);
			break;
		}
	}
	
	//console.log($Range.toString());
	$Selection.addRange($Range);
});//}}}

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

	function __construct()
	{//{{{
		HTML::$styles = array_merge(HTML::$styles, $this->styles);
		HTML::$style .= $this->style;
		HTML::$scripts = array_merge(HTML::$scripts, $this->scripts);
		HTML::$script .= $this->script;
	}//}}}

	function phpdbg()
	{//{{{
		Page::required_in_iframe();
		
		$php_file = array_get_string('file', $_POST);
		if(!is_string($php_file)) {
			trigger_error("Can't get path to php file from POST", E_USER_WARNING);
			return(false);
		}
		
		$commands = array_get_string('commands', $_POST);
		if(!is_string($commands)) {
			trigger_error("Can't get phpdbg commands from POST", E_USER_WARNING);
			return(false);
		}
		
		$working_directory = pathinfo($php_file, PATHINFO_DIRNAME);
		$PHPDebugger = new PHPDebugger($php_file, $working_directory);
		
		$output = '';
		$COMMAND = explode("\n", $commands);
		foreach($COMMAND as $command) {
			$command = trim($command);
			if(empty($command)) continue;
			if(preg_match('/^#.*$/', $command) == 1) continue;
			
			$return = $PHPDebugger->send($command);
			if(!is_string($return)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
				trigger_error("Can't send command to phpdbg", E_USER_WARNING);
				return(false);
			}
			
			$command = htmlentities($command);
			$output .= ">>> {$command}\n{$return}";
		}
		
		$output = htmlentities($output);
		
		$HTML = new HTML();
		HTML::$script .= $this->dblclick_regexp_selection;
		HTML::$body .= "<pre>{$output}</pre>";
		
		return(true);
	}//}}}

	function help()
	{//{{{
		Page::required_in_iframe();
		
		$style = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
pre {
	text-wrap: wrap;
}
b {
	cursor: pointer;
}
a { 
	text-decoration: none;
	color: #37F;
}
HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}
		HTML::$style .= $style;
/*		

		$HELP[""] = ['',
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
HEREDOC];
/////////////////////////////////////////////////////////////////////////////}}}
*/

		$HELP["back"] = ['back - shows the current backtrace',
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
Command: back  Alias: t  show trace

Provide a formatted backtrace using the standard debug_backtrace() functionality.  An optional unsigned integer argument specifying the maximum number of frames to be traced; if omitted then a complete backtrace is given.

Examples

    prompt>  back 5
    prompt>  t 
 
A backtrace can be executed at any time during execution.
HEREDOC];
/////////////////////////////////////////////////////////////////////////////}}}

		$HELP["run"] = ['run - attempt execution',
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
Command: run  Alias: r  attempt execution

Enter the vm, starting execution. Execution will then continue until the next breakpoint or completion of the script. Add parameters you want to use as $argv. Add a trailing < filename for reading STDIN from a file.

Examples

    prompt>  run
    prompt>  r
    Will cause execution of the context, if it is set

    prompt>  r test < foo.txt
    Will execute with $argv[1] == "test" and read from the foo.txt file for STDIN

Note that the execution context must be set. If not previously compiled, then the script will be compiled before execution.
HEREDOC];
/////////////////////////////////////////////////////////////////////////////}}}

		$HELP["step"] = ['step - continue execution until other line is reached',
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
Command: step  Alias: s  step through execution

Execute opcodes until next line

Examples

    prompt>  s
    Will continue and break again in the next encountered line
HEREDOC];
/////////////////////////////////////////////////////////////////////////////}}}

		$HELP["continue"] = ['continue - continue execution',
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
Command: continue  Alias: c  continue execution

Continue with execution after hitting a break or watchpoint

Examples

    prompt>  continue
    prompt>  c
    Continue executing until the next break or watchpoint

Note continue will trigger a "not running" error if not executing.
HEREDOC];
/////////////////////////////////////////////////////////////////////////////}}}

		$HELP["break"] = ['break - set a breakpoint at the specified target',
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
Command: break  Alias: b  set breakpoint

Breakpoints can be set at a range of targets within the execution environment.  Execution will be paused if the program flow hits a breakpoint.  The break target can be one of the following types:

  Target   Alias Purpose
  at       @     specify breakpoint by location and condition
  del      ~     delete breakpoint by breakpoint identifier number

Break at takes two arguments. The first is any valid target. The second is a valid PHP expression which will trigger the break in execution, if evaluated as true in a boolean context at the specified target.

Note that breakpoints can also be disabled and re-enabled by the set break command.

Examples

    prompt>  break test.php:100
    prompt>  b test.php:100
    Break execution at line 100 of test.php

    prompt>  break 200
    prompt>  b 200
    Break execution at line 200 of the currently PHP script file

    prompt>  break \mynamespace\my_function
    prompt>  b \mynamespace\my_function
    Break execution on entry to \mynamespace\my_function

    prompt>  break classX::method
    prompt>  b classX::method
    Break execution on entry to classX::method

    prompt>  break 0x7ff68f570e08
    prompt>  b 0x7ff68f570e08
    Break at the opline at the address 0x7ff68f570e08

    prompt>  break my_function#14
    prompt>  b my_function#14
    Break at the opline #14 of the function my_function

    prompt>  break \my\class::method#2
    prompt>  b \my\class::method#2
    Break at the opline #2 of the method \my\class::method

    prompt>  break test.php:#3
    prompt>  b test.php:#3
    Break at opline #3 in test.php

    prompt>  break if $cnt > 10
    prompt>  b if $cnt > 10
    Break when the condition ($cnt > 10) evaluates to true

    prompt>  break at phpdbg::isGreat if $opt == 'S'
    prompt>  break @ phpdbg::isGreat if $opt == 'S'
    Break at any opcode in phpdbg::isGreat when the condition ($opt == 'S') is true

    prompt>  break at test.php:20 if !isset($x)
    Break at every opcode on line 20 of test.php when the condition evaluates to true

    prompt>  break ZEND_ADD
    prompt>  b ZEND_ADD
    Break on any occurrence of the opcode ZEND_ADD

    prompt>  break del 2
    prompt>  b ~ 2
    Remove breakpoint 2

Note: Conditional breaks are costly in terms of runtime overhead. Use them only when required as they significantly slow execution.

Note: An address is only valid for the current compilation.

HEREDOC];
/////////////////////////////////////////////////////////////////////////////}}}

		$HELP["set"] = ['set - set the phpdbg configuration',
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
Command: set  Alias: S  set phpdbg configuration

The set command is used to configure how phpdbg looks and behaves.  Specific set commands are as follows:

   Type    Alias    Purpose
   prompt     p     set the prompt
   color      c     set color  <element> <color>
   colors     C     set colors [<on|off>]
   break      b     set break id <on|off>
   breaks     B     set breaks [<on|off>]
   quiet      q     set quiet [<on|off>]
   stepping   s     set stepping [<opcode|line>]
   refcount   r     set refcount [<on|off>]

Valid colors are none, white, red, green, yellow, blue, purple, cyan and black.  All colours except none can be followed by an optional -bold or -underline qualifier.

Color elements can be one of prompt, notice, or error.

Examples

     prompt>  S C on
     Set colors on

     prompt>  set p >
     prompt>  set color prompt white-bold
     Set the prompt to a bold >

     prompt>  S c error red-bold
     Use red bold for errors

     prompt>  S refcount on
     Enable refcount display when hitting watchpoints

     prompt>  S b 4 off
     Temporarily disable breakpoint 4.  This can be subsequently re-enabled by a S b 4 on.
HEREDOC];
/////////////////////////////////////////////////////////////////////////////}}}

		$HELP["ev"] = ['ev - evaluate some code',
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
Command: ev  Alias: 

The ev command takes a string expression which it evaluates and then displays. It evaluates in the context of the lowest (that is the executing) frame, unless this has first been explicitly changed by issuing a frame command. 

Examples

    prompt>  ev $variable
    Will print_r($variable) on the console, if it is defined

    prompt>  ev $variable = "Hello phpdbg :)"
    Will set $variable in the current scope

Note that ev allows any valid PHP expression including assignments, function calls and other write statements. This enables you to change the environment during execution, so care is needed here.  You can even call PHP functions which have breakpoints defined. 

Note: ev will always show the result, so do not prefix the code with return
HEREDOC];
/////////////////////////////////////////////////////////////////////////////}}}

		$HELP["quit"] = ['quit - exit phpdbg',
/////////////////////////////////////////////////////////////////////////////{{{
<<<'HEREDOC'
Command: quit  Alias: q  exit phpdbg
HEREDOC];
/////////////////////////////////////////////////////////////////////////////}}}

		$content = '';
		$page = '';
		foreach($HELP as $key => $array) {
		
			$command = htmlentities($key);
			$title = htmlentities($array[0]);
			$text = htmlentities($array[1]);
			
			$content .= '<a href="#'.$command.'">'.$title.'</a><br>'."\n";
			$page .= '<hr /><pre id="'.$command.'">'.$text.'</pre>'."\n";
		}
		HTML::$body .= $content.$page;
		
		$HTML = new HTML;
		return(true);
	}//}}}

}//}}}

$Main = new Main();

