<?php

/**
 * ANP Network Content Render
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */


/************* RENDERING FUNCTIONS *****************/

// Input: array of posts and parameters
// Output: rendered as 'normal' or 'highlight' HTML
function render_html( $posts_array, $options_array, $post_type = 'post' ) {
    
    $posts_array = $posts_array;
    $settings = $options_array;

    // Make each parameter as its own variable
    extract( $settings, EXTR_SKIP );

    switch ( $style ) {
        case 'highlights':
            //CALL RENDER HIGHLIGHTS HTML FUNCTION
            $rendered_html = render_highlights_html( $posts_array, $settings );
            break;

        case 'block':
            //CALL RENDER BLOCK HTML FUNCTION
            $rendered_html = render_block_html( $posts_array, $settings, $post_type );
            break;
        
        default:
            //CALL RENDER LIST HTML FUNCTION
            $rendered_html = render_list_html( $posts_array, $settings, $post_type );
            break;
    }
    
    return $rendered_html;
}

// Input: array of post data
// Output: HTML list of posts
function render_list_html( $posts_array, $options_array, $post_type = 'post' ) {

    $posts_array = $posts_array;
    $settings = $options_array;

    // Make each parameter as its own variable
    extract( $settings, EXTR_SKIP );
    
    // Convert strings to booleans
    $show_meta = (filter_var($show_meta, FILTER_VALIDATE_BOOLEAN));
    $show_excerpt = (filter_var($show_excerpt, FILTER_VALIDATE_BOOLEAN));
    $show_site_name = (filter_var($show_site_name, FILTER_VALIDATE_BOOLEAN));
    
    $html = '<ul class="network-' . $post_type . '-list ' . $style . '-list">';

    foreach( $posts_array as $post => $post_detail ) {

        global $post;
        
        $post_id = $post_detail['post_id'];

        if( isset( $post_detail['categories'] ) ) {
            $post_categories = implode(", ", $post_detail['categories']);
        }

        if( 'event' == $post_type ) {

            // use a template for the output so that it can easily be overridden by theme
            // check for template in active theme
            $template = locate_template( array( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'anp-event-list-template.php' ));
            
            // if none found use the default template
            $template = ( '' !== $template ) ? $template : ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-event-list-template.php';

        } else {

            // use a template for the output so that it can easily be overridden by theme
            // check for template in active theme
            $template = locate_template( array( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'anp-post-list-template.php' ));
            
            // if none found use the default template
            $template = ( '' !== $template ) ? $template : ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-post-list-template.php';

        }
        
        
        include ( $template ); 

    }

    $html .= '</ul>';
    
    return $html;

}

// Input: array of post data
// Output: HTML list of posts
function render_block_html($posts_array, $options_array, $post_type = 'post') {

    $posts_array = $posts_array;
    $settings = $options_array;
    
    // Make each parameter as its own variable
    extract( $settings, EXTR_SKIP );
        
    $html = '<div class="network-' . $post_type . '-list style-' . $style . '">';

    foreach( $posts_array as $post => $post_detail ) {

        global $post;
        
        $post_id = $post_detail['post_id'];
        $post_categories = ( isset( $post_detail['categories'] ) ) ? implode( ", ", $post_detail['categories'] ) : '';
        
        // Convert strings to booleans
        $show_meta = (filter_var($show_meta, FILTER_VALIDATE_BOOLEAN));
        $show_thumbnail = (filter_var($show_excerpt, FILTER_VALIDATE_BOOLEAN));
        $show_site_name = (filter_var($show_site_name, FILTER_VALIDATE_BOOLEAN));

        if( 'event' == $post_type ) {

            // use a template for the output so that it can easily be overridden by theme
            // check for template in active theme
            $template = locate_template( array( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'anp-event-block-template.php' ));
            
            // if none found use the default template
            $template = ( '' !== $template ) ? $template : ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-event-block-template.php';

        } else {

            // use a template for the output so that it can easily be overridden by theme
            // check for template in active theme
            $template = locate_template(array( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'glocal-network-contentanp-post-block-template.php') );
            
            // if none found use the default template
            $template = ( $template == '' ) ? ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-post-block-template.php' : '';

        }
        
        
        include ( $template ); 

    }

    $html .= '</div>';

    return $html;

}

// Input: array of post data and parameters
// Output: HTML list of posts
function render_highlights_html($posts_array, $options_array) {
    
    $highlight_posts = $posts_array;
    $settings = $options_array;
    
    // Extract each parameter as its own variable
    extract( $settings, EXTR_SKIP );
        
    $title_image = ($title_image) ? 'style="background-image:url(' . $title_image . ')"' : '';
    
    $html = '';
    
    // use a template for the output so that it can easily be overridden by theme
    // check for template in active theme
    $template = locate_template(array( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'anp-post-highlights-template.php'));

    // if none found use the default template
    $template = ( $template == '' ) ? ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-post-highlights-template.php' : '';

    include ( $template ); 

    return $html;

}

// Input: array of site data and parameters
// Output: list of sites render as HTML
function render_sites_list($sites_array, $options_array) {

    $sites = $sites_array;
    $settings = $options_array;
    
    // Extract each parameter as its own variable
    extract( $settings, EXTR_SKIP );
    
    $show_image = (filter_var($show_image, FILTER_VALIDATE_BOOLEAN));
    $show_meta = (filter_var($show_meta, FILTER_VALIDATE_BOOLEAN));
    
    if(!$show_image) { 
        $class .= ' no-site-image';
    } else {
        $class .= ' show-site-image';
    }
    
    $html = '<ul id="' . $id . '" class="sites-list ' . $class . '">';
    
    foreach($sites as $site) {
                
        $site_id = $site['blog_id'];
        
        // CALL GET SLUG FUNCTION
        $slug = get_site_slug($site['path']);
        
        // use a template for the output so that it can easily be overridden by theme
        // check for template in active theme
        $template = locate_template(array( ANP_NETWORK_CONTENT_PLUGIN_DIR . 'anp-sites-list-template.php'));

        // if none found use the default template
        $template = ( $template == '' ) ? ANP_NETWORK_CONTENT_PLUGIN_DIR . 'templates/anp-sites-list-template.php' : '';

        include ( $template ); 
        
    }
    
    $html .= '</ul>';
    
    return $html;
    
}
