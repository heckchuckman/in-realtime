<?php
	
	require_once('irt-options.php');

	($irtOptions['userID']) ? $user = $irtOptions['userID'] : $user = '';
	//
	($_GET['client']) ? $client = $_GET['client'] : $client = $irtOptions['clientID'];
	($_GET['secret']) ? $secret = $_GET['secret'] : $secret = $irtOptions['clientSecret'];
	($_GET['token']) ? $token = $_GET['token'] : $token = $irtOptions['accessToken'];
	//
	($_GET['publish']) ? $publish = $_GET['publish'] : $publish = $irtOptions['publishState'];
	($_GET['category']) ? $category = $_GET['category'] : $category = $irtOptions['publishCategory'];
	($_GET['author']) ? $author = $_GET['author'] : $author = $irtOptions['publishAuthor'];
	//
	($_GET['email']) ? $notify = $_GET['email'] : $notify = $irtOptions['emailNotify']['notify'];
	($_GET['emailchoice']) ? $address = $_GET['emailchoice'] : $address = $irtOptions['emailNotify']['address'];
	//
	($_GET['img']) ? $postImg = $_GET['img'] : $postImg = $irtOptions['imageInPost'];
	//
	($_GET['feature']) ? $feature = $_GET['feature'] : $feature = $irtOptions['imageInFeature']['attach'];
	($_GET['featimg']) ? $featureImg = $_GET['featimg'] : $featureImg = $irtOptions['imageInFeature']['size'];
	//
	//
	$irtOptionsFile = 'irt-options.php';
	$irtOptionsArray = '<?php' . "\r\n\r\n" .

		'$irtOptions = array(' . "\r\n" .
			"\t" .'\'userID\' => \'' . $user . '\',' . "\r\n" .
			"\t" .'\'clientID\' => \'' . $client . '\',' . "\r\n" .
			"\t" .'\'clientSecret\' => \'' . $secret . '\',' . "\r\n" .
			"\t" .'\'accessToken\' => \'' . $token . '\',' . "\r\n" .
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

	$irtOptionsWrite = fopen($irtOptionsFile, "w");
	fwrite($irtOptionsWrite, $irtOptionsArray);
	fclose($irtOptionsWrite);

	echo "your settings have been saved.";

?>