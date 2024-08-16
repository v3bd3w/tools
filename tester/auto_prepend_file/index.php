<?php 
$name = "\x80";

$source = 
<<<HEREDOC
class {$name} {
	private static \$class = NULL;
	public static function init() {
		if(self::\$class === NULL) {
			self::\$class = new {$name}();
			return(true);
		}
		trigger_error("Duplicate call x80::init detected", E_USER_ERROR);
	}
	public static function status() {
		if(self::\$class === NULL) return(true);
	}
	public function __construct() {
		if({$name}::status() === true) return(\$this->start());
		trigger_error("Duplicate declare x80 class detected", E_USER_ERROR);
	}
HEREDOC;
$source = preg_replace("/\n/", "", $source);

$LINE = file(__DIR__.'/x80.php');
if(!is_array($LINE))
	trigger_error("Can't get contents of x80 class", E_USER_ERROR);

array_shift($LINE);
$source .= "\n".implode("", $LINE)."\n";

$source .= "} {$name}::init();";
eval($source); unset($source, $name, $LINE);

