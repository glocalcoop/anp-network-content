<?php

/**
 * ANP Network Content Filter
 *
 * @author    Pea, Glocal
 * @license   GPL-2.0+
 * @link      http://glocal.coop
 * @since     1.0.1
 * @package   ANP_Network_Content
 */


/**
 * Sort Function
 * @param post array
 * @param @var string sort_by
 * @param @var string post_type
 * @return array sorted by appropriate key
 */

// function sort_posts( $sort_by, $post_type = null ) {

//     $post_type = ( $post_type ) ? $post_type : 'post';

//     if( 'event' == $post_type ) {

//         switch() {
//             case :
//                 break;
//             case :
//                 break;
//             default :

//         }

//     } else { // assume 'post' is $post_type 



//     }


// }


/**
 * Build event meta_query
 * @param @var event_scope - future, past, all
 * @return meta_query array
 */
function event_scope_meta_args( $event_scope = 'future' ) {

    $date_format = 'Y-m-d H:i:s';
    $date = new DateTime( date( $date_format ) );

    $meta_query['meta_query'] = [];

    switch ( $event_scope ) {
        case 'past':
            $starting_date = new DateTime( date( $date_format ) );
            // Change the interval to change how far back to display events
            $interval = apply_filters( 'anp_network_events_all_interval', 'P1Y' );
            $starting_date->sub( new DateInterval( $interval ) );
            $meta_query['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key'     => '_eventorganiser_schedule_start_start',
                    'value'   => $starting_date->format( $date_format ), // after what
                    'compare' => '>'
                ),
                array(
                    'key'     => '_eventorganiser_schedule_start_finish',
                    'value'   => $date->format( $date_format ), // before today
                    'compare' => '<'
                ),
            );
            break;
        case 'all':
            // Change the interval to change how far back to display events
            $interval = apply_filters( 'anp_network_events_all_interval', 'P1Y' );
            $date->sub( new DateInterval( $interval ) );
            $meta_query['meta_query'] = array(
                array(
                    'key'     => '_eventorganiser_schedule_start_start',
                    'value'   => $date->format( $date_format ),
                    'compare' => '>'
                )
            );
            break;
        default :
            $meta_query['meta_query'] = array(
                array(
                    'key'     => '_eventorganiser_schedule_start_start',
                    'value'   => $date->format( $date_format ),
                    'compare' => '>='
                )
            );
    }

    return $meta_query;

}


/**
 * Build tax_query
 * @param @var string taxonomy name
 * @param @var string field type (slug, ID)
 * @param @var array taxonomy terms
 * @return tax_query array
 */
function taxonomy_query_args( $taxonomy, $field = 'slug', $terms = [] ) {

    if( !empty( $terms ) ) {

        $tax_query['tax_query'] = [];

        $tax_query = array(
            'taxonomy'  => $taxonomy,
            'field'     => $field,
            'terms'     => $terms,
        );

        return $tax_query;

     }

     return;

}



/**
 * Limit Function
 * @param array, sort key (e.g. 'post_count')
 * @return array sorted by key
 */

/**
 * Build tax_query
 * @param @var string taxonomy name
 * @param @var string field type (slug, ID)
 * @param @var array taxonomy terms
 * @return tax_query array
 */



