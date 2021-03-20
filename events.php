<?php
/**
 * Plugin Name: Events Plugin
 * Description: This plugin will add a Custom Post Type for Events
 * Plugin URI: https://vicodemedia.com
 * Author: Victor Rusu
 * Version: 1
**/

//* Don't access this file directly
defined( 'ABSPATH' ) or die();

/*------------------------------------*\
	Create Custom Post Types
\*------------------------------------*/
add_action('init', 'event_post_type');
function event_post_type() {
    register_post_type('event', array(
        'labels' => array(
            'name' => __('Events', 'vicodemedia'),
            'singular_name' => __('Event', 'vicodemedia'),
            'add_new' => __('Add New', 'vicodemedia'),
            'add_new_item' => __('Add New Event', 'vicodemedia'),
            'edit_item' => __('Edit Event', 'vicodemedia'),
            'new_item' => __('New Event', 'vicodemedia'),
            'view_item' => __('View Event', 'vicodemedia'),
            'view_items' => __('View Events', 'vicodemedia'),
            'search_items' => __('Search Events', 'vicodemedia'),
            'not_found' => __('No events found.', 'vicodemedia'),
            'not_found_in_trash' => __('No events found in trash.', 'vicodemedia'),
            'all_items' => __('All Events', 'vicodemedia'),
            'archives' => __('Event Archives', 'vicodemedia'),
            'insert_into_item' => __('Insert into Event', 'vicodemedia'),
            'uploaded_to_this_item' => __('Uploaded to this Event', 'vicodemedia'),
            'filter_items_list' => __('Filter Events list', 'vicodemedia'),
            'items_list_navigation' => __('Events list navigation', 'vicodemedia'),
            'items_list' => __('Events list', 'vicodemedia'),
            'item_published' => __('Event published.', 'vicodemedia'),
            'item_published_privately' => __('Event published privately.', 'vicodemedia'),
            'item_reverted_to_draft' => __('Event reverted to draft.', 'vicodemedia'),
            'item_scheduled' => __('Event scheduled.', 'vicodemedia'),
            'item_updated' => __('Event updated.', 'vicodemedia')
        ),
        'has_archive'   => true,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'revisions'),
        'can_export' => true
    ));
}


// add event date field to events post type
function add_post_meta_boxes() {
    add_meta_box(
        "post_metadata_events_post", // div id containing rendered fields
        "Event Date", // section heading displayed as text
        "post_meta_box_events_post", // callback function to render fields
        "event", // name of post type on which to render fields
        "side", // location on the screen
        "low" // placement priority
    );
}
add_action( "admin_init", "add_post_meta_boxes" );

// save field value
function save_post_meta_boxes(){
    global $post;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // if ( get_post_status( $post->ID ) === 'auto-draft' ) {
    //     return;
    // }
    update_post_meta( $post->ID, "_event_date", sanitize_text_field( $_POST[ "_event_date" ] ) );
}
add_action( 'save_post', 'save_post_meta_boxes' );

// callback function to render fields
function post_meta_box_events_post(){
    global $post;
    $custom = get_post_custom( $post->ID );
    $advertisingCategory = $custom[ "_event_date" ][ 0 ];
    echo "<input type=\"date\" name=\"_event_date\" value=\"".$advertisingCategory."\" placeholder=\"Event Date\">";
}


// generate shortcode
add_shortcode('events-list', 'vm_events');
function vm_events(){
    global $post;
    $args = array(
        'post_type'=>'event', 
        'post_status'=>'publish', 
        'posts_per_page'=>50, 
        'orderby'=>'meta_value',
        'meta_key' => '_event_date',
        'order'=>'ASC'
    );
    $query = new WP_Query($args);

    $content = '<ul>';
    if($query->have_posts()):
		while($query->have_posts()): $query->the_post();
            // display event
            $content .= '<li><a href="'.get_the_permalink().'">'. get_the_title() .'</a> - '.date_format(date_create(get_post_meta($post->ID, '_event_date', true)), 'jS F').'</li>'; 
        endwhile;
    else: 
        _e('Sorry, nothing to display.', 'vicodemedia');
    endif;
    $content .= '</ul>';

    return $content;
}