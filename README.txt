=== Freshjet ===
Contributors: contactjavas
Tags: mailjet, freshjet, wp_mail, smtp, transactional, email
Donate link: https://www.patreon.com/bagus
Requires at least: 5.2
Tested up to: 5.2.2
Requires PHP: 7.2
Stable tag: trunk
License: GPL-3.0 License
License URI: https://oss.ninja/gpl-3.0?organization=Fresh-Forces

Mailjet `wp_mail()` drop-in replacement.

== Description ==
Freshjet is a [Mailjet](https://www.mailjet.com/) `wp_mail()` drop-in replacement. This plugin is very simple:

- Giving you drop-in replacement for `wp_mail`
- Support for [Mailjet Passport Template](https://app.mailjet.com/templates/transactional)
- Plus one flexible function: `bulk_mail`

== Installation ==
= Through the WordPress administrative area: =
- From WordPress administrative area, go to _Plugins_ -> _Add New_
- Search for _Freshjet_
- Install and then activate it
- [Visit this page](https://app.mailjet.com/transactional) to see your Mailjet Keys.
- Insert your _API Public Key (SMTP username)_ and _API Secret Key (SMTP password)_ in _yoursite/wp-admin/admin.php?page=freshjet_

= Download manually: =
- Download the plugin from [WordPress plugins page](https://wordpress.org/plugins/freshjet/)
- Upload to your _wp-content_ directory
- Activate it from _Plugins_ menu in admin area
- [Visit this page](https://app.mailjet.com/transactional) to see your Mailjet Keys.
- Insert your _API Public Key (SMTP username)_ and _API Secret Key (SMTP password)_ in _yoursite/wp-admin/admin.php?page=freshjet_

== Frequently Asked Questions ==
= What part of Mailjet\'s feature does this plugin support? =

We support the transactional part. Mostly, for the `wp_mail ()` drop-in replacement.

= What kind of Mailjet template do you support? =

We support the Passport Transactional Template.

= Do you have a GitHub account for this?

Yes, we have it [here] (https://github.com/freshforces-borndigital/freshjet)

== Screenshots ==
1. admin-screenshot-1.png

== Changelog ==
= 0.5.3 =
- `wp_mail()` drop-in replacement
- Provide support for Mailjet's Transactional Passport Template

== Upgrade Notice ==
Just update the plugin