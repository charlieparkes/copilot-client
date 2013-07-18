<?php

/**
-==// Copilot Client //==-
*/

//Copyright 2013 Technical Solutions, LLC.
//Confidential & Proprietary Information.

namespace CP\Client ;

define(	'APP_NAME'			,	'Copilot-Client'	);
define(	'APP_VERSION'		,	'0.3.0'				);
define(	'DEV'				, 	TRUE 				);
define(	'APP_ERR_HANDLING'	, 	TRUE 				); // turn this off if you want to catch the exception outside of copilot.

/**
* This class represents one disposable call to the Copilot RESTful API.
*/
class request
{
	protected 	$url			;
	protected 	$verb			;
	protected 	$requestBody	;
	protected 	$requestLength	;
	protected 	$username		;
	protected 	$password		;
	protected 	$acceptType		;
	protected 	$responseBody	;
	protected 	$responseInfo	;
	protected 	$responseData  	;


	/**
	* CONSTRUCTOR
	*/
	public function __construct ($url = NULL, $verb = 'GET', $requestBody = NULL)
	{
		$this->url				= $url 					;
		$this->verb				= $verb 				;
		$this->requestBody		= $requestBody 			;
		$this->requestLength	= 0 					;
		$this->username			= NULL 					;
		$this->password			= NULL 					;
		$this->acceptType		= 'application/json' 	;
		$this->responseBody		= NULL 					;
		$this->responseInfo		= NULL 					;
		$this->responseData 	= NULL 					;

		if ($this->requestBody !== NULL)
		{
			$this->buildPostBody();
		}
	}


	/**

	*/
	public function flush ()
	{
		$this->requestBody		= NULL ;
		$this->requestLength	= 0 ;
		$this->verb				= 'GET' ;
		$this->responseBody		= NULL ;
		$this->responseInfo		= NULL ;
	}


	/**

	*/
	public function execute ()
	{
		$ch = curl_init();
		$this->setAuth($ch);

		try
		{
			switch (strtoupper($this->verb))
			{
				case 'GET':
					$this->executeGet($ch);
					break;
				case 'POST':
					$this->executePost($ch);
					break;
				case 'PUT':
					$this->executePut($ch);
					break;
				case 'DELETE':
					$this->executeDelete($ch);
					break;
				default:
					throw new \InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
			}

			$this->decodeData() ;

		}
		catch (\InvalidArgumentException $e)
		{
			curl_close($ch);
			throw $e;
		}
		catch (\Exception $e)
		{
			curl_close($ch);

			if(APP_ERR_HANDLING == TRUE)
			{
				echo "<h2>Error</h2>" ;
				echo "<span style=\"font-weight: bold;\">Message:</span> ". $e->getMessage(), "<br><span style=\"font-weight: bold;\">Location:</span> ", $e->getFile(), " on line ", $e->getLine(), "<br>" ;
				echo "<h3>Stack Trace:</h3>" ;
				$errorTrace = $e->getTrace() ;
				foreach($errorTrace as $trace)
				{
					echo  $trace['file'], " on line ", $trace['line'], " ", $trace['function'],  "<br>" ;
				}
			}
			else
			{
			throw $e ;
			}
		}
	}


	/**

	*/
	public function buildPostBody ($data = null)
	{
		$data = ($data !== null) ? $data : $this->requestBody;

		if (!is_array($data))
		{
			throw new InvalidArgumentException('Invalid data input for postBody.  Array expected');
		}

		$data = http_build_query($data, '', '&');
		$this->requestBody = $data;
	}


	/**

	*/
	protected function executeGet ($ch)
	{		
		$this->doExecute($ch) ;
	}


	/**

	*/
	protected function executePost ($ch)
	{
		$this->doExecute($ch) ;
	}


	/**

	*/
	protected function executePut ($ch)
	{
		$this->doExecute($ch) ;
	}


	/**

	*/
	protected function executeDelete ($ch)
	{
		$this->doExecute($ch) ;
	}


	/**

	*/
	protected function doExecute (&$curlHandle)
	{
		$this->setCurlOpts($curlHandle);
		$this->responseBody = curl_exec($curlHandle);

		if($this->responseBody === FALSE)
		{
			throw new \Exception(curl_error($curlHandle)) ;
		} else {
			$this->responseInfo	= curl_getinfo($curlHandle);
			curl_close($curlHandle);
		}
	}


	/**

	*/
	protected function setCurlOpts (&$curlHandle)
	{
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
		curl_setopt($curlHandle, CURLOPT_URL, $this->url);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array ('Accept: ' . $this->acceptType));
	}


	/**

	*/
	protected function setAuth (&$curlHandle)
	{
		if ($this->username !== null && $this->password !== null)
		{
			curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($curlHandle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}
	}


	/**
	Needs better error handling.
	*/
	private function decodeData()
	{
		if($this->responseBody !== NULL) {

			$this->responseData = json_decode($this->responseBody, true) ;

		}
		else
		{
			echo "no data" ;
		}
	}


	/**

	*/
	public function getData($blockName = NULL)
	{
		if($blockName !== NULL && isset($this->responseData['blocks'][$blockName]) !== FALSE)
		{
			return $this->responseData['blocks'][$blockName] ;
		}
	}


	/**

	*/
	public function getAllData()
	{
		if($this->responseData !== NULL)
		{
			return $this->responseData ;
		}
	}
}

?>