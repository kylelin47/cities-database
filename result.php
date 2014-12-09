#!/usr/local/bin/php
<?php

session_start();
include "database.php"
$connection = oci_connect($username,
                          $password,
                          $connection_string);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

 $User_city = $_SESSION['User_City'];
 $selectCity = $_POST['city'];
 $TravleMethod = $_POST['travelMethod'];
 $long1 = $_SESSION['User_city_LONG'];
 $lat1 = $_SESSION['User_city_LAT'];
                    
 
 
 if($TravleMethod == "car"){
     $Speed = 50;
     //km/h;
 }
 else if($TravleMethod == "plane"){
     $Speed = 487.5;
     
 }
 else if($TravleMethod == "human"){
     $Speed = 5;
 }

    $query = "SELECT LATITUDE, LONGITUDE FROM CITIES WHERE NAME = '{$selectCity}'";
    $sql_User = oci_parse($connection, $query);
    oci_execute($sql_User);
    echo "<html>";
   
                     
    while(($row = oci_fetch_assoc($sql_User)) == TRUE){
        $long = $row["LONGITUDE"];
        $lat = $row["LATITUDE"];

        }
    echo "</html>";


    
$R = 6371; // km
$angle1 =  ($lat * M_PI / 180);
$angle2 =  ($lat1 * M_PI / 180);
$arg = $long - $long1;
$arg1 = $lat - $lat1;
$temp =  ($arg * M_PI / 180);
$temp1 =  ($arg1 * M_PI / 180);

$a = sin($temp1/2) * sin($temp1/2) +
      cos($angle1) * cos($angle2) *
      sin($temp/2) * sin($temp/2);
$c = 2 * atan2(sqrt($a), sqrt(1-$a));

$d =$R * $c;
$T = $d/$Speed;


echo "<html>";

echo "Estimate arriving time takes:   ";
echo "$T";
echo "hours";
echo "</html>;"

?>


