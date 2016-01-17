<?php 
	include dirname(__FILE__) . '/partials/pageCheck.php';
	$thisPage="profile";
    if ($userID==0) {
        header('Location: 403.php');
        die();
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>User Profile</title>
        <?php include("partials/includes.php"); ?>
        <script type="text/javascript" src="js/validator.js"></script>
        <script type="text/javascript" src="js/profile.js"></script>
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('header.php');?>
    			<div id="main" class="container">
    				<h1><?php echo $user->name; ?></h1>
    				<p><?php 
    				    if ($userID==1) {
    				        echo '<h3><span class="label label-primary">Da Supa User!</span></h3>';
    				    }
    				    foreach($user->getTenantRoles($tenantID) as $role) {?>
    				    <span class="badge">
    				        <?php echo ucwords($role); ?>
    				    </span>
    				    <?php } ?>
    				</p>
                    <div><button class="btn btn-default" data-toggle="collapse" data-target="#passwordPanel">Change Password</button>
                    </div>    
                    <div id="passwordPanel" class="panel panel-default collapse">
                      <div class="panel-heading">
                        <h3 class="panel-title">Change Password</h3>
                      </div>
                      <div class="panel-body">
                            <form id="frmChangePassword" class="form-horizontal" data-toggle="validator" action="service/user.php?action=changePass" method="POST" onsubmit="changePassword(); return false;">
                                <input type="hidden" id="txtPasswordUserId" name="id" value="<?php echo $userID ?>">
                                <div class="form-group">
                                    <label for="txtOldPassword" class="col-sm-2 control-label">Old Password:</label>
                                    <div class="col-sm-2">
                                        <input type="password" name="original" class="form-control" id="txtOldPassword" placeholder="Old Password" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="txtNewPassword1" class="col-sm-2 control-label">New Password:</label>
                                    <div class="col-sm-2">
                                        <input type="password" name="new1" data-minlength="8" data-minlength-error="Passwords must be at least 8 characters long" class="form-control" id="txtNewPassword1" placeholder="New Password" required>
                                    </div>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group">
                                    <label for="txtNewPassword2" class="col-sm-2 control-label">Retype New Password:</label>
                                    <div class="col-sm-2">
                                        <input type="password" name="new2" data-minlength="8" data-minlength-error="Passwords must be at least 8 characters long" class="form-control" id="txtNewPassword2" placeholder="Retype New Password" data-match="#txtNewPassword1" data-match-error="The two new passwords must match." required>
                                    </div>
                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                    <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
                                    <span id="passwordStatus" class="sr-only">(warning)</span>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                          <button id="btnPasswordSubmit" type="submit" class="btn btn-primary">Update Password</button>
                                    </div>
                                </div>
                                <div id="password-message" class="alert alert-danger hidden">
                                        <a class="close_link" href="#" onclick="hideElement('password-message');"></a>
                                        <span id='password-message-text'>Message goes here.</span>
                                    </div>
                            </form>
                      </div>
                    </div>
	        	</div>	
        		<?php include("footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    