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
          function initMap() {
            <?php echo "var user = {lat: $userLat , lng: $userLng };"; ?>
              var map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 15,
                        center: user
                      });
            <?php
              //create an instance of the DbManager and call the findClosestRestos function
              require_once('DbManager.php');
              $manager = new DbManager();
              $badrestos = $manager->findClosestRestos($userLat, $userLng);

              //display each resto found on the map if there are restos found
              if(count($badrestos) != 0){
              //  $count = count($badrestos);
              //  echo "the count is $count";
                foreach( $badrestos as $row) {
                  $lat = $row['lat'];
                  $lng = $row['long'];
                  $establishment = $row['establishment'];
                  $establishment = addslashes($establishment);
                  echo "var marker = new google.maps.Marker({
                        position: { lat:$lat, lng:$lng },
                        map: map,
                        title: '$establishment'
                      });";
                  }
                }
              ?>
            }
            //list on the side of the map the names of the restaurants as a list
            var list = document.getElementById('listRestos');
            list.innerHTML = "<?php
                                foreach($badrestos as $row) {
                                  echo '<li class=\"list-item list-item-danger\">' .
                                      '<img src=\"radioactive_icon.png\" align=\"left\">'. $row['establishment'] . '</li>';
                                }
                              ?>";
            //display the message to avoid these restos if there are, if none found, display another message
            var msg = document.getElementById('message');
            msg.innerHTML = "<?php  echo ((count($badrestos)===0)?'You can eat anywhere!':'Avoid these restaurants!') ?>";
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
