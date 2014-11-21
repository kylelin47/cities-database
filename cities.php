#!/usr/local/bin/php
<?php
session_start();
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
function escapeSQL($string, $wildcard=false)
{    
    $result = str_replace("'","''",$string);
    if ($wildcard == true) $result = str_replace("%","%%",$result);
    return $result;
}

$query = 'SELECT ';
$attributes = $_POST['attributes'];
$att_count = count($attributes);
$agg_funs = array('SUM', 'AVG', 'MIN', 'MAX', 'COUNT');
for ($i=0; $i < $att_count; $i++)
{
    $agg_fun = $attributes[$i];
    if (in_array($attributes[$i], $agg_funs))
    {
        $sum_over = $_POST['sum_over'];
        $avg_over = $_POST['avg_over'];
        $min_over = $_POST['min_over'];
        $max_over = $_POST['max_over'];
        if ($attributes[$i] == 'SUM')
        {
            $query = $query . $attributes[$i];
            $sum_count = count($sum_over);
            for ($j=0; $j < $sum_count; $j++)
            {
                $query = $query . '(';
                $query = $query . $sum_over[$j];
                $query = $query . ')';
                if ($j < $sum_count - 1)
                {
                    $query = $query . ', ' . $agg_fun;
                }
            }
        }
        else if ($attributes[$i] == 'AVG')
        {
            $avg_count = count($avg_over);
            for ($k=0; $k < $avg_count; $k++)
            {
                $query = $query . "floor(" . $attributes[$i];
                $query = $query . '(';
                $query = $query . $avg_over[$k];
                $query = $query . '))';
                if ($k < $avg_count - 1)
                {
                    $query = $query . ', ';
                }
            }
        }
        else if ($attributes[$i] == 'MIN')
        {
            $query = $query . $attributes[$i];
            $min_count = count($min_over);
            for ($k=0; $k < $min_count; $k++)
            {
                $query = $query . '(';
                $query = $query . $min_over[$k];
                $query = $query . ')';
                if ($k < $min_count - 1)
                {
                    $query = $query . ', ' . $agg_fun;
                }
            }
        }
        else if ($attributes[$i] == 'MAX')
        {
            $query = $query . $attributes[$i];
            $max_count = count($max_over);
            for ($k=0; $k < $max_count; $k++)
            {
                $query = $query . '(';
                $query = $query . $max_over[$k];
                $query = $query . ')';
                if ($k < $max_count - 1)
                {
                    $query = $query . ', ' . $agg_fun;
                }
            }
        }
        else if ($attributes[$i] == 'COUNT')
        {
            $query = $query . 'COUNT(asciiname)';
            $count_set = true;
        }
    }
    else {
        $query = $query . escapeSQL($attributes[$i]);
    }
    if ($i < $att_count - 1)
    {
    	$query = $query . ',';
    }
}
$query = $query . 
         ' FROM (SELECT asciiname, country, population, dem, latitude, longitude, time_zone FROM cities ORDER BY population DESC)';
$firstWhere = true;
if (!empty($_POST['wheres']))
{
    $wheres = $_POST['wheres'];
    $wheres_count = count($wheres);
    $valid_entries = 0;
    $possible_attributes = array('asciiname', 'Country', 'Population', 'dem', 'Latitude', 'Longitude');
    $attributes_count = count($possible_attributes);
    for ($i = 0; $i < $attributes_count; $i++)
    {
        if (isset($wheres[$possible_attributes[$i]]) && $wheres[$possible_attributes[$i]] !== "")
        {
            $valid_entries++;
        }
    }
    if ($firstWhere && $valid_entries > 0)
    {
        $query = $query . ' WHERE ';
        $firstWhere = false;
    }
    else if ($valid_entries > 0)
    {
        $query = $query . ' AND ';
    }
    $hits = 0;
    for ($i = 0; $i < $attributes_count; $i++)
    {
        if (isset($wheres[$possible_attributes[$i]]) && $wheres[$possible_attributes[$i]] !== "")
        {
            $selected_attribute = $possible_attributes[$i];
            $selected_where = escapeSQL($wheres[$selected_attribute]);
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
                $selected_where2 = escapeSQL($wheres[$selected_attribute2]);
                if ($selected_where2 === "")
                    $selected_where2 = "1125140637";
                $query = $query . $selected_attribute . " BETWEEN " . strval($selected_where) . " AND " . strval($selected_where2);
                $hits++;
                if ($hits < $valid_entries)
                {
                    $query = $query . " AND ";
                }
            }
        }
    }
}
if (isset($sum_over) || isset($avg_over) || isset($min_over) || isset($max_over) || isset($count_set) || !empty($_POST['group']))
{
    $group_by = array();
    $j = 0;
	$first = 0;
	for ($k = 0; $k < $att_count; $k++) {
		if (!in_array($attributes[$k], $agg_funs))
		{
			if ($first != 0) {
			$query = $query . ", "; }
            else
            {
            	$query = $query . " GROUP BY ";
            }
			$query = $query . escapeSQL($attributes[$k]);
            $group_by[$j] = escapeSQL($attributes[$k]);
            $j = $j + 1;
			$first = 1;
		}
	}
}
if (!empty($_POST['havings']))
{
    $firstHaving = true;
    $havings = $_POST['havings'];
    $havings_count = count($havings);
    $valid_entries = 0;
    $possible_attributes = array('sum(Population)', 'sum(dem)', 'floor(avg(Population))', 'count(asciiname)',
                                 'floor(avg(dem))', 'min(Population)', 'min(dem)', 'max(Population)', 'max(dem)');
    $attributes_count = count($possible_attributes);
    for ($i = 0; $i < $attributes_count; $i++)
    {
        if (isset($havings[$possible_attributes[$i]]) && $havings[$possible_attributes[$i]] !== "")
        {
            $valid_entries++;
        }
    }
    if ($firstHaving && $valid_entries > 0)
    {
        $query = $query . ' HAVING ';
        $firstHaving = false;
    }
    else if ($valid_entries > 0)
    {
        $query = $query . ' AND ';
    }
    $hits = 0;
    for ($i = 0; $i < $attributes_count; $i++)
    {
        if (isset($havings[$possible_attributes[$i]]) && $havings[$possible_attributes[$i]] !== "")
        {
            $selected_attribute = $possible_attributes[$i];
            $selected_having = $havings[$selected_attribute];
            $selected_attribute2 = $selected_attribute . '2';
            $selected_having2 = $havings[$selected_attribute2];
            if ($selected_having2 === "")
                $selected_having2 = "99999999999";
            $query = $query . $selected_attribute . " BETWEEN " . strval($selected_having) . " AND " . strval($selected_having2);
            $hits++;
            if ($hits < $valid_entries)
            {
                $query = $query . " AND ";
            }
        }
    }
}
//$query = $query . " ORDER BY population DESC";
if (!empty($_POST['num_rows']) && is_int($_POST['num_rows']))
{
    $query = 'SELECT * FROM(' . $query . ') WHERE ROWNUM<=' . (string) $_POST['num_rows'];
}
$statement = oci_parse($connection, $query);
/*$statement2 = oci_parse($connection, 'INSERT INTO history(username, query, date) VALUES(:username, :query, :date)');
oci_bind_by_name($statement2, ":username", $_SESSION['NAME']);
oci_bind_by_name($statement2, ":query", $query);
oci_bind_by_name($statement2, ":date", sysdate);
oci_execute($statement2);
oci_commit($connection);
oci_free_statement($statement2);*/
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
$sumC = 0;
$avgC = 0;
$minC = 0;
$maxC = 0;
for ($i=0; $i < $att_count; $i++)
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
            $att = $sum_over[$sumC];
            if ($att === 'dem')
            {
                $att = 'Elevation';
            }
                echo " " . $att;
                $sumC = $sumC + 1;
                if ($sumC < $sum_count)
                {
                    $i = $i - 1;
                }
        }
        else if ($attributes[$i] == 'AVG')
        {
            $att = $avg_over[$avgC];
            if ($att === 'dem')
            {
                $att = 'Elevation';
            }
            echo " " . $att;
            $avgC = $avgC + 1;
            if ($avgC < $avg_count)
            {
                $i = $i - 1;
            }
        }
        else if ($attributes[$i] == 'MIN')
        {
            $att = $min_over[$minC];
            if ($att === 'dem')
            {
                $att = 'Elevation';
            }
            echo " " . $att;
            $minC = $minC + 1;
            if ($minC < $min_count)
            {
                $i = $i - 1;
            }
        }
        else if ($attributes[$i] == 'MAX')
        {
            $att = $max_over[$maxC];
            if ($att === 'dem')
            {
                $att = 'Elevation';
            }
            echo " " . $att;
            $maxC = $maxC + 1;
            if ($maxC < $max_count)
            {
                $i = $i - 1;
            }
        }
	}
	echo "</th>\n";
}
echo "</tr>\n";

//variables to keep metadata about resulting table
$totalpop = 0;
$minpop = 99999999999;
$maxpop = 0;
$totalele = 0;
$minele = 99999999999;
$maxele = -9999999999;
$haspop = false;
$hasele = false;
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }

    if (isset($row['POPULATION']))
    {
        $haspop = true;
        $totalpop = $totalpop + $row['POPULATION'];
        if ($row['POPULATION'] > $maxpop) {
            $maxpop = $row['POPULATION'];
        }
        if ($row['POPULATION'] < $minpop) {
            $minpop = $row['POPULATION'];
        }
    }
    
    if (isset($row['DEM']))
    {
        $hasele = true;
        $totalele = $totalele + $row['DEM'];
        if ($row['DEM'] > $maxele) {
            $maxele = $row['DEM'];
        }
        if ($row['DEM'] < $minele) {
            $minele = $row['DEM'];
        }
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
