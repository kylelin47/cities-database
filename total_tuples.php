#!/usr/local/bin/php
<?php
include "database.php";
$connection = oci_connect($username,
                          $password,
                          $connection_string);
$total_tuples = 0;
$statement = oci_parse($connection, "SELECT COUNT(*) FROM cities");
oci_execute($statement);
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item)
    {
        $total_tuples += intval($item);
    }
}
oci_free_statement($statement);
$statement = oci_parse($connection, "SELECT COUNT(*) FROM history");
oci_execute($statement);
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item)
    {
        $total_tuples += intval($item);
    }
}
oci_free_statement($statement);
$statement = oci_parse($connection, "SELECT COUNT(*) FROM login");
oci_execute($statement);
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item)
    {
        $total_tuples += intval($item);
    }
}
oci_free_statement($statement);
$statement = oci_parse($connection, "SELECT COUNT(*) FROM countries");
oci_execute($statement);
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item)
    {
        $total_tuples += intval($item);
    }
}
oci_free_statement($statement);
$statement = oci_parse($connection, "SELECT COUNT(*) FROM continents");
oci_execute($statement);
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    foreach ($row as $item)
    {
        $total_tuples += intval($item);
    }
}
oci_free_statement($statement);
oci_close($connection);
echo $total_tuples;
?>
