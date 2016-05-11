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


/************* SORTING FUNCTIONS *****************/

// Inputs: array of posts data
// Output: array of posts data sorted by post_date
function sort_by_date($posts_array) {

    $posts_array = $posts_array;

    usort($posts_array, function ($b, $a) {
        return strcmp($a['post_date'], $b['post_date']);
    });

    return $posts_array;

}

// Inputs: array of posts data
// Output: array of posts data sorted by site
function sort_by_site($posts_array) {

    $posts_array = $posts_array;

    usort($posts_array, function ($b, $a) {
        return strcmp($a['site_id'], $b['site_id']);
    });

    return $posts_array;

}

// Input: array of sites
// Output: array of sites sorted by last_updated
function sort_sites_by_last_updated($sites_array) {
    
    $sites = $sites_array;
    
    usort($sites, function ($b, $a) {
        return strcmp($a['last_updated'], $b['last_updated']);
    });
}

// Input: array of sites
// Output: array of sites sorted by post_count
function sort_sites_by_most_active($sites_array) {
    
    $sites = $sites_array;
    
    usort($sites, function ($b, $a) {
        return strcmp($a['post_count'], $b['post_count']);
    });
}

// Input: associative array, sort key (e.g. 'post_count') and sort order (e.g. ASC or DESC)
// Output: array sorted by key
function sort_array_by_key($array, $key, $order='ASC') {
    $a = $array;
    $subkey = $key;
    $b = [];
    $c = [];
    
    foreach($a as $k => $v) {
        $b[$k] = strtolower($v[$subkey]);
    }
    if($order == 'DESC') {
        arsort($b);
    } else {
        asort($b);
    }
    
    foreach($b as $key => $val) {
        $c[] = $a[$key];
    }
    return $c;
}


/************* MISC HELPER FUNCTIONS *****************/

// Input: post excerpt text
// Output: cleaned up excerpt text
function custom_post_excerpt($post_id, $length='55', $trailer=' ...') {
    $the_post = get_post($post_id); //Gets post ID

        $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
        $excerpt_length = $length; //Sets excerpt length by word count
        $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
        $words = explode(' ', $the_excerpt, $excerpt_length + 1);

        if(count($words) > $excerpt_length) :
            array_pop($words);
            $trailer = '<a href="' . get_permalink($post_id) . '">' . $trailer . '</a>';
            array_push($words, $trailer);
            $the_excerpt = implode(' ', $words);
        endif;
    
    return $the_excerpt;
}

// Input: array of posts and max number parameter
// Output: array of posts reduced to max number
function limit_number_posts($posts_array, $max_number) {
    
    $posts = $posts_array;
    $limit = $max_number;

    if( $limit && ( count($posts) > $limit ) ) {
        array_splice($posts, $limit);
    }
    
    return $posts;
}

// Input: site path
// Output: site slug string
function get_site_slug($site_path) {
    
    $path = $site_path;
    $stripped_path = str_replace('/', '', $path); // Strip slashes from path to get slug
    
    if(!$path) { // If there is no slug (it's the main site), make slug 'main'
        $slug = 'main';
    }
    else { // Otherwise use the stripped path as slug  
        $slug = $stripped_path;
    }
    
    return $slug;
}

// Input: post_id
// Output: string of post classes (to be used in markup)
function get_post_markup_class($post_id) {
    
    $post_id = $post_id;
    
    $markup_class_array = get_post_class(array('list-item'), (int) $post_id);
    
    $post_markup_class = implode(" ", $markup_class_array);
    
    return $post_markup_class;
}

// Input: site_id
// Output: site image URL as string
function get_site_header_image($site_id) {
    //store the current blog_id being viewed
    global $blog_id;
    $current_blog_id = $blog_id;

    //switch to the main blog designated in $site_id
    switch_to_blog($site_id);

    $site_image = get_custom_header();

    //switch back to the current blog being viewed
    switch_to_blog($current_blog_id);

    return $site_image->thumbnail_url;
}

// Input: array
// Output: new array with sanitized values

function sanitize_input( $input ) {
    // Initialize the new array that will hold the sanitize values
    $new_input = array();
    // Loop through the input and sanitize each of the values
    foreach ( $input as $key => $val ) {

        //Get variable type
        $type = gettype( $val );

        if( isset( $input[ $key ] ) ) {

            // Sanitize value
            $sanitized_val = sanitize_text_field( $val );

            // Set type back to original variable type
            settype( $sanitized_val, $type );

            // Assign sanitized value
            $new_input[ $key ] = $sanitized_val;

        }
        
    }
    return $new_input;
}
