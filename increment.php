#!/usr/local/bin/php
<?php
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
$minimum = $_POST['minimum'];
$maximum = $_POST['maximum'];
$increment = $_POST['increment'];
$increment_over = str_replace("'","''",$_POST['increment_over']);
$attributes = $_POST['attributes'];
$att_count = count($attributes);
echo "<html>";
echo "<head>";
echo "<script src='sorttable.js' type='text/javascript'></script>";
echo "<link href='http://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>";
echo "<link rel='stylesheet' href='styles/table_styles.css' type='text/css'/>";
echo "</head>\n";
echo "<body>";
echo "<table class='sortable'>\n";
echo "<tr>\n";
for ($i=0; $i < $att_count; $i++)
{
    echo "<th>";
    if ($attributes[$i] == 'count(asciiname)')
        echo "Number of Cities";
    else if ($attributes[$i] == 'sum(Population)')
        echo "Sum of Population";
    else if ($attributes[$i] == 'sum(dem)')
        echo "Sum of Elevation";
    else if ($attributes[$i] == 'floor(avg(Population))')
        echo "Average Population";
    else if ($attributes[$i] == 'floor(avg(dem))')
        echo "Average Elevation";
    else if ($attributes[$i] == 'min(Population)')
        echo "Minimum Population";
    else if ($attributes[$i] == 'min(dem)')
        echo "Minimum Elevation";
    else if ($attributes[$i] == 'max(Population)')
        echo "Maximum Population";
    else if ($attributes[$i] == 'max(dem)')
        echo "Maximum Elevation";
    echo "</th>";
}
echo "<th>";
echo $increment_over;
echo "</th>";
for ($i = $minimum; $i < $maximum; $i = $i + $increment)
{
    if ($i + $increment > $maximum) $increment = $maximum - $i;
    $query = "SELECT ";
    for ($j = 0; $j < $att_count; $j++)
    {
        $query = $query . $attributes[$j];
        if ($j < $att_count - 1)
        {
            $query = $query . ',';
        }
    }
    $query = $query . " FROM cities WHERE " . $increment_over . " BETWEEN " . strval($i) . " AND " . strval($i + $increment);
    $statement = oci_parse($connection, $query);
    oci_execute($statement);
    while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
        echo "<tr>\n";
        foreach ($row as $item) {
            echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
        }
    }
    echo "<td>" . strval($i) . " - " . strval($i + $increment) . "</td>";
    oci_free_statement($statement);
}
echo "</body>";
echo "</html>";
//
// VERY important to close Oracle Database Connections and free statements!
//
oci_close($connection);
?>
