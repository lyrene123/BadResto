<?php
  /**
  * The following script handles the submit action of the form in the index page
  * by checking if the form has been submitted through POST and if not, redirect
  * to the index page. The script will then handle the validation of the form's
  * input and then display the map only if the latitude and longitude are valid.
  */
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //the following will check if the form contains any valid error code
    if(isset($_POST['error']) && ($_POST['error'] >= 1 && $_POST['error'] <= 5)){
      //redirect to error page with the corresponding code in the query string
      $errCode = $_POST['error'];
      if(!is_numeric($errCode)){
        header("location:error.php?error=6");
        exit;
      }else{
        header("location:error.php?error=$errCode");
        exit;
      }
    }

    //the following will validate the lat and long of the user
    if(isset($_POST['latitude']) && isset($_POST['longitude'])){
      $inputLat = $_POST['latitude'];
      $inputLong = $_POST['longitude'];
      if(is_numeric($inputLat) && is_numeric($inputLong)){
        $userLat = $_POST['latitude'];
        $userLng = $_POST['longitude'];
        include_once("map.php");
      } else {
        //if lat and long not valid, error code is 7
        header("location:error.php?error=7");
        exit;
      }
    } else {
      //if lat and long not existing, error code is 7
      header("location:error.php?error=7");
      exit;
    }
  }else{
    header('location:index.php');
    exit;
  }
?>
