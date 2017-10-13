        <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST'){

        if(isset($_POST['error']) && ($_POST['error'] >= 1 && $_POST['error'] <= 5)){
        switch($_POST['error']) {
                case 2:
                    echo '<p>Permission Denied!</p>';
                    break;
                case 3:
                    echo '<p>Position Unavailable!</p>';
                    break;
                case 4:
                    echo '<p>System Unresponsive!</p>';
                    break;
                case 5:
                    echo '<p>Unknown Error!</p>';
                    break;
                default:
                    echo '<p>Service is not Available!</p>';
            }
        }
        if(isset($_POST['latitude']) && isset($_POST['longitude'])){
                 $userLat = $_POST['latitude'];
                 $userLng = $_POST['longitude'];
            } 
        }else{
            header('location:index.php');
            exit;
        }
        ?>
        <!DOCTYPE html>
        <html>
          <head>
          <meta charset="UTF-8">
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js" type="text/javascript"></script>
          <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
          crossorigin="anonymous" type="text/javascript"></script>
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">
          <link href="myStyle.css" type="text/css" rel="stylesheet">
          </head>
          <body class="bg-purple">
          <div class="container-fluid">
             <div class="row">
                 <div class="col-sm-6 padding-y-s bg-purple">
                      <h3>Montreal Don't Eat Here</h3>    
                 </div>
                 <div class="col-sm-6 padding-y-s bg-purple">
                       <h3 id="message">You can eat Anywhere</h3>
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
                       <ul id="listRestos"></ul>               
                        <script>
            function initMap() {
            <?php
                echo "var user = {lat: $userLat , lng: $userLng };";
            ?>
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 14,
                center: user
             });
                          
              <?php
                function findClosestRestos($currentLat, $currentLng){
                    include('PDOConnection.php');
      try{
          $pdo=new PDO("pgsql:dbname=$dbname;host=$serverName;port=$port;sslmode=require",$user,$password);
          $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
          $sql = "SELECT DISTINCT owner,category,establishment,address,city_postal,lat,long,(6371 * acos(cos(radians($currentLat)) * cos(radians(lat)) * cos(radians(long) - radians($currentLng) ) + sin(radians($currentLat)) * sin(radians(lat)))) AS distance
              FROM restaurant
              GROUP BY id,owner,category,establishment,address,city_postal,lat,long,distance
              HAVING (6371 * acos(cos(radians($currentLat)) * cos(radians(lat)) * cos(radians(long) - radians($currentLng)) + sin(radians($currentLat)) * sin(radians(lat)))) < 25
              ORDER BY distance asc
              LIMIT 10";
          $preparedStatement = $pdo->prepare($sql);
          $preparedStatement->execute();
          $badrestos = $preparedStatement->fetchAll();
          return $badrestos;
        } catch (PDOException $e){
          echo $e->getMessage();
        } finally {
          unset($pdo);
        }
    }

                $badrestos = findClosestRestos($userLat, $userLng);
                if(count($badrestos) != 0){
                foreach( $badrestos as $row) {
                    $lat = $row['lat'];
                    $lng = $row['long'];
                    $establishment = $row['establishment'];
                    echo "var marker = new google.maps.Marker({
                         position: { lat:$lat, lng:$lng },
                         map: map,
                        title: '$establishment'
                        }); ";
                    
                }
                }
                                
                ?> 
            }
                        
        var list = document.getElementById('listRestos');  
        list.innerHTML = "<?php
                foreach($badrestos as $row){
                    echo '<li>' . $row['establishment'] . '</li>';
                }
            ?>";
        var msg = document.getElementById('message'); 
        msg.innerHTML = "<?php  echo ((count($badrestos)===0)?'You can eat anywhere':'Avoid these restaurants!') ?>";

            </script>
                     </div>
                 </div>
             </div> 
               </div>
            
            <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyChIKbAPmhu962oWCXZlpZzx2fVeKT01AQ&callback=initMap">
            </script>
            <footer class="text-center bg-purple padding-y-s">
                <h5>Copyright © 2017 The Spartans</h5>
            </footer> 
        </body>
        </html>

