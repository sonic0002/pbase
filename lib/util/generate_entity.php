<?php
	/* This script is to generate the model entity in the database */
	$DOCUMENT_ROOT=$_SERVER["DOCUMENT_ROOT"];
	include_once($DOCUMENT_ROOT.'/lib/util/model/EntityGenerator.php');

	echo "DOCUMENT ROOT : ".$DOCUMENT_ROOT.'<br/ >';

	// (new EntityGenerator($DOCUMENT_ROOT.'/lib/entity','Feed',array("Id","Title","Description","Url","Topic","Content","Source","MetaScript","Status","CreatedAt")))->generate();
	// (new EntityGenerator($DOCUMENT_ROOT.'/lib/entity','User',array("Id","Username","Email","Password","CreatedAt","UpdatedAt")))->generate();
	
	echo 'Finish generating all models';
