#!/usr/local/bin/php
<?php
session_start();
if (isset($_POST['logout']))
{
    unset($_SESSION['NAME']);
}
if (isset($_SESSION['NAME']))
{
    include "database.php";
    $connection = oci_connect($username,
                              $password,
                              $connection_string);
    $statement = oci_parse($connection, "SELECT * FROM (SELECT english FROM history WHERE username=:username ORDER BY date_created DESC) WHERE ROWNUM <= 10");
    oci_bind_by_name($statement, ":username", $_SESSION['NAME']);
    oci_execute($statement);
    echo "<!doctype html>";
    echo "<html>";
    echo "	<title>User</title>";
    echo "	<link rel = 'stylesheet' href = 'styles/standard.css' type = 'text/css' />";
    echo "	<body>";
    echo "		<h2>User</h2>";
    echo "		<div id = 'box'>";
    echo "			<ul id = 'toolBar'>";
    echo "				<li><a href = 'index.html'>Home</a></li>";
    echo "				<li><a href = 'FAQ.html'>FAQ</a></li>";
    echo "				<li><a href = 'Guide.html'>Guide</a></li>";
    echo "				<li><a href = 'Queries.html'>Queries</a></li>";
    echo "				<li><a href = 'User.php'>User</a></li>";
    echo "			</ul>";
    echo "<div class = 'userBox'>";
    echo "You are " . $_SESSION['NAME'];
    echo "<form id='logout' action='User.php' method='post'>";
    echo "<input type='hidden' name='logout' value='1' />";
    echo "<input type='submit' value='logout' />";
    echo "</form>";
    echo "<h1>Past 10 Queries</h1>";
    while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
        $english = $item;
    }
    echo "<td>";
    echo '<br>';
    echo '<a href = "UserOnly.php" >Click here to see the queries JUST for you</a>';
    echo '<br>';
    echo "<form action='cities.php' method='post' target='_blank'>";
    echo "<input type='hidden' name='english' value='" . $english . "' />";
    echo "<input type='submit' value='Search' />";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
    }
    echo "</div>";
    echo "		</div>";
    echo "	</body>";
    echo "</html>";
    oci_free_statement($statement);
    oci_close($connection);
}
else
{
    echo "<!doctype html>";
    echo "<html>";
    echo "	<title>User</title>";
    echo "	<link rel = 'stylesheet' href = 'styles/standard.css' type = 'text/css' />";
    echo "	<body>";
    echo "		<h2>User</h2>";
    echo "		<div id = 'box'>";
    echo "			<ul id = 'toolBar'>";
    echo "				<li><a href = 'index.html'>Home</a></li>";
    echo "				<li><a href = 'FAQ.html'>FAQ</a></li>";
    echo "				<li><a href = 'Guide.html'>Guide</a></li>";
    echo "				<li><a href = 'Queries.html'>Queries</a></li>";
    echo "				<li><a href = 'User.php'>User</a></li>";
    echo "			</ul>";
    echo "			<div id = 'LogIn'>";
    echo "			<form action = 'login.php' method = 'post'>";
    echo "				Username: <input type = 'text' name = 'Username'><br>";
    echo "				Password: <input type = 'password' name = 'Password'><br>";
    echo "				<input type = 'submit' value = 'log in'>";
    echo "				<a href = 'register.html'><br>Register?</a>";
    echo "			</form>";
    echo "			</div>";
    echo "		</div>";
    echo "	</body>";
    echo "</html>";
}
?>
