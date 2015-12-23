<?php 

	include dirname(__FILE__) . '/partials/pageCheck.php';
	include_once dirname(__FILE__) . '/classes/core/database.php';
	include_once dirname(__FILE__) . '/classes/core/utility.php';
	include_once dirname(__FILE__) . '/classes/core/user.php';
    $thisPage="login";
	
	Utility::debug("login.php: logging in user.",5);
	$username = '';
	$password = '';
	$remember_choice = false;
	$successURL = 'index.php';
    $requestMethod = $_SERVER['REQUEST_METHOD'];
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
    if (isset($_POST['source'])) {
        $source = $_POST['source'];
    }

	$errorMessage = '';
    $user = new User(0,$tenantID);    	
	// attempt to login user;
    if ($requestMethod=="GET") {
        // not a post, so don't try to load user        
    }
    elseif (strlen($username)<=0 || strlen($password)<=0) {
        $errorMessage = "You must enter both a username and password.";
	}
	else {
		// try to create a new user object
		try {
			$user->validateUser($username, $password, $tenantID);
			Utility::debug('User ' . $user->name . ' logged in succesfully.',5);
			
			// initiate new user session
			$_SESSION['userID'] = $user->id;
			$_SESSION['user_screenname'] = $user->name;
			header( 'Location: ' . $successURL);
			}
		catch (Exception $e) {	
			$errorMessage = $e->getMessage();
			Utility::debug('Login failed: ' . $errorMessage,9);
		}
	}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Food Finder: Login</title>
        <?php include("partials/includes.php"); ?>  
    </head>
    <body>
    	<?php 
                    include('header.php');

                ?>
    	<?php if(strlen($errorMessage)>0) { ?>
    		<div class="edit">
    			<div class="alert alert-danger">
    				<?php echo $errorMessage; ?>
    			</div>
    		</div>
    	<?php } 
            if ($user->id==0)  { ?>
    		<div class="login_form">
				<form action="login.php" method="post">
				    <input id="txtSource" name="source" type="hidden" value="login.php" />
					<div class="form-group">
						<label for="txtUserName">Username</label>
						<input id="txtUsername" name="username" type="text" class="form-control" placeholder="Username"></input>								
					</div>
					<div class="form-group">
						<label for="txtPassword">Password</label>
						<input id="txtPassword" name="password" type="password" class="form-control" placeholder="Password"></input>								
					</div>
					<div class="form-group">
						<input type="button" class="btn btn-default" value="Cancel" onclick="hideElement('topnav_login');"/>	
                        <input type="submit" class="btn btn-primary" value="Submit"/>
					</div>
				</form>
			</div>
    	<?php } ?>	
    </body>
</html>
    