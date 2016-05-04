# passwordless Login
Simple php passwordless login system

## How to use
```php
<?php
// include class file to your working script
include 'inc/passwordless.php';

// Create an instance of the passwordless class.
// It takes one argument, which can be either json or array. Defaults to json if no argument is passed
$p = new passwordless('json');

// To send code to email and create session
$p->sendCode('trial@example.com');
?>
```
