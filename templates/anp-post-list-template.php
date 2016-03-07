<?php
/*
* Template for the output of the Network Posts List
* Override by placing a file called plugins/glocal-network-content/anp-post-list-template.php in your active theme
*/ 

$html .= '<li class="type-post post-item siteid-' . $post_detail['site_id'] . '">';
$html .= '<header class="entry-header">';
if($show_thumbnail && $post_detail['post_image']) {
	//Show image
	$html .= '<a href="' . esc_url( $post_detail['permalink'] ) . '" class="post-thumbnail">';
	$html .= '<img class="attachment-post-thumbnail wp-post-image item-image" src="' . $post_detail['post_image'] . '">';
	$html .= '</a>';
}
$html .= '<h4 class="entry-title">';
$html .= '<a href="' . esc_url( $post_detail['permalink'] ) . '">';
$html .= $post_detail['post_title'];
$html .= '</a>';
$html .= '</h4>';
$html .= '</header>';

if($show_meta) {
	$html .= '<div class="entry-meta">';
	if($show_site_name) {
		$html .= '<span class="site-name"><span class="meta-label">' . __( 'Posted In', 'anp-network-content' ) . '</span> <a href="' . esc_url( $post_detail['site_link'] ) . '">';
		$html .= $post_detail['site_name'];
		$html .= '</a></span>';
	}
	$html .= '<span class="post-date posted-on date"><span class="meta-label">' . __( 'Posted On', 'anp-network-content' ) . '</span> <time class="entry-date" datetime="' . $post_detail['post_date'] . '">';
	$html .= date_i18n( get_option( 'date_format' ), strtotime( $post_detail['post_date'] ) );
	$html .= '</time></span>';
	$html .= '<span class="entry-author"><span class="meta-label">' . __( 'Posted By', 'anp-network-content' ) . '</span> <a href="' . esc_url( $post_detail['site_link'] . '/author/' . $post_detail['post_author'] ) . '">';
	$html .= $post_detail['post_author'];
	$html .= '</a></span>';
	$html .= '</div>';
}
if($show_excerpt) {
	$html .= '<div class="entry-content" itemprop="articleBody">' . $post_detail['post_excerpt'] . '</div>';
}
if($show_meta) {
	$html .= '<div class="entry-meta"><span class="meta-label">' . __( 'Category', 'anp-network-content' ) . '</span>';
	$html .= ( isset( $post_categories ) ) ? '<span class="category tags">' . $post_categories . '</span>' : '';
	$html .= '</div>';
}
$html .= '</li>';

?>