<?php
add_action('init', 'swarm_location_register');
 
function swarm_location_register() {
 
	$labels = array(
		'name' => _x('Locations', 'post type general name'),
		'singular_name' => _x('Location', 'post type singular name'),
		'add_new' => _x('Add New', 'Location'),
		'add_new_item' => __('Add New Location'),
		'edit_item' => __('Edit Location'),
		'new_item' => __('New Location'),
		'view_item' => __('View Location'),
		'search_items' => __('Search Location'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => plugins_url() . '/swarm-digital-business-directory/images/icon.png',
		'rewrite' => true,
		'has_archive' => true,
		'capability_type' => 'page',
		'hierarchical' => true, 
		'menu_position' => null,
		'supports' => array('title','editor','thumbnail')
	  ); 
 
	register_post_type( 'gm_location' , $args );
}

?>