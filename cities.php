#!/usr/local/bin/php
<?php
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
$query = 'SELECT ';
$attributes = $_POST['attributes'];
$N = count($attributes);
for ($i=0; $i < $N; $i++)
{
	$query = $query . $attributes[$i];
    if ($attributes[$i] === 'SUM')
    {
        $sum_over = $_POST['sum_over'];
        $M = count($sum_over);
        for ($j=0; $j < $M; $j++)
        {
            $query = $query . '(';
            $query = $query . $sum_over[$j];
            $query = $query . ')';
            if ($j < $M - 1)
            {
                $query = $query . ', SUM';
            }
        }
    }
	if ($i < $N - 1)
	{
		$query = $query . ',';
	}
}
$num_rows_string = (string) $_POST['num_rows'];
$query = $query . 
         ' FROM (SELECT asciiname, country, population, dem, latitude, longitude, time_zone FROM cities ORDER BY population DESC) WHERE ROWNUM<=' .
         $num_rows_string;
$agg_funs = array('SUM', 'AVG', 'MIN', 'MAX');
if (isset($sum_over))
{
	$first = 0;
	for ($k=0; $k<$N; $k++) {
		if (!in_array($attributes[$k], $sum_over)
		 && !in_array($attributes[$k], $agg_funs))
		{
			if ($first != 0) {
			$query = $query . ", "; }
            else
            {
            	$query = $query . " GROUP BY ";
            }
			$query = $query . $attributes[$k];
			$first = 1;
		}
	}
}
$statement = oci_parse($connection, $query);
oci_execute($statement);

echo "<html>";
echo "<head>";
echo "<script src='sorttable.js' type='text/javascript'></script>";
echo "<link href='http://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>";
echo "<link rel='stylesheet' href='styles/table_styles.css' type='text/css'/>";
echo "</head>\n";
echo "<body>";
echo "<table class='sortable'>\n";
echo "<tr>\n";
$sumCount = 0;
for ($i=0; $i < $N; $i++)
{
	echo "<th>";
	if ($attributes[$i] == 'asciiname')
	{
		echo "Name";
	}
    else if ($attributes[$i] == 'time_zone')
	{
		echo "Time Zone";
	}
    else if ($attributes[$i] == 'dem')
	{
		echo "Elevation";
	}
	else
	{
		echo $attributes[$i];
        if ($attributes[$i] == 'SUM')
        {
            echo " " . $sum_over[$sumCount];
            $sumCount = $sumCount + 1;
            if ($sumCount < $M)
            {
                $i = $i - 1;
            }
        }
	}
	echo "</th>\n";
}
echo "</tr>\n";
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    if (isset($row['LATITUDE']) && isset($row['LONGITUDE']))
    {
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
    }
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
