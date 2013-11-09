<?
	require_once('database.php');

	class Fragment
	{
		private $db;
		function __construct()
		{
			$this->db = new Database();
		}
		function AddFragment($page_id, $token, $content)
		{

			$v = $this->db->query(
				"INSERT INTO fragments VALUES(?,?,?,?)",
				array(NULL, $page_id, $token, $content)
			);
			if(!$v)
			{
				throw new Exception('Could not add fragment: '.
					$this->db->error);
			}
		}
		function EditFragment($fragment_id, $content)
		{
			$v = $this->db->query(
				"UPDATE fragments SET $content = ? WHERE id = ?",
				array($content, $fragment_id)
			);
			if(!$v)
			{
				throw new Exception('Could not edit fragment: '.
					$this->db->error);
			}			
		}
/*		function DeletePage($page_id)
		{
			$v = $this->db->query(
				"DELETE FROM pages WHERE id = ?",
				array($page_id)
			);
		}*/
	}