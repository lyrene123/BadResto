<?php
  createTables();
  fillTables();
?>



<?php

  function createTables(){
    include('PDOConnection.php');
    try{
        $pdo=new PDO("pgsql:dbname=$dbname;host=$serverName",$user,$password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $query = 'DROP TABLE IF EXISTS restaurant;
                  CREATE TABLE restaurant (
                  owner varchar(80),
                  category varchar(80),
                  establishment varchar(80),
                  address varchar(80),
                  city_postal varchar(60) NOT NULL primary key,
                  lat numeric,
                  long numeric);';
        $pdo->exec($query);
      } catch (PDOException $e){
        echo $e->getMessage();
      } finally {
        unset($pdo);
      }
    }

    function fillTables(){
      $url = "http://donnees.ville.montreal.qc.ca/dataset/a5c1f0b9-261f-4247-99d8-f28da5000688/resource/92719d9b-8bf2-4dfd-b8e0-1021ffcaee2f/download/inspection-aliments-contrevenants.xml";
      $results = file_get_contents($url);
      $xml = new \DOMDocument();
      @$xml->loadXML($results);

      $items = @$xml->getElementsByTagName('contrevenant');
      foreach ($items as $resto) {

        $owner = $resto->getElementsByTagName('proprietaire')[0]->textContent;
        $category = $resto->getElementsByTagName('categorie')[0]->textContent;
        $establish = $resto->getElementsByTagName('etablissement')[0]->textContent;
        $address = $resto->getElementsByTagName('adresse')[0]->textContent;
        $city = $resto->getElementsByTagName('ville')[0]->textContent;

        $geoLoc = getGeoLocation($address);
        sleep(1);
        $lat = $geoLoc[0];
        $long = $geoLoc[1];

        addRecord($owner,$category,$establish,$address,$city,$lat,$long);
      }
    }

    function getGeoLocation($address){
      $address = urlencode($address);
      $urlMap = "http://maps.google.com/maps/api/geocode/xml?address={$address}&sensor=false";
      $rsp = file_get_contents($urlMap);
      $rspXML = new \DOMDocument();
      @$rspXML->loadXML($rsp);

      $status = @$rspXML->getElementsByTagName('status')[0]->textContent;
      if($status == 'OK'){
        $lat = @$rspXML->getElementsByTagName('result')[0]
                      ->getElementsByTagName('geometry')[0]
                      ->getElementsByTagName('location')[0]
                      ->getElementsByTagName('lat')[0]->textContent;

        $long = @$rspXML->getElementsByTagName('result')[0]
                      ->getElementsByTagName('geometry')[0]
                      ->getElementsByTagName('location')[0]
                      ->getElementsByTagName('lng')[0]->textContent;

        if($lat && $long){
          $location = array();
          array_push($location,$lat,$long);
          return $location;
        }else{
          return false;
        }
      }else{
        return false;
      }
    }


    function addRecord($owner, $cat, $establish, $address, $city, $lat, $long){
      include('PDOConnection.php');
      $isDuplicate = checkDuplicates($city);

      //if no duplicates, add record
      if(!$isDuplicate){
        try{
          $pdo=new PDO("pgsql:dbname=$dbname;host=$serverName",$user,$password);
          $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

          $query = $pdo->prepare("INSERT INTO restaurant(owner,category,establishment,address,city_postal,lat,long) VALUES
                    (?,?,?,?,?,?,?);");
          $query->bindParam(1,$owner);
          $query->bindParam(2,$cat);
          $query->bindParam(3,$establish);
          $query->bindParam(4,$address);
          $query->bindParam(5,$city);
          $query->bindParam(6,$lat);
          $query->bindParam(7,$long);
          $query->execute();
        } catch (PDOException $e){
          echo $e->getMessage();
        } finally {
          unset($pdo);
        }
      }
    }

    function checkDuplicates($citypostal){
      include('PDOConnection.php');
      try{
        $pdo=new PDO("pgsql:dbname=$dbname;host=$serverName",$user,$password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $query = $pdo->prepare("SELECT * FROM restaurant WHERE city_postal = ?;");
        $query->bindParam(1, $citypostal);
        $query->execute();

        if($query->rowCount() > 0){
          return true; //duplicates existing
        }else{
          return false; //no duplicates
        }
      } catch (PDOException $e){
        echo $e->getMessage();
      } finally {
       unset($pdo);
      }
  	}



?>
