<?php

$oStrings = json_decode(file_get_contents('strings.json'));
session_start();
$sGuid = FALSE;
if(array_key_exists('sguid', $_SESSION)){
	$sGuid = $_SESSION['sguid'];
	$oUsers = json_decode(file_get_contents('../../model/users.json'));
	if(!isset($oUsers->$sGuid)){
		$sGuid = FALSE;
	}
}
if($_SERVER['REQUEST_METHOD'] == 'GET'){
	$sKey = $_SERVER['QUERY_STRING'];
	$sStart = '<div class="editable"';
	$sEnd = '</div>';
	if($sGuid){
		$sStart .=  ' contentEditable="true" name="'.$sKey.'"';
		$sEnd .= '<button class="save" style="display:none;">Save</button>';		
	}
	echo $sStart . ' >' . $oStrings->$sKey->value .$sEnd;
}else{
	//this is where I do the update, if it's an admin user
	if($sGuid){
		echo 'content saved under: '.$_POST['text_id'];
		$sKey = $_POST['text_id'];
		$oStrings->$sKey->value = $_POST['content'];
		$oStrings->$sKey->update = date("Y-m-d h:i:s");
		$oStrings->$sKey->user = $oUsers->$sGuid->socialid;
		file_put_contents('strings.json', json_encode($oStrings));
	}
}


?>