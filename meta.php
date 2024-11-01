<?php
function gm_wpb_initialize_cmb_meta_boxes() {
	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once(plugin_dir_path( __FILE__ ) . 'init.php');
}

add_action( 'init', 'gm_wpb_initialize_cmb_meta_boxes', 9999 );

function gm_wpb_address_information( $address_information_meta_boxes ) {
	$prefix = '_location_'; // Prefix for all fields
		
	$address_information_meta_boxes[] = array(
		'id' => 'location_address',
		'title' => 'Location Address',
		'pages' => array('gm_location'), // post type
		'context' => 'side',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => 'Address',
				'id'   => $prefix . 'address',
				'type' => 'text_small',
			),
			array(
				'name' => 'City/Suburb',
				'id'   => $prefix . 'city',
				'type' => 'text_small',
			),
			array(
				'name' => 'Province',
				'id'   => $prefix . 'state_province',
				'type' => 'text_small',
			),
			array(
				'name' => 'Postal Code',
				'id'   => $prefix . 'postal_code',
				'type' => 'text_small',			
			),
			array(
				'name' => 'Country',
				'id'   => $prefix . 'country',
				'type' => 'text_small',			
			),
			/*array(
				'name' => 'Latitude',
				'id'   => $prefix . 'latitude',
				'type' => 'text_small',			
			),
			array(
				'name' => 'Longitude',
				'id'   => $prefix . 'longitude',
				'type' => 'text_date',
			),*/
		),
	);
	
	return $address_information_meta_boxes;
}


add_filter( 'cmb_meta_boxes', 'gm_wpb_address_information' );

function gm_wpb_personal_information( $personal_information_meta_boxes ) {
	$prefix = '_location_'; // Prefix for all fields
		
	$personal_information_meta_boxes[] = array(
		'id' => 'location_details',
		'title' => 'Location Details',
		'pages' => array('gm_location'), // post type
		'context' => 'side',
		'priority' => 'high',
		'show_names' => true, // Show field names on the left
		'fields' => array(
			array(
				'name' => 'Telephone',
				'id'   => $prefix . 'telephone',
				'type' => 'text_small',
			),
			array(
				'name' => 'Fax',
				'id'   => $prefix . 'fax',
				'type' => 'text_small',
			),
			array(
				'name' => 'E-mail',
				'id'   => $prefix . 'email',
				'type' => 'text_small',
			),
			array(
				'name' => 'Custom URL',
				'id'   => $prefix . 'custom_url',
				'type' => 'text_small',
			)
			/*array(
				'name' => 'Latitude',
				'id'   => $prefix . 'latitude',
				'type' => 'text_small',			
			),
			array(
				'name' => 'Longitude',
				'id'   => $prefix . 'longitude',
				'type' => 'text_date',
			),*/
		),
	);
	
	return $personal_information_meta_boxes;
}


add_filter( 'cmb_meta_boxes', 'gm_wpb_personal_information' );

 /////////////////////////////// Categories  /////////////////////////////////////////////////
 

//include the main class file
require_once("Tax-meta-class/Tax-meta-class.php");
 
/*
* configure taxonomy custom fields
*/
$config = array(
   'id' => 'demo_meta_box',                         // meta box id, unique per meta box
   'title' => 'Demo Meta Box',                      // meta box title
   'pages' => array('gm_location_categories'),                    // taxonomy name, accept categories, post_tag and custom taxonomies
   'context' => 'normal',                           // where the meta box appear: normal (default), advanced, side; optional
   'fields' => array(),                             // list of meta fields (can be added by field arrays)
   'local_images' => false,                         // Use local or hosted images (meta box images for add/remove)
   'use_with_theme' => false                        //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
);
 
/*
* Initiate your taxonomy custom fields
*/
 
$my_meta = new Tax_Meta_Class($config);
 
 
/*
* Add fields
*/

//Image field
$my_meta->addImage('image_field_id',array('name'=> 'Category Icon', 'width' => '30', 'height'=> '30'));





	
//select field
//radio field
/*
* Don't Forget to Close up the meta box deceleration
*/
//Finish Taxonomy Extra fields Deceleration
$my_meta->Finish();
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>