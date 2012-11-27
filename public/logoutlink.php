<?php
if(array_key_exists('guid', $_COOKIE)){
	$sGuid = $_COOKIE['guid'];
	echo '<a id="logoutlink" href="googleauth2login.php?action=logout" >logout</a>';
}

?>