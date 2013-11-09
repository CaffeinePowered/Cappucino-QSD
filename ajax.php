<?

	//Pretty much all permissions filtering here requires User
	//so let's include it here
	require_once('php/users.php');
	$u = new User();

	$module = $_GET['module'];
	$function = $_GET['function'];
	$vars = explode(',',$_GET['vars']);

	function json_error($msg)
	{
		return json_encode(array('error' => $msg));
	}

	if(file_exists('php/' . $module . '.php'))
	{
		require_once('php/' . $module . '.php');
	}else{
		echo json_error("No such module.");
		exit;
	}
	try{
		switch(strtolower($function))
		{
			case 'loggedin':
			{
				echo json_encode(array('loggedin'=>
					$u->GetLoggedIn()));
				break;
			}
			case 'login':
			{
				$username = $vars[0];
				$password = $vars[1];
				echo json_encode(array('login'=>
					$u->LoginNormal($username, $password)));
				break;
			}
			case 'register':
			{
				foreach($vars as $var)
				{
					if(!$var)
					{
						return false;
					}
				}
				$username = $vars[0];
				$password = $vars[1]; 
				$email = $vars[2]; 
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				    return false;
				}
				$firstname = $vars[3]; 
				$lastname = $vars[4];
				echo json_encode(array('register'=>
					$u->RegisterNormal($username, $password, 
						$email, $firstname, $lastname)));
				//login automatically
				$u->LoginNormal($username, $password);
				//Registration should also kick off a new Site
				require_once('php/sites.php');
				$s = new Site();
				$s->AutoGenerateSite();

				break;
			}
			case 'logout':
			{
				unset($_SESSION['LoginNormal']);
				echo json_encode(array('logout'=>true));
				break;
			}
			case 'uid':
			{
				echo json_encode(array('uid'=>$u->GetCurrentUserID()));
				break;
			}
			default:
			{
				throw new Exception('Invalid request.');
			}
		}
	}catch(Exception $e)
	{
		echo json_error('Processing error: ' . $e->getMessage());
	}