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

if(true) // grey style
{//{{{
	$style = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
* { 
	font-family: 'monospace'; font-size: 20px; 
	line-height: 20px; caret-color: #FFF;
	text-underline-offset: 7px;
	text-decoration-thickness: 1px;
}
body { background-color: #000; color: #AAA; }

::-webkit-scrollbar { height: 10px; width: 10px; background: #000; } 
::-webkit-scrollbar-thumb { background: #35363a; } 
::-webkit-scrollbar-corner { background: #000; }

a { color: #37F; } a:visited { color: #A00; }

b { font-weight: normal; color: #EEE; }
i { font-style: normal; color: #37F; }
s { text-decoration: none; color: #555; }
u { text-decoration: none; color: #A00; }

hr { height: 1px; border: none; background: #444; }

h1 { font-weight: normal; font-size: 80px; line-height: 80px; color: #EEE; }
h2 { font-weight: normal; font-size: 60px; line-height: 60px; color: #EEE; }
h3 { font-weight: normal; font-size: 40px; line-height: 40px; color: #EEE; }
h4 { font-weight: normal; font-size: 20px; line-height: 20px; color: #EEE; }

/* fieldset {{{ */
fieldset 
	{ 
	border: solid 1px #666; 
	border-left: solid 2px #666;
	border-right: solid 2px #666;
	border-radius: 3px; 
	width: min-content;
	background: #111;
	}
legend {
	border: solid 1px #888;
	border-left: solid 2px #888;
	border-right: solid 2px #888;
	background: #000;
	color: #666;
	border-radius: 2px;
	padding-left: 24px;
	padding-right: 24px;
	}
/* }}} fieldset */

/* selection {{{ */
::selection 
	{
	background: #666;
	color: #DDD;
	}
input[type='text']:focus
,textarea:focus
,input[type='file']:focus
,input[type='checkbox']:focus
,input[type='radio']:focus
,select:focus
,button:focus 
	{
	outline: solid 1px #37F;
	}
/* }}} selection */

/* inputs {{{ */
input[type='text']
,textarea
,input[type='file']
,select
	{	
	border: solid 1px #444;
	border-left: solid 2px #444;
	border-right: solid 2px #444;
	background: #000;
	color: #AAA;
	border-radius: 2px;
	padding-left: 4px;
	padding-right: 4px;
	margin-top: 4px;
	margin-bottom: 4px;
	}
input[type='text']:focus
,textarea:focus
,input[type='file']:focus
,select:focus
	{
	border: solid 1px #000;
	border-left: solid 2px #000;
	border-right: solid 2px #000;
	}
input[type='text']
,textarea
,input[type='file']
	{
	width: 640px;
	}
textarea 
	{
	resize: none;
	height: 80px;
	}
/* }}} inputs */

/* buttons {{{ */
button 
,input::file-selector-button	
	{
	border: solid 1px #444;
	border-radius: 1px;
	background: #333;
	color: #AAA;
	height: 32px;
	margin-top: 4px;
	margin-bottom: 4px;
	}
button:focus 
	{
	border: solid 1px #000;
	}
button:active 
	{
	background: #666;
	color: #EEE;
	}
input::file-selector-button 
	{
	font-family: 'monospace';
	font-size: 20px;
	margin-right: 8px;
	}
/* }}} buttons */

/* appearance = none {{{ */
input[type='checkbox']
,input[type='radio']
,select
	{
	appearance: none;
	}
input[type='checkbox']
,input[type='radio'] 
	{
	position: relative;
	width: 20px;
	height: 20px;
	border: solid 2px #444;
	border-radius: 2px;
	background: #000;
}
input[type='checkbox']:checked,
input[type='radio']:checked {
	background: #888;
}
input[type='checkbox']
	{
	top: 7px;
	}
input[type='radio'] 
	{
	top: 4px;
	}
/* }}} appearance = none */

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
	HTML::$style .= $style;
}//}}}

/// Mysqli wrapper with advanced functionality

class DB
{//{{{
	static $mysqli = NULL;
	
	static function open(string $host, string $user, string $password, string $database) // mysqli object
	{//{{{
		$mysqli = &DB::$mysqli;

		try {
			$mysqli = @new mysqli($host, $user, $password, $database);
			$errno = mysqli_connect_errno();
			if($errno !== 0) {
				trigger_error("Can't connect to database because: {$mysqli->connect_error}", E_USER_WARNING);
				return(false);
			}
		} 
		catch(Exception $Exception) {
			if(defined('DEBUG') && DEBUG) var_dump([
				'$host' => $host
				,'$user' => $user
				,'$password' => $password
				,'$database' => $database
			]);
			$message = $Exception->getMessage();
			trigger_error("Can't connect to database because: {$message}", E_USER_WARNING);
			return(false);
		}

		try {
			$return = $mysqli->set_charset("utf8");
			$errno = mysqli_connect_errno();
			if($errno !== 0) {
				trigger_error("Can't set database client character because: {$mysqli->connect_error}", E_USER_WARNING);
				return(false);
			}
		}
		catch(Exception $Exception) {
			$message = $Exception->getMessage();
			trigger_error("Can't set database client character because: {$message}", E_USER_WARNING);
			return(false);
		}

		$mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, TRUE);

		register_shutdown_function(function () {
			if(is_object(DB::$mysqli) && isset(DB::$mysqli->server_info)) {
				$return = DB::$mysqli->close();
				if(!$return) {
					trigger_error("Can't close database connection", E_USER_WARNING);
					return(false);
				}
				return(true);
			}
			return(NULL);
		} );
		
		return($mysqli);
	}//}}}
	
	function __construct()
	{//{{{
		$mysqli = &DB::$mysqli;
		if (!is_object($mysqli)) {
			throw new Exception("Connection to database is not open");
		}
		return(NULL);
	}//}}}

	function query(string $sql) // true, array
	{//{{{
		$mysqli = &DB::$mysqli;
		
		$mysqli_result = $mysqli->query($sql);
		if ($mysqli_result === false) {
			trigger_error($mysqli->error, E_USER_WARNING);
			return(false);
		}
		
		if($mysqli_result === true) {
			return(true);
		}
		
		$result = [];
		while(true) {
			$row = $mysqli_result->fetch_assoc();
			if($row === NULL) break;
			array_push($result, $row);
		}
		$mysqli_result->free();
		
		return($result);
	}//}}}

	function id() // int
	{//{{{
		$id = DB::$mysqli->insert_id;
		return($id);
	}//}}}

	function queries(string $sql) // true
	{//{{{
		$mysqli = &DB::$mysqli;
		
		$mysqli_result = $mysqli->multi_query($sql);
		if ($mysqli_result === false) {
			trigger_error($mysqli->error, E_USER_WARNING);
			return(false);
		}
		
		while($mysqli->more_results()) {
			$mysqli_result = $mysqli->next_result();
			if ($mysqli_result === false) {
				trigger_error($mysqli->error, E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
	}//}}}
	
	function escape(string $variable) // string
	{//{{{
		$string = DB::$mysqli->real_escape_string($variable);
		return($string);
	}//}}}

	function name_escape(string $name) // string
	{//{{{
		$name = addcslashes($name, "`\\");
		return($name);
	}//}}}
	
	function int($variable) // string
	{//{{{
		$number = intval($variable, 10);
		$number = strval($number);
		return($number);
	}//}}}

	function float($variable) // string
	{//{{{
		$number = floatval($variable);
		$number = strval($number);
		return($number);
	}//}}}

	function encode($variable) // escaped string
	{//{{{
		$json = json_encode($variable, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		if(!is_string($json)) {
			$error_msg = json_last_error_msg();
			trigger_error("JSON {$error_msg}", E_USER_WARNING);
			return(false);
		}
		
		$string = $this->escape($json);
		return($string);
	}//}}}

	function decode(string $json)
	{//{{{
		$variable = json_decode($json, true);
		$error = json_last_error();
		if($variable === NULL && $error !== JSON_ERROR_NONE) {
			$error_msg = json_last_error_msg();
			trigger_error("JSON {$error_msg}", E_USER_WARNING);
			return(false);
		}
		
		return($variable);
	}//}}}
	
}//}}}

/// Checking the configuration and connecting to the database

if(true) // DB::open
{//{{{
	if(!defined('CONFIG')) {
		trigger_error("Constant 'CONFIG' is not defined", E_USER_ERROR);
		exit(255);
	}

	if(!(
		@is_string(CONFIG["database"]["host"])
		&& @is_string(CONFIG["database"]["user"])
		&& @is_string(CONFIG["database"]["password"])
		&& @is_string(CONFIG["database"]["database"])
	)) {
		if(defined('DEBUG') && DEBUG) @var_dump(['CONFIG["database"]' => CONFIG["database"]]);
		trigger_error('Incorrect CONFIG["database"]', E_USER_ERROR);
		exit(255);
	}
	
	$mysqli = DB::open(
		CONFIG["database"]["host"]
		, CONFIG["database"]["user"]
		, CONFIG["database"]["password"]
		, CONFIG["database"]["database"]
	);
	if(!is_object($mysqli)) {
		if(defined('DEBUG') && DEBUG) var_dump(['CONFIG["database"]' => CONFIG["database"]]);
		trigger_error("Can't open connection to database", E_USER_ERROR);
		exit(255);
	}
}//}}}

/// Getting typed variables from an array

function array_get_string(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	$result = strval($array[$key]);
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

/// preg search part

class Page
{//{{{
	var $styles = [];
	var $scripts = [];
	
	var $viewer_path = '/viewer.php';
	
	function __construct()
	{//{{{
		HTML::$styles = $this->styles;
		HTML::$scripts = $this->scripts;
		HTML::$style .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
div[name='centering_container'] {
	position: absolute;
	left: 0px;
	top: 0px;
	height: 100%;
	width: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
}
HEREDOC;
////////////////////////////////////////////////////////////////////////////////

		if(true) // get $favicon, $terminus
		{//{{{
			// favicon.ico : 264
			// terminus.ttf : 17980
			$contents = file_get_contents(__FILE__);
			$offset = __COMPILER_HALT_OFFSET__;
			$favicon = substr($contents, $offset, 264);
			$terminus = substr($contents, ($offset+264), 17980);
			unset($contents);
		
			HTML::$head .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<link rel="icon" href="data:image/x-icon;base64,{$favicon}">

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		
			$style = HTML::$style;
			HTML::$style = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
@font-face {
	font-family: 'monospace';
	src: url(data:font/truetype;base64,{$terminus});
}
{$style}
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		}//}}}
		
		HTML::$script .= 
////////////////////////////////////////////////////////////////////////////////
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
////////////////////////////////////////////////////////////////////////////////
	}//}}}

	function index()
	{//{{{
		$csrf_input = HTML::generate_csrf_input();
		$url_path = htmlentities(HTML::get_url_path());
		$form = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<form action="{$url_path}" method="get">
	<button name="page" value="install" type="submit">Install database</button>
	<button name="page" value="configure" type="submit">Configure search</button>
	<button name="page" value="search" type="submit">Start searching</button>
	<button name="page" value="edit" type="submit">Edit results</button>
</form>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////

		HTML::$body .= $form;
		
		return(true);
	}//}}}

	function install()
	{//{{{
		$return = Data::create_tables();
		if(!$return) {
			trigger_error("Can't create tables in database", E_USER_WARNING);
			return(false);
		}
		
		$url_path = htmlentities(HTML::get_url_path());
		$body = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
Tables have been created in the database. <a href="{$url_path}">Go to index page</a>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		
		HTML::$body .= $body;
		return(true);
	}//}}}

	function configure()
	{//{{{
		$default_query = Data::get_default_query();
		if(!is_array($default_query)) {
			trigger_error("Can't get default query", E_USER_WARNING);
			return(false);
		}
		
		$_ = [];
		$_["search_path"] = htmlentities($default_query["search_path"]);
		$_["file_pattern"] = htmlentities($default_query["file_pattern"]);
		$_["line_pattern"] = htmlentities($default_query["line_pattern"]);
		
		$csrf_input = HTML::generate_csrf_input();
		$url_path = htmlentities(HTML::get_url_path());
		$form = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<form action="{$url_path}" method="get">
	<button name="page" value="install" type="submit">Install database</button>
	<button name="page" value="search" type="submit">Start searching</button>
	<button name="page" value="edit" type="submit">Edit results</button>
</form>
<hr />
<fieldset>
	<legend>Default query</legend>
	<form action="{$url_path}" method="post">
		{$csrf_input}
		<label>
			Search path
			<input name="search_path" value="{$_["search_path"]}" type="text" autofocus/>
		</label>
		<label>
			File pattern
			<input name="file_pattern" value="{$_["file_pattern"]}" type="text" />
		</label>
		<label>
			Line pattern
			<input name="line_pattern" value="{$_["line_pattern"]}" type="text" />
		</label>
		<button name="action" value="save_default_query" type="submit">Save</button>
	</form>
</fieldset>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////

		HTML::$body .= $form;
		return(true);
	}//}}}

	function search()
	{//{{{
		$default_query = Data::get_default_query();
		if(!is_array($default_query)) {
			trigger_error("Can't get default query", E_USER_WARNING);
			return(false);
		}
		
		$_ = [];
		$_["search_path"] = htmlentities($default_query["search_path"]);
		$_["file_pattern"] = htmlentities($default_query["file_pattern"]);
		$_["line_pattern"] = htmlentities($default_query["line_pattern"]);
		
		$csrf_input = HTML::generate_csrf_input();
		$url_path = htmlentities(HTML::get_url_path());
		$form = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<form action="{$url_path}" method="get">
	<button name="page" value="install" type="submit">Install database</button>
	<button name="page" value="configure" type="submit">Configure search</button>
	<button name="page" value="edit" type="submit">Edit results</button>
</form>
<hr />
<fieldset>
	<legend>PCRE Search</legend>
	<form action="{$url_path}" method="post">
		{$csrf_input}
		<label>
			Search path
			<input name="search_path" value="{$_["search_path"]}" type="text" autofocus/>
		</label>
		<label>
			File pattern
			<input name="file_pattern" value="{$_["file_pattern"]}" type="text" />
		</label>
		<label>
			Line pattern
			<input name="line_pattern" value="{$_["line_pattern"]}" type="text" />
		</label>
		<button name="action" value="preg_search" type="submit">Search</button>
	</form>
</fieldset>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////

		HTML::$body .= $form;
		return(true);
	}//}}}
	
	function result()
	{//{{{
		$query_id = array_get_int("id", $_GET);
		if(!is_int($query_id)) {
			trigger_error("Can't get 'id' from GET", E_USER_WARNING);
			return(false);
		}
	
		$results = Data::get_results($query_id);
		if(!is_array($results)) {
			trigger_error("Can't get results", E_USER_WARNING);
			return(false);
		}
		
		$table = '<table name="results">';
		foreach($results as $key => $result) {
			$index = $key+1;
			
			$_ = [];
			$_["viewer"] = htmlentities($this->viewer_path);
			$_["file"] = htmlentities($result["file"]);
			$_["line"] = htmlentities($result["line"]);
			
			$tr = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
<tr>
	<td>
		<input name="ids[{$key}]" value="{$result["id"]}" type="checkbox" tabindex="{$index}" />
	</td>
	<td>
		<a name="line" href="{$_["viewer"]}?file={$_["file"]}#{$result["number"]}">{$_["line"]}</a>
	</td>
</tr>

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}
			$table .= $tr;
		}
		$table .= '</table>';
		
		$csrf_input = HTML::generate_csrf_input();
		$url_path = htmlentities(HTML::get_url_path());
		$form = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
<form action="{$url_path}" method="get">
	<button name="page" value="install" type="submit">Install database</button>
	<button name="page" value="configure" type="submit">Configure search</button>
	<button name="page" value="search" type="submit">Start searching</button>
	<button name="page" value="edit" type="submit">Edit results</button>
</form>
<hr />
<form action="{$url_path}" method="post">
	<input name="query_id" value="{$query_id}" type="hidden" />
	{$csrf_input}
{$table}
        <button name="action" value="delete_results" type="submit">Delete</button>
</form>

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

		$style = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
a[name='line'] {
	text-decoration: none;
	color: #AAA;
}
a[name='line']:focus {
	outline: none;
}
input[type='checkbox'] {
	margin-top: 0px;
	top: 4px;
}
HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

		HTML::$style .= $style;
		HTML::$body .= $form;
		
		return(true);
	}//}}}
	
	function edit()
	{//{{{
		$queries = Data::get_queries();
		if(!is_array($queries)) {
			trigger_error("Can't get queries", E_USER_WARNING);
			return(false);
		}
		
		$url_path = htmlentities(HTML::get_url_path());
		$table = '<table name="queries">';
		foreach($queries as $key => $query) {
			$index = $key+1;
			
			$_ = [];
			$_["search_path"] = htmlentities($query["search_path"]);
			$_["file_pattern"] = htmlentities($query["file_pattern"]);
			$_["line_pattern"] = htmlentities($query["line_pattern"]);
			
			$tr = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
<tr>
	<td>
		<input name="ids[{$key}]" value="{$query["id"]}" type="checkbox" tabindex="{$index}" />
	</td>
	<td name="line_pattern">
		<a name="query" href="{$url_path}?page=result&id={$query["id"]}">{$_["line_pattern"]}</a>
	</td>
	<td>
		{$_["search_path"]}
	</td>
	<td>
		{$_["file_pattern"]}
	</td>
</tr>

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}
			$table .= $tr;
		}
		$table .= '</table>';
		
		$csrf_input = HTML::generate_csrf_input();
		$form = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
<form action="{$url_path}" method="get">
	<button name="page" value="install" type="submit">Install database</button>
	<button name="page" value="configure" type="submit">Configure search</button>
	<button name="page" value="search" type="submit">Start searching</button>
</form>
<hr />
<form action="{$url_path}" method="post">
	{$csrf_input}
{$table}
        <button name="action" value="delete_queries" type="submit">Delete</button>
</form>

HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

		$style = 
/////////////////////////////////////////////////////////////////////////////{{{
<<<HEREDOC
a[name='query'] {
	text-decoration: none;
	color: #AAA;
}
a[name='query']:focus {
	outline: none;
}
input[type='checkbox'] {
	margin-top: 0px;
	top: 4px;
}
td {
	padding-right: 10px;
}
td[name='line_pattern'] {
	padding-right: 40px;
}
HEREDOC;
/////////////////////////////////////////////////////////////////////////////}}}

		HTML::$style .= $style;
		HTML::$body .= $form;
		
		return(true);
	}//}}}
	
}//}}}

class Action
{//{{{
	static function save_default_query()
	{//{{{
		$search_path = array_get_string("search_path", $_POST);
		if(!is_string($search_path)) {
			trigger_error("Can't get 'search_path' from POST", E_USER_WARNING);
			return(false);
		}
		
		$file_pattern = array_get_string("file_pattern", $_POST);
		if(!is_string($file_pattern)) {
			trigger_error("Can't get 'file_pattern' from POST", E_USER_WARNING);
			return(false);
		}
		
		$line_pattern = array_get_string("line_pattern", $_POST);
		if(!is_string($line_pattern)) {
			trigger_error("Can't get 'line_pattern' from POST", E_USER_WARNING);
			return(false);
		}
		
		$return = Data::set_default_query($search_path, $file_pattern, $line_pattern);
		if(!$return) {
			trigger_error("Can't set data of default query", E_USER_WARNING);
			return(false);
		}
		
		$url_path = HTML::get_url_path();
		header("Location: {$url_path}?page=configure");
		
		return(true);
	}//}}}
	
	static function preg_search()
	{//{{{
		$search_path = array_get_string("search_path", $_POST);
		if(!is_string($search_path)) {
			trigger_error("Can't get 'search_path' from POST", E_USER_WARNING);
			return(false);
		}
		
		$file_pattern = array_get_string("file_pattern", $_POST);
		if(!is_string($file_pattern)) {
			trigger_error("Can't get 'file_pattern' from POST", E_USER_WARNING);
			return(false);
		}
		
		$line_pattern = array_get_string("line_pattern", $_POST);
		if(!is_string($line_pattern)) {
			trigger_error("Can't get 'line_pattern' from POST", E_USER_WARNING);
			return(false);
		}
		
		$paths = Search::get_all_paths($search_path);
		$paths = Search::filter_paths($paths, $file_pattern);
		$results = Search::preg_match_files($paths, $line_pattern);
		
		$query_id = Data::insert_query($search_path, $file_pattern, $line_pattern);
		if(!is_int($query_id)) {
			trigger_error("Can't insert query to database", E_USER_WARNING);
			return(false);
		}
		
		foreach($results as $result) {
			$id = Data::insert_result($query_id, $result["file"], $result["number"], $result["line"]);
			if(!is_int($id)) {
				trigger_error("Can't insert result", E_USER_WARNING);
				return(false);
			}
		}
		
		$url_path = HTML::get_url_path();
		header("Location: {$url_path}?page=result&id={$query_id}");
		
		return(true);
	}//}}}
	
	static function delete_results()
	{//{{{
		$ids = array_get_array("ids", $_POST);
		if(!is_array($ids)) {
			trigger_error("Can't get 'ids' array from POST", E_USER_WARNING);
			return(false);
		}
		
		$query_id = array_get_int("query_id", $_POST);
		if(!is_int($query_id)) {
			trigger_error("Can't get 'query_id' from POST", E_USER_WARNING);
			return(false);
		}
		
		foreach($ids as $id) {
			$id = intval($id);
			$return = Data::delete_result($id);
			if(!$return) {
				trigger_error("Can't delete result", E_USER_WARNING);
				return(false);
			}
		}
		
		$url_path = HTML::get_url_path();
		header("Location: {$url_path}?page=result&id={$query_id}");
		
		return(true);
	}//}}}
	
	static function delete_queries()
	{//{{{
		$ids = array_get_array("ids", $_POST);
		if(!is_array($ids)) {
			trigger_error("Can't get 'ids' array from POST", E_USER_WARNING);
			return(false);
		}
		
		foreach($ids as $id) {
			$id = intval($id);
			$return = Data::delete_query($id);
			if(!$return) {
				trigger_error("Can't delete query", E_USER_WARNING);
				return(false);
			}
		}
		
		$url_path = HTML::get_url_path();
		header("Location: {$url_path}?page=edit");
		
		return(true);
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
				
			case('install'):
				$return = $Page->install();
				if(!$return) {
					trigger_error("Can't create 'install' page", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('configure'):
				$return = $Page->configure();
				if(!$return) {
					trigger_error("Can't create 'configure' page", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('search'):
				$return = $Page->search();
				if(!$return) {
					trigger_error("Can't create 'search' page", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('result'):
				$return = $Page->result();
				if(!$return) {
					trigger_error("Can't create 'result' page", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('edit'):
				$return = $Page->edit();
				if(!$return) {
					trigger_error("Can't create 'edit' page", E_USER_WARNING);
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
	
		switch($action) {				
			case('save_default_query'):
				$return = Action::save_default_query();
				if(!$return) {
					trigger_error("Can't save default query", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('preg_search'):
				$return = Action::preg_search();
				if(!$return) {
					trigger_error("Can't perform preg search", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('delete_results'):
				$return = Action::delete_results();
				if(!$return) {
					trigger_error("Can't perform delete results", E_USER_WARNING);
					return(false);
				}
				return(true);
				
			case('delete_queries'):
				$return = Action::delete_queries();
				if(!$return) {
					trigger_error("Can't perform delete queries", E_USER_WARNING);
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

class Data
{//{{{
	static $table_prefix = 'preg_search';

	static function create_tables()
	{//{{{
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["queries"] = "{$table_prefix}_queries";
		$_["results"] = "{$table_prefix}_results";
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
DROP TABLE IF EXISTS `{$_["queries"]}`;
CREATE TABLE `{$_["queries"]}` (
	`id` INT AUTO_INCREMENT KEY,
	`search_path` TEXT,
	`file_pattern` TEXT,
	`line_pattern` TEXT
);

SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
INSERT INTO `{$_["queries"]}` (
	`id`, `search_path`, `file_pattern`, `line_pattern`
) VALUES (
	0, '', '', ''
);

DROP TABLE IF EXISTS `{$_["results"]}`;
CREATE TABLE `{$_["results"]}` (
	`id` INT AUTO_INCREMENT KEY,
	`query_id` INT,
	`file` TEXT,
	`number` TEXT,
	`line` TEXT
);
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database queries", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}
	
	static function set_default_query(string $search_path, string $file_pattern, string $line_pattern)
	{//{{{
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["table"] = "{$table_prefix}_queries";
		$_["search_path"] = $DB->escape($search_path);
		$_["file_pattern"] = $DB->escape($file_pattern);
		$_["line_pattern"] = $DB->escape($line_pattern);
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
UPDATE `{$_["table"]}` SET
	`search_path`='{$_["search_path"]}',
	`file_pattern`='{$_["file_pattern"]}',
	`line_pattern`='{$_["line_pattern"]}'
 WHERE
	`id`=0
;
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}
	
	static function get_default_query()
	{//{{{
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["table"] = "{$table_prefix}_queries";
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
SELECT `search_path`,`file_pattern`,`line_pattern`
 FROM `{$_["table"]}` WHERE `id`=0 LIMIT 1;
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		if(empty($array)) {
			trigger_error("Default query not exists", E_USER_WARNING);
			return(false);
		}
		
		return($array[0]);
	}//}}}
	
	static function insert_query(string $search_path, string $file_pattern, string $line_pattern)
	{//{{{
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["table"] = "{$table_prefix}_queries";
		$_["search_path"] = $DB->escape($search_path);
		$_["file_pattern"] = $DB->escape($file_pattern);
		$_["line_pattern"] = $DB->escape($line_pattern);
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`search_path`,`file_pattern`,`line_pattern`
) VALUES (
	'{$_["search_path"]}', '{$_["file_pattern"]}', '{$_["line_pattern"]}'
);
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		return($id);
	}//}}}
	
	static function delete_query(int $id)
	{//{{{
		if(!( $id > 0 )) {
			trigger_error("Incorrect id", E_USER_WARNING);
			return(false);
		}
	
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["table"] = "{$table_prefix}_queries";
		$_["id"] = $DB->int($id);
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
DELETE FROM `{$_["table"]}` WHERE `id`={$_["id"]};
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}
	
	static function get_queries()
	{//{{{
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["table"] = "{$table_prefix}_queries";
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
SELECT `id`,`search_path`,`file_pattern`,`line_pattern`
 FROM `{$_["table"]}` WHERE `id`>0;
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return($array);
	}//}}}

	static function insert_result(int $query_id, string $file, int $number, string $line)
	{//{{{
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["table"] = "{$table_prefix}_results";
		$_["query_id"] = $DB->int($query_id);
		$_["file"] = $DB->escape($file);
		$_["number"] = $DB->int($number);
		$_["line"] = $DB->escape($line);
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
INSERT INTO `{$_["table"]}` (
	`query_id`,`file`,`number`,`line`
) VALUES (
	{$_["query_id"]}, '{$_["file"]}', {$_["number"]}, '{$_["line"]}'
);
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		$id = $DB->id();
		return($id);
	}//}}}
	
	static function delete_result(int $id)
	{//{{{
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["table"] = "{$table_prefix}_results";
		$_["id"] = $DB->int($id);
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
DELETE FROM `{$_["table"]}` WHERE `id`={$_["id"]};
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}
	
	static function delete_results(int $query_id)
	{//{{{
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["table"] = "{$table_prefix}_results";
		$_["query_id"] = $DB->int($query_id);
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
DELETE FROM `{$_["table"]}` WHERE `query_id`={$_["query_id"]};
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$return = $DB->query($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}
	
	static function get_results(int $query_id)
	{//{{{
		$DB = new DB;
		$_ = [];
		
		$table_prefix = $DB->name_escape(self::$table_prefix);
		
		$_["table"] = "{$table_prefix}_results";
		$_["query_id"] = $DB->int($query_id);
		$sql = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
SELECT `id`,`query_id`,`file`,`number`,`line` FROM `{$_["table"]}` WHERE `query_id`={$_["query_id"]};
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return($array);
	}//}}}
	
}//}}}

class Search
{//{{{

	static function get_all_paths($directory) // get all paths in a directory
	{//{{{
		if(!is_string($directory)) // directory path to get paths including subdirectories
			trigger_error('Passed variable "$directory" is not string', E_USER_ERROR);
		
		$paths = [$directory]; // result is array with paths
		
		for($index = 0; $index < count($paths); $index++) {
			if(is_dir($paths[$index]) !== true) {
				continue;
			}
			$path = $paths[$index];
			
			$resource = opendir($path);
			if(is_resource($resource) !== true) {
				if (defined('DEBUG') && DEBUG) var_dump(['$path' => $path]);
				trigger_error("Can't open directory for read contents", E_USER_WARNING);
				continue;
			}
			
			while (true) {
				$name = readdir($resource);
				if(is_string($name) !== true) { 
					break;
				}
				if($name == "." || $name == "..") {
					continue;
				}
				
				array_push($paths, "{$path}/{$name}");
			}
		}
		
		return($paths);
	}//}}}
	
	static function filter_paths($paths, $pattern) // filter regular files by perl regex match
	{//{{{
		$result = [];
		foreach($paths as $path) {
			if(!is_file($path)) {
				continue;
			}
			if(preg_match($pattern, $path) != 1) {
				continue;
			}
			array_push($result, $path);
		}
		return($result);
	}//}}}
	
	static function preg_match_files($paths, $pattern) // filter lines in files by perl regex match
	{//{{{
		if(!is_array($paths))
			trigger_error('Passed variable "$paths" is not array', E_USER_ERROR);
		
		if(!is_string($pattern))
			trigger_error('Passed variable "$pattern" is not string', E_USER_ERROR);
		
		$result = [];
		
		foreach($paths as $file) {
			
			$contents = file_get_contents($file);
			if(!is_string($contents)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't get contents from file", E_USER_WARNING);
				continue;
			}
			
			$LINE = explode("\n", $contents);
			$number = 0;
			foreach($LINE as $line) {
				$number += 1;
				$line = trim($line);
				if(preg_match($pattern, $line) != 1) {
					continue;
				}
				array_push($result, [
					"file" => $file
					,"number" => $number
					,"line" => $line
				]);
			}
		}
		return($result);
	}//}}}

}//}}}

$Main = new Main();

// favicon.ico : 264
// terminus.ttf : 17980
__halt_compiler();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bGVkMSA6IDUtMi0yMDE5AABVAG4AdABpAHQAbABlAGQAMQAAVW50aXRsZWQxAABWAGUAcgBzAGkAbwBuACAAMAAwADEALgAwADAAMAAgAABWZXJzaW9uIDAwMS4wMDAgAABVAG4AdABpAHQAbABlAGQAMQAAVW50aXRsZWQxAAAAAAACAAAAAAAA/4UAMAAAAAEAAAAAAAAAAAAAAAAAAAAAAKgAAAABAAIBAgADAAQABQAGAAcACAAJAAoACwAMAA0ADgAPABAAEQASABMAFAAVABYAFwAYABkAGgAbABwAHQAeAB8AIAAhACIAIwAkACUAJgAnACgAKQAqACsALAAtAC4ALwAwADEAMgAzADQANQA2ADcAOAA5ADoAOwA8AD0APgA/AEAAQQBCAEMARABFAEYARwBIAEkASgBLAEwATQBOAE8AUABRAFIAUwBUAFUAVgBXAFgAWQBaAFsAXABdAF4AXwBgAGEAiwEDAQQBBQEGAQcBCAEJAQoBCwEMAQ0BDgEPARABEQESARMBFAEVARYBFwEYARkBGgEbARwBHQEeAR8BIAEhASIBIwEkASUBJgEnASgBKQEqASsBLAEtAS4BLwEwATEBMgEzATQBNQE2ATcBOAE5AToBOwE8AT0BPgE/AUABQQFCAUMBRACrAUUHdW5pMDAwOQd1bmkwNDAxB3VuaTA0MTAHdW5pMDQxMQd1bmkwNDEyB3VuaTA0MTMHdW5pMDQxNAd1bmkwNDE1B3VuaTA0MTYHdW5pMDQxNwd1bmkwNDE4B3VuaTA0MTkHdW5pMDQxQQd1bmkwNDFCB3VuaTA0MUMHdW5pMDQxRAd1bmkwNDFFB3VuaTA0MUYHdW5pMDQyMAd1bmkwNDIxB3VuaTA0MjIHdW5pMDQyMwd1bmkwNDI0B3VuaTA0MjUHdW5pMDQyNgd1bmkwNDI3B3VuaTA0MjgHdW5pMDQyOQd1bmkwNDJBB3VuaTA0MkIHdW5pMDQyQwd1bmkwNDJEB3VuaTA0MkUHdW5pMDQyRgd1bmkwNDMwB3VuaTA0MzEHdW5pMDQzMgd1bmkwNDMzB3VuaTA0MzQHdW5pMDQzNQd1bmkwNDM2B3VuaTA0MzcHdW5pMDQzOAd1bmkwNDM5B3VuaTA0M0EHdW5pMDQzQgd1bmkwNDNDB3VuaTA0M0QHdW5pMDQzRQd1bmkwNDNGB3VuaTA0NDAHdW5pMDQ0MQd1bmkwNDQyB3VuaTA0NDMHdW5pMDQ0NAd1bmkwNDQ1B3VuaTA0NDYHdW5pMDQ0Nwd1bmkwNDQ4B3VuaTA0NDkHdW5pMDQ0QQd1bmkwNDRCB3VuaTA0NEMHdW5pMDQ0RAd1bmkwNDRFB3VuaTA0NEYHdW5pMDQ1MQd1bmkyMTE2AAAAAAAB//8AAgAAAAEAAAAA1bQyuAAAAADYRq3HAAAAANh+Vrs=
