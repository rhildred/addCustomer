<?php
$oVlog = json_decode(file_get_contents('../js/vlog.json'));
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
		$oVlog->entries->$sEntry->title = preg_replace('/PfenningsOrganic.ca/', '', $oEntry->title->{'$t'}); 
		$sContent = $oEntry->content->{'$t'};
		preg_match_all("|<span(.*)</span>|Usm", $sContent, $aSpans);
		$oVlog->entries->$sEntry->description = preg_replace('/Read more.*/', '', strip_tags($aSpans[0][0]));
		preg_match_all("|src=\"(.*)\"|Usm", $sContent, $aImages);
		$sImage = preg_replace('/src=\"/', 'url(\'', $aImages[0][0]);
		$sImage = preg_replace( '/"/', '\'', $sImage);
		$oVlog->entries->$sEntry->image = $sImage . ')';
		$oVlog->entries->$sEntry->link = $oEntry->link[0]->href;
	}
}
if($bDirty){
	file_put_contents('../js/vlog.json', json_encode($oVlog));
}
?>
<style>
	article div {
		width: 160px;
		height: 150px;
		background-repeat: no-repeat;
		background-position: center;
		display: table-cell;
		vertical-align: middle;
	}
	article div img {
		display: block;
		margin-left: auto;
		margin-right: auto;
	}
</style>
<section>
<?php foreach ($oVlog->entries as $sKey => $oEntry) {?>
<article>
<h1><?php echo $oEntry -> title; ?></h1>
<figure class="dropzone">
	<div style="background-image: <?php echo $oEntry -> image; ?>" >
		<a href="<?php echo $oEntry -> link; ?>" rel="prettyPhoto" >
			<img src="images/play.svg" alt="Play Video"/>
		</a>
	</div>
</figure>
<p><?php echo $oEntry -> description; ?></p>
</article>

<?php } ?>
</section>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
<script src="./js/jquery.prettyPhoto.js" type="text/javascript"	charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$("a[rel^='prettyPhoto']").prettyPhoto();
	}); 
	$.event.props.push('dataTransfer');
	$(".dropzone").bind('dragover', function (e){
    	e.stopPropagation();
		e.preventDefault();
		e.dataTransfer.dropEffect = 'copy';
		// Explicitly show this is a copy.
	});
	$(".dropzone").bind('drop', function (evt){
		evt.stopPropagation();
		evt.preventDefault();

		var files = evt.dataTransfer.files;
		// files is a FileList of File objects. List some properties.
		myFileObject = files[0]
		// Open Our formData Object
		var formData = new FormData();

		// Append our file to the formData object
		// Notice the first argument "file" and keep it in mind
		formData.append('my_uploaded_file', myFileObject);

		// Create our XMLHttpRequest Object
		var xhr = new XMLHttpRequest();

		// Open our connection using the POST method
		xhr.open("POST", 'images/');
		var oTarget = evt.target;
		if(oTarget.nodeName == "A"){
			oTarget = oTarget.parentNode;
		}
		else if(oTarget.nodeName == "IMG"){
			oTarget = oTarget.parentNode.parentNode;
		}
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
				var re= new RegExp('(.jpg)', 'gi');
				oTarget.style.backgroundImage="url(images/" + myFileObject.name.replace(re, "_160x150.jpg") + ')';
			}
		}
		// Send the file
		oTarget.style.backgroundImage="url(images/spinner.gif)";
		xhr.send(formData);

	});
</script>

