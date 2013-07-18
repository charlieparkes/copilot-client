<?php

require_once("copilot.client.php") ;
echo "loaded<br>" ;

$request = new CP\Client\request('http://localhost/copilot/v1/query', 'GET') ;
echo "created<br>" ;

$request->execute() ;
echo "executed<br>" ;

echo "<br><br>" ;

if(DEV) { print_r($request) ; }


// Example of how to get a block of data.
/*
$request->getData['users']
*/


// Load a file which will test every single API call.
/* 

require_once("copilot.client.apitester.php") ;

$test = new CP\Client\test(
							array(
									'each url you want to test'
								 )
						  );

$test->execute() ;

*/

?>