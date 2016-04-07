# ANP Network Content

Contributors: misfist
Tags: multi-site, content
Requires at least: 4.4
Tested up to: 4.4.1
Stable tag: 1.6.1
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

1.6.1 - Fixed PHP warnings in `inc/render.php` and `inc/shortcodes.php`