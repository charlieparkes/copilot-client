<?php

require_once("copilot.client.php") ;

// New request example.

		$request = new CP\Client\request('http://localhost/copilot/v1/users', 'GET') ;
		$request->execute() ;

// Example of how to get a block of data.

	$users = $request->getData('users') ;

	if($users !== NULL)
	{
		foreach($users as $user)
		{
			echo $user[0], " ", $user[1], "<br>" ;
		}
	}

?>