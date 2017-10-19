<?php
  /**
  * The following script will handle the error messages for different error
  * that occur depending on the number passed to the query string
  */
  if(isset($_GET['error']) && is_numeric($_GET['error'])){
    $errCode = $_GET['error'];
    $errormessage;
    switch($errCode) {
      case 2: $errormessage = 'Permission Denied!'; break;
      case 3: $errormessage = 'Position Unavailable!'; break;
      case 4: $errormessage = 'System Unresponsive!'; break;
      case 5: $errormessage = 'Unknown Error!'; break;
      case 6: $errormessage = 'Something bad happened!'; break;
      case 7: $errormessage = 'Cannot retrieve your current location'; break;
      default: $errormessage = 'Service is not Available!';
    }
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"
  type="text/javascript"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
  integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
  crossorigin="anonymous" type="text/javascript"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
  integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
  crossorigin="anonymous" type="text/css" />
  <link href="myStyle.css" type="text/css" rel="stylesheet" />
  <title>Montreal Don't Eat Here</title>
</head>
<body class="bg-purple">
  <h1 class="pos-center text-50"> <?php echo $errormessage ?></h1>
  <h5 class="pos-bottom">Copyright &#169; 2017 The Spartans</h5>
</body>
</html>
