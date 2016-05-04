# passwordless
Simple php passwordless login system

## How to use
```php
<?php
// include class file to your working script
include 'inc/passwordless.php';

// Create an instance of the passwordless class.
// It takes one argument, which can be either json or array else, it defaults to json
$p = new passwordless('json');

if (isset($_GET['code'])) {
  echo $p->validate($_GET['code']);
} else if (isset($_GET['email'])) {
  echo $p->sendCode($_GET['email']);
} else {
echo 'Submit value (Email or code) using php url get for trial';
}
?>
```
