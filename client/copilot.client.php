<?php

/**
-==// Copilot Client //==-
*/

//Copyright 2013 Technical Solutions, LLC.
//Confidential & Proprietary Information.

// Need to consider adding json upstream upload.

namespace CP\Client ;

define(	'NAME'				,	'Copilot-Client'	);
define(	'VERSION'			,	'1.3.0'				);
define(	'ENVIRONMENT'		, 	'DEV' 				);
define(	'APP_ERR_HANDLING'	, 	TRUE 				); // turn this off if you want to catch the exception outside of copilot.
define( 'CP_URL'			, 	'http://localhost/copilot');
define( 'CP_DEFAULT_KEY' 	, 	'1myUyZTm28vZKqJFYTPs7ou6MOMIHu3h');
/**
* An instance of this class represents one disposable call to the Copilot RESTful API.
*/
class request
{
	protected 	$url				;
	protected 	$verb				;
	protected 	$requestBody		;
	protected 	$requestLength		;
	protected 	$username			;
	protected 	$password			;
	protected 	$acceptType			;
	protected	$requestFilters 		;
	protected 	$requestFields 		;
	protected 	$responseBody		;
	protected 	$responseInfo		;
	protected 	$responseData  		;
	protected 	$URIrequestLimit 	;


	/**
	* CONSTRUCTOR
	*/
	public function __construct ($verb = 'GET', $url = NULL, $requestFilters = NULL, $requestFields = NULL)
	{
		$this->url				= $url 					;
		$this->verb				= $verb 				;
		$this->requestLength	= 0 					;
		$this->username			= NULL 					;
		$this->password			= NULL 					;
		$this->acceptType		= 'application/json' 	;
		$this->URIrequestLimit	= 2000					;

		$this->requestBody		= NULL 					;
		$this->requestFilters 	= $requestFilters 		;
		$this->requestFields 	= $requestFields 		;

		$this->responseBody		= NULL 					;
		$this->responseInfo		= NULL 					;
		$this->responseData 	= NULL 					;

		$append = "empty" ;

		if ($this->requestFilters !== NULL && !empty($this->requestFilters))
		{
			$appendFilters = '&(' ;
			$run = false ;
			$filters = NULL ;
			foreach($this->requestFilters as $filterKey => $filterVal)
			{	
				if($run) $filters .= ',' ;
				$filters .= $filterKey . '=' . $filterVal ;
				$run = true ;

			}
			$appendFilters .= urlencode($filters) ;
			$appendFilters .= ')' ;
			$this->appendFilters = $appendFilters ;
		}
		
		if ($this->requestFields !== NULL && !empty($this->requestFields))
		{
			$appendFields = '@(' ;
			$run = false ;
			$fields = NULL ;
			foreach($this->requestFields as $field)
			{
				if($run) $fields .= ',' ;
				$fields .= $field ;
				$run = true ;
			}
			$appendFields .= urlencode($fields) ;
			$appendFields .= ')' ;
			$this->appendFields = $appendFields ;
		}

		if ( ($this->requestFilters !== NULL && !empty($this->requestFilters)) && ($this->requestFields !== NULL && !empty($this->requestFields)) )
		{
			$append = $this->appendFilters . '::' . $this->appendFields ;
			$this->append = $append ;
		}

		//Concatinate URL if user didn't include it already.
		if(strpos('http', $this->url) === FALSE)
		{
			$this->url = CP_URL . $this->url ;
		}

		//Build full url with complete query string.
		if($append !== "empty" && strlen($this->url."?".$append) < $this->URIrequestLimit)
		{
			$this->url .= "?" . $append ;
		}
		
		//Build postable data.
		$data['FIELDS'] = ($requestFields !== NULL) ? $requestFields : "NULL" ;
		$data['FILTERS'] = ($requestFilters !== NULL) ? $requestFilters : "NULL" ;

		$this->requestBody = array('TOKEN'=>CP_DEFAULT_KEY, 'FIELDS'=>$data['FIELDS'], 'FILTERS'=>$data['FILTERS']) ;
	}


	public function flush ()
	{
		$this->requestBody		= NULL ;
		$this->requestLength	= 0 ;
		$this->verb				= 'GET' ;
		$this->responseBody		= NULL ;
		$this->responseInfo		= NULL ;
	}


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


	public function obfuscate ($action, $string, $key = CP_DEFAULT_KEY)
	{
	$output = false;

	$iv = md5(md5($key));

	if( $action == 'encrypt' ) {
		$output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, $iv);
		$output = base64_encode($output);
	}
	else if( $action == 'decrypt' ){
		$output = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, $iv);
		$output = rtrim($output, "");
	}
	return $output;
	}


	public function buildPostBody ()
	{
		//$this->requestBody = !is_array($this->requestBody) ? $this->requestBody : array() ;
		//if (!is_array($data))
		//$data = http_build_query($data, '', '&'); //make data safe for url

		if ($this->requestBody !== NULL)
		{
			$this->requestBody = $this->obfuscate('encrypt', json_encode($this->requestBody)) ;
			$this->requestBody = http_build_query(array('QUERY'=>$this->requestBody), '', '&');
		}
		else
		{
			$this->requestBody = "NULL" ;
		}

		//$this->requestBody = "test1=data1&test2=data2" ;
	}


	protected function executeGet ($ch)
	{		
		$this->doExecute($ch) ;
	}


	protected function executePost ($ch)
	{
		if (!is_string($this->requestBody))
		{
			$this->buildPostBody();
		}
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/x-www-form-urlencoded ',                                                                                
		    'Content-Length: ' . strlen($this->requestLength))                                                                       
		);  
		
		$this->doExecute($ch);	
	}


	protected function executePut ($ch)
	{
		if (!is_string($this->requestBody))
		{
			$this->buildPostBody();
		}
		
		$this->requestLength = strlen($this->requestBody);
		
		$fh = fopen('php://temp', 'rw+');
		fwrite($fh, $this->requestBody);
		rewind($fh);
		
		curl_setopt($ch, CURLOPT_INFILE, $fh);
		curl_setopt($ch, CURLOPT_INFILESIZE, $this->requestLength);
		curl_setopt($ch, CURLOPT_PUT, true);
		
		$this->doExecute($ch);
		
		fclose($fh);
	}


	protected function executeDelete ($ch)
	{
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		
		$this->doExecute($ch);
	}


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


	protected function setCurlOpts (&$curlHandle)
	{
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
		curl_setopt($curlHandle, CURLOPT_URL, $this->url);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array ('Accept: ' . $this->acceptType));
	}


	protected function setAuth (&$curlHandle)
	{
		if ($this->username !== null && $this->password !== null)
		{
			curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($curlHandle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}
	}


	private function decodeData()
	{
		if($this->responseBody !== NULL)
		{
			$this->responseData = json_decode($this->responseBody, true) ;
		}
		else
		{
			return "no data" ;
		}
	}


	public function getRawRequest()
	{
		return $this->requestBody ;
	}


	public function getRawData()
	{
		if($this->responseBody !== NULL)
		{
			return $this->responseBody ;
		}
		else
		{
			return "no data" ;
		}
	}


	public function getBlock($blockName = NULL)
	{
		if($blockName !== NULL && isset($this->responseData['blocks'][$blockName]) !== FALSE)
		{
			return $this->responseData['blocks'][$blockName] ;
		}
	}


	public function getAllBlocks()
	{
		if($this->responseData !== NULL)
		{
			return $this->responseData ;
		}
	}


	/**
	Accessors and Mutators
	*/
	public function getAcceptType ()
	{
		return $this->acceptType;
	} 
	
	public function setAcceptType ($acceptType)
	{
		$this->acceptType = $acceptType;
	} 
	
	public function getPassword ()
	{
		return $this->password;
	} 
	
	public function setPassword ($password)
	{
		$this->password = $password;
	} 
	
	public function getResponseBody ()
	{
		return $this->responseBody;
	} 
	
	public function getResponseInfo ()
	{
		return $this->responseInfo;
	} 
	
	public function getUrl ()
	{
		return $this->url;
	} 
	
	public function setUrl ($url)
	{
		$this->url = $url;
	} 
	
	public function getUsername ()
	{
		return $this->username;
	} 
	
	public function setUsername ($username)
	{
		$this->username = $username;
	} 
	
	public function getVerb ()
	{
		return $this->verb;
	} 
	
	public function setVerb ($verb)
	{
		$this->verb = $verb;
	} 
}

?>