<?
	require_once('database.php');
	require_once('users.php');

	class Message
	{
		private $db;
		function __construct()
		{
			$this->db = new Database();
		}
		function SendMessage($to_username, $subject, $content)
		{
			//find recipient ID
			$v = $this->db->query(
				"SELECT id FROM users WHERE username = ?",
				array($to_username)
			);
			$to_uid = $v[0]['id'];
			
			//find sender ID
			$u = new User();
			$session = $u->GetSessionNormal();
			$from_uid = $session['id'];

			$v = $this->db->query(
				"INSERT INTO messages VALUES(?,?,?,?,?,?)",
				array(NULL, $from_uid, $to_uid, $subject, $content,
					date("Y-m-d H:i:s"))
			);
		}
		function GetMessageRaw($id)
		{
			$v = $this->db->query(
				"SELECT * FROM messages WHERE id = ?",
				array($id)
			);
			if(!$v[0]['id'])
			{
				throw new Exception('No such Message.');
			}
			return $v[0];
		}
		function GetAllUserMessages()
		{
			$u = new User();
			$session = $u->GetSessionNormal();
			$uid = $session['id'];
			$v = $this->db->query(
				"SELECT * FROM messages WHERE to_user_id = ?",
				array($id)
			);		
		}
		function DeleteMessage($id)
		{
			if(!$id)
			{
				throw new Exception('All fields are required for delete.');
			}
			$v = $this->db->query(
				"DELETE FROM messages WHERE id = ?",
				array($id)
			);
		}
	}