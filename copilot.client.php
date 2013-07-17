<?php

/**
-==// Copilot Client //==-
*/

//Copyright 2013 Technical Solutions, LLC.
//Confidential & Proprietary Information.

namespace CP ;

define(	'APP_NAME'		,	'Copilot-Client'	);
define(	'APP_VERSION'	,	'0.1.0'				);

class CP_Client
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

	public function __construct ($url = null, $verb = 'GET', $requestBody = null)
	{
		$this->url				= $url;
		$this->verb				= $verb;
		$this->requestBody		= $requestBody;
		$this->requestLength	= 0;
		$this->username			= null;
		$this->password			= null;
		$this->acceptType		= 'application/json';
		$this->responseBody		= null;
		$this->responseInfo		= null;
		$this->responseData 	= array() ;

		if ($this->requestBody !== null)
		{
			$this->buildPostBody();
		}
	}

	public function flush ()
	{
		$this->requestBody		= null;
		$this->requestLength		= 0;
		$this->verb				= 'GET';
		$this->responseBody		= null;
		$this->responseInfo		= null;
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
					throw new InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
			}

			$this->decodeData() ;

		}
		catch (InvalidArgumentException $e)
		{
			curl_close($ch);
			throw $e;
		}
		catch (Exception $e)
		{
			curl_close($ch);
			throw $e;
		}

	}

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

	protected function executeGet ($ch)
	{		
		$this->doExecute($ch) ;
	}

	protected function executePost ($ch)
	{
		$this->doExecute($ch) ;
	}

	protected function executePut ($ch)
	{
		$this->doExecute($ch) ;
	}

	protected function executeDelete ($ch)
	{
		$this->doExecute($ch) ;
	}

	protected function doExecute (&$curlHandle)
	{
		$this->setCurlOpts($curlHandle);
		$this->responseBody = curl_exec($curlHandle);
		$this->responseInfo	= curl_getinfo($curlHandle);

		curl_close($curlHandle);
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

	public function decodeData() {
		if($this->responseBody !== null) {

			$this->responseData = json_decode($this->responseBody, true) ;

			foreach($this->responseData['blocks'] as $entry)
			{
				echo $entry['name'], '<br><br>' ;

				foreach($entry['data'] as $subentry)
				{
					foreach($subentry as $subsubentry)
					{
						echo $subsubentry, '<br>' ;
					}
				}
			}

		}
		else
		{
			echo "no data" ;
		}
	}

}

?>