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
define("DB_HOST_LOCAL",$local_db_host);
define("DB_NAME_LOCAL",$local_db_name);
define("DB_USER_LOCAL",$local_db_user);
define("DB_PASS_LOCAL",$local_db_pass);

// Production DB
define("DB_SERVER",$db_host);
define("DB_NAME",$db_name);
define("DB_USER",$db_user);
define("DB_PW",$db_pass);
define("DB_TIMEZONE","America/New_York");

//Pilot's Edited Files
require_once('class.pilot/class.db.php') ;
require_once('class.pilot/class.tss.main.php') ;

//Copilot's Nearly Untouched Files
require_once('class.copilot/class.db.php') ;
require_once('class.copilot/class.tss.main.php') ;


function testOutputs($callbackFunctionName, $className = "tss_main", $args = NULL)
{
	echo '<div class="item"><div class="left"><span class="name"><span class="class">' . $className . '</span><span class="dots">::</span><span class="func">' . $callbackFunctionName . '()</span></span></div>' ;

	if($className == "tss_main")
	{
		$main1 = new tss_main() ;
		$main2 = new tss_main() ;
	}

	if($args == NULL)
	{
		$data1 = call_user_func(array($main1, $callbackFunctionName)) ;
		$data2 = call_user_func(array($main2, $callbackFunctionName)) ;
	}
	else
	{
		$data1 = call_user_func_array(array($main1, $callbackFunctionName), $args) ;
		$data2 = call_user_func_array(array($main2, $callbackFunctionName), $args) ;
	}

	if($data1 == $data2 && $data1 !== NULL && $data2 !== NULL)
	{
		echo '<div class="right"><span style="margin: 0px 3px">. . .</span>output <span class="status ok">verified.</span><br><br></div><div class="clear"></div></div>' ;
	}
	else
	{
		print_r($data1) ; echo '<br><br>' ;
		print_r($data2) ; echo '<br><br>' ;
		if($data1 == $data2) echo "===" ;
		echo '<div class="right"><span style="margin: 0px 3px">. . .</span>output <span class="status not">failed.</span><br><br></div><div class="clear"></div></div>' ;
	}

	unset($main, $data1, $data2) ;
}

?>

<html>
<head>
<style>
body {
	background-color: #272822 ;
	color: #F8F8F2 ;
	font-family: 'Source Code Pro', 'Ubuntu Mono', 'Monaco', 'Menlo', 'Consolas', "Courier New", monospace ;
	font-size: 0.8em ;
}
.item {
	clear: both ;
	text-align: center ;
	position: relative ;
	width: 1000px ;
	left: 50% ;
	margin-left: -500px ;
}
.left {
	width: 500px ;
	text-align: right ;
	float: left ;
	overflow-x: visible ;
}
.right {
	width: 500px ;
	text-align: left ;
	float: right ;
	overflow-x: visible ;
}
.clear {
	width: 100% ;
	clear: both ;
}
.name {
}
.class{
	color: #66D9EF ;
}
.func{}
.dots {
	color: #F92672 ;
}
.status {}
.ok {
	font-weight: normal ;
	color: #A6E22E ;
}
.not {
	font-weight: bold ;
	color: #F92672 ;
}
</style>
</head>
<body>

<?php

$workingTestsOnly = 0 ;
$unsafeTests = 0 ;

if($workingTestsOnly == TRUE)
{
testOutputs("get_technician_list") ;
testOutputs("get_user_list") ;
testOutputs("get_customer_list") ;
testOutputs("get_distance_list") ;
testOutputs("get_priority_list") ;
testOutputs("get_substatus_list", "tss_main", array('2')) ;
testOutputs("get_role_list") ;
testOutputs("get_service_type_list") ;
testOutputs("get_role_list") ;
testOutputs("get_status_list") ;
testOutputs("get_state_list") ;
testOutputs("get_event_tabs") ;
testOutputs("get_timezone_list") ;
}
elseif($unsafeTests == TRUE)
{
testOutputs("append_to_log", "tss_main", array('3262', 'DEV: Copilot log entry.', '406')) ;
}
else
{
// needs review

	//testOutputs("get_project_list") ;

// needs query

	//testOutputs("get_user_fullname") ;
	//testOutputs("get_user_email") ;
	//testOutputs("get_user_timezone") ;

// queued

	//testOutputs("is_site_billable") ;
	//testOutputs("get_permitted_file_extensions") ;
}

?>

</body>
</html>