<?php

	/* 
		Parameters:
			configFile: Array with the values of the row that will be inserted
	*/

	include_once("DBHelper.php");
	$dbhelper=new DBHelper($_REQUEST['configFile']);
	$char=$dbhelper->character();
	$esc=$dbhelper->escape();
	$columns=$dbhelper->getColumns($dbhelper->query);
	$string="<select name='searchField' class='searchField'>";
	$colName="";
	if(isset($_REQUEST['searchFields'])){
		for($i=0;$i<count($_REQUEST['searchFields']);$i++){
			$colName=((isset($dbhelper->colNames[$_REQUEST['searchFields'][$i]]))?$dbhelper->colNames[$_REQUEST['searchFields'][$i]]:$_REQUEST['searchFields'][$i]);
			$string.="<option value='".$_REQUEST['searchFields'][$i]."'>".$colName."</option>";
		}
	}else{
		for($i=0;$i<count($columns);$i++){
			$colName=((isset($dbhelper->colNames[$columns[$i]]))?$dbhelper->colNames[$columns[$i]]:$columns[$i]);
			$string.="<option value='".$columns[$i]."'>".$colName."</option>";
		}
	}
	$string.="</select>";		
	echo $string;
?>