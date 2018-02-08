<?php
include_once("import-lib.php")

$libguides = './libguides2_export.xml';
$tester = new lgImporter($libguides);
$tester->accountsImport();
