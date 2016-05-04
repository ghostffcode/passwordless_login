<?php
/**
 * This is a simple user authentication system without password
 * It only works and asks the user for the code sent to their email address to work
 */

class passwordless {

  private $format = 'json';
  private $trial = 3;
  private $show = false;

  function __construct($f = '', $sh = false) {
    // Start a user session on class instance
    session_start();
    // Check specified format
    if (!empty($f) && $f == 'array') {
      $this->format = 'array';
    }
    // Handle if code should be returned as part of result
    ($sh) ? $this->show = true : $this->show = false;
  }

  public function sendCode ($email = '') {
    $code = '';
    // This function will email the code to the user
    $res = ['info' => '', 'email' => $email, 'emailSuccess'=> false];
    // Chech=k if the email isset and not null
    if (!empty($email)) {
      // Check if the value entered is a valid email address
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Add success as first value of the array
        $res['info'] = 'success';
        // Generate the code for the user and add to session
        $code = $this->genCode(5);
        // Send the code as email to the user
        if ($this->sendEmail($email, $code)) {
          $_SESSION['code'] = $code;
          $_SESSION['email'] = $email;
          // Set a trial counter in the session
          $_SESSION['trial'] = 0;
          $res['emailSuccess'] = true;
          ($this->show) ? $res['code'] = $code : $res['code'] = null;
        } else {
          $_SESSION['email'] = $email;
          $res['emailSuccess'] = false;
        }
      } else {
        // Let user know that the email is invalid
        $res['info'] = 'Invalid Email';
      }
    } else {
      // Let user know that no email was submitted
      $res['info'] = 'No Input';
    }

    // Check format and return selected format
    if ($this->format == 'array') {
      return $res;
    } else {
      return json_encode($res);
    }
  }

  private function secure($min, $max) {
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd >= $range);
    return $min + $rnd;
}

  private function genCode ($len) {
    // Generate the verification code for the user
    $token = "";
    //$num = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    //$num.= "abcdefghijklmnopqrstuvwxyz";
    $num = "0123456789";
    $max = strlen($num) - 1;
    for ($i=0; $i < $len; $i++) {
        $token .= $num[$this->secure(0, $max)];
    }
    return $token;
  }

  private function sendEmail ($email = '', $code = '') {
    $res = false;
    // This will handle the validation code email sending
    $msg = 'Your code is'. $code;
    $subject = 'Passwordless Login code';

    // Check if the email was a success and return true, else return false
    if (mail($email,$subject,$msg)) {
      $res = true;
    }
    return $res;
  }

  public function validate ($code = '') {
    $res = ['info'=> 'error', 'loggedin' => false, 'detail'=> null];
    // This function will validate the code emailed to the user

    // Check to be sure is not logged in already
    if (!isset($_SESSION['loggedin'])) {
      // Check if the input is a number and session is set
      if (is_numeric($code) && isset($_SESSION) && (isset($_SESSION['code']))) {
        // Check if code matches the code in session
        if ($code === $_SESSION['code']) {
          // if true, log the user in by setting up necessary session values
          $_SESSION['loggedin'] = 1;
          // Remove code from session or not, whatever
          unset($_SESSION['code']);
          // Remove trial counter from session
          unset($_SESSION['trial']);
          // Setup the return array variable
          $res['info'] = 'success';
          $res['loggedin'] = true;
        }
      } else {

        // Each time it fails, increment the trial times by 1
        // but first check if the a trial has been initiated
        if (isset($_SESSION['trial'])) {
          // Check if the number is still within limit
          if ($_SESSION['trial'] <= $this->trial) {
            $_SESSION['trial'] += 1;
          }
        } else {
          $_SESSION['trial'] = 0;
        }

        // Also check if the limit has been reached
        if ($_SESSION['trial'] > $this->trial) {
          // If limit has been reached, resend an email
          if (isset($_SESSION['email'])) {
            $res['detail'] = 'You have reached your limit. Get another code.';
          } else {
            $res['detail'] = 'You have reached your limit';
          }
          // Setup the return array variable
          $res['info'] = 'error';
          $res['loggedin'] = false;
        } else {
          $res['info'] = 'error';
          $res['loggedin'] = false;
          $res['detail'] = 'Code you entered is invalid/expired';
        }
      }
    } else {
      $res['info'] = 'error';
      $res['loggedin'] = true;
      $res['detail'] = 'You\'ve already been logged in';
    }

    if ($this->format == 'array') {
      return $res;
    } else {
      return json_encode($res);
    }
  }

  public function loggedIn () {
    // This function will check and see if the user is logged in
    $res = false;
    // Check if the session isset
    if (isset($_SESSION)) {
      // If true, check if the loggedin session variable is one of them
      if (isset($_SESSION['loggedin']) && ($_SESSION['loggedin'] === 1)) {
        $res = true;
      }
    }
    return $res;
  }

  public function logOut ($loc = 'index.php') {
    // This will log the user out
    // remove all session variables
    session_unset();
    // destroy the session
    session_destroy();

    header("Location: $loc");
  }

}
 ?>
