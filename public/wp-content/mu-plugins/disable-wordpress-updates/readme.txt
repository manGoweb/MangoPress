=== Disable All WordPress Updates ===
Contributors: Alphawolf
Donate link: http://www.schloebe.de/donate/
Tags: disable, updates, theme, core, auto-update
Requires at least: 3.4
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Disables the theme, plugin and core update checking, the related cronjobs and notification system.

== Description ==

This plugin completely disables the theme, plugin and core update checking system in WordPress. The plugin prevents WordPress from
checking for updates including cronjobs, and prevents any notifications from being displayed.

This plugin combines und extends these three plugins:

* [Disable WordPress Theme Update](http://wordpress.org/extend/plugins/disable-wordpress-theme-updates/)
* [Disable WordPress Plugin Update](http://wordpress.org/extend/plugins/disable-wordpress-plugin-updates/)
* [Disable WordPress Core Update](http://wordpress.org/extend/plugins/disable-wordpress-core-update/)

It's *very* important that you keep your WordPress theme, core and plugins up to date. If you don't, your blog or website could
be **susceptible to security vulnerabilities** or performance issues.

If you use this plugin, make sure you keep yourself up to date with new releases of your active WordPress version, plugins
and themes and update them as new versions are released (simply by deactivating this plugin for a short time).

[Developer on Google+](https://plus.google.com/118074611982254715031 "Developer on Google+") | [Developer on Twitter](http://twitter.com/wpseek "Developer on Twitter")

**Looking for more WordPress plugins? Visit [www.schloebe.de/portfolio/](http://www.schloebe.de/portfolio/)**


== Frequently Asked Questions ==

= What can I do to be up to date with new releases of my WordPress version, plugins and themes? =

Simply deativate the plugin for a short time. In case new versions are available you see the WordPress notifications.


== Installation ==

1. Download the plugin and unzip it.
2. Upload the folder disable-wordpress-updates/ to your /wp-content/plugins/ folder.
3. Activate the plugin from your WordPress admin panel.
4. Installation finished.

With activating the plugin all theme, core and plugin update checkings are disabled. If you want to have a quick look if new versions
are existing, simply deactive this plugin for a short time.


== Screenshots ==

None. :)


== Changelog ==

= 1.4.7 =
* Fixed a PHP notice

= 1.4.6 =
* Hide maintenance and update nags

= 1.4.5 =
* Check for already defined constants (Thanks Andreas Ek!)

= 1.4.4 =
* WordPress HTTP API level blocking of outgoing wordpress.org API checks

= 1.4.3 =
* Improved blocking of outgoing wordpress.org API checks which caused slow speed on local environments (Thanks Makapaka!)

= 1.4.2 =
* Update indicator not showing in the front-end toolbar anymore (Thanks flixflix!)

= 1.4.1 =
* Fixed an issue that still allowed WordPress to update minor (security and maintenance) versions

= 1.4.0 =
* Disable all automatic background updates (thanks sLa NGjI's)

= 1.3.1.1 =
* Fixed a PHP notice on dev environments (thanks dejernet)

= 1.3.1 =
* Significant performance improvements when disabling updates (props flynsarmy)

= 1.3.0.1 =
* Minor updates

= 1.3 =
* New plugin maintainer. Hello. :) Name's Oliver.
* Code rewrite and cleanup

= 1.2 =
* Name and URL Update (German plugin description updated)

= 1.1 =
* URL Update because of permalink changes

= 1.0 =
* Initial release