<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
    <?php
        $submit = $_POST['submit'];
        $FName = $_POST['FirstName'];
        $LName = $_POST['LastName'];
        $Username = $_POST['Username'];
        $Password = $_POST['Password'];
        $Password1 = $_POST['Password1'];
        $CITY = $_POST['City'];
        $COUNTRY =$_POST['Country'];
        $connection = oci_connect('kylin','citiesdatabase','//oracle.cise.ufl.edu/orcl');
        if($Password != $Password1){
            echo '<html>';
            echo '<a href = User.html>Click the following link to return to the previous page.</a>';
            echo '</html>';
            echo '<br>';
            die("Error: missmatched password");
            
        }
        else{
        if(!$connection){
            die("connection failed".$connection->connect_error);
        }
        $sql_User =oci_parse($connection, "SELECT Username FROM login");
        oci_execute($sql_User);
    
        /*
        if ($result->num_rows > 0) {
    // output data of each row
        while($row = $result->fetch_assoc()) {
         echo "Username: " . $row["Username"]. "<br>";
         }
         }else {
             echo "0 results";
        }
        */
        $temp_result = FALSE;
        while(($row = oci_fetch_assoc($sql_User))!= FALSE) {
            // echo $row['USERNAME'];
             if($Username == $row['USERNAME']){
                 $temp_result = TRUE;
             }
         }
        if($temp_result){
            echo "Username already exist <br>";
            echo '<html>';
            echo '<a href = index.php>Click the following link to return to the previous page.</a>';
            echo '</html>';
        }
        else{
            //determine is_admin
            
        if($COUNTRY == "admin"){
            $is_admin = 1;
        }
        else{
            $is_admin = 0;
        }
        oci_free_statement($sql_User);
        $sql = "INSERT INTO login (USERNAME,PASSWORD,FNAME,LNAME,CITY,COUNTRY,is_admin) 
               VALUES (:Username, :Password, :FName, :LName, :CITY, :COUNTRY, :is_admin)";   
        $sql_User =oci_parse($connection, $sql);
        oci_bind_by_name($sql_User, ":Username", $Username);
        oci_bind_by_name($sql_User, ":Password", $Password);
        oci_bind_by_name($sql_User, ":FName", $FName);
        oci_bind_by_name($sql_User, ":LName", $LName);
        oci_bind_by_name($sql_User, ":CITY", $CITY);
        oci_bind_by_name($sql_User, ":COUNTRY", $COUNTRY);
        oci_bind_by_name($sql_User, ":is_admin", $is_admin);
        if(oci_execute($sql_User) == TRUE){
                oci_commit($connection);
                echo $sql;
                echo "New record created successfully";
                echo "<br>";
                echo '<a href = "index.html" >Click this Link back to homePage</a>';
        }
        else{
            echo "Error";
        }
        }
        }
        
        oci_free_statement($sql_User);
        oci_close($connection);
         
    ?>
