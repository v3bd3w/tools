window.addEventListener("load", function(event) {
	var $Element = document.getElementById("about");
	$Element.addEventListener("click", function(event) {
		var $Navigator = window.navigator;
		var $String = $Navigator.userAgent;
		alert($String);
	} );
} );

