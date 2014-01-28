<?php
	
	/*******************************************************************************
	* DBHelper                                                                     *
	*                                                                              *
	* Version: 1.0                                                                 *
	* Date:    2014-01-26                                                          *
	* Author:  enri_pin                                                            *
	* Email:   enri_pin@yahoo.com                                                  *
	*******************************************************************************/
	
	class dbHelper{
	
		var $db, $dbname, $host, $port, $user, $pass, $query, $table, $csvChar, $fileName, $debugMode;
		var $fields=array(), $colSizes=array(), $colNames=array(), $colColorsOdd=array(), $colColorsEven=array();
		var $colOrderColors=array(), $colOrderFontColors=array(), $colFontColors=array(), $colHeadColors=array();
		var $colHeadFontColors=array(), $friendlyOutput=array(), $colAlign=array();
		
		function dbHelper($configFile){
			/*
				$configFile:
				    Path of the file that contains all the connection parameters and other configuration info
			*/
			include_once($configFile);
			$this->db=strtolower($db); //Database manager to be used (mysql, sqlite, sqlserver or postgres)
			if(isset($host))	$this->host=$host; //Host where the database is located (does not apply to sqlite)
			if(isset($dbname)) $this->dbname=$dbname; //Name of the database to be used (does not apply to sqlite)
			if(isset($user)) $this->user=$user; //User name to be usedto connect to the database (does not apply to sqlite)
			if(isset($pass)) $this->pass=$pass; //User password (does not apply to sqlite)
			if(isset($table)) $this->table=$table; //Table source of information that will be shown (you can use a sql query instead like information source but if you'll allow the edition you'll need use this option too)
			if(isset($port)) $this->port=$port; //Port that will be used for the connection (only for postgresql)
			if(isset($fields))	$this->fields=$fields; //Array with the fields name that will be shown from the table (This is optional if you don't want to show all the fields from a table)
			if(isset($colSizes))	$this->colSizes=$colSizes; //Associative array ("field_name"=>"width"). Use this option if you want to set a specific width in one o several table columns 
			if(isset($colNames))	$this->colNames=$colNames; //Associative array ("field_name"=>"desired name"). Use this option if you want to change the column name that will be shown in the table header
			if(isset($colColorsOdd))	$this->colColorsOdd=$colColorsOdd; //This option allows you to change the default color used in a column. This is the color of the odd cells in the column.
			if(isset($colColorsEven))	$this->colColorsEven=$colColorsEven; //This option allows you to change the default color used in a column. This is the color of the even cells in the column.
			if(isset($colFontColors))	$this->colFontColors=$colFontColors; //Associative array ("field_name"=>"#color"). This option allows you to change the default font color of one or several columns.
			if(isset($colHeadColors))	$this->colHeadColors=$colHeadColors; //Associative array ("field_name"=>"#color"). This option allows you to change the default background color of one or several columns headers.
			if(isset($colOrderColors))	$this->colOrderColors=$colOrderColors; //Associative array ("Odd|Even"=>"#color"). This option allows you to change the default colors of the active order column
			if(isset($colOrderFontColors))	$this->colOrderFontColors=$colOrderFontColors; ////Associative array ("Odd"|"Even"=>"#color"). This option allows you to change the default font colors of the active order column.
			if(isset($colHeadFontColors))	$this->colHeadFontColors=$colHeadFontColors; //Associative array ("field_name"=>"#color"). This option allows you to change the default font color of one or several columns headers.
			if(isset($friendlyOutput))	$this->friendlyOutput=$friendlyOutput; //Associative array ("field_name"=>array("db_output"=>"html_replacement")). This option allows you to replace a specific string with whatever html string.
			if(isset($colAlign))	$this->colAlign=$colAlign; //Associative array ("field_name"=>"left|center|right"). This option allows you to replace an specific column aligment.
			$this->csvChar=((isset($csvChar))?$csvChar:","); //This option allows you to change the default CSV character "," by another one
			$this->debugMode=((isset($debugMode))?$debugMode:false); //This option allows you to see the sql query that is shown in the table
			$this->fileName=((isset($fileName))?$fileName:"EasyTables"); //This option allows you to change the default name of the output files
			$this->query=((isset($query))?$query:DBHelper::createQuery());
		}
		
		function createQuery(){
		    //This function returns a sql query when you only specify the table name, and maybe the fields to be shown
			$char=DBHelper::character();
			return "select ".(count($this->fields)>0?$char.implode($char.",".$char,$this->fields).$char:"*")." from ".$char.$this->table.$char;
		}
		
		function character(){
		    //This function returns 
			switch($this->db){
				case "mysql":
					$character="`";
				break;
				case "postgres":
				case "sqlite":
				case "sqlserver":
					$character='"';
				break;
			}
			return $character;
		}
		
		function nameDontAllowedChars(){
		    //Function that returns an array with the characters that won't be allown in the table and field names
			return array("/","'",'"',"*","?","=",",","%","(",")","[","]","&",'\\',".","<",">",";","´","{","}","@",":");
		}
		
		function escape(){
		    //Returns the escape character according with the database manager used
			switch($this->db){
				case "mysql":
					$character="\'";
				break;
				case "postgres":
				case "sqlserver":
				case "sqlite":
					$character="''";
				break;
			}
			return $character;
		}
		
		function connection(){
		    //Return the conecction according with the database manager selected
			try {
				switch($this->db){
					case "mysql":
						$conexion=new PDO('mysql:host='.$this->host.';dbname='.$this->dbname.';charset=utf8',$this->user,$this->pass);
					break;
					case "sqlserver":
						$conexion=new PDO('sqlsrv:Server='.$this->host.';Database='.$this->dbname,$this->user,$this->pass);
					break;
					case "postgres":
						$conexion=new PDO('pgsql:host='.$this->host.';dbname='.$this->dbname.';port='.$this->port,$this->user,$this->pass);
					break;
					case "sqlite":
						$conexion=new PDO('sqlite:'.$this->dbname);
					break;
				}
				return $conexion;
			}catch (PDOException $e) {
				echo $e->getMessage();
				die();
			}
		}
		
		function getColumns($query, $params=array()){
		    //Returns an array with the column names of the result fields from execute a specific query
			$array_columnas=array();
			$n_campos=0;
			$conexion=DBHelper::connection();
			try{
				$result = $conexion->prepare($query);
				$conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				foreach($params as $index => $ide){
					$result->bindValue($index,$ide[0],$ide[1]);
				}
				$result->execute();
				for($i=0;$i<$result->columnCount();$i++){
					$meta=$result->getColumnMeta($i);
					$array_columnas[$i]=$meta['name'];
				}
				$result=null;
				DBHelper::closeConnection($conexion);
				return $array_columnas;
			}catch (PDOException $e) {
				echo $e->getMessage();
				return false;
			}
		}
		
		function getQuery($query,$params=array()){
		    //Return a multidimensional array with the data result from execute a query
			$array_campos=array();
			$arreglo=array(array());
			$fila=0;
			$conexion=DBHelper::connection();
			try{
				$result = $conexion->prepare($query);
				$conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$param=array();
				$val=array();
				$query_copy=$query;
				foreach($params as $index => $ide){
					$result->bindValue($index,$ide[0],$ide[1]);
					if($this->debugMode) $query_copy=str_replace($index, $ide[0], $query_copy);
				}
				if($this->debugMode) echo $query_copy."<br><br>";
				$result->execute();
				if($result!=false){
					for($i=0;$i<$result->columnCount();$i++){
						$meta=$result->getColumnMeta($i);
						$array_campos[$i]=$meta['name'];
					}
					while ($row = $result->fetch(PDO::FETCH_ASSOC)){
						for($i=0;$i<count($array_campos);$i++){
							if($row[$array_campos[$i]]===NULL){
								$arreglo[$fila][$i]="";
							}else
								$arreglo[$fila][$i]=$row[$array_campos[$i]];
						}
						$fila++;
					}
				}
			}catch (PDOException $e) {
				switch	($e->getCode()){
					case "HY000":
					break;
					default:
						echo $e->getMessage();
					break;
				}
				return false;
			}
			$result=null;
			DBHelper::closeConnection($conexion);
			if(count($arreglo)==0){
				return false;
			}else{
				if(count($arreglo)==1){
					for($i=0;$i<count($arreglo[0]);$i++)
						if($arreglo[0][$i]!="")	return $arreglo;
					return false;
				}else
					return $arreglo;
			}
			
		}
		
		function executeQuery($query,$params=array()){
		    //This function execute a query 
			try{
				$conexion=DBHelper::connection();
				$result = $conexion->prepare($query);
				$conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				$query_copy=$query;
				//echo $query."\n";
				//print_r($params);
				foreach($params as $index => $ide){
					//echo $index."-".$ide[0]."-".$ide[1];
					$result->bindValue($index,$ide[0],$ide[1]);
					if($this->debugMode) $query_copy=str_replace($index, $ide[0], $query_copy);
				}
				if($this->debugMode) echo $query_copy."\nResult: ";
				if($result->execute()) return true;
				else return false;
				DBHelper::closeConnection($conexion);
			}catch (PDOException $e) {
				if($this->debugMode)	echo $e->getMessage();
				else{
					switch ($e->getCode()){
						case '23000':
							echo "Error. Index value duplicated!";
						break;
						case '21S01':
							echo "Error. Some of fields can be mandatory!";
						break;
						default:
							echo "There is an Error. You can't perform this action!";
						break;
					}
				}
				return false;
			}
		}
		
		function getPrimaryKey($table){
		    //This functions return the primary keys from a table or false if the table doesn't have primary keys
			$keys=array();
			$i=0;
			$conexion=DBHelper::connection();
			switch($this->db){
				case "mysql":
					$query="SHOW KEYS FROM ".$table." WHERE Key_name = 'PRIMARY'";
					$column='Column_name';
				break;
				case "postgres":
					$query="SELECT kcu.constraint_name, kcu.column_name FROM INFORMATION_SCHEMA.TABLES t LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc ON tc.table_catalog = t.table_catalog AND tc.table_schema = t.table_schema AND tc.table_name = t.table_name AND tc.constraint_type = 'PRIMARY KEY' LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu ON kcu.table_catalog = tc.table_catalog AND kcu.table_schema = tc.table_schema AND kcu.table_name = tc.table_name AND kcu.constraint_name = tc.constraint_name WHERE   t.table_schema NOT IN ('pg_catalog', 'information_schema') and t.table_name='".$table."'";
					$column='column_name';
				break;
				case "sqlserver":
					$query="select I.name as CONSTRAINT_NAME, I.type_desc as TYPE_DESC, AC.name as COLUMN_NAME from sys.tables as T inner join sys.indexes as I on T.[object_id] = I.[object_id] inner join sys.index_columns as IC on IC.[object_id] = I.[object_id] and IC.[index_id] = I.[index_id] inner join sys.all_columns as AC on IC.[object_id] = AC.[object_id] and IC.[column_id] = AC.[column_id] where T.name='".$table."' and I.is_primary_key='1'";
					$column='COLUMN_NAME';
				break;
				case "sqlite":
					$query="PRAGMA table_info(".$table.")";
					$column='name';
				break;
			}
			$pk_query=$conexion->query($query);
			if($pk_query!=false){
				if($this->db!="sqlite"){
					while($pk=$pk_query->fetch(PDO::FETCH_ASSOC)){
						$keys[$i]=$pk[$column];
						$i++;
					}
				}else{
					while($pk=$pk_query->fetch(PDO::FETCH_ASSOC)){
						if($pk['pk']=="1"){
							$keys[$i]=$pk[$column];
							$i++;
						}
					}
				}
			}
			$pk_query=null;
			DBHelper::closeConnection($conexion);
			return ((count($keys)>0)?$keys:false);
		}

		function closeConnection($conexion){
			$conexion=null;
		}
	}
?>