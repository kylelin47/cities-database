#!/usr/local/bin/php
<?php
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
$query = 'SELECT ';
$attributes = $_POST['attributes'];
$att_count = count($attributes);
$agg_funs = array('SUM', 'AVG', 'MIN', 'MAX');
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
            $query = $query . "floor(" . $attributes[$i];
            $avg_count = count($avg_over);
            for ($k=0; $k < $avg_count; $k++)
            {
                $query = $query . '(';
                $query = $query . $avg_over[$k];
                $query = $query . ')';
                if ($k < $avg_count - 1)
                {
                    $query = $query . ', ' . $agg_fun;
                }
            }
            $query = $query . ")";
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
    }
    else {
        $query = $query . $attributes[$i];
    }
    if ($i < $att_count - 1)
    {
    	$query = $query . ',';
    }
}
$query = $query . 
         ' FROM (SELECT asciiname, country, population, dem, latitude, longitude, time_zone FROM cities ORDER BY population DESC)';
if (!empty($_POST['num_rows']))
{
    $query = $query . ' WHERE ROWNUM<=' . (string) $_POST['num_rows'];
}
if (isset($sum_over) || isset($avg_over) || isset($min_over) || isset($max_over))
{
	$first = 0;
	for ($k = 0; $k < $att_count; $k++) {
		if (!in_array($attributes[$k], $sum_over)
		 && !in_array($attributes[$k], $avg_over)
         && !in_array($attributes[$k], $min_over)
         && !in_array($attributes[$k], $max_over)
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
