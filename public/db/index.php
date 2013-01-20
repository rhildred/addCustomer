<?php
$action = array_key_exists('code', $_GET) ? 'complete' : (array_key_exists('action', $_POST) ? $_POST['action'] : '');
$action = (array_key_exists('action', $_GET) ? $_GET['action'] : $action);

require_once('../../adodb5/adodb.inc.php');
require_once('../../adodb5/adodb-active-record.inc.php');

$db = NewADOConnection('mysql');
if($_SERVER['SERVER_PORT'] == 8080){
	$db->Connect("localhost", "root", "", "test1");
}else{
	$db->Connect('localhost', 'production user', 'production password', "production database name");
}

ADOdb_Active_Record::SetDatabaseAdapter($db);

switch ($action) {
	case 'load':
	case 'find':
		$sObject = $_GET['object'];
		eval('class ' . $sObject . ' extends ADOdb_Active_Record{}');
		$sTemplate = new $sObject;
		if(array_key_exists('bindvars', $_GET)){
			$aResults = $sTemplate->find($_GET['where'], split(',', $_GET['bindvars']));
		}else{
			$aResults = $sTemplate->find('1');
		}
		if(!$aResults){
			echo json_encode(array(error => $sTemplate->ErrorMsg())) . "\n";
			break;				
		}
		if($action == 'find'){
			$aRc = new stdClass();
			$aRc->items = array();
			foreach ($aResults as $oResult) {
				$aRc->items[] = $oResult;
			}
		}else{
			if(count($aResults) == 0){
				$aRc = null;
			}else{
				$aRc = $aResults[0];				
			}
		}
		echo json_encode($aRc) . "\n";
		break;
	case 'save':
	case 'delete':
		$sGuid = FALSE;
		if(array_key_exists('sguid', $_COOKIE)){
			$sGuid = $_COOKIE['sguid'];
			if(file_exists('../../model/users.json')){
				$oUsers = json_decode(file_get_contents('../../model/users.json'));
				if(!isset($oUsers->$sGuid)){
					$sGuid = FALSE;
				}
			}
		}
		if(!$sGuid){
			echo json_encode(array(error => "not authorized")) . "\n";	
			break;		
		}
		$sObject = $_GET['object'];
		eval('class ' . $sObject . ' extends ADOdb_Active_Record{}');
		$sTemplate = new $sObject;
		foreach ($_GET as $key => $value) {
			$sTemplate->$key = $value;
		}
		if(array_key_exists('id', $_GET)){
			if(!$sTemplate->delete()){
				echo json_encode(array(error => $sTemplate->ErrorMsg(), "object" => $sTemplate)) . "\n";
				break;				
			}
		}
		if($action == 'save'){
			if(!$sTemplate->save()){
				echo json_encode(array(error => $sTemplate->ErrorMsg(), "object" => $sTemplate)) . "\n";
				break;
			}
		}
		echo json_encode($sTemplate)  . "\n";
		break;
	default:
		if(array_key_exists('object', $_GET)){
			$sObject = $_GET['object'];
		}else{
			$sObject = 'test';
		}
		eval('class ' . $sObject . ' extends ADOdb_Active_Record{}');
		$sTemplate = new $sObject;
		if(array_key_exists('bindvars', $_GET)){
			$aResults = $sTemplate->find($_GET['where'], split(',', $_GET['bindvars']));
		}else{
			$aResults = $sTemplate->find('1');
		}
		if(!$aResults){
			echo json_encode(array(error => $sTemplate->ErrorMsg())) . "\n";
			break;				
		}
		foreach ($aResults as $oResult) {
			echo '<div class="' . $sObject . '">';
			foreach ($oResult as $key => $value) {
				if(substr($key, 0, 1) != '_' && $key != 'lockMode' && $key != 'foreignName'){
					echo '<span class="' . $key . '">' . $value . '</span>';
				}
			}
			echo "</div>\n";
		}
}

?>