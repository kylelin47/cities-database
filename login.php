<?php
include "database.php";

 $User_username = $_POST['username'];
 $User_password = $_POST['password'];
 
if($User_username && $User_password ){
        $connection = oci_connect('kylin','citiesdatabase','//oracle.cise.ufl.edu/orcl');
         
         if($connection){
            $sql_User =oci_parse($connection, "SELECT * FROM login");
            oci_execute($sql_User);
        
        $temp_result = FALSE;
        while(($row = oci_fetch_assoc($sql_User))!= FALSE) {
             if(($User_username == $row['USERNAME'])&&($User_password == $row['PASSCODE'])){
                 $temp_result = TRUE;
                 $FN = $row['FNAME'];
                 $LA = $row['LNAME'];
             }
         }
         if($temp_result == TRUE){
             echo '<html>';
             echo "Welcom Back:"." &nbsp". $FN.",".$LA;
             echo '<br>';
             echo '<a href = "index.html" >Click this Link back to homePage</a>';
             echo '</html>';
         }
         else{
             echo '<html>';
             echo '<a href = "User.html" >Invalid User and Password information</a>';
             echo '</html>';
         }
         }
         }

else{
	die('False input Or No input');
}
 //
// VERY important to close Oracle Database Connections and free statements!
//
oci_free_statement($sql_User);
oci_close($connection);
?>
