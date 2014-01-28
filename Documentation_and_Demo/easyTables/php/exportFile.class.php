<?php

	/*******************************************************************************
	* exportFile                                                                   *
	*                                                                              *
	* Version: 1.0                                                                 *
	* Date:    2014-01-26                                                          *
	* Author:  enri_pin                                                            *
	* Email:   enri_pin@yahoo.com                                                  *
	*******************************************************************************/

	require("fpdf.php");
	class PDF extends FPDF{
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

	class exportFile{
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
	
		function init(){
			ini_set('memory_limit', '1000M');
			set_time_limit(0);
			
			include_once("DBHelper.class.php");
			global $char, $esc, $dontAllowed, $values, $dbhelper, $params, $copyFields, $fields;
			$this->dbhelper=new DBHelper($_POST['configFile']);
			$this->char=$this->dbhelper->character();
			$this->esc=$this->dbhelper->escape();
			$this->dontAllowed=$this->dbhelper->nameDontAllowedChars();
			if(!isset($_POST['searchValue']))	$_POST['searchValue']="";
			if(!isset($_POST['searchField']))	$_POST['searchField']="0";
			$orderField=((isset($_POST['orderField']))?str_replace($this->dontAllowed,'',$_POST['orderField']):"");
			$sense=((isset($_POST['sense']))?((($_POST['sense']=="asc")||($_POST['sense']=="desc"))?$_POST['sense']:""):"");
			$nResults=$_POST['nResults'];
			
			$this->params=array();
			$condition="";
			if(($_POST['searchValue']!="")&&($_POST['searchField']!="0")){
				$searchField=$this->char.str_replace($this->dontAllowed,'',$_REQUEST['searchField']).$this->char;
				$condition=$searchField." like :searchValue";
				$this->params=array(
					":searchValue"=>array("%".$_REQUEST['searchValue']."%",PDO::PARAM_STR)
				);
			}
			$query="select count(*) as total from (".$this->dbhelper->query.") as table_count ".($condition!="" ? "where ".$condition:"");
			$nRows=$this->dbhelper->getQuery($query,$this->params);
			$nPages=ceil($nRows[0][0]/$nResults);
			
			$init=$_POST['actual'];
			$init=($init-1)*$nResults;
			if($init<0)	$first=0;
			$limits="";
			switch($this->dbhelper->db){
				case "mysql":
				case "sqlite":
					$limits=" limit :ninit, :nresult";
					$this->params[':nresult']=array((int)$nResults,PDO::PARAM_INT);
				break;
				case "postgres":
					$limits=" limit :nresult offset :ninit";
					$this->params[':nresult']=array((int)$nResults,PDO::PARAM_INT);
				break;
				case "sqlserver":
					$limits=" a.row_1ab2>:ninit and a.row_1ab2<=:nresult ";
					$this->params[':nresult']=array((int)($init+$nResults),PDO::PARAM_INT);
				break;
			}
			$this->params[':ninit']=array((int)$init,PDO::PARAM_INT);
			//echo $this->dbhelper->query;
			
			$this->fields=$this->dbhelper->getColumns($this->dbhelper->query);
			$this->copyFields=$this->fields;

			for($l=0;$l<count($this->fields);$l++) $this->fields[$l]=$this->char.$this->fields[$l].$this->char;
			if($this->dbhelper->db!="sqlserver"){
				$query="select * from (".$this->dbhelper->query.") as result1";
				if($condition!="") $query.=" where ".$condition;
				if(($orderField!="")&&($sense!=""))
					$query.=" order by ".$this->char.$orderField.$this->char." ".$sense;
				$query.=$limits;
			}else{
				$ord=((($orderField!="")&&($sense!=""))?$orderField." ".$sense:$ord=$this->fields[0]);
				$query="select ".implode(',',$this->fields)." from(select *, ROW_NUMBER() over(order by ".$this->char.$ord.$this->char.") as row_1ab2 from (".$this->dbhelper->query.") b";
				$query.=(($condition!="")?" where ".$condition:"").") a where ".$limits;
			}
			//echo $query;
			$result=$this->dbhelper->getQuery($query,$this->params);
			
			exportFile::exportTo($result);
		}
	
		function exportTo($result){
			switch ($_POST['exportOptions']){
				case "CSV":
				case "Csv":
				case "csv":
					exportFile::generateCSV($result);
				break;
				case "PDF":
				case "pdf":
				case "Pdf":
					exportFile::generatePDF($result);				
				break;
				case "Excel":
				case "excel":
				case "EXCEL":
					exportFile::generateExcel($result);
				break;
				case "XML":
				case "Xml":
				case "xml":
					exportFile::generateXML($result);
				break;
			}
		}
	
		function generatePDF($result){
			
			for($i=0;$i<count($result);$i++){
				for($j=0;$j<count($result[0]);$j++){
					$result[$i][$j]=utf8_decode($result[$i][$j]);
				}
			}
			$pdf = new PDF();
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->SetFont('Arial','',10);
			for($i=0;$i<count($this->copyFields);$i++) if(isset($this->dbhelper->colNames[$this->copyFields[$i]])) $this->copyFields[$i]=$this->dbhelper->colNames[$this->copyFields[$i]];
			if($result!=false){
				$pdf->FancyTable($this->copyFields,$result);
			}
			$pdf->Output($this->dbhelper->fileName.".pdf", 'I');
		}
	
		function generateCSV($result){
			header('Content-Type:text/csv;charset=utf-8');
			header('Content-Disposition:attachment;filename="'.$this->dbhelper->fileName.'.csv"');
			header("Content-Type:application/force-download");
			
			$fp = fopen('php://output', 'w');
			for($i=0;$i<count($this->copyFields);$i++) if(isset($this->dbhelper->colNames[$this->copyFields[$i]])) $this->copyFields[$i]=$this->dbhelper->colNames[$this->copyFields[$i]];
			fputcsv($fp, $this->copyFields, $this->dbhelper->csvChar);
			
			if($result!=false){
				for($i=0;$i<count($result);$i++){
					fputcsv($fp, $result[$i], $this->dbhelper->csvChar);
				}
			}
			fclose($fp);
		}
	
		function generateExcel($result){
			if((@include_once("PHPExcel.php"))===false){
				echo "<p>If you want to enable the option to export to Excel files you'll need to download the <a href='http://phpexcel.codeplex.com/'>PHPExcel Classes</a>. After decompress the file copy the content of the directory <strong>'Classes/'</strong> inside the directory <strong>'easyTables/php/'</strong></p>";
				die();
			}
			require_once("PHPExcel/Writer/Excel2007.php");
			global $leter;
			$this->leter=array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
				
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
			
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Multi-DB Tables");
			$objPHPExcel->setActiveSheetIndex(0);
			
			for($i=0;$i<count($this->copyFields);$i++) if(isset($this->dbhelper->colNames[$this->copyFields[$i]])) $this->copyFields[$i]=$this->dbhelper->colNames[$this->copyFields[$i]];
			
			for($i=0;$i<count($this->copyFields);$i++){
				$objPHPExcel->getActiveSheet()->SetCellValue(exportFile::getCell($i, 0), $this->copyFields[$i]);
				$objPHPExcel->getActiveSheet()->getStyle(exportFile::getCell($i,0))->applyFromArray($styleCell);
			}
			if($result!=false){
				for($i=0;$i<count($result);$i++){
					for($j=0;$j<count($result[0]);$j++){
						$objPHPExcel->getActiveSheet()->SetCellValue(exportFile::getCell($j,$i+1), $result[$i][$j]);
					}
				}
			}
			$objPHPExcel->getActiveSheet()->setTitle($this->dbhelper->fileName);
			$objPHPExcel->getSecurity()->setLockWindows(true);
			$objPHPExcel->getSecurity()->setLockStructure(true);
			$objPHPExcel->getProperties()->setTitle($this->dbhelper->fileName);
			
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'.$this->dbhelper->fileName.'.xlsx"');
			header('Cache-Control: max-age=0');
			$objPHPExcel->getActiveSheet()->getStyle(exportFile::getCell(0,0).":".exportFile::getCell(count($this->fields)-1,0))->applyFromArray($styleHeader);
			$objPHPExcel->getActiveSheet()->getStyle(exportFile::getCell(0,0).":".exportFile::getCell(count($this->fields)-1,count($result)))->applyFromArray($styleTable);
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			$objWriter->save('php://output');
		}
	
		function getCell($column,$row){
			$row++;
			if($column<=count($this->leter)-1){
				$cell=$this->leter[$column].$row;
			}else{
				$cociente=0;
				$residuo=$column;
				while($residuo>count($this->leter)-1){
					$residuo-=count($this->leter)-1;
					$cociente++;
				}
				$cell=$this->leter[$cociente-1].$this->leter[$residuo-1].$row;
			}
			return $cell;
		}
	
		function generateXML($result){
			header("Content-type: text/xml");
			header('Content-Disposition:attachment;filename="'.$this->dbhelper->fileName.'.xml"');
			header("Content-Type:application/force-download");
			
			$xml = new DOMDocument('1.0', 'UTF-8');
			$root = $xml->appendChild($xml->createElement(str_replace(" ","-",$this->dbhelper->fileName)));
			
			if($result!=false){
				for($i=0;$i<count($result);$i++){
					$row = $root->appendChild($xml->createElement('row'));
					for($j=0;$j<count($this->copyFields);$j++){
						$field=$row->appendChild($xml->createElement(str_replace(" ","-",$this->copyFields[$j])));
						$field->appendChild($xml->createTextNode($result[$i][$j]));
					}
				}
			}
			$xml->formatOutput = true;
			echo $xml->saveXML();
		}
	}

	$file=new exportFile();
	$file->init();
?>