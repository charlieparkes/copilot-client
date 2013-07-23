<?php

require_once("copilot.client.php") ;
require_once("copilot.client.tester.php") ;

	// output routes or test a route
	if(isset($_GET['route']) && isset($_GET['method'])) 
	{
		//header('Content-Type: application/json');

		//Script timer. 
		$mtime = explode(" ",microtime());$starttime = $mtime[1] + $mtime[0];

		$test = new CP\Client\test(array(array( "route"=>"http://localhost/copilot".urldecode($_GET['route']),"method"=>urldecode($_GET['method']) ))) ;
		$test->execute() ;

		// End the script timer.
		$mtime = explode(" ",microtime());$totaltime = (($mtime[1] + $mtime[0]) - $starttime);
		echo '<div style="position:fixed;top:0px;right:0px;background:#000000;color:#FFFFFF;">Script Time: '.$totaltime.'</div>' ;
	}
	else
	{
		//Script timer. 
		$mtime = explode(" ",microtime());$starttime = $mtime[1] + $mtime[0];

		$json_url = 'http://localhost/copilot/routes.php' ;
		$ch = curl_init($json_url) ;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($ch), true) ;

		// End the script timer.
		$mtime = explode(" ",microtime());$totaltime = (($mtime[1] + $mtime[0]) - $starttime);

		header('Content-Type: text/html');
		require_once('test.html.php') ;
	}

?>