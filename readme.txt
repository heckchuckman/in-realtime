=== In Real-Time : An Instagram Plug-in ===
Contributors: heckchuckman
Donate link: http://heckchuckman.com/in-realtime
Tags: instagram, photography
Requires at least: 3.2
Tested up to: 3.4.2
Stable tag: 1.5

In Real-Time is a different kind of Instagram plug-in. Using the Instagram Real-time API, this plug-in is developed for users that want posts created automatically for each photo, rather than have a widget or post with images embedded. Developed for Devs but useful for all.

== Description ==

In Real-Time is a different kind of Instagram plug-in. Using the Instagram Real-time API, this plug-in is developed for users that want posts created automatically for each photo, rather than have a widget or post with images embedded. Developed for Devs but useful for all.

== Installation ==

1. Upload 'in-realtime' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure your settings in the 'In Real-Time' menu panel.

== Frequently Asked Questions ==

= I posted a photo to Instagram but a post on my blog wasn’t created!?! =

Don’t worry. There seem to be a couple of gremlins in the Instagram Real-time service from time to time. The In-RealTime plug-in compensates for this by requesting the previous 8 posts and checking to see if the images have been saved to your server. The missing post(s) should be created the next time you post to Instagram.

In addition, Instagram uses what they call the "2 second rule." This rule basically states that if your server consistently doesn't respond to the post-back within 2 seconds, the Instagram service will throttle back posts to your server to up to an hour. Unfortunately, there is nothing I can do to the codebase for this. If you are experiencing this issue, you may need a better host!

= I’m getting PHP errors with the install and posts aren’t created… =

I’ve noticed that there are a few hosting configurations that cause issues with both writing the preferences and image files to the server. Windows servers and PHP in safe mode both seem to have issues, at the moment. I’m investigating a fix for these issues.

= I use Opera and set-up won’t work! =

It appears as though the Instagram website, which is needed for OAuth and setting your token, doesn’t play well with Opera. Please use Chrome or Firefox.

== Changelog ==

= 1.5 =
- Added some environment checks and outputs to help troubleshoot issues with SafeMode and directory permissions.
- Updated the Admin page
- Added support for Comments and Pings
- Code optimizations

= 1.0.1 =
Fixed a directory error. Tested on other platforms.

= 1.0 =
First release.

== Upgrade Notice ==

= 1.5 =
Added some environment checks and outputs to help troubleshoot issues with SafeMode and directory permissions. Also added support for Comments and Pings, reformatted some areas of the Admin Panel and did code optimizations.