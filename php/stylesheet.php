<?
	require_once('database.php');

	class Stylesheet
	{
		private $db;
		function __construct()
		{
			$this->db = new Database();
		}
		function AddStylesheet($name, $content)
		{
			$v = $this->db->query(
				"INSERT INTO stylesheets VALUES(?,?,?)",
				array(NULL, $name, $content)
			);
			if(!$v)
			{
				throw new Exception('Could not add Stylesheet: '.
					$this->db->error);
			}
		}
		function GetStylesheetRaw($id)
		{
			$v = $this->db->query(
				"SELECT * FROM stylesheets WHERE id = ?",
				array($id)
			);
			if(!$v[0]['id'])
			{
				throw new Exception('No such Stylesheet.');
			}
			return $v[0];
		}
		function GetStylesheet($id, $vars)
		{
			//check if $vars is associative
			if (is_array($vars) && 
				array_diff_key($vars, array_fill(
					0, count($vars), null)))
			{
				throw new Exception('GetStylesheet expects assoc array');
			}
			$Stylesheet = $this->GetStylesheetRaw($id);
			foreach($vars as $token => $value)
			{
				$Stylesheet = str_replace($token, $value, $Stylesheet);
			}
			return $Stylesheet;
		}
		function EditStylesheet($id, $name, $content)
		{
			if(!($id && $name && $content))
			{
				throw new Exception('All fields are required for edit.');
			}
			$v = $this->db->query(
				"UPDATE stylesheets SET name = ?, content = ? WHERE id = ?".
				array($name, $content, $id)
			);
			if(!$v)
			{
				throw new Exception('Could not edit Stylesheet: '.
					$this->db->error);
			}			
		}
		function DeleteStylesheet($id)
		{
			if(!$id)
			{
				throw new Exception('All fields are required for delete.');
			}
			$v = $this->db->query(
				"DELETE FROM stylesheets WHERE id = ?",
				array($id)
			);
		}
	}