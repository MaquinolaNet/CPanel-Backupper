<?php
	require_once 'Lib/Std.php';
	require_once 'Lib/Json.php';

	require_once 'SimpleAPI.php';

	class CPanelBackupper extends SimpleAPI
	{
		private $Scheme = null;
		private $Hostname = null;
		private $Port = null;

		private $Username = null;
		private $AccessHashPath = null;
		private $AccessHash = null;

		private $FTPHostname = null;
		private $FTPPort = null;
		private $FTPUsername = null;
		private $FTPPassword = null;
		private $FTPDirectory = null;

		private $Email = null;

		public function __construct()
		{
			$Config = Json::Decode('config/CPanelBackupper.json');

			if($Config === false)
				throw new Exception("config/CPanelBackupper.json isn't readable");

			if(empty($Config['Scheme']))
				throw new Exception("config/CPanelBackupper.json[Scheme] doesn't exist");

			if(empty($Config['Hostname']))
				throw new Exception("config/CPanelBackupper.json[Hostname] doesn't exist");

			if(empty($Config['Port']))
				throw new Exception("config/CPanelBackupper.json[Port] doesn't exist");

			if(!is_int($Config['Port']))
				throw new Exception("config/CPanelBackupper.json[Port] isn't an integer");

			if(empty($Config['Username']))
				throw new Exception("config/CPanelBackupper.json[Username] doesn't exist");

			if(empty($Config['AccessHashPath']))
				throw new Exception("config/CPanelBackupper.json[AccessHashPath] doesn't exist");

			$this->Scheme = $Config['Scheme'];
			$this->Hostname = $Config['Hostname'];
			$this->Port = $Config['Port'];

			$this->Username = $Config['Username'];
			$this->AccessHashPath = $Config['AccessHashPath'];

			if(!is_readable($this->AccessHashPath))
				throw new Exception("Access Hash's path isn't readable ({$this->AccessHashPath})");

			$this->AccessHash = file_get_contents($this->AccessHashPath);

			if(empty($this->AccessHash))
				throw new Exception("Access Hash ({$this->AccessHashPath}) is empty");


			$this->Endpoint = $this->Scheme . $this->Hostname . ':' . $this->Port . '/json-api';

			$this->DefaultHeaders = array('Authorization' => "WHM {$this->Username}:" . str_replace(array("\r", "\n"), '', $this->AccessHash));

			parent::__construct();

			if(!is_array($Config['FTP']))
				throw new Exception("config/CPanelBackupper.json[FTP] isn't an array");

			if(empty($Config['FTP']['Hostname']))
				throw new Exception("config/CPanelBackupper.json[FTP][Hostname] doesn't exists");

			if(empty($Config['FTP']['Port']))
				throw new Exception("config/CPanelBackupper.json[FTP][Port] doesn't exists");

			if(!is_int($Config['FTP']['Port']))
				throw new Exception("config/CPanelBackupper.json[FTP][Password] doesn't exists");

			if(empty($Config['FTP']['Username']))
				throw new Exception("config/CPanelBackupper.json[FTP][Username] doesn't exists");

			if(empty($Config['FTP']['Password']))
				throw new Exception("config/CPanelBackupper.json[FTP][Password] doesn't exists");

			if(empty($Config['FTP']['Directory']))
				throw new Exception("config/CPanelBackupper.json[FTP][Directory] doesn't exists");

			if(empty($Config['Email']))
				throw new Exception("config/CPanelBackupper.json[Email] doesn't exists");

			$this->FTPHostname = $Config['FTP']['Hostname'];
			$this->FTPPort = $Config['FTP']['Port'];
			$this->FTPUsername = $Config['FTP']['Username'];
			$this->FTPPassword = $Config['FTP']['Password'];
			$this->FTPDirectory = $Config['FTP']['Directory'];

			$this->Email = $Config['Email'];
		}

		public function Backup()
		{
			$Users = Json::Decode('config/Users.json');

			if(is_array($Users))
			{
				$Return = array();

				foreach($Users as $User)
				{
					$Response = $this->Get('cpanel', array
					(
						'api.version' => 1,
						'cpanel_jsonapi_apiversion' => 1,

						'cpanel_jsonapi_user' => $User,
						'cpanel_jsonapi_module' => 'Fileman',
						'cpanel_jsonapi_func' => 'fullbackup',

						'server' => $this->FTPHostname,
						'port' => $this->FTPPort,
						'user' => $this->FTPUsername,
						'pass' => $this->FTPPassword,
						'rdir' => $this->FTPDirectory,
						'dest' => 'passiveftp',

						'email' => $this->Email
					));

					$Return[$User] = !empty($Response['Json']['event']['result']);
				}

				return $Return;
			}
			else
				throw new Exception("config/Users.json must be an array");
		}
	}