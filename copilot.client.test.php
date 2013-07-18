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
		$this->showStyles = TRUE ;
	}


	/**

	*/
	public function execute() {

		if($this->showStyles !== FALSE) 
		{
			include("copilot.style.php") ;
		}

		if($this->URLs !== NULL)
		{
			foreach($this->URLs as $URL)
			{
				echo '<h1>', $URL['type'], ' <a href="', $URL['dest'], '">', $URL['dest'], '</a></h1>' ;
				
				$request = new \CP\Client\request($URL['dest'], $URL['type']) ;
				
				try
				{
					$request->execute() ;
				}
				catch(Exception $e) // if any error is being returned directly
				{
					echo $e ;
				}

				print_r($request->getAllData()) ;
			}
		}
	}
}

?>
