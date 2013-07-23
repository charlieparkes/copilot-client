<?php

$local_db_host      = "localhost:3306" ;
$local_db_name      = "copilot" ;
$local_db_user      = "admin" ;
$local_db_pass      = "password" ;

$db_host            = "mysql-pilot-dev.crfispfaqs9z.us-east-1.rds.amazonaws.com" ;
$db_name            = "tsspilot" ;
$db_user            = "tsspilot" ;
$db_pass            = "p!lot2013" ;

// Local/DEV DB
define( "DB_HOST_LOCAL",        $local_db_host) ;
define( "DB_NAME_LOCAL",        $local_db_name) ;
define( "DB_USER_LOCAL",        $local_db_user) ;
define( "DB_PASS_LOCAL",        $local_db_pass) ;

// Production DB
define( "DB_SERVER",            $db_host) ;
define( "DB_NAME",              $db_name) ;
define( "DB_USER",              $db_user) ;
define( "DB_PW",                $db_pass) ;
define( "DB_TIMEZONE",          "America/New_York");

//Pilot's Edited Files
require_once('class.pilot/class.db.php') ;
require_once('class.pilot/class.tss.main.php') ;

//Copilot's Nearly Untouched Files
require_once('class.copilot/class.db.php') ;
require_once('class.copilot/class.tss.main.php') ;


function testOutputs($callbackFunctionName, $className = "tss_main")
{
	if($className == "tss_main")
	{
		$main1 = new pilot\tss_main() ;
		$main2 = new tss_main() ;
	}

	$data1 = call_user_func(array($main1, $callbackFunctionName)) ;
	$data2 = call_user_func(array($main2, $callbackFunctionName)) ;

	if($data1 == $data2)
	{
		echo 'The output for "' . $callbackFunctionName . '" were equal.<br><br>' ;
	}
	else
	{
		echo '"' . $callbackFunctionName . '" were not equal.' ;
	}

	unset($main, $data1, $data2) ;
}


testOutputs("get_technician_list") ;
testOutputs("get_user_list") ;
//testOutputs("get_project_list") ;
testOutputs("get_customer_list") ;
testOutputs("get_distance_list") ;
testOutputs("get_priority_list") ;
//testOutputs("get_substatus_list") ;
testOutputs("get_role_list") ;
testOutputs("get_service_type_list") ;
testOutputs("get_role_list") ;
testOutputs("get_state_list") ;
testOutputs("get_event_tabs") ; // needs review
//testOutputs("append_to_log") ;
//testOutputs("get_user_fullname") ;
//testOutputs("get_user_email") ;
//testOutputs("get_user_timezone") ;
testOutputs("get_timezone_list") ;
//testOutputs("is_site_billable") ;
//testOutputs("get_permitted_file_extensions") ;

?>