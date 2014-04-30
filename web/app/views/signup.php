<html>
	<head>
		<meta name="viewport" content="width=400"/>
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<style type="text/css">
			body, html {
				padding: 0;
				margin: 0;
				font-family: Verdana, Arial, sans-serif;
			}
			.center {
				width: 400px;
				margin: 0 auto;
			}
			.container {
				background: #008110;
				-moz-border-radius: 15px;
				-webkit-border-radius: 15px;
				border-radius: 15px;
				border: 3px solid #333;
				margin: 10px;
				overflow: hidden;
			}
			.bod {
				padding: 0 20px 20px;
			}
			.header {
				float: left;
				width: 100%;
				background: #444;
			}
			h1 {
				color: #FFF;
				margin: 10px 0;
				padding: 0;
				text-align: center;
			}
			h3 {
				margin-bottom: 0;
				text-align: center;
				float: left;
			}
			label {
				width: 100%;
				float: left;
				margin-top: 10px;
				font-weight: bold;
			}
			input[type="text"], input[type="password"], input[type="submit"] {
				width: 100%;
				float: left;
				color: #444;
				margin-top: 10px;
				font-size: 20px;
				padding: 3px;
				background: #FFF;
			}
			input[type="submit"] {
				background: #444;
				margin-top: 30px;
				-moz-border-radius: 10px;
				-webkit-border-radius: 10px;
				border-radius: 10px;
				border: 3px solid #333;
				color: #FFF;
				font-weight: bold;
			}
			.clr {
				clear: both;
			}
			ul {
				padding: 0;
				margin: 0;
				list-style: none;
			}
			li {
				padding: 0;
				margin: 0;
			}
			ul.errors {
				margin-top: 10px;
				color: #fff;
				float: left;
			}
		</style>
	</head>
	<body onload="window.scrollTo(0, 1);">
		<div class="center">
			<div class="container">
				<div class="header">
					<h1>Spike App Signup</h1>
				</div>
				<div class="bod">
				<?
					if (isset($errors)) {
						print '<ul class="errors">';
						foreach ($errors->all('<li>:message</li>') as $error)
						{
						    print $error;
						}
						print '</ul>';
					}
				 ?>
				<? if($body == 'profile'): ?>
					
					<form action="" method="post">
						
						<label for="email">Email</label>
						<input type="text" disabled="disabled" value="<?= @$values['email'] ?>" name="email" />
						
						<label for="first_name">First Name</label>
						<input type="text" value="<?= @$values['first_name'] ?>" name="first_name" id="first_name" />
						
						<label for="last_name">Last Name</label>
						<input type="text" value="<?= @$values['last_name'] ?>" name="last_name" id="last_name" />
						
						<label for="professional_title">Professional Title</label>
						<input type="text" value="<?= @$values['professional_title'] ?>" name="professional_title" id="professional_title" />
						
						<label for="password">Password</label>
						<input type="password" value="" name="password" id="password" />
						
						<label for="repeat_password">Repeat Password</label>
						<input type="password" value="" name="repeat_password" id="repeat_password" />
						
						<input type="hidden" value="<?= @$values['token'] ?>" name="token" />
						<input type="hidden" value="<?= @$values['email'] ?>" name="email" />
						<input type="submit" value="Signup" />
					</form>
				<? elseif($body == 'success'): ?>
					<h3>Thanks for signing up!
					<br> You are now ready to use the Spike app.</h3>
				<? elseif($body == 'check_mail'): ?>
					<h3>Check your email, and follow the link we sent you to complete your sign up.</h3>
				<? else: ?>
					<?= (isset($badToken))?'<ul class="errors">Your token has expired. Not to worry, you may send yourself a new one here.</ul>':'' ?>
					<form action="/signup" method="post">
						<label for="email_start">Email</label>
						<input type="text" value="<?= @$values['email_start'] ?>" name="email_start" />
						<input type="submit" value="Continue" />
					</form>
				<? endif; ?>
					<br class="clr">
				</div>
			</div>
		</div>
	</body>
</html>