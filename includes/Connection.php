<?php

class Connection
{
	public static function make()
	{
		require_once 'Consts.php';
		$options = [
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		];
		try
		{
			return new PDO("mysql:host=" . serverName . ";dbname=" . dbName . ";charset=utf8",userName,pass, $options);
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
}