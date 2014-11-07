<?php
include "database.php";

 $User_username = $_POST['username'];
 $User_password = $_POST['password'];
 
if($User_username && $User_password ){
	$connection = oci_connect($username,$password,$connection_string);
         
         if($Connection){
             echo '<html>';
             echo '<a href = "index.html" >Click this Link back to homePage</a>';
             echo '</html>';
         }
}
else{
	die('False input Or No input');
}
 //
// VERY important to close Oracle Database Connections and free statements!
//
oci_free_statement($statement);
oci_close($connection);
?>
