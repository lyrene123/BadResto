<?php

  /**
  * The DbManager class contains the necessary functions to use for inserting
  * and reading into the restaurant database
  */
  class DbManager{

    /**
    * The class constructor will simply create a DbManager instance
    */
    public function __construct() {

    }

    /**
    * Drops the restaurant table if it exists and re-creates the restaurant
    * table on the database.
    */
    public function createTables(){
      require_once('PDOConnection.php'); //file containing credentials
      try{
          $pdo=new PDO("pgsql:dbname=$dbname;host=$serverName;port=$port;sslmode=require",$user,$password);
          $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
          $query = 'DROP TABLE IF EXISTS restaurant;
                    CREATE TABLE restaurant (
                    id SERIAL UNIQUE,
                    owner varchar(80),
                    category varchar(80),
                    establishment varchar(80),
                    address varchar(80),
                    city_postal varchar(60),
                    lat numeric,
                    long numeric);';
          $pdo->exec($query);
        } catch (PDOException $e){
          echo $e->getMessage();
        } finally {
          unset($pdo);
        }
      }

      /**
      * Retrieves all restaurant records from the Montreal site url and
      * stores record in the database, in the restaurant table
      */
      public function fillTables(){
        //retrieve Montreal site into XML form
        $url = "http://donnees.ville.montreal.qc.ca/dataset/a5c1f0b9-261f-4247-99d8-f28da5000688/resource/92719d9b-8bf2-4dfd-b8e0-1021ffcaee2f/download/inspection-aliments-contrevenants.xml";
        $results = file_get_contents($url);
        $xml = new \DOMDocument();
        @$xml->loadXML($results);

        //retrieve all restaurant element and loop through each one of them
        $items = @$xml->getElementsByTagName('contrevenant');
        foreach ($items as $resto) {
          //get the necessary info for each restaurant
          $owner = $resto->getElementsByTagName('proprietaire')[0]->textContent;
          $category = $resto->getElementsByTagName('categorie')[0]->textContent;
          $establish = $resto->getElementsByTagName('etablissement')[0]->textContent;
          $address = $resto->getElementsByTagName('adresse')[0]->textContent;
          $city = $resto->getElementsByTagName('ville')[0]->textContent;

          //get the longitude and latitude with Google API
          $geoLoc = $this->getGeoLocation($address);
          sleep(1);

          //if the getGeoLocation function returned an array, retrieve lat and long
          $lat; $long;
          if(is_array($geoLoc)){
            $lat = $geoLoc[0];
            $long = $geoLoc[1];
          }else{
            //if getGeoLocation function returned false, set lat and long to 0
            $lat = 0.0000;
            $long = 0.0000;
          }

          //add each record in the database
          $this->addRecord($owner,$category,$establish,$address,$city,$lat,$long);
        }
      }

      /**
      * Sends a request to the Google API service to find the latitude
      * and longitude of an address and returns the result as an array or boolean false
      * if none found.
      *
      * @param string $address The address which we want to get the lat and long
      * @return an array containing lat and long of the address or false if not found
      */
      private function getGeoLocation($address){
        //send the request and retrieve the result as XML
        $address = urlencode($address);
        $urlMap = "http://maps.google.com/maps/api/geocode/xml?address={$address}&sensor=false";
        $rsp = file_get_contents($urlMap);
        $rspXML = new \DOMDocument();
        @$rspXML->loadXML($rsp);

        //if status of response if OK, get the longitude and latitude
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

          //return longtitude and latitude as an array or false if none found
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

      /**
      * Adds a record to the restaurant table with the input values.
      *
      * @param string $owner The restaurant owner name
      * @param string $cat The restaurant category
      * @param string $establish The name of the restaurant
      * @param string $address The address of the restaurant
      * @param string $city The city in which the restaurant is located
      * @param string $lat The latitude of the restaurant
      * @param string $long The longitude of the restaurant
      */
      private function addRecord($owner, $cat, $establish, $address, $city, $lat, $long){
        require_once('PDOConnection.php'); //file containing credentials
          try{
            $pdo=new PDO("pgsql:dbname=$dbname;host=$serverName",$user,$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $query = $pdo->prepare("INSERT INTO restaurant(owner,category,
                                      establishment,address,city_postal,lat,long)
                                      VALUES (?,?,?,?,?,?,?);");
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

      /**
      * Finds the closest restaurants from the restaurant table in the database
      * and returns the list of 10 closes restaurants in a 25 km radius
      *
      * @param float $currentLat The latitude of the position of the user
      * @param float $currentLng The longitude of the position of the user
      */
      function findClosestRestos($currentLat, $currentLng){
        require_once('PDOConnection.php');
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
  }
?>
