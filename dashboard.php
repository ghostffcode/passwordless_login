<?php

// Include class file
include 'inc/passwordless.php';

// Create an instance of the class
$p = new passwordless();

// Check if the user is logged in using the loggedin() method
if ($p->loggedIn()) {
  echo 'User Is Logged In And Can See Dashboard';
} else {
  echo 'You don\'t have authorization. Login/Redirect';
}

?>
