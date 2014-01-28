<?php
	/*$db='mysql';
	$host='localhost';
	$dbname='test';
	$user="root";
	$pass="qoskA45";*/
	
	$db="sqlite";
	$dbname="../../Examples/Dabatase.s3db";
	$table='products';
	$colSizes=array(
		"id"=>"50px",
		"name"=>"100px",
		"description"=>"300px"
	);
	$friendlyOutput=array(
		'in_stock'	=>	array(
						'0'	=>	'<img src="../easyTables/css/accept.png">',
						'1' =>	'<img src="../easyTables/css/cancel.png">'
					)
	);
	$colColorsOdd=array(
		"id"=>"#E38F33",
		"price"=>"#E38F33",
	);
	$colColorsEven=array(
		"id"=>"#EDCF76",
		"price"=>"#EDCF76",
	);
	$colAlign=array(
		"price"=>"center",
		"in_stock"=>"center",
	);
	$colHeadColors=array(
		"id"=>"#CF0707",
		"price"=>"#CF0707",
		"name"=>"#1527CA",
	);
	$colOrderColors=array(
		"Even"=>"#7CE1F3",
		"Odd"=>"#3ECAF9"
	);
	$colOrderFontColors=array(
		"Even"=>"#000",
		"Odd"=>"#000"
	);
?>
