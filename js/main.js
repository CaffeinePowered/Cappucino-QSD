$(function(){

	var uid;

	$('#logout').click(function(){
		logoutHandler();
	});

	$.getJSON('ajax/users/loggedin/check', function(data)
	{
		if(!(data.loggedin == true))
		{
			loginBox();
		}else{
			getCurrentUID();
			startEditor();
		}
	});

	function registerBox()
	{
		console.log('Displaying Registerbox');
		$.ajax({
			url:'ajax/static/register'
		}).done(function(html){
			$('#editor').html(html);
			$('#regbackbtn').click(loginBox);
			$('#doregisterbtn').click(registerHandler);
		});
	}

	function loginBox()
	{
		$.ajax({
			url:'ajax/static/loginbox'
		}).done(function(html){
			console.log('Displaying Loginbox');
			$('#editor').html(html);
			$('#registerbtn').click(registerBox);
			$('#loginbtn').click(loginHandler);
		});
	}

	function loginHandler()
	{
		var username = $('#username').val();
		var password = $('#password').val();
		$.getJSON('ajax/users/login/'+username+','+password,
			function(data){
				if(data.error == undefined && data.login != false)
				{
					uid = data.login;
					statusBarNotice('Login OK')
					startEditor();
				}else{
					statusBarError('Login failed.');
				}
			}
		);
	}

	function registerHandler()
	{
		var username = $('#username').val();
		var password = $('#password').val();
		var email = $('#email').val();
		var firstname = $('#firstname').val();
		var lastname = $('#lastname').val();
		{
			$.getJSON(
				'ajax/users/register/'
				+ username + ','
				+ password + ','
				+ email + ','
				+ firstname + ','
				+ lastname,
				function(data)
				{
					if(data.register != false)
					{
						statusBarNotice('Thanks for registering, ' + firstname +
							'! Taking you to the editor...');
						getCurrentUID();
						startEditor();
					}else{
						statusBarError('Someone with those details already exists!')
					}
				}
			);
		}
	}

	function logoutHandler()
	{
		$.ajax({
			'url':'ajax/users/logout/now'
		}).done(function(){
			document.location = '/sitebuilder/';
		});
	}

	function getCurrentUID()
	{
		$.getJSON('ajax/users/uid/get',
			function(data)
			{
				uid = data.uid;
			}
		);
	}

	function statusBar(message)
	{
		$("#statusbar").html(message);
	}

	function statusBarError(message)
	{
		$("#statusbar").css("background-color","red");
		$("#statusbar").css("color","white");

		statusBar(message);

		setTimeout(function(){
			$("#statusbar").css("background-color","white");
			$("#statusbar").css("color","black");			
		},1000);
	}
	function statusBarNotice(message)
	{
		$("#statusbar").css("background-color","darkgreen");
		$("#statusbar").css("color","white");

		statusBar(message);

		setTimeout(function(){
			$("#statusbar").css("background-color","white");
			$("#statusbar").css("color","black");			
		},1000);
	}

	function startEditor(uid)
	{
		alert("Editor Started!");
	}

});
