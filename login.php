<?php
 $username = $_POST['username'];
 $username = $_POST['password'];
 
if($username && $username ){
	 $connection = ori_connect('cise.ufl.edu','kylin','citiesdatabase') or die("False connection");
	ori_select_db('Login') or die("False DB");
}
else
	die("False input Or No input");

 
?>
