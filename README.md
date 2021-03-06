# ANP Network Content

Contributors: misfist
Tags: multi-site, content
Requires at least: 4.4
Tested up to: 4.4.1
Version: 1.7.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Widgets and shortcodes that display network content on your multi-site install.

## Description

A plugin that enables you to display posts, events and a site listing from all sites within a multi-site instance.

## Useage

Display using a shortcode.

### Network Posts

`[anp_network_posts title="Module Title" title_image="/path/to/image.png" number_posts="20" exclude_sites="2,3" posts_per_site="5" style="block" show_meta=1 show_excerpt=1 show_site_name=1 id="unique-id" class="class-name"]`

### Network Events

`[anp_network_events title="Module Title" exclude_sites="2,3" posts_per_site="5" style="block" show_meta=1 show_excerpt=1 show_site_name=1 id="unique-id" class="class-name"]`

### Network Sites

`[anp_network_sites number_sites="20" exclude_sites="1,2" sort_by="registered" default_image="/path/to/image.jpg" show_meta=1 show_image=1 id="unique-id" class="class-name"]`

### Revisions

= 1.7.0 September 21, 2016 =
* Added template locate function
* Fixed template from local theme

= 1.6.12 June 28, 2016 =
* [bugfix #1466] Fixed path to JS file `upload-media.js`.

= 1.6.11 June 20, 2016 =
* Fixed event block display template, which was missing a closing `div` tag
* Change `post-excerpt` class to `entry-excerpt`

= 1.6.10 June 20, 2016 =
* Fixed rendering issue causing post-type list class not to display in ul tag

= 1.6.9 June 19, 2016 =

* Fixed template issue causing thumbnail not to appear when selected
* Added conditional to block view to hide excerpt if not selected
* Changed default view to list
* Improved comments
* Removed highlights template

= 1.6.8 June 7, 2016 =

* [Feature #1411]Added `event_scope` to Shortcake use `attrs`

= 1.6.7 June 7, 2016 =

* Modularized widgets into separate class files.
* [Bugfix #1134]Fixed event widget so it saves event scope properly.
* Removed "Highlights" style.

= 1.6.6 June 6, 2016 =

* Fixed mark-up issue causing event widget not to save Scope selections.

= 1.6.5 May 21, 2016 =

* Added `get_sites_select_array` function to return an array of site id => site name key value pairs for use in select

= 1.6.4 May 19, 2016 =

* Changed thumbnail size to 'medium'
* Removed $style from listing class
* Changed thumbnail markup classes to `.entry-image` and `.entry-image-link`
* Changed 'Last Recent Post' heading to 'Latest Post'
* Commented out Network Posts Highlight widget since it's no longer needed
* Modified event list template to be consistent with post lists

1.6.3 - [bugfix] Fixed PHP warning in `sort` function

1.6.2 - Added Shortcake UI shortcodes to replace quicktags

1.6.1 - Fixed PHP warnings in `inc/render.php` and `inc/shortcodes.php`
