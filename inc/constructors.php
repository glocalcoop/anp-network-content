<?php

/**
 * ANP Network Content Constructors
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */


/************* NETWORK POSTS MAIN FUNCTION *****************/

/************* Parameters *****************
    @number_posts - the total number of posts to display (default: 10)
    @posts_per_site - the number of posts for each site (default: no limit)
    @include_categories - the categories of posts to include (default: all categories)
    @exclude_sites - the site from which posts should be excluded (default: all sites (public sites, except archived, deleted and spam))
    @output - HTML or array (default: HTML)
    @style - normal (list), block or highlights (default: normal) - ignored if @output is 'array'
    @ignore_styles - don't use plugin stylesheet (default: false) - ignored if @output is 'array'
    @id - ID used in list markup (default: network-posts-RAND) - ignored if @output is 'array'
    @class - class used in list markup (default: post-list) - ignored if @output is 'array'
    @title - title displayed for list (default: Posts) - ignored unless @style is 'highlights'
    @title_image - image displayed behind title (default: home-highlight.png) - ignored unless @style is 'highlights'
    @show_thumbnail - display post thumbnail (default: False) - ignored if @output is 'array'
    @show_meta - if meta info should be displayed (default: True) - ignored if @output is 'array'
    @show_excerpt - if excerpt should be displayed (default: True) - ignored if @output is 'array' or if @show_meta is False
    @excerpt_length - number of words to display for excerpt (default: 50) - ignored if @show_excerpt is False
    @show_site_name - if site name should be displayed (default: True) - ignored if @output is 'array'
    
    Editable Templates
    ---
    Display of Network Content can be customized by adding a custom template to your theme
    plugins/glocal-network-content/
        anp-post-list-template.php
        anp-post-block-template.php
        anp-post-highlights-template.php
        anp-sites-list-template.php
*/


// Input: user-selected options array
// Output: list of posts from all sites, rendered as HTML or returned as array
function glocal_networkwide_posts_module( $parameters = [], $site_args = [] ) {

    // Default parameters
    // There aren't any now, but there might be some day.
    $defaults = array(
        'post_type'             => 'post',
        'number_posts'          => 10, //
        'exclude_sites'         => null, 
        'include_categories'    => null,
        'posts_per_site'        => null,
        'output'                => 'html',
        'style'                 => 'normal',
        // 'id'                    => 'network-posts-' . rand(),
        'class'                 => 'post-list',
        'title'                 => 'Posts',
        'title_image'           => null,
        'show_thumbnail'        => False,
        'show_meta'             => True,
        'show_excerpt'          => True,
        'excerpt_length'        => 55,
        'show_site_name'        => True,
    );

    // Get a list of sites
    $default_site_args = array(
        'archived'   => 0,
        'spam'       => 0,
        'deleted'    => 0,
        'public'     => 1
    );
    
    // CALL MERGE FUNCTION
    $settings = get_merged_settings( $parameters, $defaults );
    $site_args = get_merged_settings( $site_args, $default_site_args );

    // Extract each parameter as its own variable
    extract( $settings, EXTR_SKIP );
    
    $exclude = $exclude_sites;
    // Strip out all characters except numbers and commas. This is working!
    $exclude = preg_replace( "/[^0-9,]/", "", $exclude );
    $exclude = explode( ",", $exclude );
       
    $sites = wp_get_sites( $site_args );

    // CALL EXCLUDE SITES FUNCTION
    $sites_list = exclude_sites( $exclude, $sites );
    
    // CALL GET POSTS FUNCTION
    $posts_list = get_posts_list( $sites_list, $settings );  
    
    if($output == 'array') {
        
        // Return an array
        return $posts_list;
        
        // For testing
        //return '<pre>' . var_dump($posts_list) . '</pre>';
            
    } else {
        // CALL RENDER FUNCTION
        
        return render_html($posts_list, $settings);
            
    }

}


/************* NETWORK SITES MAIN FUNCTION *****************/

/************* Parameters *****************
    @return - Return (display list of sites or return array of sites) (default: display)
    @number_sites - Number of sites to display/return (default: no limit)
    @exclude_sites - ID of sites to exclude (default: 1 (usually, the main site))
    @sort_by - newest, updated, active, alpha (registered, last_updated, post_count, blogname) (default: alpha)
    @default_image - Default image to display if site doesn't have a custom header image (default: none)
    @instance_id - ID name for site list instance (default: network-sites-RAND)
    @class_name - CSS class name(s) (default: network-sites-list)
    @hide_meta - Select in order to update date and latest post. Only relevant when return = 'display'. (default: false)
    @show_image - Select in order to hide site image. (default: false)
    @show_join - Future
    @join_text - Future
*/

// Input: user-selected options array
// Output: list of sites, rendered as HTML or returned as array
function glocal_networkwide_sites_module($parameters = []) {

    /** Default parameters **/
    $defaults = array(
        'return' => 'display',
        'number_sites' => 0,
        'exclude_sites' => '1', 
        'sort_by' => 'alpha',
        'default_image' => null,
        'show_meta' => False,
        'show_image' => False,
        'id' => 'network-sites-' . rand(),
        'class' => 'network-sites-list',
    );
    
    // CALL MERGE FUNCTION
    $settings = get_merged_settings($parameters, $defaults);

    // Extract each parameter as its own variable
    extract( $settings, EXTR_SKIP );
    
    // CALL GET SITES FUNCTION
    $sites_list = get_sites_list($settings);
    
    // Sorting
    switch ($sort_by) {
        case 'newest':
            $sites_list = sort_array_by_key($sites_list, 'registered', 'DESC');
        break;
        
        case 'updated':
            $sites_list = sort_array_by_key($sites_list, 'last_updated', 'DESC');
        break;
        
        case 'active':
            $sites_list = sort_array_by_key($sites_list, 'post_count', 'DESC');
        break;
        
        default:
            $sites_list = sort_array_by_key($sites_list, 'blogname');
        
    }
        
    if($return == 'array') {
        
        return $sites_list;
        
    }
    else {
        
    // CALL RENDER FUNCTION
    
        return render_sites_list($sites_list, $settings);
        
    }
    
}