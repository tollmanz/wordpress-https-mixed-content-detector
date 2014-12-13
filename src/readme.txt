=== Plugin Name ===
Contributors: tollmanz
Tags: https, tls, ssl
Requires at least: 4.0.1
Tested up to: trunk
Stable tag: 1.0.0
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

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release