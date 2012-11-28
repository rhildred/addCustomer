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
	$h320x300 = imagecreatetruecolor(320, 300);
	imagecopyresampled($h320x300, $hCanvas, 0, 0, 0, 0, 320, 300, $nXNew, $nYNew);
	$sOutfile = preg_replace('/\.jpg/i', '_320x300.jpg', $sOut);
	imagejpeg($h320x300, $sOutfile, 100);
	$h160x150 = imagecreatetruecolor(160, 150);
	imagecopyresampled($h160x150, $hCanvas, 0, 0, 0, 0, 160, 150, $nXNew, $nYNew);
	$sOutfile = preg_replace('/\.jpg/i', '_160x150.jpg', $sOut);
	imagejpeg($h160x150, $sOutfile, 100);
	unlink($sIn);
}

resize_uploaded_file($_FILES['my_uploaded_file']['tmp_name'], basename($_FILES['my_uploaded_file']['name']));
?>