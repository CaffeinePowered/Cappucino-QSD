<?
	require_once('database.php');

	class Site
	{
		private $db;
		function __construct()
		{
			$this->db = new Database();
		}
	}