<?php
	class Std
	{
		public static function In()
		{
			$In = fopen('php://stdin', 'r');

			if($In !== false)
				return trim(fgets($In));

			return false;
		}

		public static function Out($String = null, $NewLines = 1)
		{ // printf like, with array as params ?
			for($i = 0; $i < $NewLines; $i++)
				$String .= PHP_EOL;

			if(file_put_contents('php://stdout', $String) === strlen($String))
				return true;

			return false;
		}
	}