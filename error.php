<?php
  if(isset($_GET['error']) && is_numeric($_GET['error'])){
    $errCode = $_GET['error'];
    switch($errCode) {
      case 2: echo 'Permission Denied!'; break;
      case 3: echo 'Position Unavailable!'; break;
      case 4: echo 'System Unresponsive!'; break;
      case 5: echo 'Unknown Error!'; break;
      case 6: echo 'Something bad happened!'; break;
      case 7: echo 'Cannot retrieve your current location'; break;
      default: echo 'Service is not Available!';
    }
  }
?>
