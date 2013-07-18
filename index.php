<?php

include("copilot.client.php") ;
echo "loaded<br>" ;

$request = new CP\Client\request('http://localhost/copilot/v1/users', 'GET') ;
echo "created<br>" ;

$request->execute() ;
echo "executed<br>" ;

echo "<br><br>" ;

print_r($request) ;

?>