<?php
	require_once 'Lib/Std.php';
	require_once 'Lib/Unirest.php';

	abstract class SimpleAPI
	{
		protected $Endpoint = null;

		protected $DefaultHeaders = array();
		protected $DefaultGetParameters = array();

		public function __construct()
		{
			if(!is_string($this->Endpoint) || !parse_url($this->Endpoint))
				trigger_error(get_class($this) . ' must redefine $Endpoint', E_USER_ERROR);

			$this->Endpoint = rtrim($this->Endpoint, '/');
		}

		protected function Connect($Request, Array $Headers = array(), $Parameters = null, Array $SuccessHeaders = array(200, 301, 302))
		{
			return $this->Request(Unirest\Method::CONNECT, $Request, $Parameters, $Headers, $SuccessHeaders);
		}

		protected function Delete($Request, Array $Headers = array(), $Body = null, Array $SuccessHeaders = array(200, 301, 302))
		{
			return $this->Request(Unirest\Method::DELETE, $Request, $Body, $Headers, $SuccessHeaders);
		}

		protected function Get($Request, Array $Parameters = array(), Array $Headers = array(), Array $SuccessHeaders = array(200, 301, 302))
		{
			return $this->Request(Unirest\Method::GET, $Request, $Parameters, $Headers, $SuccessHeaders);
		}

		protected function Head($Request, Array $Headers = array(), $Parameters = null, Array $SuccessHeaders = array(200, 301, 302))
		{
			return $this->Request(Unirest\Method::HEAD, $Request, $Parameters, $Headers, $SuccessHeaders);
		}

		protected function Options($Request, Array $Headers = array(), $Parameters = null, Array $SuccessHeaders = array(200, 301, 302))
		{
			return $this->Request(Unirest\Method::OPTIONS, $Request, $Parameters, $Headers, $SuccessHeaders);
		}

		protected function Patch($Request, Array $Headers = array(), $Body = null, Array $SuccessHeaders = array(200, 301, 302))
		{
			return $this->Request(Unirest\Method::PATCH, $Request, $Body, $Headers, $SuccessHeaders);
		}

		protected function Post($Request, Array $Headers = array(), $Body = null, Array $SuccessHeaders = array(200, 301, 302))
		{
			return $this->Request(Unirest\Method::POST, $Request, $Body, $Headers, $SuccessHeaders);
		}

		protected function Put($Request, Array $Headers = array(), $Body = null, Array $SuccessHeaders = array(200, 301, 302))
		{
			return $this->Request(Unirest\Method::PUT, $Request, $Body, $Headers, $SuccessHeaders);
		}

		protected function Trace($Request, Array $Headers = array(), $Body = null, Array $SuccessHeaders = array(200, 301, 302))
		{
			return $this->Request(Unirest\Method::TRACE, $Request, $Body, $Headers, $SuccessHeaders);
		}

		protected function Request($Method, $Request, $Body = null, Array $Headers = array(), Array $SuccessHeaders = array(200, 301, 302))
		{
			try
			{
				$URL = $this->Endpoint;

				if(is_array($Request))
				{
					foreach($Request as $Key => $Value)
					{
						if(is_int($Key))
							$URL .= "/{$Value}";
						else
							$URL .= "/{$Key}/{$Value}";
					}
				}
				else
					$URL .= '/' . $Request;

				if(!empty($this->DefaultGetParameters))
					$URL .= '?' . http_build_query($this->DefaultGetParameters);

				$Headers = array_merge($this->DefaultHeaders, $Headers);

				$Response = Unirest\Request::send($Method, $URL, $Body, $Headers);

				if(in_array($Response->code, $SuccessHeaders))
					return array('Code' => $Response->code, 'Headers' => $Response->headers, 'Body' => $Response->raw_body, 'Json' => json_decode($Response->raw_body, true));

				$this->Warning("Response code {$Response->code}. Request to {$URL}");

			}
			catch(Exception $Exception)
			{
				$this->Warning(get_class($Exception) . ' thrown (' . $Exception->GetMessage() . ')');
			}

			return false;
		}

		protected function Info($String, $NewLines = 1)
		{
			Std::Out();
			return Std::Out('[Info] [API ' . get_class($this) . '] ' . $String, $NewLines);
		}

		protected function Warning($String, $NewLines = 1)
		{
			Std::Out();
			return Std::Out('[Warning] [API ' . get_class($this) . '] ' . $String, $NewLines);
		}
	}
