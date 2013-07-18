<?php

require_once("copilot.client.php") ;

// New request example.

	$request = new CP\Client\request('http://localhost/copilot/v1/users', 'GET') ;
	$request->execute() ;


// Example of how to get a block of data.

	$data = $request->getData('users') ;

	//print_r($data) ;
	//foreach($data as $dataPart)
	//{
	//	echo $dataPart[0], " ", $dataPart[1], "<br>" ;
	//}



// Load a file which will test every single API call.

	require_once("copilot.client.test.php") ;

	$test = new CP\Client\test(
								array(
										'each url you want to test'
									 )
							  ) ;
	/*
	$test->execute() ;
	*/

?>