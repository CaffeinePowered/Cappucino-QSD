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
			dear mysqli team: there is no excuse for the braindead clusterfuck
			that is ::bind_params() and it's stupid arglist thing.
		*/
		private function makeValuesReferenced($arr){
					$refs = array();
					foreach($arr as $key => $value)
					    $refs[$key] = &$arr[$key];
					return $refs;
		}
		public function LastID()
		{
			return $this->db->insert_id;
		}
		public function query($stmt, $vars)
		{
			if(!is_null($vars) && !is_array($vars))
			{
				throw new Exception("Invalid var format");
			}
			$s = $this->db->prepare($stmt);
			if(!$s)
			{
				throw new Exception("Statement dissappeared: ".
					$this->db->error);
			}
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


				/*   ____ _             _      
				  / ___(_) __ _ _ __ | |_    
				 | |  _| |/ _` | '_ \| __|   
				 | |_| | | (_| | | | | |_    
				  \____|_|\__,_|_| |_|\__|   
				 | | | | __ _  ___| | __     
				 | |_| |/ _` |/ __| |/ /     
				 |  _  | (_| | (__|   <      
				 |_|_|_|\__,_|\___|_|\_\     
				  / _ \ / _|                 
				 | | | | |_                  
				 | |_| |  _|                 
				  \___/|_|         _   _     
				 |  _ \  ___  __ _| |_| |__  
				 | | | |/ _ \/ _` | __| '_ \ 
				 | |_| |  __/ (_| | |_| | | |
				 |____/ \___|\__,_|\__|_| |_|

				Caution: There are so many things wrong with mysqli_stmt::bind_param I'm not even going to begin

				 */
                //print "QUERY: $stmt VARS: " . 
                //	print_r(array_merge(array($s, 'bind_param'), $this->makeValuesReferenced($vars)),true);         	
				call_user_func_array(array($s, 'bind_param'), 
					array_merge(array($types), $this->makeValuesReferenced($vars))
				);
				/*
				  _____           _                 
				 | ____|_ __   __| |                
				 |  _| | '_ \ / _` |                
				 | |___| | | | (_| |                
				 |_____|_| |_|\__,_|  _             
				  / ___(_) __ _ _ __ | |_           
				 | |  _| |/ _` | '_ \| __|          
				 | |_| | | (_| | | | | |_           
				  \____|_|\__,_|_| |_|\__|       __ 
				 | | | | __ _  ___| | __   ___  / _|
				 | |_| |/ _` |/ __| |/ /  / _ \| |_ 
				 |  _  | (_| | (__|   <  | (_) |  _|
				 |_|_|_|\__,_|\___|_|\_\  \___/|_|  
				 |  _ \  ___  __ _| |_| |__         
				 | | | |/ _ \/ _` | __| '_ \        
				 | |_| |  __/ (_| | |_| | | |       
				 |____/ \___|\__,_|\__|_| |_|       
				                                */    
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
