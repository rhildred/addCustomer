<?php
$action = array_key_exists('code', $_GET) ? 'complete' : (array_key_exists('action', $_POST) ? $_POST['action'] : '');
$action = (array_key_exists('action', $_GET) ? $_GET['action'] : $action);

define('KEY', '95842300516.apps.googleusercontent.com');
define('SECRET', 'JB2DBmIDDKRmNgsbcmwK4yKm');

define('CALLBACK_URL', 'http://'. $_SERVER['HTTP_HOST'] . preg_replace('/\?code.*/','', $_SERVER['REQUEST_URI']));
define('AUTHORIZATION_ENDPOINT', 'https://accounts.google.com/o/oauth2/auth');
define('ACCESS_TOKEN_ENDPOINT', 'https://accounts.google.com/o/oauth2/token');

/***************************************************************************
 * Function: Run CURL
 * Description: Executes a CURL request
 * Parameters: url (string) - URL to make request to
 *             method (string) - HTTP transfer method
 *             headers - HTTP transfer headers
 *             postvals - post values
 **************************************************************************/
function run_curl($url, $method = 'GET', $postvals = null){
    $ch = curl_init($url);
    
    //GET request: send headers and return data transfer
    if ($method == 'GET'){
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
        	CURLOPT_SSL_VERIFYPEER => false
        );
        curl_setopt_array($ch, $options);
    //POST / PUT request: send post object and return data transfer
    } else {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => http_build_query($postvals),
            CURLOPT_RETURNTRANSFER => 1,
        	CURLOPT_SSL_VERIFYPEER => false
        );
        curl_setopt_array($ch, $options);
    }
    if( ! $response = curl_exec($ch)) 
    { 
        trigger_error(curl_error($ch)); 
    } 
    curl_close($ch);
    
    return $response;
}

if ($action == 'complete') {
	//capture code from auth
	$code = $_GET["code"];
	//construct POST object for access token fetch request
	$postvals = array('grant_type' => 'authorization_code',
	                  'client_id' => KEY,
	                  'client_secret' => SECRET,
	                  'code' => $code,
	                  'redirect_uri' => CALLBACK_URL);
	
	//get JSON access token object (with refresh_token parameter)
	$sReturn = run_curl(ACCESS_TOKEN_ENDPOINT, 'POST', $postvals);
	$token = json_decode($sReturn);
	
	//construct URI to fetch profile for current user
	$profile_url = "https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=" . $token->access_token;
	
	//fetch profile of current user
	$oProfile = json_decode(run_curl($profile_url, 'GET'));
	$sSocialId = 'google:' . $oProfile -> id;
	$sGuid = base64_encode(uniqid());
	$sStart = date("Y-m-d h:i:s");
	$oUsers = json_decode(file_get_contents('../model/users.json'));
	$sOldSession = $oUsers->$sSocialId->session;
	unset($oUsers->$sOldSession);
	$oUsers->$sSocialId->session = $sGuid;
	$oUsers->$sSocialId->start = $sStart;
	if($oUsers->$sSocialId->badmin){
		setcookie('guid', $sGuid);
		$oUsers->$sGuid->socialid = $sSocialId;
	}else{
		setcookie('guid', time() - 3600);
	}
	file_put_contents('../model/users.json', json_encode($oUsers));
	$sDir = $_SERVER['REQUEST_URI'];
	$sDir = 'http://' . $_SERVER['HTTP_HOST'] . preg_replace('/googleauth2login.php.*/', '', $sDir);
	header("Location: $sDir");
} elseif ($action == 'logout') {
	if(array_key_exists('guid', $_COOKIE)){
		$sGuid = $_COOKIE['guid'];
		$oUsers = json_decode(file_get_contents('../model/users.json'));
		setcookie("guid", "", time() - 3600);
		$sSocialId = $oUsers->$sGuid->socialid;
		unset($oUsers->$sSocialId->session);
		unset($oUsers->$sGuid);
		file_put_contents('../model/users.json', json_encode($oUsers));
	}
	$sDir = $_SERVER['REQUEST_URI'];
	$sDir = 'http://' . $_SERVER['HTTP_HOST'] . preg_replace('/googleauth2login.php.*/', '', $sDir);
	header("Location: $sDir");
} else {
	//construct Google auth2 URI
	$auth_url = AUTHORIZATION_ENDPOINT . "?redirect_uri=" . CALLBACK_URL . "&client_id=" . KEY . "&scope=https://www.googleapis.com/auth/userinfo.profile" . "&response_type=code" . "&max_auth_age=0";

	//forward user to Facebook auth page
	header("Location: $auth_url");
}

?>