<?php

	function excel_reader($fileName) {
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);

		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

		date_default_timezone_set('Europe/London');

		/** Include PHPExcel_IOFactory */
		require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';

		if (!file_exists($fileName . ".xlsx")) {
			return "File ".$fileName.".xlsx doesn't exists";
		}

		$objReader = PHPExcel_IOFactory::createReader('Excel2007');

		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($fileName . ".xlsx");
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

		$excelArr = array();
		$abc = array("A", "B", "C", "D", "E", "F", "G");
		$lastRow = $objWorksheet->getHighestRow();		
		foreach ($abc as $abcKey => $abcValue) {
			$column = $abcValue;
			for ($row = 1; $row <= $lastRow; $row++) {
				switch ($column) {
					case 'A':	//Id
					    $excelArr['id'][$row-1] = $objWorksheet->getCell($column.$row)->getValue();
						break;
					case 'B':	//Key Content
					    $excelArr['clv_content'][$row-1] = $objWorksheet->getCell($column.$row)->getValue();
						break;
					case 'C':	//Language nl
					    $excelArr['nl'][$row-1] = $objWorksheet->getCell($column.$row)->getValue();					
						break;
					case 'D':	//Language fr
					    $excelArr['fr'][$row-1] = $objWorksheet->getCell($column.$row)->getValue();					
						break;
					case 'E':	//Language es
					    $excelArr['es'][$row-1] = $objWorksheet->getCell($column.$row)->getValue();					
						break;
					case 'F':	//Language en
					    $excelArr['en'][$row-1] = $objWorksheet->getCell($column.$row)->getValue();					
						break;
					case 'G':	//Language be
					    $excelArr['be'][$row-1] = $objWorksheet->getCell($column.$row)->getValue();					
						break;
				}
			}
		}

		return $excelArr;

		//TEST DRAWING A TABLE SHOWING THE CONTENT IN YOUR EXCEL
		// echo '<table border=1>' . "\n";

		// foreach ($objWorksheet->getRowIterator() as $row)
		// {
		//   echo '<tr>' . "\n";
		//   $cellIterator = $row->getCellIterator();
		//   $cellIterator->setIterateOnlyExistingCells(false);

		//   foreach ($cellIterator as $cell)
		//   {
		//     echo '<td>' . $cell->getValue() . '</td>' . "\n";
		//   }

		//   echo '</tr>' . "\n";
		// }

		// echo '</table>' . "\n";
	}

	function xml_creator($arrExcel, $lang, $fileName) {
		//CONFIG XML - XLIFF
		$xml = new DOMDocument('1.0', 'utf-8');

		$domElement 	= $xml->createElement('xliff','');
		$domAttribute1 	= $xml->createAttribute('version');
		$domAttribute2 	= $xml->createAttribute('xmlns');

		// Value for the created attribute
		$domAttribute1->value = '1.2';
		$domAttribute2->value = 'urn:oasis:names:tc:xliff:document:1.2';

		// Don't forget to append it to the element
		$domElement->appendChild($domAttribute1);
		$domElement->appendChild($domAttribute2);
		$file = $xml->createElement("file", '');

		$fileAttribute1 		= $xml->createAttribute('source-language');
		$fileAttribute1->value 		= $lang;
		$file->appendChild($fileAttribute1);

		$fileAttribute2 		= $xml->createAttribute('datatype');
		$fileAttribute2->value 	= 'plaintext';
		$file->appendChild($fileAttribute2);

		$fileAttribute3 		= $xml->createAttribute('original');
		$fileAttribute3->value 	= 'file.ext';
		$file->appendChild($fileAttribute3);

		$domElement->appendChild($file);

		$body = $xml->createElement("body", "");
		$file->appendChild($body);
		//END CONFIG

		//	ADDING FIELDS TO XLIFF WITH DE 'LANG' PARAMETER
		foreach ($arrExcel as $excelKey => $arrExcelValue) {
            $arrSize = sizeof($arrExcelValue)-1;
            for ($row = 1; $row <= $arrSize; $row++) {
				$transUnit					= $xml->createElement("trans-unit","");
				$transUnitAttribute1 		= $xml->createAttribute('id');
				$transUnitAttribute1->value = $arrExcelValue[$row];
				$transUnit->appendChild($transUnitAttribute1);

				$transUnitSource	= $xml->createElement("source",$arrExcel['clv_content'][$row]);
				$transUnitTarget	= $xml->createElement("target",$arrExcel[$lang][$row]);

				$transUnit->appendChild($transUnitSource);
				$transUnit->appendChild($transUnitTarget);

				$body->appendChild($transUnit);
            }

            break;	//BREAK POINT, JUST WE NEED ONE LOOP
		}

		// Append it to the document itself
		$xml->appendChild($domElement);
		$xml->formatOutput = true;  //poner los string en la variable $strings_xml:

		$strings_xml = $xml->saveXML();

		if (!is_dir($fileName.'Parsed')) {
		    mkdir($fileName.'Parsed', 0700);
		    mkdir($fileName.'Parsed'.'/translations',0700);
		}		

		$xml->save($fileName.'Parsed'.'/translations/messages.'.$lang.'.xlf');

		return 1;
	}

$fileName = "demo";	//Origin Folder
$arrExcel = excel_reader($fileName);

//for ($row=2; $row <= sizeof($arrExcel); $row++ ) {
foreach (array_slice($arrExcel,2) as $key => $value ) {
	xml_creator($arrExcel, $key, $fileName);
}

?>