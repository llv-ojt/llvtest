=== Header and Footer ===
Tags: header, footer, blog, page, single, post, head, tracking, facebook, og meta tag, open graph, ads
Requires at least: 2.9
Tested up to: 3.9
Stable tag: trunk
Donate link: http://www.satollo.net/donations

Header and Footer plugin let you to add html code to the head and footer sections of your blog... and more!

== Description ==

Why you have to install 10 plugins to add Google Analytics code, MyBlogLog
tracking code, Google Webmaster verificaton code, MyblogLog verification code,
tradedoubler verification code (and so on...) on head of footer section of your blog
pages?

With Header and Footer plugin you can just copy the code those services give you
in a centralized point to manage them all.

* manage the head section code
* manage the footer code
* manage the facebook og:image tag
* manage codes to be added before and after post content (social buttons, ads, ...)
* PDF manual with examples
* recognize and execute PHP code to add logic
* few SEO options
* mobile detection

Offial page: [Header and Footer](http://www.satollo.net/plugins/header-footer).

== Installation ==

1. Put the plugin folder into [wordpress_dir]/wp-content/plugins/
2. Go into the WordPress admin interface and activate the plugin
3. Optional: go to the options page and configure the plugin

== Frequently Asked Questions ==

FAQs are answered on [Header and Footer](http://www.satollo.net/plugins/header-footer) page.

== Screenshots ==

1. Configuration panel for blog HEAD and footer sections
2. Configuration panel for post content
3. Configuration panel for Facebook "og" tags
4. Configurable snippets of code to be recalled on other configurations (to save time)
5. BBPress integration

== Changelog ==

= 1.5.4 =

* Fixed the "global post" variable when injections contain php

= 1.5.3 =

* Fixed a link

= 1.5.2 =

* Fixed a debug notice

= 1.5.1 =

* Fixed some debug notices
* ru_RU translation by [Eugene Zhukov](https://plus.google.com/u/0/118278852301653300773)
* Added the "thank you" panel
* Fixed the missing user agent notice

= 1.5.0 =

* Mobile detection added

= 1.4.5 =

* Full size og:image
* Improved the bbpress integration

= 1.4.4 =

* Fixed a debug warning

= 1.4.3 =

* Performance improvements

= 1.4.2 =

* Added top and bottom injection controls on single posts and pages

= 1.4.1 =

* Added global variables "hefo_page_top", "hefo_page_bottom" that, if set to false, blocks the page injection
* Added global variables "hefo_post_top", "hefo_post_bottom" that, if set to false, blocks the page injection
* Added configuration to inject code on excerpts
* Added global variable $hefo_count which counts the number of process excerpts

= 1.4.0 =

* Chaged the top bar
* Fixed some CSS

= 1.3.9 =

* Added a SEO option for noindex meta tag on page 2 and up of the home page
* Added a SEO option for canonical on home page (save you from URLs with query string parameter used by plugins)
* Added a SEO option for noindex meta tag on seach result pages

= 1.3.8 =

* Removed the init configuration, too much dangerous

= 1.3.7 =

* Added the init configuration

= 1.3.5 =

* Added notes and parked codes
* Added code snippets
* $post made global for post and page header and footer

= 1.3.4 =

* Added an important note about tabs and image selection on the facebook tab, only informative
* Added a .po file, but it is no still time to translate!

= 1.3.3 =

* Fixed the not loading CSS and sone layout problems

= 1.3.2 =

* Fixed the readme file...
* Fixed some labels
* Added the screenshots (hope they'll show up this time...)

= 1.3.1 =

* Added bbPress "compatibility" for og:image Facebook meta tag
* Administration panel tabbed
* Added Facebook og:type support
* Fix the og:image on home page when there is no default image specified
* Facebook og: tags added earlier on head section that other codes

= 1.3.0 =

* added configuration to inject code before and after pages
* small graphical changes

= 1.2.0 =

* compatibility check with WordPress 3.2.1
* updated the Facebook Open Graph image tag (og:image)
* integrated with WordPress media gallery image picker and uploader
* some CSS changes
* added the Satollo.net news iframe
* added configurations to inject code before and after posts
* added a PDF manual

= 1.0.6 =

* WP 2.7.1 compatibility check

= 1.0.5 =

* added the german translation by Ev. Jugend Schwandorf - Sebastian M�ller (http://www.ej-schwandorf.de)

= 1.0.4 =

* fixed the usage of short php tag

= 1.0.3 =

* added the "only home" header text