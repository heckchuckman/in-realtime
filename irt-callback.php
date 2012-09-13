<?php

require_once('irt-functions.php');
//
$irtCode = $_GET['code'];
$irtToken = $_GET['hub_challenge'];
$irtPost = file_get_contents('php://input');
//
// The option vars
//
($irtOptions['userID']) ? $user = $irtOptions['userID'] : $user = '';
($irtOptions['clientID']) ? $client = $irtOptions['clientID'] : $client = '';
($irtOptions['clientSecret']) ? $secret = $irtOptions['clientSecret'] : $secret = '';
($irtOptions['accessToken']) ? $irtAccess = $irtOptions['accessToken'] : $irtAccess = '';
($irtOptions['publishState']) ? $publish = $irtOptions['publishState'] : $publish = '';
($irtOptions['publishCategory']) ? $category = $irtOptions['publishCategory'] : $category = 0;
($irtOptions['publishAuthor']) ? $author = $irtOptions['publishAuthor'] : $author = 1;
($irtOptions['emailNotify']['notify']) ? $notify = $irtOptions['emailNotify']['notify'] : $notify = '';
($irtOptions['emailNotify']['address']) ? $address = $irtOptions['emailNotify']['address'] : $address = '';
($irtOptions['imageInPost']) ? $postImg = $irtOptions['imageInPost'] : $postImg = '';
($irtOptions['imageInFeature']['attach']) ? $feature = $irtOptions['imageInFeature']['attach'] : $feature = '';
($irtOptions['imageInFeature']['size']) ? $featureImg = $irtOptions['imageInFeature']['size'] : $featureImg = '';
//
$irtOptionsFile = 'irt-options.php';
//
// setting and saving the main authorization
//
if (isset($irtCode)) {

	$irtPostAuthArgs = array(
		'client_id' => '' . $irtOptions['clientID'] . '',
		'client_secret' => '' . $irtOptions['clientSecret'] . '',
		'grant_type' => 'authorization_code',
		'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . '/wp-content/plugins/in-realtime/irt-callback.php',
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

	$result = curl_exec($irtPostAuth);
	curl_close($irtPostAuth);

	$auth = json_decode($result);
	$authUser = $auth->user->id;
	$authToken = $auth->access_token;

	$optionsArray = '<?php' . "\r\n\r\n" .

		'$irtOptions = array(' . "\r\n" .
			"\t" .'\'userID\' => \'' . $authUser . '\',' . "\r\n" .
			"\t" .'\'clientID\' => \'' . $client . '\',' . "\r\n" .
			"\t" .'\'clientSecret\' => \'' . $secret . '\',' . "\r\n" .
			"\t" .'\'accessToken\' => \'' . $authToken . '\',' . "\r\n" .
			"\t" .'\'publishState\' => \'' . $publish . '\',' . "\r\n" .
			"\t" .'\'publishCategory\' => ' . $category . ',' . "\r\n" .
			"\t" .'\'publishAuthor\' => ' . $author . ',' . "\r\n" .
			"\t" .'\'emailNotify\' => array(' . "\r\n" .
				"\t\t" .'\'notify\' => \'' . $notify . '\',' . "\r\n" .
				"\t\t" .'\'address\' => \'' . $address . '\'' . "\r\n" .
			"\t" .'),' . "\r\n" .
			"\t" .'\'imageInPost\' => \'' . $postImg . '\',' . "\r\n" .
			"\t" .'\'imageInFeature\' => array(' . "\r\n" .
				"\t\t" .'\'attach\' => \'' . $feature . '\',' . "\r\n" .
				"\t\t" .'\'size\' => \'' . $featureImg . '\'' . "\r\n" .
			"\t" .')' . "\r\n" .
		');' . "\r\n\r\n" .

	'?>';

	$optionsWrite = fopen($irtOptionsFile, "w");
	fwrite($optionsWrite, $optionsArray);
	fclose($optionsWrite);

	$irtPostSubArgs = array(
		'client_id' => '' . $irtOptions['clientID'] . '',
		'client_secret' => '' . $irtOptions['clientSecret'] . '',
		'object' => 'user',
		'object_id' => '' . $authUser . '',
		'aspect' => 'media',
		'verify_token' => '' . $authToken . '',
		'callback_url' => 'http://' . $_SERVER['SERVER_NAME'] . '/wp-content/plugins/in-realtime/irt-callback.php',
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

	if ($irtOptions['emailNotify']['notify'] == 'yes') irtEmailNotifications($irtOptions['emailNotify']['address'], 'In Real-Time OAuth', 'token response = '. $authToken . "\r\n" . 'authorized UserID = '. $authUser . "\r\n" . 'full result = ' . $result);

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

}
//
// the token echo for authorization
//
if (isset($irtToken)) {
	
	echo $irtToken;

	// if ($irtOptions['emailNotify']['notify'] == 'yes') irtEmailNotifications($irtOptions['emailNotify']['address'], 'In Real-Time : token callback', 'token = '. $irtToken . '');

}
//
// the main callback functionality. this is the meat.
//
if ($irtCode == null && $irtToken == null) {

	$irtBaseline = $irtPost;
	$irtResponse = json_decode($irtPost, true);
	$irtID = $irtResponse[0]['object_id'];
	$irtTime = $irtResponse[0]['time'];
	$irtImageReq = 'https://api.instagram.com/v1/users/'. $irtID .'/media/recent/?access_token='. $irtAccess .'&max_timestamp='. $irtTime .'&count=8';
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