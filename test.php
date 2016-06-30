<?php 
    include dirname(__FILE__) . '/core/partials/pageCheck.php';
    $thisPage = 'test';
    
    // must be superuser to access this page
    if ($userID!=1) {
        header('Location: 403.php');
        die();
    }
    
echo '<p>' . $_SERVER['DOCUMENT_ROOT'];

echo '<p>' . $_SERVER['SERVER_NAME'];

phpinfo();

// send a test message

// The message
$message = "This is a test message\r\nLine 2\r\nLine 3";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Send
//mail('mossr19@gmail.com', 'Message from PHP Test', $message);

						
	
						
					