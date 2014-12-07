#!/usr/local/bin/php
<?php
session_start();
include "database.php";
	$connection = oci_connect($username,
                              $password,
                              $connection_string);
							  
	$adminAction = $_POST('action');
	if ($adminAction = "crtTbl") {
	echo "<form>Please enter the name of the table: <input type='text' value='".$tblName"'/><br>";
	$statement = "CREATE TABLE ".$tblName." (";
	do {
	echo "Please enter the attributes with its type: <input type='text' value='".$attrName"'/>";
	echo "<select>";
	echo "<option value='int'>int</option>";
	echo "<option value='varchar(25)'>string</option>";
	echo "</select>";
	echo "more attributes? <input type = 'checkbox' value = '".$more"'/><br>"
    echo "<input type='submit' value='".$eleType."' /><br>";
	$statement = $statement.$attrName." ".$eleType.", ";
	} while ($more = 'yes');
	echo "</form>"
	$statement = $statement.";";
	$statement = oci_parse($connection, $statement);
    oci_execute($statement);
	}
	else if ($adminAction = "insEle") {
	do {
	echo "<form>Please enter the name of the table: <input type='text' value='".$tblName"'/><br>";
	$statement = "INSERT INTO ".$tblName." VALUES (";
	echo "Please enter the new element: <input type='text' value='".$newEle"'/>";
	echo "more elements? <input type = 'checkbox' value = '".$more"'/>"
    echo "<input type='submit'/></form><br>";
	$statement = $statement.$newEle.");";
	$statement = oci_parse($connection, $statement);
    oci_execute($statement);
	} while ($more = 'yes');
	}
	else if ($adminAction = "rmTbl") {
	do {
	echo "<form>Please enter the name of the table: <input type='text' value='".$tblName"'/><br>";
	echo "remove more tables? <input type = 'checkbox' value = '".$more"'/><br>"
	echo "<input type='submit'/></form><br>";
	$statement = "DELETE ".$tblName.";";
	$statement = oci_parse($connection, $statement);
    oci_execute($statement);
	} while ($more = 'yes');
	}
	else if ($adminAction = "rmEle") {
	do {
	echo "<form>Please enter the name of the table: <input type='text' value='".$tblName"'/><br>";
	$statement = "DELETE FROM ".$tblName." Where ";
	echo "Please enter the condition: <input type = 'text' value = '".$attrName"'/><br>";
	echo "remove more elements? <input type = 'checkbox' value = '".$more"'/>"
	echo "<input type='submit'/></form><br>";
	$statement = oci_parse($connection, $statement);
    oci_execute($statement);
	} while ($more = 'yes');
	}
	else {
	echo "<form>enter your SQL command: <input type='text' value='".$sqlCom"'/></form><br>";
	$statement = oci_parse($connection, $sqlCom);
    oci_execute($statement);
	}
	
?>