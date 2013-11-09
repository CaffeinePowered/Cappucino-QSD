<?
	require_once('database.php');
	require_once('users.php');
	require_once('domains.php');
	require_once('pages.php');
	require_once('fragments.php');

	define(DEFAULT_TEMPLATE_ID, 1);
	define(DEFAULT_STYLESHEET_ID, 1);

	class Site
	{
		private $db;
		function __construct()
		{
			$this->db = new Database();
		}
		function RegisterNewSite($site_title, $domain_id)
		{
			$u = new User();
			$user_id = $u->GetCurrentUserID();
			$v = $this->db->Query(
				"INSERT INTO sites VALUES (?,?,?,?,?,?)",
				array(
					NULL,
					$site_title,
					$user_id,
					DEFAULT_TEMPLATE_ID,
					DEFAULT_STYLESHEET_ID,
					$domain_id
				)
			);
			//Get the site we just added and add to the session
			$_SESSION['CurrentSite'] = $this->db->LastID();

			//add a default home page
			$p = new Page();
			$page_id = $p->AddPage('/', $_SESSION['CurrentSite'],'Home',
				'This is some default home page content.');

			//add a fragment for the default home page
			$f = new Fragment();
			$f->AddFragment($page_id, 'content', 'This is my Web page.');

			//send a message to user informing them of their good fortune
			//well, don't send, inject more like
			$v = $this->db->Query(
				"INSERT INTO messages VALUES (?, ?, ?, ?, ?, ?)",
				array(
					NULL,
					$user_id,	//todo: create an admin user, and insert its id here
					$user_id,
					'We\'ve set up your new Web site!',
					'Please visit it at RandomSubdomainName',
					NULL
				)
			);

			return $_SESSION['CurrentSite'];
		}
		function AutoGenerateSite()
		{
			$site_title = ucwords('My New Web Site');
			$d = new Domain();
			$domain_id = $d->RegisterNewSubdomain($d->RandomSubdomainName());
			return $this->RegisterNewSite($site_title, $domain_id); 
		}

	}
