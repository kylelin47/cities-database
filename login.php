<?php
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
 
 if ($connection){
 	
 	
 }
 
 else 
 	die("No Connection");


 //
// VERY important to close Oracle Database Connections and free statements!
//
oci_free_statement($statement);
oci_close($connection);
?>
