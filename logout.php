<?php

include 'inc/passwordless.php';

$p = new passwordless('json');

if ($p->logOut()) {
  header('Location: index.php');
}

?>
