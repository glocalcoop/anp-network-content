<?php

/**
 * ANP Network Content Shortcake UI
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.6.2
 * @package   ANP_Network_Content
 */


/**
 * Shortcake
 *
 */

/**
 * Network Content Shortcode UI
 *
 * @param shortcode tag {string}
 * @param {array} of attributes
 * @return voide
 * @link https://github.com/wp-shortcake/shortcake/wiki/Registering-Shortcode-UI
 *
 */
function anp_network_posts_shortcode_ui() {
    
    if( ! function_exists( 'shortcode_ui_register_for_shortcode' ) )
        return;
        
    shortcode_ui_register_for_shortcode( 'anp_network_posts', array( 
        'label'         => 'Network Posts',
        'listItemImage' => 'dashicons-admin-post',
        'attrs'         => array(
            array(
                'label'    => 'Number of Posts',
                'attr'     => 'number_posts',
                'type'     => 'number',
            )
        )
    ) );

    // Requires that Event Organizer plugin is active
    if( function_exists( 'eventorganiser_register_script' ) ) {
        shortcode_ui_register_for_shortcode( 'anp_network_events', array( 
            'label'         => 'Network Events',
            'listItemImage' => 'dashicons-calendar-alt',
            'attrs'         => array(
                array(
                    'label'    => 'Number of Posts',
                    'attr'     => 'number_posts',
                    'type'     => 'number',
                )
            )
        ) );
    }

}

add_action( 'init', 'anp_network_posts_shortcode_ui' );


