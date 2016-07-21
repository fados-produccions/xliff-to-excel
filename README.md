        XLIFFtoExcel | ExceltoXLIFF


==============================================
                PURPOSE:
==============================================

The purpose of this scripts are parse the content of a XLIFF(Symfony-Translation-File) to a only Excel with all the different languages that we need for our project.

So, Users could translate the static content of the variables inside excel and later use the EXCELtoXLIFF to reGenerate all the XLIFFs to use in our project.


==============================================
                XLIFFtoExcel
==============================================

Using the Script:

To use this script we need have set-up a WAMP,LAMP,MAMP service. Also you can use a Apache, Php server with your own configuration.

Inside XLIFFtoExcel.php

Line: 164, you can change the value of the $mainroot variable because is the name where all the XLIFF are hosted

Line: 165, we add the string "/translations/" because it the hierarchy that we use with all XLIFFs translations, you can set-up too.

Inside the source you will see a sample-folder "demo" where you could see the format name of the XLIFF files as: messages.es.xliff, so you should use this kind of template_name to can use correctly the script. A template filename could be: messages.[lang].xliff

Once you hace this configured, run the script and automatically. It will be generated to Excel files (filename.xls, filename.xlsx)

==============================================
                XLIFFtoExcel
==============================================

Using the Script:

To use this script we need have set-up a WAMP,LAMP,MAMP service. Also you can use a Apache, Php server with your own configuration.

Inside EXCELtoXLIFF.php

Line: 151, you can change the value of the $fileName variable BUT we read .XLSX FILES NOT .XLS. It feel free to edit the code if you need it.

Once you hace this configured, run the script and automatically. It will be generated a new folder with all the translations parsed to XLIFF template withe this pattern:

    $filename . "Parsed"/translations/messages.[lang].xliff