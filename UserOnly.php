#!/usr/local/bin/php
<!doctype html>

<html>
	<title>YourQueries</title>
	<link rel = "stylesheet" href = "styles/standard.css" type = "text/css" />
	
	<body>
		<h2>YourQueries</h2>

		<div id = "box">
			<ul id = "toolBar">
				<li><a href = "index.html">Home</a></li>
				<li><a href = "FAQ.html">FAQ</a></li>
				<li><a href = "Queries.html">Queries</a></li>
				<li><a href = "User.html">User</a></li>
			</ul>
                    
               
			
                 <?php
                 session_start();
                 $buff_name = $_SESSION['NAME'];
                 $connection = oci_connect('kylin','citiesdatabase','//oracle.cise.ufl.edu/orcl');
                 $query = "SELECT city FROM login WHERE Username =  '{$buff_name}'";
                 $sql_User =oci_parse($connection, $query);
                 oci_execute($sql_User);
                 /*
                  * CITY
                  */
                 echo "<br>";
                 echo "<br>";
                 echo "<center>Background information about your hometown</center>";
                 echo "<br>";
                 if(($row = oci_fetch_assoc($sql_User)) == TRUE){
                    $User_city = $row["CITY"];
                    $_SESSION['User_City'] = $User_city;
                   // echo"{$User_city}";
                 }
                 else{
                     echo"ERROR";
                 }
                 echo "<br>";
                 echo "<br>";
                 echo "Population in your hometown {$User_city}";
                 /*
                  * CITY POPULATION
                  */
                 echo ": ";
                 $query = "SELECT POPULATION FROM CITIES WHERE NAME = '{$User_city}'";
                 $sql_User = oci_parse($connection, $query);
                 oci_execute($sql_User);
                 
                if(($row = oci_fetch_assoc($sql_User)) == TRUE){
                    $User_city_population = $row["POPULATION"];
                    echo"{$User_city_population}";
                 }
                 else{
                     echo"ERROR";
                 }
                 echo "<br>";              
                 echo "<br>";
                 echo "<br>";
                 echo "<a href = 'http://en.wikipedia.org/wiki/Elevation'>Elevation</a> in your hometown {$User_city}"; 
                 
                 $query = "SELECT elevation FROM CITIES WHERE NAME = '{$User_city}'";
                 $sql_User = oci_parse($connection, $query);
                 oci_execute($sql_User);
                 
                if(($row = oci_fetch_assoc($sql_User)) == TRUE){
                    $User_city_elevation = $row["ELEVATION"];
                    echo ":";

                    if($User_city_elevation == NULL){
                        echo "&nbsp&nbspNo Data";
                    }
                    else{
                         echo"{$User_city_elevation}";
                    }
                   
                 }
                 else{
                     echo"ERROR";
                 }                 
 
                 echo "<br>";              
                 echo "<br>";
                 echo "<br>";
                 echo "Nearby city to your hometown {$User_city}"; 
                 
                 $query = "SELECT LATITUDE, LONGITUDE  FROM CITIES WHERE NAME = '{$User_city}'";
                 $sql_User = oci_parse($connection, $query);
                 oci_execute($sql_User);
                 
                if(($row = oci_fetch_assoc($sql_User)) == TRUE){
                    $User_city_LAT = $row["LATITUDE"];
                    $User_city_LONG = $row["LONGITUDE"];
                    
                    $_SESSION['User_city_LONG'] = $User_city_LONG ;
                    $_SESSION['User_city_LAT'] = $User_city_LAT;
         
                 }
                 else{
                     echo"ERROR";
                 }
                 $Lat_upper = $User_city_LAT + 1;
                 $Lat_lower = $User_city_LAT - 1;
                 $LONG_upper = $User_city_LONG + 1;
                 $LONG_lower = $User_city_LONG - 1;
                 $query = "SELECT NAME FROM CITIES WHERE LATITUDE < $Lat_upper and LATITUDE > $Lat_lower and LONGITUDE < $LONG_upper and LONGITUDE > $LONG_lower";
                 $sql_User = oci_parse($connection, $query);
                 oci_execute($sql_User);
                 
                     echo "<html>";
                     echo ":   ";
   
                    echo "<form action = 'result.php' method = 'POST'>";                            
                     
                 while(($row = oci_fetch_assoc($sql_User)) == TRUE){
                     $CITY_NAME = $row["NAME"];

                    echo "<input type = 'radio' name = 'city' value = '$CITY_NAME'>$CITY_NAME<br>" ;                          
                 }

                     echo "<br>";
                     echo "<br>";

                    
                    /*
                        TRAVEL TIME
                    */
                     
                     
                     

                    
                echo    "Prefer Travel Method<br>";
                            
		echo	 "<input type = 'radio' name = 'travelMethod' value = 'car'>Car<br>";
                echo     "<input type = 'radio' name = 'travelMethod' value = 'plane'>Plane<br>";
                echo     "<input type = 'radio' name = 'travelMethod' value = 'human'>OnFoot<br>";                                
		echo     "<input type = 'submit' value = 'submit'>";
		echo	"</form>";
                echo    "</html>";

                     ?>                    
                    
		</div>

		

	</body>
</html>
