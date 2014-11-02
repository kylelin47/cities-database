#!/usr/local/bin/php
<?php
$connection = oci_connect($username = 'kylin',
                          $password = 'citiesdatabase',
                          $connection_string = '//oracle.cise.ufl.edu/orcl');
$statement = oci_parse($connection, 'SELECT * FROM cities');
oci_execute($statement);

echo "<table border='1'>\n";
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
	echo "<tr>\n";
	foreach ($row as $item) {
		echo "	<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
	}
    echo "</tr>\n";
}
echo "</table>\n";

//
// VERY important to close Oracle Database Connections and free statements!
//
oci_free_statement($statement);
oci_close($connection);
?>