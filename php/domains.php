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

		}
		function RegisterNewDomain($dnsname)
		{
			throw new Exception('Not implemented.');
		}
	}

	$d = new Domain();
	$d->RegisterNewSubdomain(time());