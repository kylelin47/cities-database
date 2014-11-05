#!/usr/local/bin/php
<?php
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
$cityName = strtoupper($_POST['cityName']);
$query = "SELECT asciiname, country, population, elevation, latitude, longitude FROM cities WHERE
          upper(asciiname) LIKE '%" . $cityName . "%'";
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
echo "      <th>Name</th>\n";
echo "      <th>Country</th>\n";
echo "      <th>Population</th>\n";
echo "      <th>Elevation</th>\n";
echo "      <th>Latitude</th>\n";
echo "      <th>Longitude</th>\n";
echo "</tr>\n";
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    $latitude = $row['LATITUDE'];
    $longitude = $row['LONGITUDE'];
    if ($latitude < 0)
    {
            $NoS = 'S';
            $latitude = abs($latitude);
    } else {
            $NoS = 'N';
    }
    $degrees = floor($latitude);
    $minutes = ($latitude - $degrees) * 60;
    $latitude = $degrees . '&deg;' . $minutes . $NoS;
    if ($longitude < 0)
    {
            $WoE = 'W';
            $longitude = abs($longitude);
    } else {
            $WoE = 'E';
    }
    $degrees = floor($longitude);
    $minutes = ($longitude - $degrees) * 60;
    $longitude = $degrees . '&deg;' . $minutes . $WoE;
    $mapcoord = $latitude.'+'.$longitude;
    echo "    <td align='center'><form action=''><select onChange='window.open(this.value)'>\n";
    echo "    <option value=''>(no action)</option>";
    echo "    <option value='http://google.com/maps/place/".$mapcoord."/'>Google Maps</option></td>";
   // echo "    <td align='center'><form action = 'http://google.com/maps/place/".$mapcoord.
   //      "/' target='_blank'><input type='submit' value = 'Google Maps'></form></td>\n";
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
