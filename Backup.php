<?php
	require_once 'class/Lib/Std.php';

	require_once 'class/CPanelBackupper.php';

	try
	{
		Std::Out();
		Std::Out('== CPanel Backupper ==');
		Std::Out('Maquinola.Net <contacto@maquinola.net>');

		$CPanelBackupper = new CPanelBackupper;

		Std::Out();
		Std::Out('Generating backups...', 2);

		$Backups = $CPanelBackupper->Backup();

		foreach($Backups as $Username => $Success)
			Std::Out("    {$Username} = " . var_export($Success, true));
	}
	catch(Exception $Exception)
	{
		Std::Out();
		Std::Out("Exception: {$Exception->GetMessage()}");

		exit;
	}