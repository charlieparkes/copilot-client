<?php

include("copilot.client.php") ;

$request = new CP\CP_Client('http://localhost/copilot/v1/users', 'GET') ;
$request->execute() ;

echo "hello" ;
?>