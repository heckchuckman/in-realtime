<?php
	
	require_once('irt-functions.php');

	// set an array for error messaging.
	$errors = array();
	
	//
	// Set all of the vars with proper values or push an error message.
	//

	// User ID
	if ($_POST['user']) {

		if (is_numeric($_POST['user'])) $user = $_POST['user'];	
		else array_push($errors, "invalid UserID value");

	} else {

		(isset($irtOptions['userID']) && $irtOptions['userID'] !== '') ? $user = $irtOptions['userID'] : $user = '';

	}
	// Client ID
	if ($_POST['client']) {

		if (ctype_alnum($_POST['client'])) $client = $_POST['client'];
		else array_push($errors, "invalid Client ID value");

	} else {

		 (isset($irtOptions['clientID']) && $irtOptions['clientID'] !== '') ? $client = $irtOptions['clientID'] : $client = '';

	}
	// Client Secret
	if ($_POST['secret']) {

		if (ctype_alnum($_POST['secret'])) $secret = $_POST['secret'];
		else array_push($errors, "invalid Client Secret value");

	} else {

		(isset($irtOptions['clientSecret']) && $irtOptions['clientSecret'] !== '') ? $secret = $irtOptions['clientSecret'] : $secret = '';

	}
	// OAuth Token
	if ($_POST['token']) {

		$token = $_POST['token'];

	} else {

		(isset($irtOptions['accessToken']) && $irtOptions['accessToken'] !== '') ? $token = $irtOptions['accessToken'] : $token = '';

	}
	// Publish State 'draft' | 'publish' | 'pending'| 'future' | 'private'
	if ($_POST['publish']) {

		$pv = $_POST['publish'];
		if ($pv == "draft" || $pv == "publish" || $pv == "pending" || $pv == "future" || $pv == "private") $publish = $pv;
		else array_push($errors, "invalid Publish State setting");

	} else {

		(isset($irtOptions['publishState']) && $irtOptions['publishState'] !== '') ? $publish = $irtOptions['publishState'] : $publish = "publish";

	}
	// Category for posts - INT
	if ($_POST['category'] && is_int($_POST['category'])) {

		$category = $_POST['category'];

	} else {

		(isset($irtOptions['publishCategory'])) ? $category = $irtOptions['publishCategory'] : $category = '1';

	}
	// Author of posts - INT
	if ($_POST['author'] && is_int($_POST['author'])) {

		$author = $_POST['author'];

	} else {

		(isset($irtOptions['publishAuthor'])) ? $author = $irtOptions['publishAuthor'] : $author = '1';

	}
	// Allow Comments - 'open' | 'closed'
	if ($_POST['comments']) {

		if ($_POST['comments'] == "open" || $_POST['comments'] == "closed") $comments = $_POST['comments'];

	} else {

		(isset($irtOptions['allowComments'])) ? $comments = $irtOptions['allowComments'] : $comments = "closed";

	}
	// Allow Pings - 'open' | 'closed'
	if ($_POST['pings']) {

		if ($_POST['pings'] == "open" || $_POST['pings'] == "closed") $pings = $_POST['pings'];

	} else {

		(isset($irtOptions['allowPings']) && $irtOptions['allowPings'] !== '') ? $pings = $irtOptions['allowPings'] : $pings = "closed";

	}
	// Email 'yes' | 'no' and address, if yes.
	if ($_POST['email']) {

		if ($_POST['email'] == "yes" || $_POST['email'] == "no") $notify = $_POST['email'];

	} else {

		(isset($irtOptions['emailNotify']['notify'])) ? $notify = $irtOptions['emailNotify']['notify'] : $notify = "no";

	}
	if ($_POST['emailaddr']) {

		if (filter_var($_POST['emailaddr'], FILTER_VALIDATE_EMAIL)) $address = $_POST['emailaddr'];
		else array_push($errors, "email address is not valid");
	
	} else {

		(isset($irtOptions['emailNotify']['address'])) ? $address = $irtOptions['emailNotify']['address'] : $address = '';

	}
	// Post Image selection
	if ($_POST['img'] && ($_POST['img'] == 'standard_resolution' || $_POST['img'] == 'low_resolution' || $_POST['img'] == 'thumbnail')) {

		$postImg = $_POST['img'];

	} else {

		(isset($irtOptions['imageInPost']) && $irtOptions['imageInPost'] !== '') ? $postImg = $irtOptions['imageInPost'] : $postImg = "standard_resolution";

	}
	// 
	if ($_POST['feature'] && ($_POST['feature'] == 'yes' || $_POST['feature'] == 'no')) {

		$feature = $_POST['feature'];

	} else {

		(isset($irtOptions['imageInFeature']['attach']) && $irtOptions['imageInFeature']['attach'] !== '') ? $feature = $irtOptions['imageInFeature']['attach'] : $irtOptions['imageInFeature']['attach'] = 'no';

	}
	if ($_POST['featimg'] && ($_POST['featimg'] == 'standard_resolution' || $_POST['featimg'] == 'low_resolution' || $_POST['featimg'] == 'thumbnail')) {

		$featureImg = $_POST['featimg'];

	} else {

		(isset($irtOptions['imageInFeature']['size']) && $irtOptions['imageInFeature']['size'] !== '') ? $featureImg = $irtOptions['imageInFeature']['size'] : $irtOptions['imageInFeature']['size'] = '';		
	}

	if (count($errors) == 0) {

		$options = array(
			'user' => '' . $user . '',
			'client' => '' . $client . '',
			'secret' => '' . $secret . '',
			'token' => '' . $token . '',
			'publish' => '' . $publish . '',
			'category' => $category,
			'author' => $author,
			'comments' => '' . $comments . '',
			'pings' => '' . $pings . '',
			'notify' => '' . $notify . '',
			'address' => '' . $address . '',
			'image' => '' . $postImg . '',
			'feature' => '' . $feature . '',
			'featimg' => '' . $featureImg . ''
		);

		echo irtOptionsReadWrite($options, "update");

	} else {

		echo "there are the following errors in your data: " . implode(", ", $errors);

	}
	

?>