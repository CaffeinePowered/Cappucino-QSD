<?
	class Database
	{
		private $db;
		public function __construct()
		{
			$this->db = new mysqli("localhost", 'username','password','database');
			if($this->db->connect_errno)
			{
				throw new Exception("Failed to connect to database server.");
			}
		}
		public function __destruct()
		{
			$this->db->close();
		}
		/*
			dear mysqli team: there is no excuse for the braindead clusterfk
			that is ::bind_params() and it's stupid arglist thing.

			This function shamelessly lifted from a Stack Overflow question.
		*/
		private function makeValuesReferenced($arr){
					$refs = array();
					foreach($arr as $key => $value)
					    $refs[$key] = &$arr[$key];
					return $refs;
		}
		public function query($stmt, $vars)
		{
			if(!is_null($vars) && !is_array($vars))
			{
				throw new Exception("Invalid var format");
			}
			$s = $this->db->prepare($stmt);
			if(count($vars) > 0)
			{
				$types = '';
				foreach($vars as $var)
				{
					//determine type for mysqli::bind_param()
					if(is_string($var))
					{
						if(strlen($var) > 255)
						{
							$type = 'b';
						}else{
							$type = 's';
						}
					}else if(is_float($var)){
						$type = 'd';
					}else{
						$type = 'i';	//null is counted as an int. It works so don't go changin'
					}
					$types .= $type;
				}
				call_user_func_array(array($s, 'bind_param'), 
					array_merge(array($types), $this->makeValuesReferenced($vars))
				);
				if($s->errno)
				{
					$paramcount = count($vars);
					throw new Exception("Bind failed: count $paramcount,"
						." query $stmt, error {$s->error}");
				}
			}
			if(!$s->execute())
			{
				throw new Exception("Execute error: {$s->error}");
			}
			//what type of query is this?
			$queryparts = explode(' ', $stmt);
			$querytype = strtolower($queryparts[0]);
			switch($querytype)
			{
				case 'select':
				{
					$res = $s->get_result();
					$ret = array();
					for($i = 0; $i <= $s->num_rows; $i++)
					{
						$ret[] = $res->fetch_assoc();
					}
					return $ret;
				}
				case 'update':
				case 'delete':
				case 'insert':
				{
					return $s->affected_rows;
				}
				default:
				{
					return;
				}
			}
		}
	}
