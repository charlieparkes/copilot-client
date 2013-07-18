<?php

//Copyright 2013 Technical Solutions, LLC.
//Confidential & Proprietary Information.

namespace CP\Client ;

/**
* This class runs a battery of tests on the copilot client.
*/
class test extends request
{
	/**
	* CONSTRUCTOR
	*/
	public function __construct($testableURLs = NULL)
	{
		if($testableURLs !== NULL)
		{
			foreach($testableURLs as $URL)
			{
				echo $URL, "<br>" ;
			}
		}
	}
}

?>
