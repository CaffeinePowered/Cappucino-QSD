<?
	require_once('database.php');

	class Template
	{
		private $db;
		function __construct()
		{
			$this->db = new Database();
		}
		function AddTemplate($name, $content)
		{
			$v = $this->db->query(
				"INSERT INTO templates VALUES(?,?,?)",
				array(NULL, $name, $content)
			);
			if(!$v)
			{
				throw new Exception('Could not add template: '.
					$this->db->error);
			}
		}
		function GetTemplateRaw($id)
		{
			$v = $this->db->query(
				"SELECT * FROM templates WHERE id = ?",
				array($id)
			);
			if(!$v[0]['id'])
			{
				throw new Exception('No such template.');
			}
			return $v[0]['content'];
		}
		function GetTemplate($id, $vars)
		{
			//check if $vars is associative
			if (is_array($vars) && 
				array_diff_key($vars, array_fill(
					0, count($vars), null)))
			{
				throw new Exception('GetTemplate expects assoc array');
			}
			$template = $this->GetTemplateRaw($id);
			foreach($vars as $token => $value)
			{
				$template = str_replace($token, $value, $template);
			}
			return $template;
		}
		function EditTemplate($id, $name, $content)
		{
			if(!($id && $name && $content))
			{
				throw new Exception('All fields are required for edit.');
			}
			$v = $this->db->query(
				"UPDATE templates SET name = ?, content = ? WHERE id = ?".
				array($name, $content, $id)
			);
			if(!$v)
			{
				throw new Exception('Could not edit template: '.
					$this->db->error);
			}		
		}
		function DeleteTemplate($id)
		{
			if(!$id)
			{
				throw new Exception('All fields are required for delete.');
			}
			$v = $this->db->query(
				"DELETE FROM templates WHERE id = ?",
				array($id)
			);
		}
	}