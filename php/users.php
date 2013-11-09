<?
	require_once("database.php");

	class User
	{
		private $db;
		/*
			Constructor
		*/
		public function __construct()
		{
			$this->db = new Database();
			if(!$_SESSION)
			{
				session_start();
			}
		}
		/*
			A pepper is an "application specific secret".
			http://barkingiguana.com/2009/08/03/securing-passwords-with-salt-pepper-and-rainbows/
		*/
		public function Pepper()
		{
			return 
			"TXBtLQ0ER0UA9zgeGjnCZE305IvIjTcwnlCL9vb1".
			"25AZn3oNvrZOx2l2L3rcAbe5O03JVJ9VwbcRumfF".
			"yUP8VgH6bMcwqs0MVUYydbaNHmg5WKla24xR0rT5".
			"FHZqE6SdMoVbOrd1wdLV54u3msAUQGFwV3pw3Cu1".
			"QwzlwG2RHw4EuLtZ0xdgLI87BB5zdBtTudJ5OKNW";
		}
		/*
			Returns a hashing salt to be stored in the user's profile.
		*/
		public function Salt()
		{
			$fp = fopen('/dev/urandom', 'r');
			$fresh = base64_encode(fread($fp, 32));
			fclose($fp);
			return $fresh;
		}
		public function GetLoggedIn()
		{
			if($this->GetSessionNormal())
			{
				return true;
			}
			return false;
		}
		public function GetCurrentUserID()
		{
			$session = $this->GetSessionNormal();
			return $session['id'];
		}
		/*
			Retrieves session data for the currently logged-in user.
		*/
		public function GetSessionNormal()
		{
			$sessiondata = unserialize($_SESSION['LoginNormal']);
			return $sessiondata[0];
		}
		/*
			Inverse of RegisterNormal().
		*/
		public function LoginNormal($username, $password)
		{
			//Find user's salt...
			$user = $this->db->query(
				"SELECT * FROM users WHERE username = ?",
					array($username)
				);
			$salt = $user[0]['salt'];
			$hashed = hash("sha256",$salt . $password . $this->Pepper());
			$results = $this->db->query("SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1", array($username, $hashed));
			
			//print "<br />HASH2 = $hashed<br />SALT2 = $salt<br />";

			if($results[0] != NULL)
			{
				$uid = $results[0]['id'];
				$_SESSION['LoginNormal'] = serialize($results);
				return $uid;
			}
			return false;
		}
		/*
			Registration method for those who do not succumb to the social networking craze.
		*/
		public function RegisterNormal($username, $password, $email, $firstname, $lastname)
		{
			$salt = $this->Salt();
			$hashed = hash("sha256",$salt . $password . $this->Pepper());
			//print "<br />HASH1 = $hashed<br />SALT1 = $salt<br />";
			$this->db->query(
				"INSERT INTO users VALUES (".
				"?,".		//id
				"?,".			//username
				"?,".			//password
				"?,".			//salt
				"?,".		//oauth_provider
				"?,".		//oauth_uid
				"?,".			//firstname
				"?,".			//lastname
				"?,".			//email
				"?,".			//signedup_date
				"?)",			//paid_until	

				array(
					NULL,
					$username,
					$hashed,
					$salt,
					NULL,
					NULL,
					ucwords($firstname),
					ucwords($lastname),
					$email,
					date("Y-m-d H:i:s"),
					date("Y-m-d H:i:s",time() + (28 * (60 * 60 * 24)))
					)	
				);
			if($this->db->LastID())
			{
				return $this->db->LastID();
			}
		}
	}
