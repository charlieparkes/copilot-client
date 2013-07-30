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
		$this->URLs = $testableURLs ;
	}


	/**

	*/
	public function execute() {

		if($this->URLs !== NULL)
		{
			foreach($this->URLs as $URL)
			{
				echo '<h1>', $URL['method'], ' <a href="', $URL['route'], '">', $URL['route'], '</a></h1>' ;
				
				$request = ($URL['method'] == "post" || $URL['method'] == "put") ? new \CP\Client\request($URL['method'], $URL['route'], array()) : new \CP\Client\request($URL['method'], $URL['route']) ;
				
				try
				{
					$request->execute() ;
				}
				catch(Exception $e) // if any error is being returned directly
				{
					echo $e ;
				}

				print_r($request->getAllBlocks()) ;
			}
		}
	}
}

?>
