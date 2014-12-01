<?php

session_start();
//include "database.php";

 $User_username = $_POST['Username'];
 $User_password = $_POST['Password'];

if(isset($User_username) && isset($User_password)){
        $connection = oci_connect('kylin','citiesdatabase','//oracle.cise.ufl.edu/orcl');
        $_SESSION['DataBaseConnect'] = $connection; 
         if($connection){
            $sql_User =oci_parse($connection, "SELECT * FROM login");
            oci_execute($sql_User);
        
        $temp_result = FALSE;
        while(($row = oci_fetch_assoc($sql_User))!= FALSE) {
             if(($User_username == $row['USERNAME'])&&($User_password == $row['PASSWORD'])){
                 $temp_result = TRUE;
                 $is_admin = $row['IS_ADMIN'];
                 $FN = $row['FNAME'];
                 $LA = $row['LNAME'];
             }
         }
         if($temp_result == TRUE){
             $_SESSION['NAME'] = $User_username;
             if($is_admin == 0){
             echo '<html>';
             echo "Welcome Back " . $FN ." " . $LA;
             echo '<br>';
             echo '<a href = "User.php" >Click this Link back to Users page</a>';
             echo '</html>';
             }
             else{
             echo '<html>';
             echo "Welcom Back Administrator:"." &nbsp". $FN.",".$LA;
             echo '<br>';
             echo '<a href = "Admin.php" >Click this Link back to ADMIN page</a>';
             echo '</html>';                 
                 
             }
         }
         else{
             echo '<html>';
             echo '<a href = "User.php" >Invalid User and Password information</a>';
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

?>

