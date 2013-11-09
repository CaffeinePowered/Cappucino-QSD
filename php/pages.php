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
			$session = $u->GetSessionNormal();
			$uid = $session['id'];

			$v = $this->db->query(
				"INSERT INTO pages VALUES(?,?,?,?,?,?,?)",
				array(NULL, $site_id, $uid, $title, $url, $content,
					date(date("Y-m-d H:i:s")))
			);
			if(!$v)
			{
				throw new Exception('Could not add Page: '.
					$this->db->error);
			}
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