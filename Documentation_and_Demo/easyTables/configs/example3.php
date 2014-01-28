<?php
	/*$db='mysql';
	$host='localhost';
	$dbname='test';
	$user="root";
	$pass="qoskA45";*/
	
	$db="sqlite";
	$dbname="../../Examples/Dabatase.s3db";
	$table='books';
	$query="select * from books where id>'200116' and id<'200300'";
	$debugMode=true;
	$fileName="myFile";
?>
