<?php

/**
 * ANP Network Content Fetch Content
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */


/************* GET CONTENT FUNCTIONS *****************/

// Input: array of user inputs and array of default values
// Output: merged array of $settings
function get_merged_settings( $user_selections_array, $default_values_array ) {

    $parameters = $user_selections_array;
    $defaults = $default_values_array;


    // Parse & merge parameters with the defaults - http://codex.wordpress.org/Function_Reference/wp_parse_args
    $settings = wp_parse_args( $parameters, $defaults );

    //Strip out tags
    foreach( $settings as $parameter => $value ) {

        if( 'array' == gettype( $value ) ) {

            array_map( 'strip_tags', $value );

        } else {

           $settings[$parameter] = strip_tags( $value ); 

        }
        
    }

    return $settings;

}

// Input: parameters array
// Output: array of sites with site information
function get_sites_list( $options_array, $site_args ) {

    $settings = $options_array;
    
    // Make each parameter as its own variable
    extract( $settings, EXTR_SKIP );
    
    // Turn exclude setting into array
    $exclude = $exclude_sites;
    // Strip out all characters except numbers and commas. This is working!
    $exclude = preg_replace( "/[^0-9,]/", "", $exclude );
    // Convert string to array
    $exclude = explode( ",", $exclude );
    
    $sites = wp_get_sites( $site_args );
    
    // CALL EXCLUDE SITES FUNCTION
    $sites = exclude_sites( $exclude, $sites );
    
    $site_list = array();
    
    foreach( $sites as $site ) {
        
        $site_id = $site['blog_id'];
        $site_details = get_blog_details( $site_id );
        
        $site_list[$site_id] = array(
            'blog_id' => $site_id,  // Put site ID into array
            'blogname' => $site_details->blogname,  // Put site name into array
            'siteurl' => $site_details->siteurl,  // Put site URL into array
            'path' => $site_details->path,  // Put site path into array
            'registered' => $site_details->registered,
            'last_updated' => $site_details->last_updated,
            'post_count' => intval($site_details->post_count),
        );
        
        
        // CALL GET SITE IMAGE FUNCTION
        $site_image = get_site_header_image($site_id);
        
        if( $site_image ) {
            $site_list[$site_id]['site-image'] = $site_image;
        }
        elseif( $default_image ) {
            $site_list[$site_id]['site-image'] = $default_image;
        }
        else {
            $site_list[$site_id]['site-image'] = '';
        }
        
        $site_list[$site_id]['recent_post'] = get_most_recent_post($site_id);
    
    }
    
    return $site_list;
    
}

// Inputs: exclude array and sites array
// Output: array of sites, excluding those specfied in parameters
function exclude_sites( $exclude_array, $sites_array ) {

    $exclude = $exclude_array;
    $sites = $sites_array;

    $exclude_length = sizeof( $exclude );
    $sites_length = sizeof( $sites );

    echo $exclude_length;
    
    // If there are any sites to exclude, remove them from the array of sites
    if( $exclude_length > 0 ) {

        for( $i = 0; $i < $exclude_length; $i++ ) {

            for( $j = 0; $j < $sites_length; $j++ ) {

                if( $sites[$j]['blog_id'] == $exclude[$i] ) {
                    // Remove the site from the list
                    unset( $sites[$j] );
                }

            }
        }

        // Fix the array indexes so they're in order again
        $sites = array_values( $sites );

        return $sites;

    }

    return $sites;

}

// Inputs: array of sites and parameters array
// Output: single array of posts with site information, sorted by post_date
function get_posts_list( $sites_array, $post_args, $render_options ) {

    $sites = $sites_array;
    $post_args = $post_args;
    $render_options = $render_options;

    // Make each parameter as its own variable
    extract( $post_args, EXTR_SKIP );
    extract( $render_options, EXTR_SKIP );

    $post_list = [];

    // For each site, get the posts
    foreach( $sites as $site => $detail ) {

        $site_id = $detail['blog_id'];
        $site_details = get_blog_details( $site_id );

        // Switch to the site to get details and posts
        switch_to_blog( $site_id );
        
        // CALL GET SITE'S POST FUNCTION
        // And add to array of posts
        
        // If get_sites_posts($site_id, $settings) isn't null, add it to the array, else skip it
        // Trying to add a null value to the array using this syntax produces a fatal error. 
        if( get_sites_posts( $site_id, $post_args ) ) { 
            $post_list = $post_list + get_sites_posts( $site_id, $post_args );
        }
        
        // Unswitch the site
        restore_current_blog();

    }

    return $post_list;

}

// Input: site id and parameters array
// Ouput: array of posts for site
function get_sites_posts( $site_id, $post_args ) {
    
    $site_id = $site_id;
    $post_args = $post_args;

    // Make each parameter as its own variable
    extract( $post_args, EXTR_SKIP );
    
    $site_details = get_blog_details( $site_id );
    
    $recent_posts = wp_get_recent_posts( $post_args );
    
    $post_list = [];

    // Put all the posts in a single array
    foreach( $recent_posts as $post => $postdetail ) {

        //global $post;
        
        $post_id = $postdetail['ID'];
        $author_id = $postdetail['post_author'];
        $prefix = $postdetail['post_date'] . '-' . $postdetail['post_name'];

        //CALL POST MARKUP FUNCTION
        $post_markup_class = get_post_markup_class($post_id);
        $post_markup_class .= ' siteid-' . $site_id;

        //Returns an array
        $post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'thumbnail' );

        $post_list[$prefix] = array(
            'post_id' => $post_id,
            'post_title' => $postdetail['post_title'],
            'post_slug' => $postdetail['post_name'],
            'post_date' => $postdetail['post_date'],
            'post_author' => get_the_author_meta( 'display_name', $postdetail['post_author'] ),
            'post_content' => $postdetail['post_content'],
            'post_excerpt' => ( $postdetail['post_excerpt'] ) ? strip_shortcodes( $postdetail['post_excerpt'] ) : null,
            'permalink' => get_permalink($post_id),
            'post_image' => $post_thumbnail[0],
            'post_class' => $post_markup_class,
            'post_type' => $postdetail['post_type'],
            'site_id' => $site_id,
            'site_name' => $site_details->blogname,
            'site_link' => $site_details->siteurl,
        );

        if( 'event' == $postdetail['post_type'] && function_exists( 'eo_get_venue' ) ) {

            $venue_id = eo_get_venue( $post_id );

            $post_list[$prefix]['start_date'] = get_post_meta( $post_id, '_eventorganiser_schedule_start_start', true );
            $post_list[$prefix]['end_date'] = get_post_meta( $post_id, '_eventorganiser_schedule_start_finish', true );
            $post_list[$prefix]['venue_name'] = eo_get_venue_name( $venue_id );
            $post_list[$prefix]['venue_address'] = eo_get_venue_address( $venue_id );
            $post_list[$prefix]['venue_lat_long'] = eo_get_venue_latlng( $venue_id );
                
        }


        //Get post categories
        $post_categories = wp_get_post_categories( $post_id );

        foreach( $post_categories as $post_category ) {
            $cat = get_category($post_category);
            $post_list[$prefix]['categories'][] = $cat->name;
        }

    }
        
    return $post_list;
    
}

// Input: site_id
// Output: array post data for single post
function get_most_recent_post($site_id) {

    $site_id = $site_id;
    
    // Switch to current blog
    switch_to_blog( $site_id );

    // Get most recent post
    $recent_posts = wp_get_recent_posts( 'numberposts=1' );
    
    // Get most recent post info
    foreach( $recent_posts as $post ) {
        $post_id = $post['ID'];

        // Post into $site_list array
        $recent_post_data = array (
            'post_id' => $post_id,
            'post_author' => $post['post_author'],
            'post_slug' => $post['post_name'],
            'post_date' => $post['post_date'],
            'post_title' => $post['post_title'],
            'post_content' => $post['post_content'],
            'permalink' => get_permalink($post_id),
        );

        // If there is a featured image, add URL to array, else leave empty
        if( wp_get_attachment_url( get_post_thumbnail_id($post_id) ) ) {
            $recent_post_data['thumbnail'] = wp_get_attachment_url(get_post_thumbnail_id($post_id));
        } else {
            $recent_post_data['thumbnail'] = '';
        }
    }

    // Exit
    restore_current_blog();
    
    return $recent_post_data;

}