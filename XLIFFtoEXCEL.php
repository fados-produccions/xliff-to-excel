<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

	function files_reader($root)	{

		$fileArray  = scandir($root,1);
		if ( !$fileArray ) {
			return 0; 			//'Directory No Correct - You can setup the error
		}

		$file_content = array();
		foreach ( $fileArray as $file) {
			if ( $file == "." or $file == "..") {
				break;
			}

			$lang_file = substr($file, 9, 2 );
			$file_content[$lang_file] = current($trans_unit = simplexml_load_file($root . '/' . $file)->file->body);
		}

		return $file_content;
	}

	function filling_excel($arrFile, $fileName) {
		if (!$arrFile) {
			return 0;
		}

		echo date('H:i:s') , " Create new PHPExcel object" , EOL;
		$objPHPExcel = new PHPExcel();

		// Set document properties
		echo date('H:i:s') , " Set document properties" , EOL;
		$objPHPExcel->getProperties()->setCreator("Fados Produccions")
									 ->setLastModifiedBy("Fados Produccions")
									 ->setTitle("Translations Excel")
									 ->setSubject("Translations Excel")
									 ->setDescription("Translations")
									 ->setKeywords("office PHPExcel php")
									 ->setCategory("Excel Translations");

		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Id');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Clave de Contenido');

		$abc = array('C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K');	// ABC EXCEL
		$i = 0;
		foreach ($arrFile as $arrKey => $file) {
			$objPHPExcel->getActiveSheet()->setCellValue($abc[$i] . 1, $arrKey);	//COLUMN LANGUAGE NAME($arrKey)
			$i++;
			foreach ($file as $fileKey => $row) {		
				if ($i == 1 ) {														//FIRST LOOP ADDING ID_ELEMENT
					if (is_array($file)) {
						$id = $row->attributes()->id;
						$objPHPExcel->getActiveSheet()->setCellValue('A' . (int) ($fileKey + 2), (int) $id);
					} else {
						$id = $file->attributes()->id;
						$objPHPExcel->getActiveSheet()->setCellValue('A' . (int) ($fileKey + 2), (int) $id);
					}
				}

				if ( is_array($file)) {
					$objPHPExcel->getActiveSheet()->setCellValue('B' . (int)($fileKey+2) , $row->source);	// source
					$objPHPExcel->getActiveSheet()->setCellValue($abc[$i-1] . (int)($fileKey + 2) , $row->target);	//target
				} else {	// No es un array y si hay, solo hay un elemento
					if ($file) {
						$objPHPExcel->getActiveSheet()->setCellValue('B2', $file->source);	//source
						$objPHPExcel->getActiveSheet()->setCellValue($abc[$i-1] . (int)2 , $file->target);	//target
					}
				}
			}
		}

		$objPHPExcel->getActiveSheet()
			->getStyle('B1:B' . (int)($fileKey+2) )
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '8DB4E3')
                    )
                )
            );

        $objPHPExcel->getActiveSheet()
		    ->getStyle('A1:' . $abc[(int)($i-1)] . '1')
            ->applyFromArray(
		        array(
		            'fill' => array(
		                'type' => PHPExcel_Style_Fill::FILL_SOLID,
		                'color' => array('rgb' => '8DB4E3')
		            ),
					'font' => array(
       					'name' => 'Arial',
                        'size' => 12,
                        'bold' => true
                    )		            
		        )
		    );   

		$rowA ='A1:A' . (int)($fileKey+2);
		$objPHPExcel->getActiveSheet()
		    ->getStyle($rowA )
            ->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'C5D9F1')
                    )
                )
            );       

		$objPHPExcel->getActiveSheet()->setTitle('Translations');
		// Save Excel 2007 file
		echo date('H:i:s') , " Write to Excel2007 format" , EOL;
		$callStartTime = microtime(true);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save(str_replace('.php', '.xlsx', $fileName . '.xlsx'));
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;

		echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
		echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
		// Echo memory usage
		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


		// Save Excel 95 file
		echo date('H:i:s') , " Write to Excel5 format" , EOL;
		$callStartTime = microtime(true);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save(str_replace('.php', '.xls', $fileName . '.xls'));
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;

		echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
		echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
		// Echo memory usage
		echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


		// Echo memory peak usage
		echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

		// Echo done
		echo date('H:i:s') , " Done writing files" , EOL;
		echo 'Files have been created in ' , getcwd() , EOL;		

		return 1;
	}

	$mainRoot = "demo";	//Origin FolderName
	$file = files_reader( $mainRoot . '/translations/');
	filling_excel($file,$mainRoot);
?>