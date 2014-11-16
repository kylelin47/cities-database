


<?php

session_start();
//include "database.php";

 $User_username = $_POST['username'];
 $User_password = $_POST['password'];

 $_SESSION['NAME'] = $User_username;
 $_SESSION["PASS"] = $User_password;

if($User_username && $User_password ){
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
             
             if($is_admin == 0){
             echo '<html>';
             echo "Welcom Back:"." &nbsp". $FN.",".$LA;
             echo '<br>';
             echo '<a href = "UserOnly.php" >Click this Link back to homePage</a>';
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

?>

