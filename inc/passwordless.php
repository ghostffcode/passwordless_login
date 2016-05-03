<?php
/**
 * This is a simple user authentication system without password
 * It only works and asks the user for the code sent to their email address to work
 */

class passwordless {

  private $format = 'json';

  function __construct($f = '') {
    // Start a user session on class instance
    session_start();
    // Check specified format
    if (!empty($f) && $f == 'array') {
      $this->format = 'array';
    }
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
          $res['emailSuccess'] = true;
        } else {
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
    // This will handle the validation code email sending

    return true;
  }

  public function validate ($code = '') {
    // This function will validate the code emailed to the user
    # code...
  }

  public function isLoggedIn () {
    // This function will check and see if the user is logged in
    # code...
  }

  public function logOut () {
    // This will log the user out
    # code...
  }


}
 ?>
