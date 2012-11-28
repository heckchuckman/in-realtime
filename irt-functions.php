<?php
//
// Some definitiions and required scripts
//
if ( !defined('IRT_DOCROOT') )
	define( 'IRT_DOCROOT', $_SERVER['DOCUMENT_ROOT'] );

require_once(IRT_DOCROOT . '/wp-config.php');

if ( !defined('IRT_SITE') )
	define( 'IRT_SITE', get_site_url() . '/' );

if ( !defined('IRT_URL') )
	define( 'IRT_URL', plugin_dir_url( __FILE__ ) );

if ( !defined('IRT_PATH') )
	define( 'IRT_PATH', plugin_dir_path( __FILE__ ) );

require_once(ABSPATH . "wp-admin" . '/includes/image.php');

if (!is_file('irt-options.php')) {

	$irtMakeOptFile = curl_init();
		curl_setopt($irtMakeOptFile, CURLOPT_URL, IRT_URL . 'irt-editor.php');
		curl_setopt($irtMakeOptFile, CURLOPT_HEADER, false);
		curl_setopt($irtMakeOptFile, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($irtMakeOptFile, CURLOPT_POST, true);
	
	$irtMakeOptFileSave = curl_exec($irtMakeOptFile);
	curl_close($irtMakeOptFile);

	// get settings
	if ($irtMakeOptFileSave) {

		@include_once('irt-options.php');

	}

} else {

	// get settings
	include_once('irt-options.php');

}
	
//
// A few vars that will be handy.
//
$wp_upload_dir = wp_upload_dir();
$irtUploadFolder = array(
	'absolute' => $wp_upload_dir['baseurl'] . '/in-realtime-images/',
	'relative' => $wp_upload_dir['basedir'] . '/in-realtime-images/'
);
//
// Creates and modifies the irt-options.php file
//
function irtOptionsReadWrite($options, $type) {

	$irtOptionsFile = 'irt-options.php';
	
	// first clear the file - a gross but necessary evil
	$irtOptionsForClear = fopen($irtOptionsFile, 'w');
	$irtOptionsClear = fwrite($irtOptionsForClear, '');

	$irtOptionsArray = '<?php' . "\r\n\r\n" .

		'$irtOptions = array(' . "\r\n" .
			"\t" .'\'userID\' => \'' . $options['user'] . '\',' . "\r\n" .
			"\t" .'\'clientID\' => \'' . $options['client'] . '\',' . "\r\n" .
			"\t" .'\'clientSecret\' => \'' . $options['secret'] . '\',' . "\r\n" .
			"\t" .'\'accessToken\' => \'' . $options['token'] . '\',' . "\r\n" .
			"\t" .'\'publishState\' => \'' . $options['publish'] . '\',' . "\r\n" .
			"\t" .'\'publishCategory\' => ' . intval($options['category']) . ',' . "\r\n" .
			"\t" .'\'publishAuthor\' => ' . intval($options['author']) . ',' . "\r\n" .
			"\t" .'\'allowComments\' => \'' . $options['comments'] . '\',' . "\r\n" .
			"\t" .'\'allowPings\' => \'' . $options['pings'] . '\',' . "\r\n" .
			"\t" .'\'emailNotify\' => array(' . "\r\n" .
				"\t\t" .'\'notify\' => \'' . $options['notify'] . '\',' . "\r\n" .
				"\t\t" .'\'address\' => \'' . $options['address'] . '\'' . "\r\n" .
			"\t" .'),' . "\r\n" .
			"\t" .'\'imageInPost\' => \'' . $options['image'] . '\',' . "\r\n" .
			"\t" .'\'imageInFeature\' => array(' . "\r\n" .
				"\t\t" .'\'attach\' => \'' . $options['feature'] . '\',' . "\r\n" .
				"\t\t" .'\'size\' => \'' . $options['featimg'] . '\'' . "\r\n" .
			"\t" .')' . "\r\n" .
		');' . "\r\n\r\n" .

	'?>';

	// write the new and existing options
	$irtOptionsWrite = fopen($irtOptionsFile, 'w');
	$irtOptionsSave = fwrite($irtOptionsWrite, $irtOptionsArray);
	
	if ($irtOptionsSave !== false) {

		fclose($irtOptionsWrite);
		return "your settings have been saved.";
	
	} else {

		return "an error occured while saving your settings. please try again.";

	}

}
//
// Sends email to desired email address as set in the admin panel.
//
function irtEmailNotifications($to, $subject, $message) {

	$headers = 'From: ' . $to . '' . "\r\n" . 'Reply-To: ' . $to . '' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $message, $headers);

}
//
// Splits the Tags from the Caption string and cleans up the title. Called twice in post creation for each piece - the Title is set to any text without a #, text is a # is made into a tag.
//
function irtTitleAndTags($str, $type) {
		
	if ($type === 'tags') {

		$tagArr = array();
		$tags = preg_match_all('/#[a-zA-Z0-9]*/', $str, $tag);
	
		foreach ($tag[0] as $value) {
			$value = substr($value, 1);
			array_push($tagArr, $value);
		}
		$tagStr = implode(', ', $tagArr);

		return $tagStr;	
	}

	if ($type === 'title') {

		$cleanTitle = preg_replace('/#[a-zA-Z0-9]*/', '', $str);
		$cleanTitle = trim($cleanTitle);	

		return $cleanTitle;

	}
	
}
//
// Creates all posts and uploads image assets to the server
//
function irtUpload($title, $time, $image, $feature) {

	global $irtOptions, $irtUploadFolder;
	$relDir = $irtUploadFolder['relative'];
	$absDir = $irtUploadFolder['absolute'];

	if ($image) {

		$irtPostFile = basename($image);
		$irtUploadFile = file_get_contents($image);
		file_put_contents($irtUploadFolder['relative'] . $irtPostFile, $irtUploadFile);

		// Create post object
		$post = array(
			'comment_status' => '' . $irtOptions['allowComments'] .'', // 'closed' or 'open'
			'ping_status' => '' . $irtOptions['allowPings'] .'', // 'closed' or 'open'
			'post_author' => $irtOptions['publishAuthor'], //The user ID number of the author.
			'post_category' => array($irtOptions['publishCategory']), //Add some categories.
			'post_content' => '<img src="' . $absDir . $irtPostFile . '" alt="' . irtTitleAndTags($title, 'title') . '" />', //The full text of the post.
			'post_excerpt' => '', //For all your post excerpt needs.
			'post_date' => '' . date('Y-m-d H:i:s', $time) . '',
			'post_date_gmt' => '' . date('Y-m-d H:i:s', $time) . '',
			'post_status' => '' . $irtOptions['publishState'] .'', // 'draft' | 'publish' | 'pending'| 'future' | 'private'
			'post_title' => '' . irtTitleAndTags($title, 'title') . '', //The title of your post.
			'post_type' => 'post', // 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type ] 
			'tags_input' => '' . irtTitleAndTags($title, 'tags') . '' // <tag>, <tag>, <...>
		);

		// Insert the post into the database
		$irtPostID = wp_insert_post($post);

		// If wanted, attach the Featured Image to the post.
		if ($feature) {

			$irtFeatureFile = basename($feature);
			$irtUploadFile = file_get_contents($feature);
			file_put_contents($relDir . $irtFeatureFile, $irtUploadFile);
			$wp_filetype = wp_check_filetype($irtFeatureFile, null);
			//
			$attachment = array(
				'guid' => $absDir . $irtFeatureFile,
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => preg_replace('/\.[^.]+$/', '', $irtFeatureFile),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			//
			$attach_id = wp_insert_attachment($attachment, $absDir . $irtFeatureFile, $irtPostID);
			$attach_data = wp_generate_attachment_metadata($attach_id, $absDir);

			if (wp_update_attachment_metadata( $attach_id,  $attach_data )) {
				// set as featured image
				return update_post_meta($irtPostID, '_thumbnail_id', $attach_id);
			}

		}

		// the mail check
		if ($irtOptions['emailNotify']['notify'] == 'yes') irtEmailNotifications($irtOptions['emailNotify']['address'], 'In Real-Time : success', 'post ID = ' . $irtPostID . '');
	
	} else {

		if ($irtOptions['emailNotify']['notify'] == 'yes') irtEmailNotifications($irtOptions['emailNotify']['address'], 'In Real-Time : failure', 'The file appears to be null. Something bad happened.');

	}

}
//
// Checks the assets to make sure they don't already exist on the server
// BUG : when the User changes the main Img size pref it recreates the post with the new main image settings.
//
function irtAssetChecker($assets) {

	global $irtOptions, $irtUploadFolder;
	$irtPostImgPref = $irtOptions['imageInPost'];

	foreach ($assets as $irtPostData) {

		/*
			$irtFileCheckPost = is_file($irtUploadFolder['relative'] . basename($irtPostData['images']['' . $irtPostImgPref . '']['url']));
			$irtFileCheckFeat = is_file($irtUploadFolder['relative'] . basename($irtPostData['images']['' . $irtFeatureImgPref . '']['url']));
		*/

		$irtFileCheckStandard = is_file($irtUploadFolder['relative'] . basename($irtPostData['images']['standard_resolution']['url']));
		$irtFileCheckLowRes = is_file($irtUploadFolder['relative'] . basename($irtPostData['images']['low_resolution']['url']));
		$irtFileCheckThumb = is_file($irtUploadFolder['relative'] . basename($irtPostData['images']['thumbnail']['url']));

		if (!$irtFileCheckStandard && !$irtFileCheckLowRes && !$irtFileCheckThumb) {

			if ($irtOptions['imageInFeature']['attach'] == 'yes') {

				$irtFeatureImgPref = $irtOptions['imageInFeature']['size'];
				$irtFeatureImg = $irtPostData['images']['' . $irtFeatureImgPref . '']['url'];
			
			} else {
			
				$irtFeatureImg = null;
			
			}

			$irtPostImg = $irtPostData['images']['' . $irtPostImgPref . '']['url'];
			irtUpload('' . $irtPostData['caption']['text'] . '', $irtPostData['created_time'], $irtPostImg, $irtFeatureImg);

		}

	}

}

//
// Function to fetch ALL images associated with an account. USE WITH CAUTION!
//
function irtGetAssetData($url) {

	$irtPostGet = curl_init();
	curl_setopt($irtPostGet, CURLOPT_URL, $url);
	curl_setopt($irtPostGet, CURLOPT_HEADER, false);
	curl_setopt($irtPostGet, CURLOPT_RETURNTRANSFER, true);

	$irtPost = curl_exec($irtPostGet);
	curl_close($irtPostGet);

	$irtPost = json_decode($irtPost, true);

	if ($irtPost['pagination']['next_url'] != '') {

		$nextReq = irtGetAssetData($irtPost['pagination']['next_url']);

		foreach ($nextReq['data'] as $value) {
			
			array_push($irtPost['data'], $value);

		}

	}

	return $irtPost;

}

?>