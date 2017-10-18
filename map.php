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
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6 padding-y-s bg-purple">
        <h3>Montreal Don't Eat Here</h3>
      </div>
      <div class="col-sm-6 padding-y-s bg-purple">
        <h3 id="message"></h3>
      </div>
    </div>
  </div>

  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-10 no-padding">
        <div id="map"></div>
      </div>

    <div class="col-sm-2 bg-white">
      <div id="resto_info">
        <ul id="listRestos" class="list-group">
        <script type="text/javascript">
          <?php include_once("bad_resto_script.php"); ?>
        </script>
        </ul>
      </div>
    </div>
  </div>
</div>
<script async="" defer="defer" src=
  "https://maps.googleapis.com/maps/api/js?key=AIzaSyChIKbAPmhu962oWCXZlpZzx2fVeKT01AQ&amp;callback=initMap"
  type="text/javascript">
</script>
  <h5>Copyright &#169; 2017 The Spartans</h5>
</body>
</html>
