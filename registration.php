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
        $connection = new mysqli('localhost','root','','test');
        if(!$connection){
            die("connection failed".$connection->connect_error);
        }
        $sql_User = "SELECT Username FROM login";
        $result = $connection->query($sql_User);
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
        while($row = $result->fetch_assoc()) {
             if($Username == $row["Username"]){
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
        $sql = "INSERT INTO login (Username,Password,Fname,Lname) 
               VALUES ('$Username','$Password','$FName','$LName')";        
        if($connection->query($sql) == TRUE){
                echo "New record created successfully";
        }
        else{
            echo "Error: " . $sql . "<br>" . $connection->error;
        }
        }

         
    ?>


