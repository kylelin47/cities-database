<?php
include "database.php";

                         
$UserLogin = $_POST['username'];
$UserPass = $_POST['password'];
 
 if ($UserLogin && $UserPass){
     $connection = oci_connect($username, $password, $connection_string) or die ("False connection");
     ori_select_db('Login') or die("False DB");
 }
 
 else {
  die("No Connection");                        
                           
 }
 


 //
// VERY important to close Oracle Database Connections and free statements!
//
oci_free_statement($statement);
oci_close($connection);
?>
