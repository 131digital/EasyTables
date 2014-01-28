<?php

	/* 
		Parameters:
			orderField: Table field that will be used in order by clause
			sense: ASC or DESC used with "order by" clause
			nResults: Number of rows that will be shown in the result table
			search: String to be searched in the database table
			searchField: Table field in where the search will be done
			actual: Page actual
			prev: Previous page from where you come
			configFile: File source of information
	*/
	
	ini_set('memory_limit', '1000M');
	set_time_limit(0);
	
	include_once("DBHelper.php");
	$dbhelper=new DBHelper($_REQUEST['configFile']);
	$char=$dbhelper->character();
	$esc=$dbhelper->escape();
	$dontAllowed=$dbhelper->nameDontAllowedChars();
	$orderField=((isset($_REQUEST['orderField']))?str_replace($dontAllowed,'',$_REQUEST['orderField']):"");
	$sense=((isset($_REQUEST['sense']))?$_REQUEST['sense']:"");
	$nResults=$_REQUEST['nResults'];
	
	$params=array();
	//Creating search condition if exists for the query
	$condition="";
	if(($_REQUEST['search']!="")&&($_REQUEST['searchField']!="0")){
		$searchField=$char.str_replace($dontAllowed,'',$_REQUEST['searchField']).$char;
		$condition=$searchField." like :searchValue";
		$params=array(
			":searchValue"=>array("%".$_REQUEST['search']."%",PDO::PARAM_STR)
		);
	}
	$query="select count(*) as total from (".$dbhelper->query.") as table_count ".(($condition!="")?"where ".$condition:"");

	$nRows=$dbhelper->getQuery($query,$params);
	$nPages=ceil($nRows[0][0]/$nResults);
	
	//======================Calculating the number of page actual============================
	$init=0;
	if(isset($_REQUEST['actual'])){
		if($_REQUEST['actual']>$nPages){
			$init=$nPages;
			$actual=$nPages;
		}else{
			$init=$_REQUEST['actual'];
			$actual=$_REQUEST['actual'];
		}
		switch($init){
			case "First":
				$init=0;
				$actual=1;
			break;
			case "Last":
				$init=(($nPages-1)*$nResults);
				$actual=$nPages;
			break;
			case ">>":
				$init=($_REQUEST['prev'])*$nResults;
				$actual=$_REQUEST['prev']+1;
			break;
			case "<<":
				$init=($_REQUEST['prev']-2)*$nResults;
				$actual=$_REQUEST['prev']-1;
			break;
			default:
				$init=($init-1)*$nResults;
			break;
		}
	}else	$actual=1;

	$fields=$dbhelper->getColumns($dbhelper->query);
	for($l=0;$l<count($fields);$l++) $fields[$l]=$char.$fields[$l].$char;
	
	//==========================Setting limits for the sql query==============================
	$limits="";
	switch($dbhelper->db){
		case "mysql":
		case "sqlite":
			$limits=" limit :ninit, :nresult";
			$params[':nresult']=array((int)$nResults,PDO::PARAM_INT);
		break;
		case "postgres":
			$limits=" limit :nresult offset :ninit";
			$params[':nresult']=array((int)$nResults,PDO::PARAM_INT);
		break;
		case "sqlserver":
			$limits=" a.row_1ab2>:ninit and a.row_1ab2<=:nresult ";
			$params[':nresult']=array((int)($init+$nResults),PDO::PARAM_INT);
		break;
	}
	$params[':ninit']=array((int)$init,PDO::PARAM_INT);
	
	//========Creating the sql instruction to be executed and shown in the result table=========
	if($dbhelper->db!="sqlserver"){
		$query="select * from (".$dbhelper->query.") as result1";
		if($condition!="") $query.=" where ".$condition;
		if(($orderField!="")&&($sense!="")){
			$query.=" order by ".$char.$orderField.$char." ".$sense;
		}
		$query.=$limits;
	}else{
		$ord="";
		if(($orderField!="")&&($sense!="")){
			$query.=" order by ".$char.$orderField.$char." ".$sense;
		}else
			$ord=$fields[0];
		$query="select ".implode(',',$fields)." from(select *, ROW_NUMBER() over(order by ".$ord.") as row_1ab2 from (".$dbhelper->query.") b";
		$query.=(($condition!="")?" where ".$condition:"").") a where ".$limits;
	}
	
	$result=$dbhelper->getQuery($query, $params);
	
	$showCheck=$_REQUEST['showCheck'];
	
	//=================Generating the html table to be shown in the web page===============
	echo "<table class='resultTable'>\n<tr class='header'>".(($showCheck!=0)?"<th class='control'>".(($showCheck!=3)?"<input type='checkbox'>":"")."</th>":"");
	$orderNumber=-1;
	$colSize="";
	$colName="";
	
    //Working with the table headers
	if(count($dbhelper->colHeadColors)>0)	$colH=$dbhelper->colHeadColors;
	if(count($dbhelper->colHeadFontColors)>0)	$colHF=$dbhelper->colHeadFontColors;
	for($i=0;$i<count($fields);$i++){
		$fields[$i]=trim($fields[$i],$char);
		$colSize=((isset($dbhelper->colSizes[$fields[$i]]))?" width='".$dbhelper->colSizes[$fields[$i]]."'":"");
		$colName=((isset($dbhelper->colNames[$fields[$i]]))?$dbhelper->colNames[$fields[$i]]:$fields[$i]);
		if($fields[$i]==$orderField){
			//Here you can change the down up icons. Modify <i class='icon-chevron-".(($sense=="asc")?"up":"down")." icon-white'>"
			echo "<th".((isset($dbhelper->colAlign[$fields[$i]]))?" align='".$dbhelper->colAlign[$fields[$i]]."'":"")." class='ord'".$colSize." name='".$fields[$i]."' ".((isset($colH[$fields[$i]]))?"style='background:".$colH[$fields[$i]].";".((isset($colHF[$fields[$i]]))?'color:'.$colHF[$fields[$i]].';':'')."'":"").">".$colName." <i class='icon-chevron-".(($sense=="asc")?"up":"down")." icon-white'></i></th>";
			$orderNumber=$i;
		}else{
			echo "<th".((isset($dbhelper->colAlign[$fields[$i]]))?" align='".$dbhelper->colAlign[$fields[$i]]."'":"")." class='ord'".$colSize." name='".$fields[$i]."' ".((isset($colH[$fields[$i]]))?"style='background:".$colH[$fields[$i]].";".((isset($colHF[$fields[$i]]))?'color:'.$colHF[$fields[$i]].';':'')."'":"").">".$colName."</th>";
		}
	}
	echo "<th class='control2'></th></tr>\n";
	$background="";
	echo "<tr class='0'></tr>";
	
	//Working with the table body
	if($result!=false){//If the table has rows
		for($i=0;$i<count($result);$i++){
		    //Setting the color of each row according the user input or by default 
			if(($i%2)==0){
				$bgorder=((isset($dbhelper->colOrderColors['Even']))?$dbhelper->colOrderColors['Even']:"#888");
				$cOrder=((isset($dbhelper->colOrderFontColors['Even']))?$dbhelper->colOrderFontColors['Even']:"#fff");
				if(count($dbhelper->colColorsEven)>0)	$colC=$dbhelper->colColorsEven;
				elseif(count($dbhelper->colColorsOdd)>0)	$colC=$dbhelper->colColorsOdd;
				if(count($dbhelper->colFontColors)>0) $colFC=$dbhelper->colFontColors;
			}else{
				$bgorder=((isset($dbhelper->colOrderColors['Odd']))?$dbhelper->colOrderColors['Odd']:"#666");
				$cOrder=((isset($dbhelper->colOrderFontColors['Odd']))?$dbhelper->colOrderFontColors['Odd']:"#fff");
				if(count($dbhelper->colColorsOdd)>0)	$colC=$dbhelper->colColorsOdd;
				elseif(count($dbhelper->colColorsEven)>0)	$colC=$dbhelper->colColorsEven;
				if(count($dbhelper->colFontColors)>0) $colFC=$dbhelper->colFontColors;
			}
			echo "<tr class='".($i+1)."'>";
			if($showCheck!=0) echo "<td class='control'>".(($showCheck!=3)?"<input type='checkbox' class='".($i+1)."'>":"")."</td>";
			//Generating the html of the table cells
			for($j=0;$j<count($fields);$j++){
				if(isset($result[$i][$j])){
					if($j!=$orderNumber){
						echo "<td";
						if((isset($colC[$fields[$j]]))||(isset($colFC[$fields[$j]]))){
							echo " style='";
							if(isset($colC[$fields[$j]])){
								echo "background:".$colC[$fields[$j]].";".((!isset($colFC[$fields[$j]]))?"'":"");
							}
							if(isset($colFC[$fields[$j]])){
								echo "color:".$colFC[$fields[$j]]."'";
							}
						}
						if(isset($dbhelper->friendlyOutput[$fields[$j]][$result[$i][$j]]))
							echo " value='".$result[$i][$j]."'".((isset($dbhelper->colAlign[$fields[$j]]))?" align='".$dbhelper->colAlign[$fields[$j]]."'":"").">".$dbhelper->friendlyOutput[$fields[$j]][$result[$i][$j]]."</td>";
						else
							echo ((isset($dbhelper->colAlign[$fields[$j]]))?" align='".$dbhelper->colAlign[$fields[$j]]."'":"").">".$result[$i][$j]."</td>";
					}else{
						if(isset($dbhelper->friendlyOutput[$fields[$j]][$result[$i][$j]]))
							echo "<td".((isset($dbhelper->colAlign[$fields[$j]]))?" align='".$dbhelper->colAlign[$fields[$j]]."'":"")." style='background:".((isset($colC[$fields[$j]]))?$colC[$fields[$j]]:$bgorder).";color:".((isset($colFC[$fields[$j]]))?$colFC[$fields[$j]]:$cOrder)."' value='".$result[$i][$j]."'>".$dbhelper->friendlyOutput[$fields[$j]][$result[$i][$j]]."</td>";
						else
							echo "<td".((isset($dbhelper->colAlign[$fields[$j]]))?" align='".$dbhelper->colAlign[$fields[$j]]."'":"")." style='background:".((isset($colC[$fields[$j]]))?$colC[$fields[$j]]:$bgorder).";color:".((isset($colFC[$fields[$j]]))?$colFC[$fields[$j]]:$cOrder)."'>".$result[$i][$j]."</td>";
					}
				}
			}
			/*
				To change the icons for edit and show you can just change the html code inside the span tags.
				Then if it's necessary you can change the .control2 width in the css/easyTable.css file
			*/
			echo "<td class='control2'><span title='see' class='".($i+1)."'>".(isset($dbhelper->changeIcons['show'])?$dbhelper->icons['show']:"<i class='icon-eye-open'></i>")."</span>".(($showCheck==2)?" <span title='edit' class='".($i+1)."'><i class='icon-pencil'></i></span>":"")."</td>";
			echo "</tr>\n";
		}
	}else{
		echo "<tr><td colspan='".(count($fields)+2)."' class='no_result'>There are no results</td></tr>";
	}
	echo "</table>";
	
	//=======================Generating the pager for the table============================
	echo "<table class='pager'><tr><td class='exp left'>Export to <select name='exportOptions'></select> <a class='get button icon arrowdown'>Get</a></td><td class='right'>";
	echo "<span class='total'>Total: ".$nRows[0][0]." results.</span> ";
	if($nRows[0][0]==0) $actual=0;
	echo "Page ".$actual." of ".$nPages."  ";
	if($nPages>5){
		if($actual<=3){
			if($actual!=1)
				echo '<input type="button" value="<<" class="pag">  ';
			for($i=1;$i<=5;$i++){
				if($i!=$actual)
					echo "<input type='button' value='".$i."' class='pag'> ";
				else
					echo "<input type='button' value='".$i."' class='actual'>  ";
			}
			echo "<input type='button' value='>>' class='pag'>  ";
			echo "<input type='button' value='Last' class='pag'>";
		}elseif($actual<=$nPages-2){
			echo "<input type='button' value='First' class='pag'>  ";
			echo '<input type="button" value="<<" class="pag">  ';
			for($i=$actual-2;$i<=$actual+2;$i++){
				if($i!=$actual)
					echo "<input type='button' value='".$i."' class='pag'>  ";
				else
					echo "<input type='button' value='".$i."' class='actual'>  ";
			}
			echo "<input type='button' value='>>' class='pag'>  ";
			echo "<input type='button' value='Last' class='pag'>";
		}else{
			echo "<input type='button' value='First' class='pag'>  ";
			echo '<input type="button" value="<<" class="pag"> ';
			for($i=$nPages-4;$i<=$nPages;$i++){
				if($i!=$actual)
					echo "<input type='button' value='".$i."' class='pag'>  ";
				else
					echo "<input type='button' value='".$i."' class='actual'>  ";
			}
			if($actual!=$nPages){
				echo "<input type='button' value='>>' class='pag'>  ";
			}
		}
	}else{
		for($i=1;$i<=$nPages;$i++){
			if($i!=$actual) 
				echo "<input type='button' value='".$i."' class='pag'>  ";
			else
				echo "<input type='button' value='".$i."' class='actual'>  ";
		}
	}
	echo "</td></tr></table>";
?>