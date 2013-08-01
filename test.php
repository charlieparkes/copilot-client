<?php

// This file is development only.
// I am testing the use of postbody to send data.

require_once("/client/copilot.client.php") ;

$request = new CP\Client\request(	'POST',
									'http://localhost/copilot/test',
									array("filter"=>"filtercontent"),
									array("field"),
									array("greeting"=>"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.")
									);
$request->execute() ;

echo $request->getURL() ;
echo "<br><br>" ;

echo $request->getRawData() ;
echo "<br><br>" ;

$req = $request->getRawRequest() ;

require_once('/client/func.obfuscate.php') ;

$json_url = 'http://localhost/copilot/key' ;
$ch = curl_init($json_url) ;
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = json_decode(curl_exec($ch), true) ;

$key = $result['log'][0]['msg'] ;

echo "<b>original:</b> " . $req, "<br>" ;

$encoded1 = urlencode( obfuscate("encrypt", $req, $key) );
echo "<b>encoded:</b> " . $encoded1, "<br>" ;

$decoded1 = obfuscate("decrypt", urldecode($encoded1), $key) ;
echo "<b>decoded:</b> " . $decoded1, "<br>" ;

echo "<b>" . (1-( strlen($req)/strlen($encoded1) )) . "% larger when encoded</b>" ;

?>
