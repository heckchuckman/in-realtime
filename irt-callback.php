<?php

require_once('irt-functions.php');
//
$irtCode = $_GET['code'];
$irtToken = $_GET['hub_challenge'];
$irtPost = file_get_contents('php://input');
//
// setting and saving the main authorization
//
if (isset($irtCode)) {

	$irtPostAuthArgs = array(
		'client_id' => '' . $irtOptions['clientID'] . '',
		'client_secret' => '' . $irtOptions['clientSecret'] . '',
		'grant_type' => 'authorization_code',
		'redirect_uri' => IRT_URL . 'irt-callback.php',
		'code' => '' . $irtCode . ''
	);

	foreach($irtPostAuthArgs as $key=>$value) {
		$fields_string .= $key . '=' . $value . '&';
	}

	$fields_string = rtrim($fields_string,'&');
	$url = 'https://api.instagram.com/oauth/access_token';

	$irtPostAuth = curl_init();
		curl_setopt($irtPostAuth, CURLOPT_URL, $url);
		curl_setopt($irtPostAuth, CURLOPT_HEADER, false);
		curl_setopt($irtPostAuth, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($irtPostAuth, CURLOPT_POST, true);
		curl_setopt($irtPostAuth, CURLOPT_POSTFIELDS, $fields_string);

	$irtPostAuthed = curl_exec($irtPostAuth);
	curl_close($irtPostAuth);
	//
	$auth = json_decode($irtPostAuthed);
	$authUser = $auth->user->id;
	$authToken = $auth->access_token;

	$irtAuthSave = curl_init();
		curl_setopt($irtAuthSave, CURLOPT_URL, IRT_URL . 'irt-editor.php');
		curl_setopt($irtAuthSave, CURLOPT_HEADER, false);
		curl_setopt($irtAuthSave, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($irtAuthSave, CURLOPT_POST, true);
		curl_setopt($irtAuthSave, CURLOPT_POSTFIELDS, 'user=' . $authUser . '&token=' . $authToken . '');

	$irtAuthSaved = curl_exec($irtAuthSave);
	curl_close($irtAuthSave);
	//
	$irtPostSubArgs = array(
		'client_id' => '' . $irtOptions['clientID'] . '',
		'client_secret' => '' . $irtOptions['clientSecret'] . '',
		'object' => 'user',
		'object_id' => '' . $authUser . '',
		'aspect' => 'media',
		'verify_token' => '' . $authToken . '',
		'callback_url' => IRT_URL . 'irt-callback.php',
	);

	foreach($irtPostSubArgs as $key=>$value) {
		$fields_string .= $key . '=' . $value . '&';
	}

	$fields_string = rtrim($fields_string,'&');
	$url = 'https://api.instagram.com/v1/subscriptions/';

	$irtPostSub = curl_init();
		curl_setopt($irtPostSub, CURLOPT_URL, $url);
		curl_setopt($irtPostSub, CURLOPT_HEADER, false);
		curl_setopt($irtPostSub, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($irtPostSub, CURLOPT_POST, true);
		curl_setopt($irtPostSub, CURLOPT_POSTFIELDS, $fields_string);

	$result = curl_exec($irtPostSub);
	curl_close($irtPostSub);

	$resultObj = json_decode($result);

	if ($resultObj->meta->code == 200) {

		if ($irtOptions['emailNotify']['notify'] == 'yes') irtEmailNotifications($irtOptions['emailNotify']['address'], 'In Real-Time OAuth Success', 'token response = '. $authToken . "\r\n" . 'authorized UserID = '. $authUser . "\r\n" . 'full result = ' . $result);

		echo '<html>
		<head>
			<style>
				body {margin: 30% 0 0 0; background-color: #ECECEC; text-align: center;}
				h2 {font: 23px/29px "HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",sans-serif;}
				p {font: 12px/1.4em sans-serif;}
			</style>
		</head>
		<body>
		<h2>Congrats!</h2>
		<p>You\'re all done. <b>Happy Instagram-ing.</b></p>
		<p>(this page will close itself in 3 seconds.)</p>
		</body>
		<script>
			setTimeout(function() { window.close(); }, 3000);	
		</script>
		</html>';

	} else {

		if ($irtOptions['emailNotify']['notify'] == 'yes') irtEmailNotifications($irtOptions['emailNotify']['address'], 'In Real-Time OAuth Failure!', 'token response = '. $authToken . "\r\n" . 'authorized UserID = '. $authUser . "\r\n" . 'full result = ' . $result);

		echo '<html>
		<head>
			<style>
				body {margin: 30% 0 0 0; background-color: #ECECEC; text-align: center;}
				h2 {font: 23px/29px "HelveticaNeue-Light","Helvetica Neue Light","Helvetica Neue",sans-serif;}
				p {font: 12px/1.4em sans-serif;}
			</style>
		</head>
		<body>
		<h2>Authorization Failed!</h2>
		<p><b>Please check your configuration and try again.</b></p>
		<p>!!! Make sure your plug-ins folder is accessible to the public and not password protected !!!</p>
		</body>
		</html>';		

	}

}
//
// the token echo for authorization
//
if (isset($irtToken)) {
	echo $irtToken;
}
//
// the main callback functionality. this is the meat.
//
if ($irtCode == null && $irtToken == null) {

	$irtBaseline = $irtPost;
	$irtResponse = json_decode($irtPost, true);
	$irtTime = $irtResponse[0]['time'];
	$irtImageReq = 'https://api.instagram.com/v1/users/'. $irtOptions['userID'] .'/media/recent/?access_token='. $irtOptions['accessToken'] .'&max_timestamp='. $irtTime .'&count=8';
	//
	//
	$irtPostGet = curl_init();
	$url = $irtImageReq;
	curl_setopt($irtPostGet, CURLOPT_URL, $url);
	curl_setopt($irtPostGet, CURLOPT_HEADER, false);
	curl_setopt($irtPostGet, CURLOPT_RETURNTRANSFER, true);
	//
	$irtPost = curl_exec($irtPostGet);
	curl_close($irtPostGet);
	//
	$irtPost = json_decode($irtPost, true);
	// the mail sanity check.
	if ($irtOptions['emailNotify']['notify'] == 'yes') irtEmailNotifications($irtOptions['emailNotify']['address'], 'In Real-Time : sanity check', 'baseline = ' . $irtBaseline . "\r\n" . 'response data = ' . print_r($irtResponse) . "\r\n" . 'post data = '. print_r($irtPost) . "\r\n" . 'request string = ' . $irtImageReq . '');
	//
	if (is_dir($irtUploadFolder['relative'])) { // check to see if directory exists
	
		irtAssetChecker($irtPost['data']);

	} else { // if not, make it.
		
		if (!mkdir($irtUploadFolder['relative'])) {
	    
	    	die('Failed to create folders...');
		
		} else {

			irtAssetChecker($irtPost['data']);
				
		}

	}

}

?>