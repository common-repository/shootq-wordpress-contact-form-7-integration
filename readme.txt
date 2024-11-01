=== Plugin Name ===
Contributors: wakeless, terroir, lizzyc	
Tags: contact, integration, photography
Requires at least: 2.9.0
Tested up to: 3.2
Stable tag: 0.3

This adds ShootQ integration to all Contact Form 7 forms on a blog.

== Description ==

This plugin adds ShootQ integration to all Contact Form 7 forms on your blog.

It does this automatically, by looking at the names of the fields and matching them up with those in the ShootQ
API.

This development is thanks to [Lizzy C Photography](http://www.lizzyc.com.au/ "Melbourne Photographer").

== Installation ==

Install [Contact Form 7](http://wordpress.org/extend/plugins/contact-form-7/) and set up a form.

Install the plugin as per usual. Add your ShootQ API key and brand name shortcut to the settings page.
Check your Contact Form 7 forms to ensure they use fields with these names. All field
names can be prefixed with **your-**

*  **name** or **first-name** and **last-name** | **your-name** or **your-first-name** and **your-last-name**
*  **phonenumber** or **your-phonenumber**
*  **email** or **your-email**
*  **type** or **your-type** (this is for the type of event)
*  **date** or **your-date**
*  **referrer** or **your-referrer**
*  **subject** or **your-subject**
*  **message** or **your-message**

== Frequently Asked Questions ==


== Changelog ==

= 0.3 =
Fix broken dates.

= 0.1 =
* Initial release
