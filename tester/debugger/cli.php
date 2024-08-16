<?php

require_once(__DIR__.'/include/class/PHPDebugger.php');

$PHPDebugger = new PHPDebugger('/srv/games/separator/cli.php', '/usr/share/wordpress', 10, true);

$commands = 
<<<'HEREDOC'
break ZEND_EXIT
run
ev var_dump($link);
continue
quit
HEREDOC;
$COMMAND = explode("\n", $commands);

foreach($COMMAND as $command) {
	$PHPDebugger->send($command);
}


