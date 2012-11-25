<?php
$oVlog = json_decode(file_get_contents('./js/vlog.json'));
$oInlog = json_decode(file_get_contents($oVlog->url));
if(!isset($oVlog->entries)){
	$oVlog->entries = new stdClass();
}
$bDirty = false;
foreach ($oInlog->feed->entry as $oEntry) {
	$sEntry = $oEntry->id->{'$t'};
	if(!isset($oVlog->entries->$sEntry)){
		$bDirty = true;
		$oVlog->entries->$sEntry = new stdClass();
		$oVlog->entries->$sEntry->title = $oEntry->title->{'$t'}; 
		$sContent = $oEntry->content->{'$t'};
		preg_match_all("|<span(.*)</span>|Usm", $sContent, $aSpans);
		$oVlog->entries->$sEntry->description = strip_tags($aSpans[0][0]);
		preg_match_all("|<img(.*)>|Usm", $sContent, $aImages);
		$oVlog->entries->$sEntry->image = $aImages[0][0];
		$oVlog->entries->$sEntry->link = $oEntry->link[0]->href;
	}
}
if($bDirty){
	file_put_contents('./js/vlog.json', json_encode($oVlog));
}
foreach ($oVlog->entries as $sKey => $oEntry) {
	print_r($oEntry->title);	
}
?>