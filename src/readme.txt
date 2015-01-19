=== HTTPS Mixed Content Detector ===
Contributors: tollmanz
Tags: https, tls, ssl
Requires at least: 4.0.1
Tested up to: trunk
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Detects and logs content that will cause mixed content warnings.

== Description ==

When deploying a TLS enabled website, you must ensure that all content loaded on the site is loaded from secure origin.
If your content is loaded from an insecure source, the security of your whole site is compromised and modern browsers
will downgrade your website's security rating.

The HTTPS Mixed Content Detector plugin attempts to identify sources of mixed content warnings. The plugin will examine
content loaded from the site when admins are viewing the site. Any content that violates the policy of loading content
that originates from "https:" resources will trigger an error and that resource will be logged. Viewing the log will
allow you to examine the site for any warnings and remove them before they cause problems for your website.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `https-mixed-content-detector` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Browse your site as an admin
1. View the reports listed in the "Content Security Policy Reports" page in the admin
1. Delete each violation report log as you fix it
1. Rinse and repeat until your site is free of violation reports

== Screenshots ==

1. Content Security Policy reports are collected in the Content Security Policy Reports list table.
2. Information about Content Security Policy reports is available via WP CLI.

== Changelog ==

= 1.2.0 =
* Add check for violation locations
* Add sampling mode for examining non-logged in traffic
* Add more content shown in the WP list table

= 1.1.0 =
* Add check for HTTPS domain when logging violation
* Add `list`, `resolve`, `remove` and `unresolve` WP CLI commands
* Update CSP directives to be more specific

= 1.0.2 =
* Remove false positives from the log

= 1.0.1 =
* Limit logging to work only for admins

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.2.0 =
Add violation locations, sampling mode and more WP list table information

= 1.1.0 =
Adds HTTPS domain checking, WP CLI commands and more specific CSP directives

= 1.0.2 =
Remove false positives from the log

= 1.0.1 =
Limit logging to work only for admins

= 1.0.0 =
Initial release