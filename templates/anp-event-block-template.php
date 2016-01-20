<?php
/*
* Template for the output of the Network Events Block
* Override by placing a file called plugins/glocal-network-content/anp-event-block-template.php in your active theme
*/

// echo '<pre>$post_detail ';
// var_dump( $post_detail );
// echo '</pre>';

$html .= '<article id="event-' . $post_detail['post_id'] . '" data-slug="' . $post_detail['post_slug'] . '" data-id="' . $post_detail['post_id']. '" data-post-date="' . $post_detail['post_date'] . '" data-site="' . $post_detail['site_name'] . '" class="post event">';

$html .= '<header class="post-header">';

if($show_thumbnail && $post_detail['post_image']) {
	//Show image
	$html .= '<a href="' . esc_url( $post_detail['permalink'] ) . '" class="post-thumbnail">';
	$html .= '<img class="attachment-post-thumbnail wp-post-image item-image" src="' . $post_detail['post_image'] . '">';
	$html .= '</a>';
}
$html .= '<h4 class="post-title">';
$html .= '<a href="' . esc_url( $post_detail['permalink'] ) . '">';
$html .= $post_detail['post_title'];
$html .= '</a>';
$html .= '</h4>';

$html .= '<div class="meta">';
$html .= '<span class="label">' . __( 'Date ', ANP_NETWORK_CONTENT_TEXT_DOMAIN ) . '</span>';
$html .= '<time class="post-date start-date" datetime="' . $post_detail['start_date'] . '">';
$html .= date_i18n( 'l, F j, Y - ', strtotime( $post_detail['start_date'] ) );
$html .= date_i18n( get_option( 'time_format' ), strtotime( $post_detail['start_date'] ) );
$html .= '</time> - ';
$html .= '<time class="post-date end-date" datetime="' . $post_detail['end_date'] . '">';
$html .= date_i18n( get_option( 'time_format' ), strtotime( $post_detail['end_date'] ) );
$html .= '</time>';

if( $post_detail['venue_name'] ) {
$html .= '<div class="event-location">';
$html .= '<span class="label">' . __( 'Location ', ANP_NETWORK_CONTENT_TEXT_DOMAIN ) . '</span>';
$html .= '<span class="venue-name">' . $post_detail['venue_name'] . '</span>';
$html .= '<div class="venue-address">';
$html .= '<span class="venue-street">' . $post_detail['venue_address']['address'] . '</span>';
$html .= '<span class="venue-city">' . $post_detail['venue_address']['city'] . ', </span>';
$html .= '<span class="venue-state">' . $post_detail['venue_address']['state'] . '</span>';
$html .= '<span class="venue-zip">' . $post_detail['venue_address']['postcode'] . '</span>';
$html .= '</div>'; // .venue-address
$html .= '</div>'; // .event-location

}

if( $show_site_name ) {
	$html .= '<span class="blog-name"><a href="' . esc_url( $post_detail['site_link'] ) . '">';
	$html .= $post_detail['site_name'];
	$html .= '</a></span>';
}	

$html .= '</div>'; // .meta

$html .= '</header>';

if( $show_excerpt ) {
	$html .= '<div class="post-excerpt" itemprop="articleBody">';
	$html .= truncate_post_content( $post_detail['post_content'], $post_detail['permalink'], 55 );
	$html .= '</div>';
}

$html .= '<footer class="post-footer">';
$html .= '</footer>';


$html .= '</article>';

?>