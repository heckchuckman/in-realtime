<?php

require_once('irt-functions.php');

if ($irtOptions['userID'] !== '' && $irtOptions['accessToken'] !== '') {

	$irtImageReq = 'https://api.instagram.com/v1/users/' . $irtOptions['userID'] . '/media/recent/?access_token='. $irtOptions['accessToken'];
	$irtPost = irtGetAssetData($irtImageReq);

	if (is_dir($irtUploadFolder['relative'])) { // check to see if directory exists

		irtAssetChecker($irtPost['data']);
		echo '' . count($irtPost['data']) . ' new posts created.';

	} else { // if not, make it.
		
		if (!mkdir($irtUploadFolder['relative'])) {
	    
	    	die('Failed to create folders...');
		
		} else {

			irtAssetChecker($irtPost['data']);
			echo '' . count($irtPost['data']) . ' new posts created.';

		}

	}

} else {

	echo "It appears that your UserID or AccessToken is not yet configured! Please complete the configuration process before using this functionality.";

}


?>