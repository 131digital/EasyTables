<?php
	/* 
		Parameters:
			orderField: Table field that will be used in order by clause
			sense: ASC or DESC used with "order by" clause
			nResults: Number of rows that will be shown in the result table
			searchValue: String to be searched in the database table
			searchField: Table field in where the search will be done
			actual: Page actual
			prev: Previous page from where you come
			configFile: File source of information
			exportOptions: Selected export format(xml, excel, pdf, csv)
	*/
	
	ini_set('memory_limit', '1000M');
	set_time_limit(0);
	
	include_once("DBHelper.php");
	$dbhelper=new DBHelper($_POST['configFile']);
	$char=$dbhelper->character();
	$esc=$dbhelper->escape();
	$dontAllowed=$dbhelper->nameDontAllowedChars();
	if(!isset($_POST['searchValue']))	$_POST['searchValue']="";
	if(!isset($_POST['searchField']))	$_POST['searchField']="0";
	$orderField=((isset($_POST['orderField']))?str_replace($dontAllowed,'',$_POST['orderField']):"");
	$sense=((isset($_POST['sense']))?((($_POST['sense']=="asc")||($_POST['sense']=="desc"))?$_POST['sense']:""):"");
	$nResults=$_POST['nResults'];
	
	$params=array();
	$condition="";
	if(($_POST['searchValue']!="")&&($_POST['searchField']!="0")){
		$searchField=$char.str_replace($dontAllowed,'',$_REQUEST['searchField']).$char;
		$condition=$searchField." like :searchValue";
		$params=array(
			":searchValue"=>array("%".$_REQUEST['searchValue']."%",PDO::PARAM_STR)
		);
	}
	$query="select count(*) as total from (".$dbhelper->query.") as table_count ".($condition!="" ? "where ".$condition:"");
	$nRows=$dbhelper->getQuery($query,$params);
	$nPages=ceil($nRows[0][0]/$nResults);
	
	$init=$_POST['actual'];
	$init=($init-1)*$nResults;
	if($init<0)	$first=0;
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
	//echo $dbhelper->query;
	
	$fields=$dbhelper->getColumns($dbhelper->query);
	$copyFields=$fields;

	for($l=0;$l<count($fields);$l++) $fields[$l]=$char.$fields[$l].$char;
	if($dbhelper->db!="sqlserver"){
		$query="select * from (".$dbhelper->query.") as result1";
		if($condition!="") $query.=" where ".$condition;
		if(($orderField!="")&&($sense!=""))
			$query.=" order by ".$char.$orderField.$char." ".$sense;
		$query.=$limits;
	}else{
		$ord=((($orderField!="")&&($sense!=""))?$orderField." ".$sense:$ord=$fields[0]);
		$query="select ".implode(',',$fields)." from(select *, ROW_NUMBER() over(order by ".$char.$ord.$char.") as row_1ab2 from (".$dbhelper->query.") b";
		$query.=(($condition!="")?" where ".$condition:"").") a where ".$limits;
	}
	//echo $query;
	$result=$dbhelper->getQuery($query,$params);
	
	switch ($_POST['exportOptions']){
		case "CSV":
		case "Csv":
		case "csv":
			header('Content-Type:text/csv;charset=utf-8');
			header('Content-Disposition:attachment;filename="'.$dbhelper->fileName.'.csv"');
			header("Content-Type:application/force-download");
			
			$fp = fopen('php://output', 'w');
			for($i=0;$i<count($copyFields);$i++) if(isset($dbhelper->colNames[$copyFields[$i]])) $copyFields[$i]=$dbhelper->colNames[$copyFields[$i]];
			fputcsv($fp, $copyFields, $dbhelper->csvChar);
			
			if($result!=false){
				for($i=0;$i<count($result);$i++){
					fputcsv($fp, $result[$i], $dbhelper->csvChar);
				}
			}
			fclose($fp);
		break;
		case "PDF":
		case "pdf":
		case "Pdf":
			require("fpdf.php");
			class PDF extends FPDF
			{
				function Footer(){
					$this->SetY(-15);
					$this->SetFont('Arial','I',8);
					$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
				}
				
				function FancyTable($header, $data){
					/*
						In this function you can change the headers background colors and font colors in general
						and other attributes of the table in the PDF file
					*/
					$this->SetFillColor(0,120,41);
					$this->SetTextColor(255);
					$this->SetDrawColor(0,0,0);
					$this->SetLineWidth(.3);
					$this->SetFont('','B');
					$fontSize=10;
					$decrementStep=0.1;
					$lineWidth = 192;
					$w=array();
					$lineWidthRow=5000;
					while($lineWidthRow>$lineWidth){
						$lineWidthRow=0;
						for($i=0;$i<count($header);$i++){
							$w[$i]=$this->GetStringWidth($header[$i])+2;
							$lineWidthRow+=$w[$i];
						}
						for($i=0;$i<count($data);$i++){
							$lineWidthRow=0;
							for($j=0;$j<count($header);$j++){
								$calculo=$this->GetStringWidth($data[$i][$j])+2;
								if($w[$j]<$calculo){
									$w[$j]=$calculo;
								}
								$lineWidthRow+=$w[$j];
							}
							if($lineWidthRow>$lineWidth){
								$i=count($data)+1;
								//echo "<br>font-size: ".$fontSize." line-".$lineWidthRow;
								$this->SetFontSize($fontSize -= $decrementStep);
							}
						}
					}
					for($i=0;$i<count($header);$i++)
						$this->Cell($w[$i],6,$header[$i],1,0,'C',true);
					$this->Ln();
					$this->SetFillColor(224,235,255);
					$this->SetTextColor(0);
					$this->SetFont('');
					$fill = false;
					foreach($data as $row)
					{
						for($i=0;$i<count($row);$i++)
							$this->Cell($w[$i],5,$row[$i],'LR',0,'L',$fill);
						$this->Ln();
						$fill = !$fill;
					}
					$this->Cell(array_sum($w),0,'','T');
				}
			}
			
			for($i=0;$i<count($result);$i++){
				for($j=0;$j<count($result[0]);$j++){
					$result[$i][$j]=utf8_decode($result[$i][$j]);
				}
			}
			$pdf = new PDF();
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->SetFont('Arial','',10);

			for($i=0;$i<count($copyFields);$i++) if(isset($dbhelper->colNames[$copyFields[$i]])) $copyFields[$i]=$dbhelper->colNames[$copyFields[$i]];
			if($result!=false){
				$pdf->FancyTable($copyFields,$result);
			}
			$pdf->Output($dbhelper->fileName.".pdf", 'I');
			
		break;
		case "Excel":
		case "excel":
		case "EXCEL":
			require_once("PHPExcel.php");
			require_once("PHPExcel/Writer/Excel2007.php");
			$leter=array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
			
			/*
				In this array you can change the headers background colors and font colors in general
				and other attributes of the table in the Excel file
			*/
			$styleHeader = array(
				'font' => array(
					'bold' => true,
					'color' => array(
						'rgb' => 'FFFFFF'
					)
				),
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					),
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					),
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
					'rotation' => 90,
					'startcolor' => array(
						'argb' => 'FF009047',
					),
					'endcolor' => array(
						'argb' => 'FF000000',
					),
				),
			);
			
			$styleTable = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				),
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					),
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
					),
				),
			);
			
			$styleCell = array(
				'borders' => array(
					'top' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'right' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
					),
				),
			);
			
			function getCell($column,$row){
				global $leter;
				$row++;
				if($column<=count($leter)-1){
					$cell=$leter[$column].$row;
				}else{
					$cociente=0;
					$residuo=$column;
					while($residuo>count($leter)-1){
						$residuo-=count($leter)-1;
						$cociente++;
					}
					$cell=$leter[$cociente-1].$leter[$residuo-1].$row;
				}
				return $cell;
			}
			
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Multi-DB Tables");
			$objPHPExcel->setActiveSheetIndex(0);
			
			for($i=0;$i<count($copyFields);$i++) if(isset($dbhelper->colNames[$copyFields[$i]])) $copyFields[$i]=$dbhelper->colNames[$copyFields[$i]];
			
			for($i=0;$i<count($copyFields);$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue(getCell($i, 0), $copyFields[$i]);
				$objPHPExcel->getActiveSheet()->getStyle(getCell($i,0))->applyFromArray($styleCell);
			}

			if($result!=false){
				for($i=0;$i<count($result);$i++){
					for($j=0;$j<count($result[0]);$j++){
						$objPHPExcel->getActiveSheet()->SetCellValue(getCell($j,$i+1), $result[$i][$j]);
					}
				}
			}
			$objPHPExcel->getActiveSheet()->setTitle($dbhelper->fileName);
			$objPHPExcel->getSecurity()->setLockWindows(true);
			$objPHPExcel->getSecurity()->setLockStructure(true);
			$objPHPExcel->getProperties()->setTitle($dbhelper->fileName);
			
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'.$dbhelper->fileName.'.xlsx"');
			header('Cache-Control: max-age=0');
			$objPHPExcel->getActiveSheet()->getStyle(getCell(0,0).":".getCell(count($fields)-1,0))->applyFromArray($styleHeader);
			$objPHPExcel->getActiveSheet()->getStyle(getCell(0,0).":".getCell(count($fields)-1,count($result)))->applyFromArray($styleTable);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
			
		break;
		case "XML":
		case "Xml":
		case "xml":
			header("Content-type: text/xml");
			header('Content-Disposition:attachment;filename="'.$dbhelper->fileName.'.xml"');
			header("Content-Type:application/force-download");
			
			$xml = new DOMDocument('1.0', 'UTF-8');
			$root = $xml->appendChild($xml->createElement(str_replace(" ","-",$dbhelper->fileName)));
			
			if($result!=false){
				for($i=0;$i<count($result);$i++){
					$row = $root->appendChild($xml->createElement('row'));
					for($j=0;$j<count($copyFields);$j++){
						$field=$row->appendChild($xml->createElement(str_replace(" ","-",$copyFields[$j])));
						$field->appendChild($xml->createTextNode($result[$i][$j]));
					}
				}
			}
			$xml->formatOutput = true;

			echo $xml->saveXML();
		break;
	}
?>