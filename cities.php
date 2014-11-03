#!/usr/local/bin/php
<?php
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
//$statement = oci_parse($connection, 'SELECT * FROM (SELECT asciiname, country, population, elevation, latitude, longitude FROM Cities ORDER BY population DESC) WHERE ROWNUM<=500');
$query = 'SELECT ';
$attributes = $_POST['attributes'];
$N = count($attributes);
for ($i=0; $i < $N; $i++)
{
	$query = $query . $attributes[$i];
	if ($i < $N - 1)
	{
		$query = $query . ',';
	}
}
$query = $query . ' FROM (SELECT asciiname, country, population, elevation, latitude, longitude FROM cities ORDER BY population DESC) WHERE ROWNUM<=500';	
$statement = oci_parse($connection, $query);
oci_execute($statement);

echo "<html>";
echo "<head>";
echo "<script src='sorttable.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' href='styles/table_styles.css' type='text/css'/>";
echo "</head>\n";
echo "<body>";
echo "<table class='sortable'>\n";
echo "<tr>\n";
for ($i=0; $i < $N; $i++)
{
	echo "<th>";
	if ($attributes[$i] == 'asciiname')
	{
		echo "Name";
	}
	else
	{
		echo $attributes[$i];
	}
	echo "</th>\n";
}
echo "</tr>\n";
echo "<tr>\n";
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    $latitude = $row['LATITUDE'];
    //echo "  <td> LAT " . $latitude . "</td>\n";
    $longitude = $row['LONGITUDE'];
    //echo "  <td> LONG " . $longitude . "</td>\n";
    if ($latitude < 0)
    {
            $NoS = 'S';
            $latitude = abs($latitude);
    } else {
            $NoS = 'N';
    }
    $degrees = floor($latitude);
    $minutes = ($latitude - $degrees) * 60;
    $latitude = $degrees . '°' . $minutes . '\'' . $NoS;
    if ($longitude < 0)
    {
            $WoE = 'W';
            $longitude = abs($longitude);
    } else {
            $WoE = 'E';
    }
    $degrees = floor($longitude);
    $minutes = ($longitude - $degrees) * 60;
    $longitude = $degrees . '°' . $minutes . '\'' . $WoE;
    echo "	<td><form action = 'http://google.com/maps/place/" . $latitude . "+" . $longitude . "><input type='submit' value = 'Map'></form></td>\n";
    echo "</tr>\n";
}
echo "</table>\n";
echo $query;
echo "</body>";
echo "</html>";
//
// VERY important to close Oracle Database Connections and free statements!
//
oci_free_statement($statement);
oci_close($connection);

?>
