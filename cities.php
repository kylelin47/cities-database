#!/usr/local/bin/php
<?php
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
$query = 'SELECT ';
$attributes = $_POST['attributes'];
$N = count($attributes);
$agg_funs = array('SUM', 'AVG', 'MIN', 'MAX');
for ($i=0; $i < $N; $i++)
{
	$query = $query . $attributes[$i];
    $agg_fun = $attributes[$i];
    if (in_array($attributes[$i], $agg_funs))
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
                $query = $query . ', ' . $agg_fun;
            }
        }
    }
	if ($i < $N - 1)
	{
		$query = $query . ',';
	}
}
$query = $query . 
         ' FROM (SELECT asciiname, country, population, dem, latitude, longitude, time_zone FROM cities ORDER BY population DESC)';
$firstWhere = true;
if (!empty($_POST['num_rows']))
{
    $query = $query . ' WHERE ROWNUM<=' . (string) $_POST['num_rows'];
    $firstWhere = false;
}
if (!empty($_POST['wheres']))
{
    $wheres = $_POST['wheres'];
    $wheres_count = count($wheres);
    $valid_entries = 0;
    $possible_attributes = array('asciiname', 'Country', 'Population', 'Elevation', 'Latitude', 'Longitude');
    $attributes_count = count($possible_attributes);
    for ($i = 0; $i < $attributes_count; $i++)
    {
        if (!empty($wheres[$possible_attributes[$i]]))
        {
            $valid_entries++;
        }
    }
    if ($firstWhere)
    {
        $query = $query . ' WHERE ';
        $firstWhere = false;
    }
    else
    {
        $query = $query . ' AND ';
    }
    $hits = 0;
    for ($i = 0; $i < $attributes_count; $i++)
    {
        if (!empty($wheres[$possible_attributes[$i]]))
        {
            $selected_attribute = $possible_attributes[$i];
            $selected_where = $wheres[$selected_attribute];
            if ($selected_attribute === "asciiname" || $selected_attribute === "Country")
            {
                $query = $query . $selected_attribute . "=" . "'" . $selected_where . "'";
                $hits++;
                if ($hits < $valid_entries)
                {
                    $query = $query . " AND ";
                }
            }
            else
            {
                $selected_attribute2 = $selected_attribute . '2';
                $selected_where2 = $wheres[$selected_attribute2];
                $query = $query . $selected_attribute . " BETWEEN " . (string)$selected_where . " AND " . (string)$selected_where2;
                $hits++;
                if ($hits < $valid_entries)
                {
                    $query = $query . " AND ";
                }
            }
        }
    }
}
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
	    $att = $sum_over[$sumCount];
	    if ($att === 'dem')
	    {
	    	$att = 'Elevation';
 	    }
            echo " " . $att;
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
