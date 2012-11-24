<?php

$oStrings = json_decode(file_get_contents('../model/strings.json'));
if($_SERVER['REQUEST_METHOD'] == 'GET'){
	$sQuery = $_SERVER['QUERY_STRING'];
	echo $oStrings->$sQuery->value;
}else{
	//this is where I do the update
}


?>