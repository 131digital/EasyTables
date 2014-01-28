<?php
	/*$db='mysql';
	$host='localhost';
	$dbname='test';
	$user="root";
	$pass="qoskA45";*/
	
	$db="sqlite";
	$dbname="../../Examples/Dabatase.s3db";
	$table='books';
	$fields=array('isbn','title','audience');
	$colNames=array(
		"isbn"=>"Number",
		"title"=>"Book name"
	);
?>
