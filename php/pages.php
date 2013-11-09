<?
	require_once('database.php');
	require_once('users.php');

	class Page
	{
		private $db;
		function __construct()
		{
			$this->db = new Database();
		}
		function AddPage($url, $site_id, $title, $content)
		{
			$u = new User();
			$user_id = $u->GetCurrentUserID();

			$params = array(NULL, $site_id, $user_id, $title, $url,
					date("Y-m-d H:i:s"));
			$v = $this->db->query(
				"INSERT INTO pages VALUES (?,?,?,?,?,?)",
				$params
			);
			if(!$v)
			{
				throw new Exception('Could not add Page: '.
					$this->db->error);
			}
			return $this->db->LastID();
		}
		function GetPageVars($page_id)
		{
			$v = $this->db->query(
				"SELECT token, content FROM fragments WHERE page_id = ?",
				array($page_id)
			);
			if(!$v[0]['token'])
			{
				throw new Exception('Could not get page contents: '.
					$this->db->error);
			}
			$varsarray = array();
			foreach($v as $fragment)
			{
				$varsarray[$fragment['token']] = $fragment['content'];
			}
			return $varsarray;
		}
		function EditPage($page_id, $url, $title, $content)
		{
			$v = $this->db->query(
				"UPDATE pages SET url = ?, title = ?, $content = ? ".
				"WHERE id = ?",
				array($url, $title, $content, $page_id)
			);
			if(!$v)
			{
				throw new Exception('Could not edit Page: '.
					$this->db->error);
			}			
		}
		function DeletePage($page_id)
		{
			$v = $this->db->query(
				"DELETE FROM pages WHERE id = ?",
				array($page_id)
			);
		}
	}