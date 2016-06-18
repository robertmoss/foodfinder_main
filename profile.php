<?php 
	include dirname(__FILE__) . '/core/partials/pageCheck.php';
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
        <script type="text/javascript" src="js/workingPanel.js"></script>
        <script type="text/javascript" src="js/profile.js"></script>
        
    </head>
    <body>
    	<div id="maincontent">
    		<div id="outer">
	    		<?php include('partials/header.php');?>
    			<div id="main" class="container">
    				<h1><?php echo $user->name; ?></h1>
    				<h3><?php 
    				    if ($userID==1) {
    				        echo '<span class="label label-primary">Da Supa User!</span>';
    				    }
    				    foreach($user->getTenantRoles($tenantID) as $role) {?>
    				    <span class="label label-primary">
    				        <?php echo ucwords($role); ?>
    				    </span>
    				    <?php } ?>
    				</h3>
    				<div id="profilePanel" class="panel panel-default">
    				    <div class="panel-heading">Your Profile</div>
    				    <div id="profileBody" class="panel-body">
    				        <?php include 'core/partials/workingPanel.php'; ?>
    				        <div id="profileView" class="collapse in"></div>
    				        <div id="profileEdit" class="collapse">
    				            <form id="frmProfile" class="form-horizontal" action="core/service/user.php" method="post" role="form">
                                    <div class="edit">
                                        <input type="hidden" id="txtProfileUserId" name="id" value="<?php echo $userID; ?>"/>
                                        <input type="hidden" id="txtProfileUsername" name="email" value="<?php echo $user->email; ?>">
                                        <input type="hidden" name="txtProfileTenantId" value="<?php echo $tenantID; ?>"/>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="txtName">Name:</label>
                                            <div class="col-sm-4">
                                                <input id="txtName" name="name" type="text" class="form-control" placeholder="Your name (for display on site)" value="<?php echo $user->name; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="txtTwitterHandle">Twitter Handle:</label>
                                            <div class="col-sm-4">
                                                <input id="txtTwitterHandle" name="twitterHandle" type="text" class="form-control" placeholder="Your Twitter Handle" value="<?php echo $user->twitterHandle; ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label" for="txtBio">Your Bio:</label>
                                            <div class="col-sm-4">
                                                <textarea rows="4" cols="100" id="txtBio" name="bio" type="text" class="form-control" placeholder="A little about yourself"><?php echo $user->bio; ?></textarea> 
                                            </div>
                                        </div>
                                    </div>
                                </form>
    				        </div>
    				        
                            <button id="btnEditProfile" class="btn btn-default" onclick="editProfile();">
                                <span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit
                            </button>
                            <button id="btnCancelProfileEdit" class="btn btn-default hidden" onclick="cancelProfileEdit();">
                                <span class="glyphicon glyphicon-remove"></span>&nbsp;Cancel
                            </button>
                            <button id="btnSaveProfile" class="btn btn-primary hidden" onclick="saveProfile();">
                                <span class="glyphicon glyphicon-ok"></span>&nbsp;Save
                            </button>
                            <div id="profileMessage" class="hidden">
                                <p>message here</p>
                            </div>

    				    </div>
    				</div>
                    <div><button class="btn btn-default" data-toggle="collapse" data-target="#passwordPanel">Change Password</button>
                    </div>    
                    <div id="passwordPanel" class="panel panel-default collapse">
                      <div class="panel-heading">
                        <h3 class="panel-title">Change Password</h3>
                      </div>
                      <div class="panel-body">
                            <form id="frmChangePassword" class="form-horizontal" data-toggle="validator" action="core/service/user.php?action=changePass" method="POST" onsubmit="changePassword(); return false;">
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
	        	<script>showWorkingPanel('Loading profile...');</script>
        		<?php include("partials/footer.php")?>     		
        	</div>
        </div>
    </body>
</html>
    