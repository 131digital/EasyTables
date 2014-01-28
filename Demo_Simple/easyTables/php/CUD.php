<?php

	/* 
		Parameters:
			fields: Database table fields
			values: Array with the values of the row that will be deleted or updated. Or the values of the new row.
			newValues: Array with the new values of the row that will be updated
			configFile: File source of information
			action: Action that will be performed by the script(Delete, Update, Insert)
	*/
	
	include_once("DBHelper.php");
	$dbhelper=new DBHelper($_REQUEST['configFile']);
	$char=$dbhelper->character();
	$esc=$dbhelper->escape();
	$dontAllowed=$dbhelper->nameDontAllowedChars();
	$fields=$_REQUEST['fields'];
	$values=$_REQUEST['values'];
	
	switch($_REQUEST['action']){
		case 'Del': //Actions for delete a row
			$keys=$dbhelper->getPrimaryKey($dbhelper->table);
			$query="delete from ".$char.$dbhelper->table.$char." where ";
			$params=array();
			for($i=0;$i<count($values)-1;$i++){
				if($keys!=false){
					for($j=0;$j<count($keys);$j++){
						if($fields[$i]==$keys[$j]){
							$query.=$char.$fields[$i].$char."=:val".$j." and ";
							$params[':val'.$j]=array($values[$i],PDO::PARAM_STR);
						}
					}
				}else{
					$query.=$char.str_replace($dontAllowed,'',$fields[$i]).$char."=:val".$i."' and ";
					$params[':val'.$i]=array($values[$i],PDO::PARAM_STR);
				}
			}
			$query=trim($query, " and ");
			$query="d".$query.(($dbhelper->db=="mysql")?" limit 1":"");
		break;
		case 'Upd': //Actions for update a row
			$newValues=$_REQUEST['newValues'];
			$query="update ".$char.$dbhelper->table.$char." set ";
			$flag=false;
			$params=array();
			for($i=0;$i<count($values)-1;$i++){
				if($values[$i]!=$newValues[$i]){
					$query.=$char.str_replace($dontAllowed,'',$fields[$i]).$char."=:val".$i.",";
					$params[':val'.$i]=array($newValues[$i],PDO::PARAM_STR);
					$flag=true;
				}
			}
			if($flag){//If there are changes in the row fields
				$query=trim($query, ",");
				$keys=$dbhelper->getPrimaryKey($dbhelper->table);
				$query.=" where ";
				if($keys!=false){
				    $keyFieldFound=false;
				    for($j=0;$j<count($keys);$j++){
				        if(in_array($keys[$j],$fields)){
				            $keyFieldFound=true;
				        }
				    }
				    if(!$keyFieldFound) $keys=false;
				}
				for($i=0;$i<count($values)-1;$i++){
					if($keys!=false){
						for($j=0;$j<count($keys);$j++){
							if($fields[$i]==$keys[$j]){
								$query.=$char.$fields[$i].$char."=:key".$j." and ";
								$params[':key'.$j]=array($values[$i],PDO::PARAM_STR);
								$keyFieldFound=true;
							}
						}
					}else{
						$query.=$char.str_replace($dontAllowed,'',$fields[$i]).$char."=:key".$i." and ";
						$params[':key'.$i]=array($values[$i],PDO::PARAM_STR);
					}
				}
				$query=trim($query, " and ").(($dbhelper->db=="mysql")?" limit 1":"");
			}else{//If there aren't changes in the row fields
				echo "nothing";
				die();
			}
		break;
		case 'Ins':	//Actions for insert a new row		
			$realValues=array();
			$realFields=array();
			
			$j=0;
			$params=array();
			for($i=0;$i<count($values);$i++){
				if($values[$i]!=""){
					$realValues[$j]=':val'.$i;
					$realFields[$j]=str_replace($dontAllowed,'',$fields[$i]);
					$params[':val'.$i]=array($values[$i],PDO::PARAM_STR);
					$j++;
				}
			}
			$query="insert into ".$char.$dbhelper->table.$char." (".$char.implode($char.",".$char,$realFields).$char.") values(".implode(",",$realValues).")";
		break;
	}
	
	if($dbhelper->executeQuery($query,$params)) echo "true";
?>