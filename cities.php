#!/usr/local/bin/php
<?php
$connection = oci_connect($username = 'kylin',
                          $password = 'citiesdatabase',
                          $connection_string = '//oracle.cise.ufl.edu/orcl');
$statement = oci_parse($connection, 'SELECT '.htmlspecialchars($_POST['attrNames']).' FROM (SELECT asciiname, country, population, elevation, latitude, longitude FROM Cities ORDER BY population DESC) WHERE ROWNUM<=500');
oci_execute($statement);
echo "<html>";
echo "<head>";
echo "<script src='sorttable.js' type='text/javascript'></script>";
echo "<link rel='stylesheet' href='table_style.css' type='text/css'/>";
echo "</head>\n";
echo "<body>";
echo "<table border='1' class='sortable'>\n";
echo "<tr>\n";
echo "      <th>Name</th>\n";
echo "      <th>Country</th>\n";
echo "      <th>Population</th>\n";
echo "      <th>Elevation</th>\n";
echo "      <th>Latitude</th>\n";
echo "      <th>Longitude</th>\n";
echo "</tr>\n";
echo "<tr>\n";
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    echo "</tr>\n";
}
echo "</table>\n";
echo "</body>";
echo "</html>";
//
// VERY important to close Oracle Database Connections and free statements!
//
oci_free_statement($statement);
oci_close($connection);

?>
