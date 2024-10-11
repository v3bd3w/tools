async function browserOnCommand($command)
{
	var $return, $parameters;
	
	if($command == "content") {
		$parameters = {
			active: true
		};
		var $tab = await browser.tabs.query($parameters);
		$tab = $tab[0];
		
		$parameters = {
			files: [
				"/content/HTTPLoader.js"
				,"/content/titleParser.js"
				,"/content/script.js"
			]
			,target: {
				tabId: $tab.id
			}
		};
		$return = await browser.scripting.executeScript($parameters);
		console.log($return[0].result);
		
		return(null);
	}
}
browser.commands.onCommand.addListener(browserOnCommand);
