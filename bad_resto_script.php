function initMap() {
  <?php echo "var user = {lat: $userLat , lng: $userLng };"; ?>
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
        $sql = "SELECT DISTINCT owner,category,establishment,address,city_postal,lat,long,(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(long) - radians(?) ) + sin(radians(?)) * sin(radians(lat)))) AS distance
                  FROM restaurant
                  GROUP BY id,owner,category,establishment,address,city_postal,lat,long,distance
                  HAVING (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(long) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) < 25
                  ORDER BY distance asc
                  LIMIT 10";
                  $preparedStatement = $pdo->prepare($sql);
                  $preparedStatement->bindParam(1,$currentLat);
                  $preparedStatement->bindParam(2,$currentLng);
                  $preparedStatement->bindParam(3,$currentLat);
                  $preparedStatement->bindParam(4,$currentLat);
                  $preparedStatement->bindParam(5,$currentLng);
                  $preparedStatement->bindParam(6,$currentLat);
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
                    foreach($badrestos as $row) {
                      echo '<li>' . $row['establishment'] . '<\/li>';
                    }
                  ?>";
var msg = document.getElementById('message');
msg.innerHTML = "<?php  echo ((count($badrestos)===0)?'You can eat anywhere!':'Avoid these restaurants!') ?>";
