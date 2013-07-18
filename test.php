<?php

require_once("copilot.client.php") ;

// Load a file which will test the requested API calls.

	require_once("copilot.client.test.php") ;

	//Script timer. 
	$mtime = explode(" ",microtime());$starttime = $mtime[1] + $mtime[0];

	$test = new CP\Client\test(
								array(
										array("dest"=>'badurl',"type"=>'GET'),
										array("dest"=>'http://localhost/copilot/v1/users',"type"=>'GET'),
										array("dest"=>'http://localhost/copilot/v1/users/technician',"type"=>'GET')
									 )
							  ) ;
	$test->execute() ;

	// End the script timer.
	$mtime = explode(" ",microtime());$totaltime = (($mtime[1] + $mtime[0]) - $starttime);
	echo '<div style="position:fixed;top:0px;right:0px;background:#000000;color:#FFFFFF;">Script Time: '.$totaltime.'</div>' ;

?>