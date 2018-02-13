<?php
include_once("import-lib.php");

$libg = './libguides2_export.xml';
$tester = new lgImporter($libg);
//$tester->accountsImport();
$tester->guidesImport();
