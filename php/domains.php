<?

	require_once('database.php');

	class Domain
	{
		private $db;
		function __construct()
		{
			$this->db = new Database();
		}
		function RegisterNewSubdomain($dnsname)
		{
			$v = $this->db->query('INSERT INTO domains VALUES (?, ?)',
				array(NULL, $dnsname . '.caffeinepoweredmedia.com'));
			if(!$v)
			{
				throw new Exception('Domain registration failed');
			}
			//return the new subdom ID (heh) that was just registered
			$v = $this->db->query('SELECT * FROM domains WHERE dnsname = ?',
				array($dnsname . '.caffeinepoweredmedia.com')
			);
			//echo "DNS:" . print_r($v,true);
			return $v[0]['id'];
		}
		function RandomSubdomainName($len = 8)
		{
			$universe_consonants = 'bcdefghjlmnprstv';
			$universe_vowels = 'aeiouy';
			$str = array();
			for($i = 0; $i < $len; $i++)
			{
				$str[] = (!($i % 2)?
					$universe_consonants{mt_rand(0,strlen($universe_consonants))}:
					$universe_vowels{mt_rand(0,strlen($universe_vowels))}
					);
			}
			return implode('',$str);
		}
		function RegisterNewDomain($dnsname)
		{
			throw new Exception('Not implemented.');
		}
	}
/*
	$d = new Domain();
	echo $d->RandomSubdomainName();*/