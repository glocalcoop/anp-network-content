<?php

/**
 * ANP Network Content Enqueue
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */


// Inputs: uses global variable $styles
// Output: conditionally renders stylesheet using WP add_action() method
// Conditionals don't work. Loading on all pages...
add_action('wp_enqueue_scripts','load_highlight_styles', 200);
function load_highlight_styles() {    
    wp_enqueue_style( 'glocal-network-posts', plugins_url( '/stylesheets/css/style.min.css' , __FILE__ ) );
}