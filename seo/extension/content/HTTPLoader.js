
// Usage example
/*
function windowOnLoad($event) {
	var $URL = "http://localhost/index.php";
	if(!true) {
		var $HTTPLoader = new HTTPLoader();
		$HTTPLoader.load("GET", $URL, undefined,'user', 'password');
	}
	else {
		var $HTTPLoader = new HTTPLoader(10, 1);
		$HTTPLoader.complete = console.log.bind(console);
		$HTTPLoader.load("GET", $URL, 'text');
	}
}
window.addEventListener("load", windowOnLoad);
*/

function HTTPLoader(
	$timeout = 30, // in seconds
	$maxResponseLength = 16 // in megabytes
) {

	this.load = function($method, $url, $data, $user, $password) {
		var $XMLHttpRequest = new XMLHttpRequest();
		$XMLHttpRequest.open($method, $url, true);
		
		$XMLHttpRequest.responseType = "text";
		
		$XMLHttpRequest.addEventListener("load", this.onLoad.bind(this, $XMLHttpRequest, $url));
		$XMLHttpRequest.addEventListener("error", this.onError.bind(this, $XMLHttpRequest, $url));
		
		$XMLHttpRequest.timeout = this.timeout;
		$XMLHttpRequest.addEventListener("timeout", this.onTimeout.bind(this, $XMLHttpRequest, $url));
		
		$XMLHttpRequest.addEventListener("progress", this.onProgress.bind(this, $XMLHttpRequest, $url));
		$XMLHttpRequest.addEventListener("abort", this.onAbort.bind(this, $XMLHttpRequest, $url));
		
		if(typeof($user) == "string" && typeof($password) == "string") {
			$XMLHttpRequest.setRequestHeader(
				"Authorization", 
				" Basic " + btoa($user + ":" + $password)
			);
		}
		
		$XMLHttpRequest.send($data);
	};

	this.maxResponseLength = 0x100000 * $maxResponseLength;

	this.onLoad = function($XMLHttpRequest, $url) {
		if($XMLHttpRequest.response.length > this.maxResponseLength) {
			console.error("Response length exceeds maximum", $url);
			this.complete($url, -4, '', '');
			return;
		}
		
		var $status = $XMLHttpRequest.status;
		var $headers = $XMLHttpRequest.getAllResponseHeaders();
		var $body = $XMLHttpRequest.response;
		
		this.complete($url, $status, $headers, $body);
	};
	
	this.onError = function($XMLHttpRequest, $url, $event) {
		console.error("XMLHttpRequest failed", $url);
		this.complete($url, -1, '', '');
	};

	this.timeout = 1000 * $timeout;
	
	this.onTimeout = function($XMLHttpRequest, $url)	{
		console.error("XMLHttpRequest timeout", $url);
		this.complete($url, -2, '', '');
	};
	
	this.onProgress = function($XMLHttpRequest, $url, $event) { 
		if($event.loaded > this.maxResponseLength) {
			console.error("Response length exceeds maximum", $url);
			$XMLHttpRequest.abort();
		}
	};
	
	this.onAbort = function($XMLHttpRequest, $url) {
		this.complete($url, -3, '', '');
	};
	
	this.complete = function($url, $status, $headers, $body) {
	};
};

