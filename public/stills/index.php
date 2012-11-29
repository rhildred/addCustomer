<?php
function resize_uploaded_file($sIn, $sOut) {
	$hIn = imagecreatefromjpeg($sIn);
	list($x, $y, $type, $attr) = getimagesize($sIn);
	if ($x > $y) {
		$nXNew = $y * 160 / 150;
		$nYNew = $y;
		$nXOffs = ($x - $nXNew) / 2;
		$hCanvas = imagecreatetruecolor($nXNew, $y);
		imagecopy($hCanvas, $hIn, 0, 0, $nXOffs, 0, $nXNew, $y);
	} else {
		$nXNew = $x;
		$nYNew = $x * 150 / 160;
		$nYOffs = ($y - $nYNew) / 2;
		$hCanvas = imagecreatetruecolor($x, $nYNew);
		imagecopy($hCanvas, $hIn, 0, 0, 0, $nYOffs, $x, $nYNew);
	}
	$h160x150 = imagecreatetruecolor(160, 150);
	imagecopyresampled($h160x150, $hCanvas, 0, 0, 0, 0, 160, 150, $nXNew, $nYNew);
	$sOutfile = preg_replace('/\.jpg/i', '_160x150.jpg', $sOut);
	imagejpeg($h160x150, $sOutfile, 100);
	unlink($sIn);
	return $sOutfile;
}
if($_SERVER['REQUEST_METHOD'] == 'GET')
{
	header('HTTP/1.0 403 Forbidden');
	exit;
}
$sGuid = FALSE;
if(array_key_exists('sguid', $_COOKIE)){
	$sGuid = $_COOKIE['sguid'];
	$oUsers = json_decode(file_get_contents('../../model/users.json'));
	if(!isset($oUsers->$sGuid)){
		$sGuid = FALSE;
	}
}
if($sGuid){
	$sNewName = resize_uploaded_file($_FILES['my_uploaded_file']['tmp_name'], basename($_FILES['my_uploaded_file']['name']));
	$oVlog = json_decode(file_get_contents('../vlog/vlog.json'));
	$sKey = $_POST['videoid'];
	$oVlog->entries->$sKey->image = "url('stills/" . $sNewName . "')"; 
	file_put_contents('../vlog/vlog.json', json_encode($oVlog));
}
?>