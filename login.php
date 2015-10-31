<?php 

	include dirname(__FILE__) . '/partials/pageCheck.php';
	include_once dirname(__FILE__) . '/classes/core/database.php';
	include_once dirname(__FILE__) . '/classes/core/utility.php';
	include_once dirname(__FILE__) . '/classes/core/user.php';

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Food Finder: Login</title>
        <link rel="stylesheet" type="text/css" href="static/css/styles.css" />	
    
		<script src="js/jquery-1.10.2.js"></script>		
		<script src="js/mustache.js"></script>
		<script src="js/core.js"></script>
    </head>
    <body>
    	<?php 
    		Utility::debug("login.php: logging in user.",5);
    		$username = '';
			$password = '';
			$remember_choice = false;
			$successURL = 'index.php';
    		if (isset($_POST['username'])) {
    			$username = trim(htmlspecialchars($_POST['username']));
    		}
    		if (isset($_POST['password'])) {
    	    	$password = trim(htmlspecialchars($_POST['password']));
			}
			if (isset($_POST['remember_me'])) {
				$remember_choice = trim($_POST["remember_me"]);
				}
			if (isset($_POST['successURL'])) {
				$successURL = $_POST['successURL'];
			}

			$errorMessage = '';    	
    		// attempt to login user;
    		if (strlen($username)<=0 || strlen($password)<=0) {
    			$errorMessage = "You must enter both a username and password.";	
    		}
			else {
				// try to create a new user object
				try {
					$user = new User(0);
					$user->validateUser($username, $password, $tenantID);
					Utility::debug('User ' . $user->name . 'logged in succesfully.',5);
					
					// initiate new user session
					$_SESSION['userID'] = $user->id;
					$_SESSION['user_screenname'] = $user->name;
					header( 'Location: ' . $successURL);
					}
				catch (Exception $e) {	
					$errorMessage = $e->getMessage();
					Utility::debug('Login failed: ' . $errorMessage,5);
				}
			}
    
    	?>
    	<hr/>
    	<?php if(strlen($errorMessage)>0) { ?>
    		<div class="edit">
    			<div class="message">
    				<?php echo $errorMessage; ?>
    			</div>
    		</div>
    		<div class="login_form">
				<form action="login.php" method="post">
					<div class="panel">
						Username: <input id="txtUsername" name="username" type="text" placeholder="Username"></input>								
					</div>
					<div class="panel">
						Password: <input id="txtPassword" name="password" type="password" placeholder="Password"></input>								
					</div>
					<div class="panel">
						<input type="submit" class="btn primary_button" value="Submit"/>
						<input type="button" class="btn" value="Cancel" onclick="hideElement('topnav_login');"/>	
					</div>
				</form>
			</div>
    	<?php } ?>	
    </body>
</html>
    