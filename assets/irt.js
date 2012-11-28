var inrealtimeOptions = {
	messages : function(txt) {

		var messages = jQuery("#irt-settings-messages"),
			message = jQuery("#irt-settings-messages > p");

		message.text("" + txt + "");
		messages.fadeIn(1000, function() {
			setTimeout(function() {
				messages.fadeOut();
			}, 5000);
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
			settingsInstagramWin,
			statusStatRecheck = jQuery("#irt-refresh-stats");

		statusStatRecheck.click(function() {
			document.location.reload(true);
		});

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

				cid = cid.replace(/\s/g, ''); // remove any white spaces
				sid = sid.replace(/\s/g, '');

			jQuery.ajax({
				type: "POST",
				url : "" + inrealtimeOptions.env + "irt-editor.php",
				data: "client=" + cid + "&secret=" + sid,
				success: function(resp) {

					// inrealtimeOptions.messages(resp);

				},
				complete : function() {
					
					// pop the auth window.
					settingsDirectionsWin = window.open("https://instagram.com/oauth/authorize/?client_id=" + cid + "&redirect_uri=" + inrealtimeOptions.env + "irt-callback.php&response_type=code", "settings", "width=1000, height=" + hgt + ", location=no, resizable=no, toolbar=no, scrollbars=no");
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
				url : "" + inrealtimeOptions.env + "irt-getall.php",
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

			if (jQuery("#irt-email").is("visible")) {

				jQuery("#irt-emailchoice").fadeIn();

			}
		
		}
		
		jQuery("#irt-email, #irt-feature").each(function() { changeCheck(this.id); });

		jQuery("#irt-email, #irt-feature").bind("change", function() { changeCheck(this.id); });

		submit.click(function() {

			var publish = jQuery("#irt-publish").val(),
				category = jQuery("#irt-category").val(),
				author = jQuery("#irt-author").val(),
				comments = jQuery("#irt-comments").val(),
				pings = jQuery("#irt-pings").val(),
				email = jQuery("#irt-email").val(),
				emailchoice = jQuery("#irt-emailchoice").val(),
				postimg = jQuery("#irt-postimg").val(),
				feature = jQuery("#irt-feature").val(),
				featureimg = jQuery("#irt-featurechoice").val(),
				argString;

			argString = "publish=" + publish + "&category=" + category + "&author=" + author + "&img=" + postimg + "&comments=" + comments + "&pings=" + pings;

			if (email === "yes") {
				argString = argString + "&email=" + email + "&emailaddr=" + emailchoice;
			} else {
				argString = argString + "&email=" + email;
			}

			if (feature === "yes") {
				argString = argString + "&feature=" + feature + "&featimg=" + featureimg;	
			} else {
				argString = argString + "&feature=" + feature;
			}

			jQuery.ajax({
				type: "POST",
				url : "" + inrealtimeOptions.env + "irt-editor.php",
				data : "" + argString + "",
				success : function(resp) {

					//window.scrollTo(0,0);
					jQuery('html, body').scrollTop(0);
					inrealtimeOptions.messages(resp);

				}
			});

		});

	},
	init : function(en) {
		this.env = en;
		this.events();
	}
};

inrealtimeOptions.init(irtEnv);