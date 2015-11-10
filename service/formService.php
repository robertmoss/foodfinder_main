<?php

	include dirname(__FILE__) . '/../partials/pageCheck.php';
	include_once dirname(__FILE__) . '/../classes/core/database.php';
	include_once dirname(__FILE__) . '/../classes/core/utility.php';

	if (isset($_GET["type"])) {
		$type=$_GET["type"];
	}
	else {
		header(' ', true, 400);
		echo 'Type is required.';
		die();
	}
	
	Utility::debug('Form service invoked for type:' . $type . ', method=' . $_SERVER['REQUEST_METHOD'], 9);
	
	$knowntypes = array('location','link','media');
	if(!in_array($type,$knowntypes,false)) {
		// unrecognized type requested can't do much from here.
		header(' ', true, 400);
		echo 'Unknown type: ' . $type;
		die();
	}
	
	$classpath = '/../classes/'; 
	$coretypes = array();
	if(in_array($type,$coretypes,false)) {
		// core types will be in core subfolder
		$classpath .= 'core/';
	}
	
	// include appropriate dataEntity class & then instantiate it
	$classfile = dirname(__FILE__) . $classpath . $type . '.php';
	if (!file_exists($classfile)) {
		header(' ', true, 500);
		Utility::debug('Unable to instantiate class for ' . $type . ' Classfile does not exist.', 1);
		echo 'Internal error. Unable to process entity.';
		die();
	}
	include_once $classfile;
	$classname = ucfirst($type); 	// class names start with uppercase
	$class = new $classname($userID,$tenantID);
	
	$id=0; 
	if (isset($_GET["id"])) {
		$id = $_GET["id"];
	}
	
	$entity='';
	if ($id>0) {
		$entity = $class->getEntity($id,$tenantID,$userID);
	 	}
?>
	<form id="<?php echo $type; ?>Form" class="form-horizontal" action="<?php echo $class->getDataServiceURL(); ?>" method="post" role="form">
		<div class="edit">
			<input type="hidden" id="<?php echo $type; ?>id" name="id" value="<?php echo $id; ?>"/>
			<input type="hidden" name="tenantid" value="<?php echo $tenantID; ?>"/>
			<input type="hidden" id="type" name="type" value="<?php echo $type; ?>"/>
			<?php
			$parentid=0;
			Utility::renderForm($class, $entity, $id, $tenantID, $parentid);
			?>	        			
		</div>
	</form>
