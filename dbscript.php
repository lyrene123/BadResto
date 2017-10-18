<?php
  /**
  * The following script creates a DbManager instance in order to create
  * and fill the restaurant table with data in the database
  */
  require_once('DbManager.php');
  $manager = new DbManager();
  $manager->createTables();
  $manager->fillTables();
?>
