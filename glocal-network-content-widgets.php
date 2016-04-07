<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'glocal_load_widgets' );

/**
 * Register our widget.
 *
 * @since 0.1
 */
function glocal_load_widgets() {
    register_widget( 'ANP_Network_Sites_Widget' );
	register_widget( 'ANP_Network_Posts_Widget' );
	register_widget( 'ANP_Network_Post_Highlights_Widget' );
    register_widget( 'ANP_Network_Events_Widget' );
}

/**
 * ANP_Network_Sites_Widget widget
 * Input: User input
 * Effect: Renders widget
 */
class ANP_Network_Sites_Widget extends WP_Widget {

    public function __construct() {

        parent::__construct(
            'anp-network-sites',
            __( 'Network Sites', 'anp-network-content' ),
            array(
                'description' => __( 'Display list of sites in your network.', 'anp-network-content' ),
                'classname'   => 'widget__anp-network-sites',
            )
        );

        add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));

    }

    /**
     * Upload the Javascripts for the media uploader
     */
    public function upload_scripts( ) {

        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('upload_media_widget', plugin_dir_url(__FILE__) . 'js/upload-media.js', array('jquery'));

        wp_enqueue_style('thickbox');
    }


    public function widget( $args, $instance ) {

        extract( $args );

        $title = apply_filters('widget_title', $instance['title']);

        // Convert array to comma-separated string
        if(is_array($instance['exclude_sites']) && (!empty($instance['exclude_sites'][0])) ) {
            $instance['exclude_sites'] = implode(',', $instance['exclude_sites'] );
        } else {
            unset( $instance['exclude_sites'] );
        }

       
        echo $before_widget;
                
        // if the title is set
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }

        // Use glocal_networkwide_sites function to display sites
        if( function_exists( 'glocal_networkwide_sites_module' ) ) {
            echo glocal_networkwide_sites_module( $instance );
        }
                
        echo $after_widget;

    }


    public function form( $instance ) {

        // Set default values
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'number_sites' => '',
            'exclude_sites' => '',
            'sort_by' => '',
            'id' => '',
            'class' => '',
            'show_meta' => true,
            'show_image' => false,
            'default_image' => '',
        ) );

        // Retrieve an existing value from the database
        $title = !empty( $instance['title'] ) ? $instance['title'] : '';
        $number_sites = !empty( $instance['number_sites'] ) ? $instance['number_sites'] : '';
        $exclude_sites = !empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : '';
        $sort_by = !empty( $instance['sort_by'] ) ? $instance['sort_by'] : '';

        $id = !empty( $instance['id'] ) ? $instance['id'] : '';
        $class = !empty( $instance['class'] ) ? $instance['class'] : '';
        $show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
        $show_image = isset( $instance['show_image'] ) ? (bool) $instance['show_image'] : false;
        $default_image = !empty( $instance['default_image'] ) ? $instance['default_image'] : '';

        // Form fields
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'title' ) . '" class="title_label">' . __( 'Title', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Widget Title', 'anp-network-content' ) . '" value="' . esc_attr( $title ) . '">';
        echo '</p>';

        // Number of Sites
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'number_sites' ) . '" class="number_sites_label">' . __( 'Number of Sites', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'number_sites' ) . '" name="' . $this->get_field_name( 'number_sites' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $number_sites ) . '">';
        echo '</p>';

        // Exclude Sites
        echo '<p>';
        echo '  <label for="exclude_sites" class="exclude_sites_label">' . __( 'Exclude Sites', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'exclude_sites' ) . '" name="' . $this->get_field_name( 'exclude_sites' ) . '[]" multiple="multiple" class="widefat">';
        echo '      <option value="" ' . selected( $exclude_sites, '', false ) . '> ' . __( 'None', 'anp-network-content' );

        $siteargs = array(
            'archived'   => 0,
            'spam'       => 0,
            'deleted'    => 0,
        );

        $sites = wp_get_sites($siteargs);

        foreach( $sites as $site ) { 
            $site_id = $site['blog_id'];
            $site_name = get_blog_details( $site_id )->blogname;
            echo '      <option id="' . $site_id . '" value="' . $site_id . '"', ( ! empty( $exclude_sites ) && in_array( $site_id,  $exclude_sites ) ) ? ' selected="selected"' : '','>' . $site_name . '</option>';
        }

        echo '  </select>';
        echo '</p>';

        // Sort by
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'sort_by' ) . '" class="sort_by_label">' . __( 'Sort By', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'sort_by' ) . '" name="' . $this->get_field_name( 'sort_by' ) . '" class="widefat">';
        echo '      <option value="blogname" ' . selected( $sort_by, 'blogname', false ) . '> ' . __( 'Alphabetical', 'anp-network-content' );
        echo '      <option value="last_updated" ' . selected( $sort_by, 'last_updated', false ) . '> ' . __( 'Recently Active', 'anp-network-content' );
        echo '      <option value="post_count" ' . selected( $sort_by, 'post_count', false ) . '> ' . __( 'Most Active', 'anp-network-content' );
        echo '      <option value="registered" ' . selected( $sort_by, 'registered', false ) . '> ' . __( 'Newest', 'anp-network-content' );
        echo '  </select>';
        echo '</p>';

        // Widget ID
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'id' ) . '" class="id_label">' . __( 'ID', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'id' ) . '" name="' . $this->get_field_name( 'id' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter ID', 'anp-network-content' ) . '" value="' . esc_attr( $id ) . '">';
        echo '</p>';

        // Widget Class
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'class' ) . '" class="class_label">' . __( 'Class', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'class' ) . '" name="' . $this->get_field_name( 'class' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Class', 'anp-network-content' ) . '" value="' . esc_attr( $class ) . '">';
        echo '</p>';

        // Default Meta
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_meta' ) . '" class="show_meta_label">' . __( 'Show Meta', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_meta' ) . '" name="' . $this->get_field_name( 'show_meta' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_meta, true, false ) . '>';        
        echo '</p>';

        // Show Image
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_image' ) . '" class="show_image_label">' . __( 'Show Site Image', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_image' ) . '" name="' . $this->get_field_name( 'show_image' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_image, true, false ) . '>';
        echo '</p>';

        // Default Image
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'default_image' ) . '" class="default_image_label">' . __( 'Default Image', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'default_image' ) . '" name="' . $this->get_field_name( 'default_image' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter path/url of default image', 'anp-network-content' ) . '" value="' . esc_url( $default_image ) . '">';
        echo '  <input class="upload_image_button button button-primary" type="button" value="Upload Image" />';
        echo '</p>';

    }

    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        $instance['title'] = !empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['number_sites'] = !empty( $new_instance['number_sites'] ) ? strip_tags( $new_instance['number_sites'] ) : '';
        $instance['exclude_sites'] = !empty( $new_instance['exclude_sites'] ) ? $new_instance['exclude_sites'] : '';
        $instance['sort_by'] = !empty( $new_instance['sort_by'] ) ? $new_instance['sort_by'] : '';
        $instance['id'] = !empty( $new_instance['id'] ) ? strip_tags( $new_instance['id'] ) : '';
        $instance['class'] = !empty( $new_instance['class'] ) ? strip_tags( $new_instance['class'] ) : '';
        $instance['show_meta'] = !empty( $new_instance['show_meta'] ) ? true : false;
        $instance['show_image'] = !empty( $new_instance['show_image'] ) ? true : false;
        $instance['default_image'] = !empty( $new_instance['default_image'] ) ? strip_tags( $new_instance['default_image'] ) : '';

        return $instance;

    }

}


/**
 * ANP_Network_Posts_Widget widget
 * Input: User input
 * Effect: Renders widget
 *
 */

class ANP_Network_Posts_Widget extends WP_Widget {

    public function __construct() {

        parent::__construct(
            'anp-network-posts',
            __( 'Network Posts', 'anp-network-content' ),
            array(
                'description' => __( 'Display list of posts from your network.', 'anp-network-content' ),
                'classname'   => 'widget__anp-network-posts',
            )
        );

    }

    public function widget( $args, $instance ) {

        extract($args);

        $title = apply_filters('widget_title', $instance['title']);

        // Convert $exclude_sites array to comma-separated string
        if(is_array($instance['exclude_sites']) && (!empty($instance['exclude_sites'][0])) ) {
            $instance['exclude_sites'] = implode(',', $instance['exclude_sites'] );
        } else {
            unset( $instance['exclude_sites'] );
        }

        // Convert $include_categories array to comma-separated string
        if( is_array($instance['include_categories']) && (!empty($instance['include_categories'][0])) ) {
            $instance['include_categories'] = implode(',', $instance['include_categories'] );
        } else {
            unset( $instance['include_categories'] );
        }


        //TODO
        // Make sure ID & Class fields contain valid characters

        echo $before_widget;

        // if the title is set
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }

        // Use glocal_networkwide_sites function to display sites
        if(function_exists('glocal_networkwide_posts_module')) {
            echo glocal_networkwide_posts_module( $instance );
        }

        echo $after_widget;

    }

    public function form( $instance ) {

        // Set default values
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'number_posts' => '',
            'exclude_sites' => '',
            'include_categories' => '',
            'style' => '',
            'posts_per_site' => '',
            'id' => '',
            'class' => '',
            'show_meta' => true,
            'show_thumbnail' => false,
            'show_excerpt' => true,
            'excerpt_length' => 20,
            'show_site_name' => true,
        ) );

        // Retrieve an existing value from the database
        $title = !empty( $instance['title'] ) ? $instance['title'] : '';
        $number_posts = !empty( $instance['number_posts'] ) ? $instance['number_posts'] : '';
        $exclude_sites = !empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : '';
        $include_categories = !empty( $instance['include_categories'] ) ? $instance['include_categories'] : '';
        $style = !empty( $instance['style'] ) ? $instance['style'] : '';
        $posts_per_site = !empty( $instance['posts_per_site'] ) ? $instance['posts_per_site'] : '';
        $id = !empty( $instance['id'] ) ? $instance['id'] : '';
        $class = !empty( $instance['class'] ) ? $instance['class'] : '';
        $show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
        $show_thumbnail = isset( $instance['show_thumbnail'] ) ? (bool) $instance['show_thumbnail'] : false;
        $show_excerpt = isset( $instance['show_excerpt'] ) ? (bool) $instance['show_excerpt'] : false;
        $excerpt_length = !empty( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '';
        $show_site_name = isset( $instance['show_site_name'] ) ? (bool) $instance['show_site_name'] : false;

        // Form fields
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'title' ) . '" class="title_label">' . __( 'Title', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Widget Title', 'anp-network-content' ) . '" value="' . esc_attr( $title ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'number_posts' ) . '" class="number_posts_label">' . __( 'Number of Posts', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'number_posts' ) . '" name="' . $this->get_field_name( 'number_posts' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $number_posts ) . '">';
        echo '</p>';

        // Exclude Sites
        echo '<p>';
        echo '  <label for="exclude_sites" class="exclude_sites_label">' . __( 'Exclude Sites', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'exclude_sites' ) . '" name="' . $this->get_field_name( 'exclude_sites' ) . '[]" multiple="multiple" class="widefat">';
        echo '      <option value="" ' . selected( $exclude_sites, '', false ) . '> ' . __( 'None', 'anp-network-content' );

        $siteargs = array(
            'archived'   => 0,
            'spam'       => 0,
            'deleted'    => 0,
        );

        $sites = wp_get_sites($siteargs);

        foreach( $sites as $site ) { 
            $site_id = $site['blog_id'];
            $site_name = get_blog_details( $site_id )->blogname;
            echo '      <option id="' . $site_id . '" value="' . $site_id . '"', ( ! empty( $exclude_sites ) && in_array( $site_id,  $exclude_sites ) ) ? ' selected="selected"' : '','>' . $site_name . '</option>';
        }

        echo '  </select>';
        echo '</p>';

        // Include Categories
        echo '<p>';
        echo '  <label for="include_categories" class="include_categories_label">' . __( 'Include Categories', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'include_categories' ) . '" name="' . $this->get_field_name( 'include_categories' ) . '[]" multiple="multiple" class="widefat">';
        echo '      <option value="" ' . selected( $include_categories, '', false ) . '> ' . __( 'None', 'anp-network-content' );

        $categories = get_categories();

        foreach( $categories as $cat ) { 
            echo '      <option id="' . $cat->slug . '" value="' . $cat->slug . '"', ( ! empty( $include_categories ) && in_array( $cat->slug,  $include_categories ) ) ? ' selected="selected"' : '','>' . $cat->name . '</option>';
        }

        echo '  </select>';
        echo '</p>';

        // Posts per Site
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'posts_per_site' ) . '" class="posts_per_site_label">' . __( 'Posts per Site', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'posts_per_site' ) . '" name="' . $this->get_field_name( 'posts_per_site' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $posts_per_site ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'style' ) . '" class="style_label">' . __( 'Display Style', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'style' ) . '" name="' . $this->get_field_name( 'style' ) . '" class="widefat">';
        echo '      <option value="" ' . selected( $style, '', false ) . '> ' . __( 'List (Default)', 'anp-network-content' );
        echo '      <option value="block" ' . selected( $style, 'block', false ) . '> ' . __( 'Block', 'anp-network-content' );
        echo '  </select>';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'id' ) . '" class="id_label">' . __( 'ID', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'id' ) . '" name="' . $this->get_field_name( 'id' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter ID', 'anp-network-content' ) . '" value="' . esc_attr( $id ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'class' ) . '" class="class_label">' . __( 'Class', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'class' ) . '" name="' . $this->get_field_name( 'class' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Class', 'anp-network-content' ) . '" value="' . esc_attr( $class ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_meta' ) . '" class="show_meta_label">' . __( 'Show Meta', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_meta' ) . '" name="' . $this->get_field_name( 'show_meta' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_meta, true, false ) . '>';        
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_thumbnail' ) . '" class="show_thumbnail_label">' . __( 'Show Thumbnail', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_thumbnail' ) . '" name="' . $this->get_field_name( 'show_thumbnail' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_thumbnail, true, false ) . '>';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_excerpt' ) . '" class="show_excerpt_label">' . __( 'Show Excerpt', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_excerpt' ) . '" name="' . $this->get_field_name( 'show_excerpt' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_excerpt, true, false ) . '>';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'excerpt_length' ) . '" class="excerpt_length_label">' . __( 'Excerpt Length', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'excerpt_length' ) . '" name="' . $this->get_field_name( 'excerpt_length' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $excerpt_length ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_site_name' ) . '" class="show_site_name_label">' . __( 'Show Site Name', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_site_name' ) . '" name="' . $this->get_field_name( 'show_site_name' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_site_name, true, false ) . '>';
        echo '</p>';

    }

    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        $instance['title'] = !empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['number_posts'] = !empty( $new_instance['number_posts'] ) ? strip_tags( $new_instance['number_posts'] ) : '';
        $instance['exclude_sites'] = !empty( $new_instance['exclude_sites'] ) ? $new_instance['exclude_sites'] : '';
        $instance['include_categories'] = !empty( $new_instance['include_categories'] ) ? $new_instance['include_categories'] : '';
        $instance['style'] = !empty( $new_instance['style'] ) ? $new_instance['style'] : '';
        $instance['posts_per_site'] = !empty( $new_instance['posts_per_site'] ) ? strip_tags( $new_instance['posts_per_site'] ) : '';
        $instance['id'] = !empty( $new_instance['id'] ) ? strip_tags( $new_instance['id'] ) : '';
        $instance['class'] = !empty( $new_instance['class'] ) ? strip_tags( $new_instance['class'] ) : '';
        $instance['show_meta'] = !empty( $new_instance['show_meta'] ) ? true : false;
        $instance['show_thumbnail'] = !empty( $new_instance['show_thumbnail'] ) ? true : false;
        $instance['show_excerpt'] = !empty( $new_instance['show_excerpt'] ) ? true : false;
        $instance['excerpt_length'] = !empty( $new_instance['excerpt_length'] ) ? strip_tags( $new_instance['excerpt_length'] ) : 20;
        $instance['show_site_name'] = !empty( $new_instance['show_site_name'] ) ? true : false;

        return $instance;

    }

}



/**
 * ANP_Network_Post_Highlights_Widget
 * Input: User input
 * Differs from Network Posts Widget by accepting $title_image and not accepting $style
 * Effect: Renders widget
 */
class ANP_Network_Post_Highlights_Widget extends WP_Widget {

    public function __construct() {

        parent::__construct(
            'anp-highlights-widget',
            __( 'Network Post Highlights', 'anp-network-content' ),
            array(
                'description' => __( 'Display a highlighted list of posts from your network.', 'anp-network-content' ),
                'classname'   => 'highlights',
            )
        );

        add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));

    }


    /**
     * Upload the Javascripts for the media uploader
     */
    public function upload_scripts( ) {

        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('upload_media_widget', plugin_dir_url(__FILE__) . 'js/upload-media.js', array('jquery'));

        wp_enqueue_style('thickbox');
    }


    public function widget( $args, $instance ) {

        extract($args);

        $instance['style'] = 'highlights';

        // Convert $exclude_sites array to comma-separated string
        if(is_array($instance['exclude_sites']) && (!empty($instance['exclude_sites'][0])) ) {
            $instance['exclude_sites'] = implode(',', $instance['exclude_sites'] );
        } else {
            unset( $instance['exclude_sites'] );
        }

        // Convert $include_categories array to comma-separated string
        if( is_array($instance['include_categories']) && (!empty($instance['include_categories'][0])) ) {
            $instance['include_categories'] = implode(',', $instance['include_categories'] );
        } else {
            unset( $instance['include_categories'] );
        }

        //TODO
        // Make sure ID & Class fields contain valid characters

        // Use glocal_networkwide_sites function to display sites
        if(function_exists('glocal_networkwide_posts_module')) {
            
            echo glocal_networkwide_posts_module( $instance );
        }

    }

    public function form( $instance ) {

        // Set default values
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'title_image' => '',
            'number_posts' => '',
            'exclude_sites' => '',
            'include_categories' => '',
            'posts_per_site' => '',
            'id' => '',
            'class' => '',
            'show_meta' => true,
            'show_thumbnail' => false,
            'show_excerpt' => true,
            'excerpt_length' => 20,
            'show_site_name' => true,
        ) );

        // Retrieve an existing value from the database
        $title = !empty( $instance['title'] ) ? $instance['title'] : '';
        $title_image = !empty( $instance['title_image'] ) ? $instance['title_image'] : '';
        $number_posts = !empty( $instance['number_posts'] ) ? $instance['number_posts'] : '';
        $exclude_sites = !empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : '';
        $include_categories = !empty( $instance['include_categories'] ) ? $instance['include_categories'] : '';
        $posts_per_site = !empty( $instance['posts_per_site'] ) ? $instance['posts_per_site'] : '';
        $id = !empty( $instance['id'] ) ? $instance['id'] : '';
        $class = !empty( $instance['class'] ) ? $instance['class'] : '';
        $show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
        $show_thumbnail = isset( $instance['show_thumbnail'] ) ? (bool) $instance['show_thumbnail'] : false;
        $show_excerpt = isset( $instance['show_excerpt'] ) ? (bool) $instance['show_excerpt'] : false;
        $excerpt_length = !empty( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '';
        $show_site_name = isset( $instance['show_site_name'] ) ? (bool) $instance['show_site_name'] : false;

        // Form fields
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'title' ) . '" class="title_label">' . __( 'Title', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Widget Title', 'anp-network-content' ) . '" value="' . esc_attr( $title ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'title_image' ) . '" class="title_image_label">' . __( 'Title Image', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'title_image' ) . '" name="' . $this->get_field_name( 'title_image' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter image path or URL', 'anp-network-content' ) . '" value="' . esc_attr( $title_image ) . '">';
        echo '  <input class="upload_image_button button button-primary" type="button" value="Upload Image" />';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'number_posts' ) . '" class="number_posts_label">' . __( 'Number of Posts', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'number_posts' ) . '" name="' . $this->get_field_name( 'number_posts' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $number_posts ) . '">';
        echo '</p>';

        // Exclude Sites
        echo '<p>';
        echo '  <label for="exclude_sites" class="exclude_sites_label">' . __( 'Exclude Sites', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'exclude_sites' ) . '" name="' . $this->get_field_name( 'exclude_sites' ) . '[]" multiple="multiple" class="widefat">';
        echo '      <option value="" ' . selected( $exclude_sites, '', false ) . '> ' . __( 'None', 'anp-network-content' );

        $siteargs = array(
            'archived'   => 0,
            'spam'       => 0,
            'deleted'    => 0,
        );

        $sites = wp_get_sites( $siteargs );

        foreach( $sites as $site ) { 
            $site_id = $site['blog_id'];
            $site_name = get_blog_details( $site_id )->blogname;
            echo '      <option id="' . $site_id . '" value="' . $site_id . '"', ( ! empty( $exclude_sites ) && in_array( $site_id,  $exclude_sites ) ) ? ' selected="selected"' : '','>' . $site_name . '</option>';
        }

        echo '  </select>';
        echo '</p>';

        // Include Categories
        echo '<p>';
        echo '  <label for="include_categories" class="include_categories_label">' . __( 'Include Categories', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'include_categories' ) . '" name="' . $this->get_field_name( 'include_categories' ) . '[]" multiple="multiple" class="widefat">';
        echo '      <option value="" ' . selected( $include_categories, '', false ) . '> ' . __( 'None', 'anp-network-content' );

        $categories = get_categories();

        foreach( $categories as $cat ) { 
            echo '      <option id="' . $cat->slug . '" value="' . $cat->slug . '"', ( ! empty( $include_categories ) && in_array( $cat->slug,  $include_categories ) ) ? ' selected="selected"' : '','>' . $cat->name . '</option>';
        }

        echo '  </select>';
        echo '</p>';

        // Posts per Site
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'posts_per_site' ) . '" class="posts_per_site_label">' . __( 'Posts per Site', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'posts_per_site' ) . '" name="' . $this->get_field_name( 'posts_per_site' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $posts_per_site ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'id' ) . '" class="id_label">' . __( 'ID', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'id' ) . '" name="' . $this->get_field_name( 'id' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter ID', 'anp-network-content' ) . '" value="' . esc_attr( $id ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'class' ) . '" class="class_label">' . __( 'Class', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'class' ) . '" name="' . $this->get_field_name( 'class' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Class', 'anp-network-content' ) . '" value="' . esc_attr( $class ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_meta' ) . '" class="show_meta_label">' . __( 'Show Meta', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_meta' ) . '" name="' . $this->get_field_name( 'show_meta' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_meta, true, false ) . '>';        
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_thumbnail' ) . '" class="show_thumbnail_label">' . __( 'Show Thumbnail', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_thumbnail' ) . '" name="' . $this->get_field_name( 'show_thumbnail' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_thumbnail, true, false ) . '>';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_excerpt' ) . '" class="show_excerpt_label">' . __( 'Show Excerpt', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_excerpt' ) . '" name="' . $this->get_field_name( 'show_excerpt' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_excerpt, true, false ) . '>';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'excerpt_length' ) . '" class="excerpt_length_label">' . __( 'Excerpt Length', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'excerpt_length' ) . '" name="' . $this->get_field_name( 'excerpt_length' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $excerpt_length ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_site_name' ) . '" class="show_site_name_label">' . __( 'Show Site Name', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_site_name' ) . '" name="' . $this->get_field_name( 'show_site_name' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_site_name, true, false ) . '>';
        echo '</p>';

    }

    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        $instance['title'] = !empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['title_image'] = !empty( $new_instance['title_image'] ) ? strip_tags( $new_instance['title_image'] ) : '';
        $instance['number_posts'] = !empty( $new_instance['number_posts'] ) ? strip_tags( $new_instance['number_posts'] ) : '';
        $instance['exclude_sites'] = !empty( $new_instance['exclude_sites'] ) ? $new_instance['exclude_sites'] : '';
        $instance['include_categories'] = !empty( $new_instance['include_categories'] ) ? $new_instance['include_categories'] : '';
        $instance['posts_per_site'] = !empty( $new_instance['posts_per_site'] ) ? strip_tags( $new_instance['posts_per_site'] ) : '';
        $instance['id'] = !empty( $new_instance['id'] ) ? strip_tags( $new_instance['id'] ) : '';
        $instance['class'] = !empty( $new_instance['class'] ) ? strip_tags( $new_instance['class'] ) : '';
        $instance['show_meta'] = !empty( $new_instance['show_meta'] ) ? true : false;
        $instance['show_thumbnail'] = !empty( $new_instance['show_thumbnail'] ) ? true : false;
        $instance['show_excerpt'] = !empty( $new_instance['show_excerpt'] ) ? true : false;
        $instance['excerpt_length'] = !empty( $new_instance['excerpt_length'] ) ? strip_tags( $new_instance['excerpt_length'] ) : 20;
        $instance['show_site_name'] = !empty( $new_instance['show_site_name'] ) ? true : false;

        return $instance;

    }

}

/**
 * ANP_Network_Events_Widget
 * Input: User input
 * Differs from Network Event Posts Widget by accepting changing post_type to event and accepting event-specific arguments
 * Effect: Renders widget
 */
class ANP_Network_Events_Widget extends WP_Widget {

    public function __construct() {

        parent::__construct(
            'anp-network-events',
            __( 'Network Events', 'anp-network-content' ),
            array(
                'description' => __( 'Display list of events from your network.', 'anp-network-content' ),
                'classname'   => 'widget__anp-network-events',
            )
        );

    }

    public function widget( $args, $instance ) {

        extract( $args );

        $title = apply_filters( 'widget_title', $instance['title'] );

        // Convert $exclude_sites array to comma-separated string
        if( isset( $instance['exclude_sites'] ) && is_array( $instance['exclude_sites'] ) ) {
            $instance['exclude_sites'] = implode( ',', $instance['exclude_sites'] );
        } else {
            unset( $instance['exclude_sites'] );
        }

        // Convert $include_event_categories array to comma-separated string
        if( isset( $instance['include_event_categories'] ) && is_array( $instance['include_event_categories'] ) ) {
            $instance['include_event_categories'] = implode( ',', $instance['include_event_categories'] );
        } else {
            unset( $instance['include_event_categories'] );
        }

        // Convert $include_event_tags array to comma-separated string
        if( isset( $instance['include_event_tags'] ) && is_array( $instance['include_event_tags'] ) ) {
            $instance['include_event_tags'] = implode( ',', $instance['include_event_tags'] );
        } else {
            unset( $instance['include_event_tags'] );
        }

        $instance['post_type'] = 'event';

        echo $before_widget;

        // if the title is set
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }

        // Use glocal_networkwide_sites function to display sites
        if( function_exists( 'glocal_networkwide_posts_module' ) ) {

            echo glocal_networkwide_posts_module( $instance );
        }

        echo $after_widget;

    }

    public function form( $instance ) {

        // Set default values
        $instance = wp_parse_args( (array) $instance, array( 
            'title' => '',
            'number_posts' => '',
            'exclude_sites' => '',
            'style' => '',
            'id' => '',
            'class' => '',
            'show_meta' => true,
            'show_site_name' => true,
            'event_scope' => (string) '',
            'include_event_categories' => '',
            'include_event_tags' => '', 
        ) );

        // Retrieve an existing value from the database
        $title = !empty( $instance['title'] ) ? $instance['title'] : '';
        // $post_type = 'event';
        $number_posts = !empty( $instance['number_posts'] ) ? $instance['number_posts'] : '';
        $exclude_sites = !empty( $instance['exclude_sites'] ) ? $instance['exclude_sites'] : '';
        $style = !empty( $instance['style'] ) ? $instance['style'] : '';
        $id = !empty( $instance['id'] ) ? $instance['id'] : '';
        $class = !empty( $instance['class'] ) ? $instance['class'] : '';
        $show_meta = isset( $instance['show_meta'] ) ? (bool) $instance['show_meta'] : false;
        $excerpt_length = !empty( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '';
        $show_site_name = isset( $instance['show_site_name'] ) ? (bool) $instance['show_site_name'] : false;
        $event_scope = isset( $instance['event_scope'] ) ? $instance['event_scope'] : '';
        $include_event_categories = !empty( $instance['include_event_categories'] ) ? $instance['include_event_categories'] : '';
        $include_event_tags = !empty( $instance['include_event_tags'] ) ? $instance['include_event_tags'] : '';

        // Form fields
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'title' ) . '" class="title_label">' . __( 'Title', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Widget Title', 'anp-network-content' ) . '" value="' . esc_attr( $title ) . '">';
        echo '</p>';

        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'number_posts' ) . '" class="number_posts_label">' . __( 'Number of Posts', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'number_posts' ) . '" name="' . $this->get_field_name( 'number_posts' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $number_posts ) . '">';
        echo '</p>';

        // Exclude Sites
        echo '<p>';
        echo '  <label for="exclude_sites" class="exclude_sites_label">' . __( 'Exclude Sites', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'exclude_sites' ) . '" name="' . $this->get_field_name( 'exclude_sites' ) . '[]" multiple="multiple" class="widefat">';
        echo '      <option value="" ' . selected( $exclude_sites, '', false ) . '> ' . __( 'None', 'anp-network-content' );

        $siteargs = array(
            'archived'   => 0,
            'spam'       => 0,
            'deleted'    => 0,
            'public'     => 1,
        );

        $sites = wp_get_sites($siteargs);

        foreach( $sites as $site ) { 
            $site_id = $site['blog_id'];
            $site_name = get_blog_details( $site_id )->blogname;
            echo '      <option id="' . $site_id . '" value="' . $site_id . '"', ( !empty( $exclude_sites ) && in_array( $site_id,  $exclude_sites) )  ? ' selected="selected"' : '','>' . $site_name . '</option>';
        }

        echo '  </select>';
        echo '</p>';

        // Include Event Categories
        echo '<p>';
        echo '  <label for="include_event_categories" class="include_event_categories_label">' . __( 'Include Categories', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'include_event_categories' ) . '" name="' . $this->get_field_name( 'include_event_categories' ) . '[]" multiple="multiple" class="widefat">';
        echo '      <option value="" ' . selected( $include_event_categories, '', false ) . '> ' . __( 'None', 'anp-network-content' );

        $categories = get_sitewide_taxonomy_terms( 'event-category' );

        foreach( $categories as $key => $value ) { 
            echo '      <option id="' . $key . '" value="' . $key . '"', ( !empty( $include_event_categories ) && in_array( $key,  $include_event_categories) ) ? ' selected="selected"' : '','>' . $value . '</option>';
        }

        echo '  </select>';
        echo '</p>';


        // Include Event Tags
        echo '<p>';
        echo '  <label for="include_event_tags" class="include_event_tags_label">' . __( 'Include Tags', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'include_event_tags' ) . '" name="' . $this->get_field_name( 'include_event_tags' ) . '[]" multiple="multiple" class="widefat">';
        echo '      <option value="" ' . selected( $include_event_tags, '', false ) . '> ' . __( 'None', 'anp-network-content' );

        $tags = get_sitewide_taxonomy_terms( 'event-tag' );

        foreach( $tags as $key => $value ) { 
            echo '      <option id="' . $key . '" value="' . $key . '"', ( !empty( $include_event_tags ) && in_array( $key,  $include_event_tags ) ) ? ' selected="selected"' : '','>' . $value . '</option>';
        }

        echo '  </select>';
        echo '</p>';

        // Event Scope
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'event_scope' ) . '" class="event_scope_label">' . __( 'Event Scope', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'event_scope' ) . '" name="' . $this->get_field_name( 'event_scope' ) . '" class="widefat">';

        $scopes = array(
            'future' => __( 'Future', 'anp-network-content' ),
            'past'   => __( 'Past', 'anp-network-content' ),
            'all'    => __( 'All', 'anp-network-content' )
        );

        foreach( $scopes as $key => $value ) {

            echo '<option value="' . $key . '" ' . selected( $event_scope, $key, false ) . '> ' . $value;

        }

        echo '  </select>';
        echo '</p>';
    

        // Style
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'style' ) . '" class="style_label">' . __( 'Display Style', 'anp-network-content' ) . '</label>';
        echo '  <select id="' . $this->get_field_id( 'style' ) . '" name="' . $this->get_field_name( 'style' ) . '" class="widefat">';

        $styles = array(
            ''      => __( 'List (Default)', 'anp-network-content' ),
            'block' => __( 'Block', 'anp-network-content' )
        );

        foreach( $styles as $key => $value ) {

            // echo '<option value="' $key . '" '  . selected( $style, '', false ) . '> ' . $value;  

            echo '<option value="' . $key . '" ' . selected( $style, $key, false ) . '> ' . $value;  

        }



        
        // echo '<option value="block" ' . selected( $style, 'block', false ) . '> ' . __( 'Block', 'anp-network-content' );
        echo '  </select>';
        echo '</p>';

        // ID
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'id' ) . '" class="id_label">' . __( 'ID', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'id' ) . '" name="' . $this->get_field_name( 'id' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter ID', 'anp-network-content' ) . '" value="' . esc_attr( $id ) . '">';
        echo '</p>';

        // Class
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'class' ) . '" class="class_label">' . __( 'Class', 'anp-network-content' ) . '</label>';
        echo '  <input type="text" id="' . $this->get_field_id( 'class' ) . '" name="' . $this->get_field_name( 'class' ) . '" class="widefat" placeholder="' . esc_attr__( 'Enter Class', 'anp-network-content' ) . '" value="' . esc_attr( $class ) . '">';
        echo '</p>';

        // Show meta
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_meta' ) . '" class="show_meta_label">' . __( 'Show Meta', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_meta' ) . '" name="' . $this->get_field_name( 'show_meta' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_meta, true, false ) . '>';        
        echo '</p>';

        // Excerpt Length
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'excerpt_length' ) . '" class="excerpt_length_label">' . __( 'Excerpt Length', 'anp-network-content' ) . '</label>';
        echo '  <input type="number" id="' . $this->get_field_id( 'excerpt_length' ) . '" name="' . $this->get_field_name( 'excerpt_length' ) . '" class="widefat" placeholder="' . esc_attr__( '0-100', 'anp-network-content' ) . '" value="' . esc_attr( $excerpt_length ) . '">';
        echo '</p>';

        // Show Site Name
        echo '<p>';
        echo '  <label for="' . $this->get_field_id( 'show_site_name' ) . '" class="show_site_name_label">' . __( 'Show Site Name', 'anp-network-content' ) . '</label>';
        echo '  <input type="checkbox" id="' . $this->get_field_id( 'show_site_name' ) . '" name="' . $this->get_field_name( 'show_site_name' ) . '" class="widefat" placeholder="' . esc_attr__( '', 'anp-network-content' ) . '" value="1" ' . checked( $show_site_name, true, false ) . '>';
        echo '</p>';

    }

    public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        $instance['title'] = !empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['number_posts'] = !empty( $new_instance['number_posts'] ) ? strip_tags( $new_instance['number_posts'] ) : '';
        $instance['exclude_sites'] = !empty( $new_instance['exclude_sites'] ) ? $new_instance['exclude_sites'] : '';
        $instance['include_event_categories'] = !empty( $new_instance['include_event_categories'] ) ? $new_instance['include_event_categories'] : '';
        $instance['include_event_tags'] = !empty( $new_instance['include_event_tags'] ) ? $new_instance['include_event_tags'] : '';
        $instance['style'] = !empty( $new_instance['style'] ) ? $new_instance['style'] : '';
        $instance['posts_per_site'] = !empty( $new_instance['posts_per_site'] ) ? strip_tags( $new_instance['posts_per_site'] ) : '';
        $instance['id'] = !empty( $new_instance['id'] ) ? strip_tags( $new_instance['id'] ) : '';
        $instance['class'] = !empty( $new_instance['class'] ) ? strip_tags( $new_instance['class'] ) : '';
        $instance['show_meta'] = !empty( $new_instance['show_meta'] ) ? true : false;
        $instance['excerpt_length'] = !empty( $new_instance['excerpt_length'] ) ? strip_tags( $new_instance['excerpt_length'] ) : 20;
        $instance['show_site_name'] = !empty( $new_instance['show_site_name'] ) ? true : false;

        return $instance;

    }

}

