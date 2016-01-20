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

/**
 * Instantiation method
 * http://stackoverflow.com/questions/2396415/what-does-new-self-mean-in-php
 *
 * @number_posts - the total number of posts to display (default: 10)
 * @posts_per_site - the number of posts for each site (default: no limit)
 * @include_categories - the categories of posts to include (default: all categories)
 * @exclude_sites - the site from which posts should be excluded (default: all sites (public sites, except archived, deleted and spam))
 * @output - HTML or array (default: HTML)
 * @style - normal (list), block or highlights (default: normal) - ignored if @output is 'array'
 * @ignore_styles - don't use plugin stylesheet (default: false) - ignored if @output is 'array'
 * @id - ID used in list markup (default: network-posts-RAND) - ignored if @output is 'array'
 * @class - class used in list markup (default: post-list) - ignored if @output is 'array'
 * @title - title displayed for list (default: Posts) - ignored unless @style is 'highlights'
 * @title_image - image displayed behind title (default: home-highlight.png) - ignored unless @style is 'highlights'
 * @show_thumbnail - display post thumbnail (default: False) - ignored if @output is 'array'
 * @show_meta - if meta info should be displayed (default: True) - ignored if @output is 'array'
 * @show_excerpt - if excerpt should be displayed (default: True) - ignored if @output is 'array' or if @show_meta is False
 * @excerpt_length - number of words to display for excerpt (default: 50) - ignored if @show_excerpt is False
 * @show_site_name - if site name should be displayed (default: True) - ignored if @output is 'array'
 * @param show_site_name - if site name should be displayed (default: True) - ignored if @output is 'array'
 *     
 * Editable Templates
 * ---
 * Display of Network Content can be customized by adding a custom template to your theme
 * plugins/glocal-network-content/
 * - anp-post-list-template.php
 * - anp-post-block-template.php
 * - anp-post-highlights-template.php
 * - anp-sites-list-template.php
 */

/**
 * @param @var array user-selected options
 * @return @var list of posts, as array or rendered as HTML
 */
function glocal_networkwide_posts_module( $parameters = [] ) {

    // Default parameters
    // There aren't any now, but there might be some day.
    $defaults = array(
        'limit'                 => null, // site_arg
        'archived'              => 0,
        'spam'                  => 0,
        'deleted'               => 0,
        'mature'                => null,
        'public'                => 1,
        'post_type'             => 'post',
        'number_posts'          => 10, // render_option
        'exclude_sites'         => array( 1 ),
        'include_categories'    => (array) null,
        'posts_per_site'        => null, // post_arg
        'output'                => 'html',
        'style'                 => 'normal',
        'sort_by'               => 'date',
        // 'id'                    => 'network-posts-' . rand(),
        'class'                 => 'post-list',
        'title'                 => 'Posts',
        'title_image'           => null,
        'show_thumbnail'        => False,
        'show_meta'             => True,
        'show_excerpt'          => True,
        'excerpt_length'        => 55,
        'show_site_name'        => True,
        'event_scope'           => 'future',
        'event_categories'      => (array) null,
        'event_tags'            => (array) null,
    );
    
    // CALL MERGE FUNCTION
    $settings = get_merged_settings( $parameters, $defaults );

    // Get a list of sites
    $site_args = array(
        'limit'         => ( isset( $settings['limit'] ) ) ? $settings['limit'] : null,
        'archived'      => ( isset( $settings['archived'] ) ) ? $settings['archived'] : null,
        'spam'          => ( isset( $settings['spam'] ) ) ? $settings['spam'] : null,
        'deleted'       => ( isset( $settings['deleted'] ) ) ? $settings['deleted'] : null,
        'mature'        => ( isset( $settings['mature'] ) ) ? $settings['mature'] : null,
        'public'        => ( isset( $settings['public'] ) ) ? $settings['public'] : null,
        'exclude_sites' => ( $settings['exclude_sites'] ) ? ( array ) $settings['exclude_sites'] : null,
    );

    $site_args = array_filter( $site_args, 'is_not_empty_or_null' );

    extract( $site_args, EXTR_SKIP );

    echo '<pre>$site_args ';
    var_dump( $site_args );
    echo '</pre>';

    // Get a list of sites
    $post_args = array(
        'post_type'             => ( $settings['post_type'] ) ? $settings['post_type'] : 'post',
        'posts_per_page'        => ( $settings['posts_per_site'] ) ? (int) $settings['posts_per_site'] : null,
        'include_categories'    => ( $settings['include_categories'] ) ? (array) $settings['include_categories'] : null,
        'event_scope'           => $settings['event_scope'],
        'event_categories'      => $settings['event_categories'],
        'event_tags'            => $settings['event_tags'],
    );

    $post_args = array_filter( $post_args, 'is_not_empty_or_null' );

    extract( $post_args, EXTR_SKIP );

    echo '<pre>$post_args ';
    var_dump( $post_args );
    echo '</pre>';

    // Get a list of sites
    $render_settings = array(
        'number_posts'      => ( $settings['number_posts'] ) ? (int) $settings['number_posts'] : null,
        'output'            => ( $settings['output'] ) ? $settings['output'] : 'html',
        'style'             => ( $settings['style'] ) ? $settings['style'] : 'list',
        'title'             => ( $settings['title'] ) ? $settings['title'] : null,
        'title_image'       => ( $settings['title_image'] ) ? $settings['title_image'] : null,
        'show_thumbnail'    => ( isset( $settings['show_thumbnail'] ) ) ? (bool) $settings['show_thumbnail'] : False,
        'show_meta'         => ( isset( $settings['show_meta'] ) ) ? (bool) $settings['show_meta'] : True,
        'show_excerpt'      => ( isset( $settings['show_excerpt'] ) ) ? (bool) $settings['show_excerpt'] : True,
        'excerpt_length'    => ( $settings['excerpt_length'] ) ? (int) $settings['excerpt_length'] : 20,
        'show_site_name'    => ( $settings['show_site_name'] ) ? (bool) $settings['show_site_name'] : True,
        'class'             => ( $settings['class'] ) ? $settings['class'] : null,
        'id'                => 'network-' . $post_type . '-list-' . rand(),
     );

    $render_settings = array_filter( $render_settings, 'is_not_empty_or_null' );

    extract( $render_settings, EXTR_SKIP );

    // echo '<pre>$render_settings ';
    // var_dump( $render_settings );
    // echo '</pre>';

    
    // $exclude = $exclude_sites;
    // Strip out all characters except numbers and commas. This is working!
    // $exclude = preg_replace( "/[^0-9,]/", "", $exclude );
    // $exclude = explode( ",", $exclude );

    $sites = wp_get_sites( $site_args );

    if ( 0 == count( $sites ) ) {
        trigger_error( "Cannot divide by zero", E_USER_WARNING );
        exit;
    }


    // echo '$exclude_sites' . $exclude_sites;

    // CALL EXCLUDE SITES FUNCTION
    $sites_list = ( isset( $exclude_sites ) ) ? exclude_sites( $exclude_sites, $sites ) : $sites;

    if( 'event' == $post_type ) {

        $meta_query = event_scope_meta_args( $event_scope );

        $post_args = array_merge( $post_args, $meta_query );

        if( isset( $event_categories ) ) {

            $post_args['tax_query'][] = taxonomy_query_args( 'event-category', 'slug', $event_categories );

        }

        if( isset( $event_tags ) ) {

            $post_args['tax_query'][] = taxonomy_query_args( 'event-tag', 'slug', $event_tags );

        }

    }
    
    // CALL GET POSTS FUNCTION
    $posts_list = get_posts_list( $sites_list, $post_args, $render_settings );

    if( empty( $posts_list ) ) {
        trigger_error( "There are no posts for the criteria selected.", E_USER_WARNING );
        return;
    }

    $sort_key = ( 'event' == $post_type ) ? 'start_date' : 'post_date';

    // CALL SORT FUNCTION
    $posts_list = sort_array_by_key( $posts_list, $sort_key );

    // CALL LIMIT FUNCTIONS
    $posts_list = ( isset( $posts_list ) ) ? limit_number_posts( $posts_list, $number_posts ) : $posts_list;  

    // echo '<pre>$posts_list ';
    // var_dump( $posts_list );
    // echo '</pre>';

    
    if( 'array' == $output ) {
        
        // Return an array
        return $posts_list;
        
        // For testing
        //return '<pre>' . var_dump($posts_list) . '</pre>';
            
    } else {
        // CALL RENDER FUNCTION
        
        return render_html( $posts_list, $render_settings, $post_type );
            
    }

}


/************* NETWORK SITES MAIN FUNCTION *****************/

/** Parameters
 * @return - Return (display list of sites or return array of sites) (default: display)
 * @number_sites - Number of sites to display/return (default: no limit)
 * @exclude_sites - ID of sites to exclude (default: 1 (usually, the main site))
 * @sort_by - newest, updated, active, alpha (registered, last_updated, post_count, blogname) (default: alpha)
 * @default_image - Default image to display if site doesn't have a custom header image (default: none)
 * @instance_id - ID name for site list instance (default: network-sites-RAND)
 * @class_name - CSS class name(s) (default: network-sites-list)
 * @hide_meta - Select in order to update date and latest post. Only relevant when return = 'display'. (default: false)
 * @show_image - Select in order to hide site image. (default: false)
 * @show_join - Future
 * @join_text - Future
*/

/**
 * @param @var array user-selected options
 * @return @var list of sites, as array or rendered as HTML
 */
function glocal_networkwide_sites_module( $parameters = [], $site_args = [] ) {

    /** Default parameters **/
    $defaults = array(
        'return' => 'display',
        'number_sites' => null,
        'exclude_sites' => '1', 
        'sort_by' => 'alpha',
        'default_image' => null,
        'show_meta' => False,
        'show_image' => False,
        'id' => 'network-sites-' . rand(),
        'class' => 'network-sites-list',
    );

    // Get a list of sites
    $default_site_args = array(
        'limit'     => null,
        'archived'  => 0,
        'spam'      => 0,
        'deleted'   => 0,
        'public'    => 1
    );
    
    // CALL MERGE FUNCTION
    $settings = get_merged_settings( $parameters, $defaults );

    $site_args = get_merged_settings( $site_args, $default_site_args );

    // Extract each parameter as its own variable
    extract( $settings, EXTR_SKIP );
    
    // CALL GET SITES FUNCTION
    $sites_list = get_sites_list( $settings, $site_args );
    
    // Sorting
    switch ($sort_by) {
        case 'newest':
            $sites_list = sort_array_by_key( $sites_list, 'registered', 'DESC');
        break;
        
        case 'updated':
            $sites_list = sort_array_by_key( $sites_list, 'last_updated', 'DESC');
        break;
        
        case 'active':
            $sites_list = sort_array_by_key( $sites_list, 'post_count', 'DESC');
        break;
        
        default:
            $sites_list = sort_array_by_key( $sites_list, 'blogname' );
        
    }
        
    if( 'array' == $return ) {
        
        return $sites_list;
        
    }
    else {
        
    // CALL RENDER FUNCTION
    
        return render_sites_list( $sites_list, $settings );
        
    }
    
}