#!/usr/local/bin/php
<?php
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
$cityName = strtoupper($_POST['cityName']);
$query = "SELECT asciiname, country, population, dem, latitude, longitude FROM cities WHERE
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

//variables to keep metadata about resulting table
$totalpop = 0;
$minpop = 99999999999;
$maxpop = 0;
$totalele = 0;
$minele = 99999999999;
$maxele = -9999999999;
$haspop = true;
$hasele = true;
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    
    $totalpop = $totalpop + $row['POPULATION'];
    if ($row['POPULATION'] > $maxpop) {
        $maxpop = $row['POPULATION'];
    }
    if ($row['POPULATION'] < $minpop) {
        $minpop = $row['POPULATION'];
    }
    
    $totalele = $totalele + $row['DEM'];
    if ($row['DEM'] > $maxele) {
        $maxele = $row['DEM'];
    }
    if ($row['DEM'] < $minele) {
        $minele = $row['DEM'];
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
    echo "    <td align='center'><form><select name='menu'>\n";
    echo "    <option value='http://google.com/maps/place/".$mapcoord."/'>Google Maps</option>";
    echo "    <option value='range_cities.php?name=".$row['ASCIINAME'].'&latitude='.$row['LATITUDE'].'&longitude='.$row['LONGITUDE'],"#'>Nearby cities</option>";
    echo "    </select>";
    echo "    <input type='button' value='GO' onClick='window.open(this.form.menu.options[this.form.menu.selectedIndex].value);'>";
    echo "    </form></td>";
    echo "</tr>\n";
}
echo "</table>\n";

//table of data about current table
if ($hasele || $haspop) {
    echo "<p>Information about this table:</p>";
    echo "<table class='sortable'>\n";
    if ($haspop) {
        echo "<tr>\n";
        echo "<td><b>Total Population: " . $totalpop . "</b></td>";
        echo "<td><b>Average Population: " . ($totalpop/$_POST['num_rows']) . "</b></td>";
        echo "</tr>";
        echo "<tr>\n";
        echo "<td><b>Min Population: " . $minpop . "</b></td>";
        echo "<td><b>Max Population: " . $maxpop . "</b></td>";
        echo "</tr>";
    }
    if ($hasele) {
        echo "<tr>\n";
        echo "<td><b>Total Elevation: " . $totalele . "</b></td>";
        echo "<td><b>Average Elevation: " . ($totalele/$_POST['num_rows']) . "</b></td>";
        echo "</tr>";
        echo "<tr>\n";
        echo "<td><b>Min Elevation: " . $minele . "</b></td>";
        echo "<td><b>Max Elevation: " . $maxele . "</b></td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo $query;
echo "</body>";
echo "</html>";
//
// VERY important to close Oracle Database Connections and free statements!
//
oci_free_statement($statement);
oci_close($connection);

?>
