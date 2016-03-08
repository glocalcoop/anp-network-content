<?php
/*
* Template for the output of the Network Posts List as Highlights module
* Override by placing a file called plugins/glocal-network-content/anp-post-highlights-template.php in your active theme
*/ 

$html .= '<aside id="highlights-module" class="widget widget__anp-network-posts highlights">';
$html .= '<h2 class="module-heading" ' . $title_image . '>';
$html .= $title;
$html .= '</h2>';
$html .= render_list_html($highlight_posts, $settings);
$html .= '</aside>';

?>