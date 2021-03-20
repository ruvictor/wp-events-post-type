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
            'name' => __('Events', 'saltcave'),
            'singular_name' => __('Event', 'saltcave'),
            'add_new' => __('Add New', 'saltcave'),
            'add_new_item' => __('Add New Event', 'saltcave'),
            'edit_item' => __('Edit Event', 'saltcave'),
            'new_item' => __('New Event', 'saltcave'),
            'view_item' => __('View Event', 'saltcave'),
            'view_items' => __('View Events', 'saltcave'),
            'search_items' => __('Search Events', 'saltcave'),
            'not_found' => __('No events found.', 'saltcave'),
            'not_found_in_trash' => __('No events found in trash.', 'saltcave'),
            'all_items' => __('All Events', 'saltcave'),
            'archives' => __('Event Archives', 'saltcave'),
            'insert_into_item' => __('Insert into service', 'saltcave'),
            'uploaded_to_this_item' => __('Uploaded to this service', 'saltcave'),
            'filter_items_list' => __('Filter services list', 'saltcave'),
            'items_list_navigation' => __('Services list navigation', 'saltcave'),
            'items_list' => __('Services list', 'saltcave'),
            'item_published' => __('Service published.', 'saltcave'),
            'item_published_privately' => __('Service published privately.', 'saltcave'),
            'item_reverted_to_draft' => __('Service reverted to draft.', 'saltcave'),
            'item_scheduled' => __('Service scheduled.', 'saltcave'),
            'item_updated' => __('Service updated.', 'saltcave')
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
    if ( get_post_status( $post->ID ) === 'auto-draft' ) {
        return;
    }
    update_post_meta( $post->ID, "_event_date", sanitize_text_field( $_POST[ "_event_date" ] ) );
}
add_action( 'save_post', 'save_post_meta_boxes' );

// callback function to render fields
function post_meta_box_events_post(){
    global $post;
    $custom = get_post_custom( $post->ID );
    $advertisingCategory = $custom[ "_event_date" ][ 0 ];
    echo "<input class=\"hasDatepicker\" type=\"date\" name=\"_event_date\" value=\"".$advertisingCategory."\" placeholder=\"Event Date\">";
}