<?php
//hook into the init action and call gmap_directory_categories when it fires
add_action( 'init', 'gmap_directory_categories', 0 );

//create two taxonomies, genres and writers for the post type "book"
function gmap_directory_categories() 
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name'                => _x( 'Categories', 'taxonomy general name' ),
    'singular_name'       => _x( 'Category', 'taxonomy singular name' ),
    'search_items'        => __( 'Search Categories' ),
    'all_items'           => __( 'All Categories' ),
    'parent_item'         => __( 'Parent Category' ),
    'parent_item_colon'   => __( 'Parent Category:' ),
    'edit_item'           => __( 'Edit Category' ), 
    'update_item'         => __( 'Update Category' ),
    'add_new_item'        => __( 'Add New Category' ),
    'new_item_name'       => __( 'New Category Name' ),
    'menu_name'           => __( 'Categories' )
  ); 	

  $args = array(
    'hierarchical'        => true,
    'labels'              => $labels,
    'show_ui'             => true,
    'show_admin_column'   => true,
    'query_var'           => true
  );

  register_taxonomy( 'gm_location_categories', array( 'gm_location' ), $args );

}
?>