<?php

	/*******************************************************************************
	* CUD                                                                          *
	*                                                                              *
	* Version: 1.0                                                                 *
	* Date:    2014-01-26                                                          *
	* Author:  enri_pin                                                            *
	* Email:   enri_pin@yahoo.com                                                  *
	*******************************************************************************/
	
	

	class CUD{
		/* 
			Parameters:
				fields: Database table fields
				values: Array with the values of the row that will be deleted or updated. Or the values of the new row.
				newValues: Array with the new values of the row that will be updated
				configFile: File source of information
				action: Action that will be performed by the script(Delete, Update, Insert)
		*/
		
		function init(){
			include_once("DBHelper.class.php");
			global $char, $esc, $dontAllowed, $fields, $values, $dbhelper, $params;
			$this->dbhelper=new DBHelper($_REQUEST['configFile']);
			$this->char=$this->dbhelper->character();
			$this->esc=$this->dbhelper->escape();
			$this->dontAllowed=$this->dbhelper->nameDontAllowedChars();
			$this->fields=$_REQUEST['fields'];
			$this->values=$_REQUEST['values'];
			$query="";
			switch($_REQUEST['action']){
				case 'Del': //Actions for delete a row
					$query=CUD::deleteRow();
				break;
				case 'Upd': //Actions for update a row
					$query=CUD::updateRow();
				break;
				case 'Ins':	//Actions for insert a new row		
					$query=CUD::insertRow();
				break;
			}
			if($this->dbhelper->executeQuery($query,$this->params)) echo "true";
		}
	
				function insertRow(){
			$realValues=array();
			$realFields=array();
			
			$j=0;
			$this->params=array();
			for($i=0;$i<count($this->values);$i++){
				if($this->values[$i]!=""){
					$realValues[$j]=':val'.$i;
					$realFields[$j]=str_replace($this->dontAllowed,'',$this->fields[$i]);
					$this->params[':val'.$i]=array($this->values[$i],PDO::PARAM_STR);
					$j++;
				}
			}
			$query="insert into ".$this->char.$this->dbhelper->table.$this->char." (".$this->char.implode($this->char.",".$this->char,$realFields).$this->char.") values(".implode(",",$realValues).")";
			
			return $query;
		}
		
		function deleteRow(){
			$keys=$this->dbhelper->getPrimaryKey($this->dbhelper->table);
			$query="delete from ".$this->char.$this->dbhelper->table.$this->char." where ";
			$this->params=array();
			for($i=0;$i<count($this->values)-1;$i++){
				if($keys!=false){
					for($j=0;$j<count($keys);$j++){
						if($this->fields[$i]==$keys[$j]){
							$query.=$this->char.$this->fields[$i].$this->char."=:val".$j." and ";
							$this->params[':val'.$j]=array($this->values[$i],PDO::PARAM_STR);
						}
					}
				}else{
					$query.=$this->char.str_replace($this->dontAllowed,'',$this->fields[$i]).$this->char."=:val".$i."' and ";
					$this->params[':val'.$i]=array($this->values[$i],PDO::PARAM_STR);
				}
			}
			$query=trim($query, " and ");
			$query="d".$query.(($this->dbhelper->db=="mysql")?" limit 1":"");
			
			return $query;
		}
		
		function updateRow(){
			$newValues=$_REQUEST['newValues'];
			$query="update ".$this->char.$this->dbhelper->table.$this->char." set ";
			$flag=false;
			$this->params=array();
			for($i=0;$i<count($this->values)-1;$i++){
				if($this->values[$i]!=$newValues[$i]){
					$query.=$this->char.str_replace($this->dontAllowed,'',$this->fields[$i]).$this->char."=:val".$i.",";
					$this->params[':val'.$i]=array($newValues[$i],PDO::PARAM_STR);
					$flag=true;
				}
			}
			if($flag){//If there are changes in the row fields
				$query=trim($query, ",");
				$keys=$this->dbhelper->getPrimaryKey($this->dbhelper->table);
				$query.=" where ";
				if($keys!=false){
					$keyFieldFound=false;
					for($j=0;$j<count($keys);$j++){
						if(in_array($keys[$j],$this->fields)){
							$keyFieldFound=true;
						}
					}
					if(!$keyFieldFound) $keys=false;
				}
				for($i=0;$i<count($this->values)-1;$i++){
					if($keys!=false){
						for($j=0;$j<count($keys);$j++){
							if($this->fields[$i]==$keys[$j]){
								$query.=$this->char.$this->fields[$i].$this->char."=:key".$j." and ";
								$this->params[':key'.$j]=array($this->values[$i],PDO::PARAM_STR);
								$keyFieldFound=true;
							}
						}
					}else{
						$query.=$this->char.str_replace($this->dontAllowed,'',$this->fields[$i]).$this->char."=:key".$i." and ";
						$this->params[':key'.$i]=array($this->values[$i],PDO::PARAM_STR);
					}
				}
				$query=trim($query, " and ").(($this->dbhelper->db=="mysql")?" limit 1":"");
				
				return $query;
				
			}else{//If there aren't changes in the row fields
				echo "nothing";
				die();
			}
		}
	
	}
	
	$action=new CUD();
	$action->init();
?>