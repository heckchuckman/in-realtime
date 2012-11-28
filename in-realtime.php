<?php

/*
Plugin Name: In Real-Time
Plugin URI: http://heckchuckman.com/in-realtime
Description: In Real-Time is a different kind of Instagram plug-in. Using the Instagram Real-time API, this plug-in is developed for users that want posts created automatically for each photo, rather than have a widget or post with images embedded. Developed for Devs but useful for all.
Version: 1.5
Author: Chuck Heckman
Author URI: heckchuckman.com
License: GPL2
*/


$wp_upload_dir = wp_upload_dir();
$wp_upload_dir_REL = $wp_upload_dir['basedir'] . '/in-realtime-images/';

if (!is_dir($wp_upload_dir_REL)) { // check to see if directory exists
	
	if (!mkdir($wp_upload_dir_REL, 0777)) {
    
    	die('Failed to the In-Realtime Images directory. Please check your installation or server config.');
	
	}

}

if (is_admin()) {

	if ( !defined('IRT_SITE') )
		define( 'IRT_SITE', get_site_url() . '/' );

	if ( !defined('IRT_URL') )
		define( 'IRT_URL', plugin_dir_url( __FILE__ ) );

	if ( !defined('IRT_PATH') )
		define( 'IRT_PATH', plugin_dir_path( __FILE__ ) );

	// create custom plugin settings menu
	add_action('admin_menu', 'inrealtime_plugin_menu');

	function inrealtime_plugin_menu() {
		add_menu_page('In Real-Time Settings', 'In Real-Time', 'manage_options', 'inrealtime', 'inrealtime_plugin_options');

		//call register settings function
		add_action('admin_init', 'register_inrealtime_settings');
	}

	function register_inrealtime_settings() {
		//register our settings
		register_setting('inrealtime-settings-group', 'access_token');
	}

	if (is_file(IRT_PATH . 'irt-options.php')) {

		include_once(IRT_PATH . 'irt-options.php');

	}

	function inrealtime_plugin_options() {

		global $irtOptions, $wp_upload_dir_REL;

		(ini_get('safe_mode')) ? $safeModeStat = "<span class='setting-bad'>ON. This plug-in may not work properly in Safe Mode!</span>" : $safeModeStat = "<span class='setting-good'>OFF</span>";

		if (is_dir($wp_upload_dir_REL)) {

			(is_writable($wp_upload_dir_REL)) ? $uploadDirStat = "<span class='setting-good'>OK and writable</span>" : $uploadDirStat = "<span class='setting-bad'>Directory exists but is NOT writable. Please adjust permissions.";
		
		} else {
			
			$uploadDirStat = "<span class='setting-bad'>The upload directory is not present. Please check your installation.</span>";
		
		}

	?>
		<link rel="stylesheet" href="<?php echo plugins_url('assets/irt.css', __FILE__); ?>" />
		<script>
			var irtEnv = "<?php echo IRT_URL; ?>";
		</script>

		<div class="wrap">

			<h2>In Real-Time Settings</h2>

			<div id="irt-settings-messages">
				<p></p>
			</div>

			<div class="settings-block">

				<?php 

					if ($irtOptions['clientID'] == null && $irtOptions['clientSecret'] == null) {

				?>

				<form id="auth-set">

					<h2>Instagram Client Settings</h2>
					<div id="irt-client-info">

						<p>client ID : <span id="irt-client-static"><?php echo $irtOptions['clientID']; ?></span></p>
						<p>client Secret : <span id="irt-secret-static"><?php echo $irtOptions['clientSecret']; ?></span></p>
						<br />

					</div>
					<div id="irt-client-edit">
						<p>
							<label for="irt-client">client ID </label><input type="text" id="irt-client" class="no-work-y" value="<?php echo $irtOptions['clientID']; ?>" size="50" /><br />
							<label for="irt-secret">client Secret </label><input type="text" id="irt-secret" class="no-work-y" value="<?php echo $irtOptions['clientSecret']; ?>" size="50" /><br />
							<br />
						</p>
					</div>
					<div id="irt-instructions">

						<h2>How-to Get Your Client ID</h2>

						<p>
							Follow these steps to create your Instagram Client ID.
						</p>

						<ol>
							<li>Click the "Get Client Info" button below. <i>(this will open a pop-up.)</i></li>
							<li>Click the "Manage Clients" button.</li>
							<li><b>VERY IMPORTANT:</b> Click the "login" link in the paragraph "Please login" to log-in to your Instagram Account.</li>
							<li>Click the "Register Your Application" button.</li>
							<li>Click the "Register a New Client" button.</li>
							<li>Name your Application. Something like <b><em><?php echo bloginfo('name'); ?>'s In Real-Time App</em></b> will do nicely.</li>
							<li>Add a Description.</li>
							<li>Your website is: <b><em><?php echo IRT_SITE; ?></em></b></li>
							<li>Your OAuth redirect_uri is: <b><em><?php echo IRT_URL . "irt-callback.php"; ?></em></b></li>
							<li>Click the "Register" button.</li>
							<li>Copy and Paste the "Client ID" and "Client Secret" into the fields above.</li>
							<li>Click the "Authenticate!" button below. <i>(another pop-up, here.)</i></li>
						</ol>
						<br />
						<br />
					</div>
					<input type="submit" class="button-primary" id="irt-setting-grabber" value="Get Client Info" onclick="return false;" />
					<input type="submit" class="button-primary" id="irt-client-change" value="Change Client Info" onclick="return false;" />
					<input type="submit" class="button-primary" id="irt-client-submit" value="Authenticate!" onclick="return false;" />
					<input type="submit" class="button-primary" id="irt-client-cancel" value="Cancel" onclick="return false;" />

				</form>

				<?php

					} else {

				?>

				<form id="auth-change">

					<h2>Instagram Client Settings</h2>
					<div id="irt-client-info">

						<p>client ID : <span id="irt-client-static"><?php echo $irtOptions['clientID']; ?></span></p>
						<p>client Secret : <span id="irt-secret-static"><?php echo $irtOptions['clientSecret']; ?></span></p>
						<br />

					</div>
					<div id="irt-client-edit">
						<p>
							<label for="irt-client">client ID </label><input type="text" id="irt-client" class="no-work-y" value="<?php echo $irtOptions['clientID']; ?>" size="50" /><br />
							<label for="irt-secret">client Secret </label><input type="text" id="irt-secret" class="no-work-y" value="<?php echo $irtOptions['clientSecret']; ?>" size="50" /><br />
							<br />
						</p>
					</div>
					<div id="irt-instructions">

						<h2>How-to Get Your Client ID</h2>

						<p>
							Follow these steps to create your Instagram Client ID.
						</p>

						<ol>
							<li>Click the "Review Client Info" button below. <i>(this will open a pop-up.)</i></li>
							<li>Click the "Manage Clients" button.</li>
							<li><b>VERY IMPORTANT:</b> Click the "login" link in the paragraph "Please login" to log-in to your Instagram Account.</li>
							<li>Click the "Register Your Application" button.</li>
							<li>Click the "Register a New Client" button.</li>
							<li>Name your Application. Something like <b><em><?php echo bloginfo('name'); ?>'s In Real-Time App</em></b> will do nicely.</li>
							<li>Add a Description.</li>
							<li>Your website is: <b><em><?php echo IRT_SITE; ?></em></b></li>
							<li>Your OAuth redirect_uri is: <b><em><?php echo IRT_URL . "irt-callback.php"; ?></em></b></li>
							<li>Click the "Register" button.</li>
							<li>Copy and Paste the "Client ID" and "Client Secret" into the fields above.</li>
							<li>Click the "Authenticate!" button below. <i>(another pop-up, here.)</i></li>
						</ol>
						<br />
						<br />
					</div>
					<input type="submit" class="button-primary" id="irt-setting-grabber" value="Review Client Info" onclick="return false;" />
					<input type="submit" class="button-primary" id="irt-client-change" value="Change Client Info" onclick="return false;" />
					<input type="submit" class="button-primary" id="irt-client-submit" value="Authenticate!" onclick="return false;" />
					<input type="submit" class="button-primary" id="irt-client-cancel" value="Cancel" onclick="return false;" />

				</form>

				<?php

					}

				?>

				<form id="opt">

					<h2>Post Settings</h2>
					<p>
						<label for="irt-publish">Publish state for posts?</label>
						<select id="irt-publish"> <!-- 'draft' | 'publish' | 'pending'| 'future' | 'private' -->
							<option <?php if ($irtOptions['publishState'] == "publish") echo "SELECTED"; ?> value="publish">publish</option>
							<option <?php if ($irtOptions['publishState'] == "draft") echo "SELECTED"; ?> value="draft">draft</option>
							<option <?php if ($irtOptions['publishState'] == "pending") echo "SELECTED"; ?> value="pending">pending</option>
							<option <?php if ($irtOptions['publishState'] == "future") echo "SELECTED"; ?> value="future">future</option>
							<option <?php if ($irtOptions['publishState'] == "private") echo "SELECTED"; ?> value="private">private</option>
						</select><br />
						<label for="irt-category">Post in category?</label>
						<select id="irt-category"> 
							<?php 

								$catArgs = array(
									'type' => 'post',
									'hide_empty' => 0
								);

								$categories = get_categories($catArgs);

								foreach ($categories as $category) {

									if ($irtOptions['publishCategory'] == $category->cat_ID) {
										$option = '<option SELECTED value="' . $category->cat_ID . '">';							
									} else {
										$option = '<option value="' . $category->cat_ID . '">';								
									}
									$option .= $category->cat_name;
									$option .= '</option>';
									echo $option;
								}
							?>
						</select><br />
						<label for="irt-author">Author of posts?</label>
							<?php 

								$selec = ($irtOptions['publishAuthor']) ? $selec = $irtOptions['publishAuthor'] : $selec = false;
								$args = array(
									'show_option_all' => null, // string
									'show_option_none' => null, // string
									'hide_if_only_one_author' => null, // string
									'orderby' => 'display_name',
									'order' => 'ASC',
									'include' => null, // string
									'exclude' => null, // string
									'multi' => false,
									'show' => 'display_name',
									'echo' => true,
									'selected' => $selec,
									'include_selected' => false,
									'name' => 'irt-author', // string
									'id' => 'irt-author', 
									'blog_id' => $GLOBALS['blog_id'],
									'who' => 'authors' // string
								);
								wp_dropdown_users( $args );

							?>
						<br />
						<label for="irt-comments">Comments</label>
						<select id="irt-comments"> <!-- 'open' | 'closed' -->
							<option <?php if ($irtOptions['allowComments'] == "closed") echo "SELECTED"; ?> value="closed">closed</option>
							<option <?php if ($irtOptions['allowComments'] == "open") echo "SELECTED"; ?> value="open">open</option>
						</select><br />
						<label for="irt-pings">Pingbacks</label>
						<select id="irt-pings"> <!-- 'open' | 'closed' -->
							<option <?php if ($irtOptions['allowPings'] == "closed") echo "SELECTED"; ?> value="closed">closed</option>
							<option <?php if ($irtOptions['allowPings'] == "open") echo "SELECTED"; ?> value="open">open</option>
						</select><br />
						<br />
					</p>

					<h2>Image Settings</h2>
					<p>
						<label for="irt-postimg">Post image</label>
						<select id="irt-postimg">
							<option <?php if ($irtOptions['imageInPost'] == "standard_resolution") echo "SELECTED"; ?> value="standard_resolution">standard (612x612)</option>
							<option <?php if ($irtOptions['imageInPost'] == "low_resolution") echo "SELECTED"; ?> value="low_resolution">medium (306x306)</option>
							<option <?php if ($irtOptions['imageInPost'] == "thumbnail") echo "SELECTED"; ?> value="thumbnail">thumbnail (150x150)</option>							
						</select>
						<br />
						
						<label for="irt-feature">Attach a feature image?</label>
						<select id="irt-feature">
							<option <?php if ($irtOptions['imageInFeature']['attach'] == "no") echo "SELECTED"; ?> value="no">no</option>
							<option <?php if ($irtOptions['imageInFeature']['attach'] == "yes") echo "SELECTED"; ?> value="yes">yes</option>
						</select>
						<select id="irt-featurechoice" class="irt-settings-choice">
							<option <?php if ($irtOptions['imageInFeature']['size'] == "thumbnail") echo "SELECTED"; ?> value="thumbnail">thumbnail (150x150)</option>
							<option <?php if ($irtOptions['imageInFeature']['size'] == "low_resolution") echo "SELECTED"; ?> value="low_resolution">medium (306x306)</option>
							<option <?php if ($irtOptions['imageInFeature']['size'] == "standard_resolution") echo "SELECTED"; ?> value="standard_resolution">standard (612x612)</option>
						</select>
						<br />
						<br />
					</p>

					<h2>Email Settings</h2>
					<p>
						<label for="irt-email">send email notifications? </label>
						<select id="irt-email">
							<option <?php if ($irtOptions['emailNotify']['notify'] == "no") echo "SELECTED"; ?> value="no">no</option>
							<option <?php if ($irtOptions['emailNotify']['notify'] == "yes") echo "SELECTED"; ?> value="yes">yes</option>
						</select>
						<input type="text" id="irt-emailchoice" value="<?php echo $irtOptions['emailNotify']['address']; ?>" class="irt-settings-choice" /><br />
						<br />
						<br />
						
						<input type="submit" class="button-primary" id="irt-submit" value="Save Settings" onclick="return false;" />
					</p>
					
				</form>

			</div>

			<div class="status-block">

				<h2>Plug-in Status</h2>
				<p>
					Safe Mode : <?php echo $safeModeStat; ?><br />
					Images Dir : <?php echo $uploadDirStat; ?><br />
					<br />
					<input type="submit" class="button-primary" id="irt-refresh-stats" value="Recheck" />
				</p>
				
				<hr noshade />

				<h2>Get All Images</h2>

				<p>
					To be used sparingly, however, clicking this button will create a post for all of your past Instagram images.<br />
					<br />

					<input type="button" class="button-primary" id="irt-getall" value="Get All Images" onclick="return false;" />
					<img id="irt-getall-spinner" src="<?php echo plugins_url('assets/spinner.gif', __FILE__); ?>" /><br />
					<span id="irt-getall-message"></span>

				</p>

				<hr noshade />

				<h2>Check Subscriptions</h2>
				<p>
					<a href="https://api.instagram.com/v1/subscriptions?client_secret=<?php echo $irtOptions['clientSecret']; ?>&client_id=<?php echo $irtOptions['clientID']; ?>" target="_blank">Click here</a> to check the subscription data for your Instagram client. (In JSON format)
					<br />
				</p>

				<hr noshade />

				<h2>Like this Plug-in?</h2>

				<p>
					<a href="http://wordpress.org/extend/plugins/in-real-time/" target="_blank">&rarr; Rate it on Wordpress.org</a><br />
					or better yet ...
				</p>
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="KCH576YQKJUMU">
					<input type="image" src="http://heckchuckman.com/files/buy-me-a-beer.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>

				<hr noshade />

				<h2>Issues?</h2>
				<p>
					<a href="https://github.com/heckchuckman/in-realtime/issues" target="_blank">&rarr; Log an Issue on GitHub</a><br />
					or ...<br />
					<a href="mailto:inrealtime.plugin@gmail.com">Send me an Email</a>
				</p>

			</div>

		</div>

		<script src="<?php echo plugins_url('assets/irt.js', __FILE__); ?>"></script>

	<?php 

	}

}

?>