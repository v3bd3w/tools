function titleParser()
{//{{{//
	var $xpathExpression = "//div/h2/a";
	var $contextNode = document;
	var $namespaceResolver = null;
	var $resultType = XPathResult.ANY_TYPE;
	var $result = null;

	var $XPathResult = document.evaluate(
		$xpathExpression,
		$contextNode,
		$namespaceResolver,
		$resultType,
		$result
	);
	
	var $result = []; $title = '', $url = '', $object = {};
	while(true) {
		var $return = $XPathResult.iterateNext();
		if(typeof($return) != 'object' || $return == null) break;
		
		$title = $return.innerText;
		$url = $return.getAttribute("href");
		
		$object = {
			title: $title
			,url: $url
		};
		$result.push($object);
	}
	
	return($result);
}//}}}//

