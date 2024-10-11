var $titles = titleParser();

var $HTTPLoader = new HTTPLoader(60, 16);

function complete($url, $status, $headers, $body)
{//{{{//
	console.log($url, ":", $status, "\n", $body);
	console.log("Save titles finish");
}//}}}//
$HTTPLoader.complete = complete.bind(null);

$data = JSON.stringify($titles);

console.log("Save titles started");
$HTTPLoader.load("POST", "http://127.0.0.1:8080/index.php?action=save_titles", $data, "user", "password");

"Content script result";
