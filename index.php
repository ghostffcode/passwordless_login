<?php
// This is the sample file to run it

include 'inc/passwordless.php';

$p = new passwordless('json');

if (isset($_GET['code'])) {
  echo $p->validate($_GET['code']);
} else if (isset($_GET['email'])) {
  echo $p->sendCode($_GET['email']);
} else {
echo 'Submit value (Email or code) using php url get for trial';
}

//unset($_SESSION['loggedin']);
?>
