<?php

/*
Plugin Name: In Real-Time
Plugin URI: http://heckchuckman.com/in-realtime
Description: In Real-Time is a different kind of Instagram plug-in. Using the Instagram Real-time API, this plug-in is developed for users that want posts created automatically for each photo, rather than have a widget or post with images embedded. Developed for Devs but useful for all.
Version: 1.0
Author: Chuck Heckman
Author URI: heckchuckman.com
License: GPL2
*/

if (is_admin()) {

	// get settings
	include_once($_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/in-realtime/irt-options.php');
	$irtUrl = $_SERVER['SERVER_NAME'];

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

	function inrealtime_plugin_options() {

		global $irtOptions, $irtUrl, $wp_upload_dir;

	?>

		<style>
			
			form {
				margin: 0 0 32px 0;
			}
			
			#irt-settings-messages {
				padding: 0px 12px;
				border: 1px black solid;
				background: #cecece;
				display: none;
			}
			
			.no-work-y {
				color: #cecece;
			}

			#irt-getall-spinner {
				margin: 0 10px 0 0;
				float: left;
			}

			#irt-getall-message {
				margin: 12px 0 0 0;
				float: left;
			}

			#auth-change #irt-instructions,
			#auth-set #irt-client-change,
			#auth-set #irt-client-info,
			#auth-change #irt-client-edit,
			#irt-client-cancel,
			#auth-change #irt-client-submit,
			.irt-settings-choice,
			#irt-getall-spinner,
			#irt-getall-message {
				display: none;
			}

		</style>

		<div class="wrap">
			<h2>In Real-Time Settings</h2>

			<div id="irt-settings-messages">
				<p></p>
			</div>

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
						<label for="irt-client">client ID </label><input type="text" id="irt-client" class="no-work-y" value="<?php echo $irtOptions['clientID']; ?>" size="70" /><br />
						<label for="irt-secret">client Secret </label><input type="text" id="irt-secret" class="no-work-y" value="<?php echo $irtOptions['clientSecret']; ?>" size="70" /><br />
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
						<li>Your website is: <b><em>http://<?php echo $irtUrl; ?></em></b></li>
						<li>Your OAuth redirect_uri is: <b><em>http://<?php echo $irtUrl . "/wp-content/plugins/in-realtime/irt-callback.php"; ?></em></b></li>
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
						<label for="irt-client">client ID </label><input type="text" id="irt-client" class="no-work-y" value="<?php echo $irtOptions['clientID']; ?>" size="70" /><br />
						<label for="irt-secret">client Secret </label><input type="text" id="irt-secret" class="no-work-y" value="<?php echo $irtOptions['clientSecret']; ?>" size="70" /><br />
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
						<li>Your website is: <b><em>http://<?php echo $irtUrl; ?></em></b></li>
						<li>Your OAuth redirect_uri is: <b><em>http://<?php echo $irtUrl . "/wp-content/plugins/in-realtime/irt-callback.php"; ?></em></b></li>
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
					<br />
				</p>

				<h2>Image Settings</h2>
				<p>
					<label for="irt-postimg">Image to be inserted into post body.</label>
					<select id="irt-postimg">
						<option <?php if ($irtOptions['imageInPost'] == "thumbnail") echo "SELECTED"; ?> value="thumbnail">thumbnail (150x150)</option>
						<option <?php if ($irtOptions['imageInPost'] == "low_resolution") echo "SELECTED"; ?> value="low_resolution">medium (306x306)</option>
						<option <?php if ($irtOptions['imageInPost'] == "standard_resolution") echo "SELECTED"; ?> value="standard_resolution">standard (612x612)</option>
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

			<hr noshade />

			<h2>Get All Images</h2>

			<p>
				To be used sparingly, however, clicking this button will create a post for all of your past Instagram images.<br />
				<br />

				<input type="button" class="button-primary" id="irt-getall" value="Get All Images" onclick="return false;" />
				<img id="irt-getall-spinner" src="<?php echo plugins_url( 'spinner.gif', __FILE__ ); ?>" /> <span id="irt-getall-message"></span>
				<br />
				<br />

			</p>

			<hr noshade />

			<h2>Like this Plug-in?</h2>

			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="KCH576YQKJUMU">
				<input type="image" src="http://heckchuckman.com/files/buy-me-a-beer.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</form>


		</div>


		<script>
			window.name = "mother";
			var inrealtimeOptions = {
				messages : function(txt) {

					var messages = jQuery("#irt-settings-messages"),
						message = jQuery("#irt-settings-messages > p");

					message.text("" + txt + "");
					messages.fadeIn(1000, function() {
						setTimeout(function() { messages.fadeOut() }, 5000);
					});

				},
				events : function() {
					var client = jQuery("#irt-client"),
						secret = jQuery("#irt-secret"),
						clientStatic = jQuery("#irt-client-static"),
						secretStatic = jQuery("#irt-secret-static"),
						clientInfoBlock = jQuery("#irt-client-info"),
						clientEditBlock = jQuery("#irt-client-edit"),
						clientChange = jQuery("#irt-client-change"),
						clientCancel = jQuery("#irt-client-cancel"),
						clientSubmit = jQuery("#irt-client-submit"),
						getAll = jQuery("#irt-getall"),
						getAllSpinner = jQuery("#irt-getall-spinner"),
						getAllMessage = jQuery("#irt-getall-message"),
						token = jQuery("#irt-token").val(),
						submit = jQuery("#irt-submit"),
						settingGrabber = jQuery("#irt-setting-grabber"),
						settingInstructions = jQuery("#irt-instructions"),
						settingsDirectionsWin,
						settingsInstagramWin;

					clientChange.click(function() {
						
						clientChange.hide();
						clientInfoBlock.hide();
						clientEditBlock.fadeIn();
						clientSubmit.show();
						clientCancel.show();
						settingInstructions.fadeIn();

					});

					clientCancel.click(function() {
						
						clientSubmit.hide();
						clientCancel.hide();
						settingInstructions.hide();
						clientEditBlock.hide();
						clientInfoBlock.fadeIn();
						clientChange.show();

						settingsInstagramWin.close();
					
					});

					clientSubmit.click(function() { // set the Client ID and Authenticate the app

						var cid = client.val(),
							sid = secret.val(),
							hgt = jQuery(window).height();

						jQuery.ajax({
							url : "../wp-content/plugins/in-realtime/irt-editor.php",
							data : "client=" + cid + "&secret=" + sid,
							success : function(resp) {

								inrealtimeOptions.messages(resp);

							},
							complete : function() {
								
								// pop the auth window.
								settingsDirectionsWin = window.open("https://instagram.com/oauth/authorize/?client_id=" + cid + "&redirect_uri=" + location.protocol + "//" + document.domain + "/wp-content/plugins/in-realtime/irt-callback.php&response_type=code", "settings", "width=1000, height=" + hgt + ", location=no, resizable=no, toolbar=no, scrollbars=no");
								settingsDirectionsWin.focus();

								settingInstructions.fadeOut();
								clientStatic.text(cid);
								secretStatic.text(sid);
								clientCancel.trigger("click");

							}
						});

					});

					settingGrabber.click(function() {
						var hgt = jQuery(window).height(),
							wdt = jQuery(window).width();
							
						settingsInstagramWin = window.open("http://instagram.com/developer/clients/manage/", "instagram", "width=700, height=" + hgt + ", left=" + (wdt/2) + ", location=no, toolbar=no, scrollbars=no");
					});

					getAll.click(function() {

						var response;

						jQuery(this).hide();
						getAllSpinner.show();
						getAllMessage
							.text("This will take a while depending on the number of images in your account.")
							.fadeIn();

						jQuery.ajax({
							url : "../wp-content/plugins/in-realtime/irt-getall.php",
							success : function(resp) {

								response = resp;

							},
							complete : function() {

								getAllSpinner.hide();

								getAllMessage
									.css("marginTop", "0px")
									.html("<b>" + response + "</b>")
									.fadeIn(1000, function() {
										setTimeout(function() { 
											getAllMessage.hide();
											getAll.show();
										}, 5000);
									})
									.css("marginTop", "12px");

							}
						});

					});

					function changeCheck(field) {

						var fieldVal = jQuery("#" + field + "").val();

						if (fieldVal === "yes") {
							jQuery("#" + field + "choice").fadeIn();
						} else {
							jQuery("#" + field + "choice").fadeOut();
						}
					
					}
					
					jQuery("#irt-email, #irt-feature").each(function() { changeCheck(this.id); });

					jQuery("#irt-email, #irt-feature").bind("change", function() { changeCheck(this.id); });

					submit.click(function() {

						var publish = jQuery("#irt-publish").val(),
							category = jQuery("#irt-category").val(),
							author = jQuery("#irt-author").val(),
							email = jQuery("#irt-email").val(),
							emailchoice = jQuery("#irt-emailchoice").val(),
							postimg = jQuery("#irt-postimg").val(),
							feature = jQuery("#irt-feature").val(),
							featureimg = jQuery("#irt-featurechoice").val(),
							argString;

						argString = "publish=" + publish + "&category=" + category + "&author=" + author + "&img=" + postimg;

						if (email === "yes") {
							argString = argString + "&email=" + email + "&emailchoice=" + emailchoice;
						} else {
							argString = argString + "&email=" + email;
						}

						if (feature === "yes") {
							argString = argString + "&feature=" + feature + "&featimg=" + featureimg;	
						} else {
							argString = argString + "&feature=" + feature;
						}

						jQuery.ajax({
							url : "../wp-content/plugins/in-realtime/irt-editor.php",
							data : "" + argString + "",
							success : function(resp) {

								inrealtimeOptions.messages(resp);

							}
						});

					});

				},
				init : function() {
					this.events();
				}
			}

			inrealtimeOptions.init();

		</script>

	<?php 

	}

}

?>