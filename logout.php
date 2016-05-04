<?php

include 'inc/passwordless.php';
$p = new passwordless('json');
// Logout the user
$p->logOut();

?>
