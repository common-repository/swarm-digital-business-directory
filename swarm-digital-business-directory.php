<?php
/*
Plugin Name: Swarm Digital Business Directory
Plugin URI: http://www.swarmdigital.co.za
Description: Create your own business directory with Google Maps
Version: V1.3
Author: Werner Louw @ Swarm Digital
License: GPL2
*/
?>
<?php

// Meta Boxes
require_once(plugin_dir_path( __FILE__ ) . 'meta.php');

// post types

require_once(plugin_dir_path( __FILE__ ) . 'post_types.php');

// Categories

require_once(plugin_dir_path( __FILE__ ) . 'categories.php');

// Shortcodes

require_once(plugin_dir_path( __FILE__ ) . 'shortcodes.php');


// register jquery and style on initialization/*//////////////////////////////////////////////////////

add_action('init', 'gm_register_script');

function gm_register_script() {
    wp_register_style( 'new_style', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'gm_enqueue_style');

function gm_enqueue_style(){
	wp_enqueue_style( 'new_style' );
}




// Add Css to Admin Area

add_action( 'admin_head', 'gm_admin_css' );
function gm_admin_css()
{
echo '<link rel="stylesheet" type="text/css" href="'.plugins_url().'/swarm-digital-business-directory/css/admin-style.css">';
}


//Template fallback
add_action("template_redirect", 'my_theme_redirect');

function my_theme_redirect() {
    global $wp;
    $plugindir = dirname( __FILE__ );

 //    echo "<pre>";
	// 	print_r($wp);
	// echo "</pre>";

    
    if (is_post_type_archive( 'gm_location' )) {
        $templatefilename = 'archive-gm_location.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        do_theme_redirect($return_template);
    }
    //A Specific Custom Post Type
    if (is_singular( 'gm_location' )) {

        $templatefilename = 'single-gm_location.php';
		if (file_exists(STYLESHEETPATH . '/' . $templatefilename)) {
            $return_template = STYLESHEETPATH . '/' . $templatefilename;
        } elseif (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        do_theme_redirect($return_template);

	}

}

function do_theme_redirect($url) {
    global $post, $wp_query;
    if (have_posts()) {
        include($url);
        die();
    } else {
        $wp_query->is_404 = true;
    }
}



// Add a menu for our option page
 add_action('admin_menu', 'gmap_plugin_add_page'); /* -------------- Add settings page ------------- */
function gmap_plugin_add_page() {
	add_options_page( 'Locations Options', 'Locations Options', 'manage_options', 'gmap_plugin', 'gmap_plugin_option_page' );
}

// Draw the option page
function gmap_plugin_option_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Locations Options</h2>
		<form action="options.php" method="post">
			<?php settings_fields('gmap_plugin_options'); ?>
			<?php do_settings_sections('gmap_plugin'); ?>
			<input name="Submit" type="submit" value="Save Changes" />
		</form>
	</div>
	<?php
}

// Register and define the settings
/* ----------------- Setting page fields --------------- */

/* ----------------- API Key --------------- */

add_action('admin_init', 'gmap_plugin_admin_init_api_key'); 
function gmap_plugin_admin_init_api_key(){
	register_setting(
		'gmap_plugin_options',
		'gmap_plugin_options_api_key',
		'gmap_plugin_validate_options'
	);
	add_settings_section(
		'gmap_plugin_main',
		'Google Maps v3 API Key',
		'gmap_plugin_section_text_api_key',
		'gmap_plugin'
	);
	add_settings_field(
		'gmap_plugin_text_string',
		'Key',
		'gmap_plugin_setting_input_api_key',
		'gmap_plugin',
		'gmap_plugin_main'
	);

}

// Draw the section header
function gmap_plugin_section_text_api_key() {
	echo '<p>To learn how to obtain an API Key, <a href="http://www.w3schools.com/googleAPI/" target="_blank">click here.</a></p>';
}

// Display and fill the form field
function gmap_plugin_setting_input_api_key() {
	// get option 'api_key' value from the database
	$options = get_option( 'gmap_plugin_options_api_key' );

	// echo "<pre>";
	// print_r($options);
	// echo "</pre>";


	$api_key = $options['api_key'];
	// echo the field
	echo "<input id='api_key' name='gmap_plugin_options_api_key[api_key]' type='text' value='$api_key' />";

}



// Validate user input (we want text only)
function gmap_plugin_validate_options( $input ) {
	$valid['api_key'] = preg_replace( '/[^a-zA-Z0-9]/', '', $input['api_key'] );
	
	if( $valid['api_key'] != $input['api_key'] ) {
		add_settings_error(
			'gmap_plugin_text_string',
			'gmap_plugin_texterror',
			'Incorrect value entered!',
			'error'
		);		
	}
	
	return $valid;
}

/* ----------------- Lng Map --------------- */

add_action('admin_init', 'gmap_plugin_admin_init_map_lng'); 
function gmap_plugin_admin_init_map_lng(){
	register_setting(
		'gmap_plugin_options',
		'gmap_plugin_options_map_lng'
	);
	add_settings_section(
		'gmap_plugin_main_map_lng',
		'',
		'gmap_plugin_section_text_map_lng',
		'gmap_plugin'
	);
	add_settings_field(
		'gmap_plugin_text_string_map_lng',
		'',
		'gmap_plugin_setting_input_map_lng',
		'gmap_plugin',
		'gmap_plugin_main_map_lng'
	);

}

// Draw the section header
function gmap_plugin_section_text_map_lng() {
	//echo '<p>Select zoom level.</p>';
}

// Display and fill the form field
function gmap_plugin_setting_input_map_lng() {
	// get option 'map_lng' value from the database
	$optionsmap_lng = get_option( 'gmap_plugin_options_map_lng' );

	$map_lng = $optionsmap_lng['map_lng'];

	echo "<input id='longitude' name='gmap_plugin_options_map_lng[map_lng]' placeholder='longitude' type='hidden' value='$map_lng' />";

}

/* ----------------- Zoom level --------------- */

add_action('admin_init', 'gmap_plugin_admin_init_zoom_level'); 
function gmap_plugin_admin_init_zoom_level(){
	register_setting(
		'gmap_plugin_options',
		'gmap_plugin_options_zoom_level',
		'gmap_plugin_validate_options_zoom_level'
	);
	add_settings_section(
		'gmap_plugin_main_zoom_level',
		'',
		'gmap_plugin_section_text_zoom_level',
		'gmap_plugin'
	);
	add_settings_field(
		'gmap_plugin_text_string_zoom_level',
		'Zoom Level',
		'gmap_plugin_setting_input_zoom_level',
		'gmap_plugin',
		'gmap_plugin_main_zoom_level'
	);

}

// Draw the section header
function gmap_plugin_section_text_zoom_level() {
	//echo '<p>Select zoom level.</p>';
}

// Display and fill the form field
function gmap_plugin_setting_input_zoom_level() {
	// get option 'zoom_level' value from the database
	$options = get_option( 'gmap_plugin_options_zoom_level' );

	$zoom_level = $options['zoom_level'];

	//echo "<input id='zoom_level' name='gmap_plugin_options_zoom_level[zoom_level]' type='hidden' value='$zoom_level' />";

	echo "<select id='zoom_level' name='gmap_plugin_options_zoom_level[zoom_level]' value='$zoom_level'>";

	for ($i=0; $i < 18; $i++) {
		if($i == $zoom_level){
			echo "<option value='".$i."' selected>".$i."</option>";
		}else{
			echo "<option value='".$i."'>".$i."</option>";
		} 
	}
	echo "</select>";

}

// Validate user input (we want text only)
function gmap_plugin_validate_options_zoom_level( $input ) {
	$valid['zoom_level'] = preg_replace( '/[^a-zA-Z0-9]/', '', $input['zoom_level'] );
	
	if( $valid['zoom_level'] != $input['zoom_level'] ) {
		add_settings_error(
			'gmap_plugin_text_string_zoom_level',
			'gmap_plugin_texterror',
			'Incorrect value entered!',
			'error'
		);		
	}
	
	return $valid;
}



/* ----------------- Center Map Location --------------- */

add_action('admin_init', 'gmap_plugin_admin_init_center_map'); 
function gmap_plugin_admin_init_center_map(){
	register_setting(
		'gmap_plugin_options',
		'gmap_plugin_options_center_map'
	);
	add_settings_section(
		'gmap_plugin_main_center_map',
		'',
		'gmap_plugin_section_text_center_map',
		'gmap_plugin'
	);
	add_settings_field(
		'gmap_plugin_text_string_center_map',
		'Center Map Location',
		'gmap_plugin_setting_input_center_map',
		'gmap_plugin',
		'gmap_plugin_main_zoom_level'
	);

}

// Draw the section header
function gmap_plugin_section_text_center_map() {
	//echo '<p>Select zoom level.</p>';
}

// Display and fill the form field
function gmap_plugin_setting_input_center_map() {
	// get option 'center_map' value from the database
	$options_center_map = get_option( 'gmap_plugin_options_center_map' );
	$map_lat = $options_center_map['center_map'];

	$optionsmap_lng = get_option( 'gmap_plugin_options_map_lng' );

	$map_lng = $optionsmap_lng['map_lng'];

	echo "<input id='center_map' name='gmap_plugin_options_center_map[center_map]' type='hidden' value='$map_lat' />";

	$options_zoom_level = get_option( 'gmap_plugin_options_zoom_level' );

	$zoom_level = $options_zoom_level['zoom_level'];

	?>

  <div id="map" style="width:500px; height:500px"></div>
  
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
  <script>
  	
  	var zoom_level = <?php echo $zoom_level; ?>;

  	var map_lat = '<?php echo $map_lat; ?>';
  	var map_lng = '<?php echo $map_lng; ?>';

	function initialize() {
		
		var $latitude = document.getElementById('center_map');

		var $zoom_level_input = document.getElementById('zoom_level');

		var $longitude = document.getElementById('longitude');

		if(map_lat != "" || map_lng != ""){
			var latitude = map_lat;
			var longitude = map_lng;
		}else{
			var latitude = -30.915538;
			var longitude = 23.656059;
		}

		if(zoom_level != ""){
			var zoom = zoom_level;
		}else{
			var zoom = 7;
		}

		var LatLng = new google.maps.LatLng(latitude, longitude);
		
		var mapOptions = {
			zoom: zoom,
			center: LatLng,
			panControl: false,
			zoomControl: false,
			scaleControl: true,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			disableDefaultUI: true
		}	
		
		var map = new google.maps.Map(document.getElementById('map'),mapOptions);
      
		
		var marker = new google.maps.Marker({
			position: LatLng,
			map: map,
			title: 'Drag Me!',
			draggable: true
		});
		
		google.maps.event.addListener(marker, 'dragend', function(marker){
			var latLng = marker.latLng;
			
			$latitude.value = latLng.lat();
			$longitude.value = latLng.lng();
		});
		
		google.maps.event.addListener(map, 'zoom_changed', function() {
			var zoomLevel = map.getZoom();
			//map.setCenter(myLatLng);
			$zoom_level_input.value = zoomLevel;
		});

		
	}
	initialize();
	</script>
	<?

}


/*  ------ GET LAT AND LONG ------ */


function get_latlong_cords_callback( $post_id ) {

    /*
     * In production code, $slug should be set only once in the plugin,
     * preferably as a class property, rather than in each function that needs it.
     */
    $postType = 'gm_location';

    // If this isn't a 'gm_location' post, don't update it.
    if ( get_post_type( $post_id ) == $postType) {
        
   

	    // - Update the post's metadata.

	    if ( isset( $_REQUEST['_location_address'] ) ) {
	        update_post_meta( $post_id, '_location_address', sanitize_text_field( $_REQUEST['_location_address'] ) );

	    }

	    if ( isset( $_REQUEST['_location_city'] ) ) {
	        update_post_meta( $post_id, '_location_city', sanitize_text_field( $_REQUEST['_location_city'] ) );
	    }

	    if ( isset( $_REQUEST['_location_state_province'] ) ) {
	        update_post_meta( $post_id, '_location_state_province', sanitize_text_field( $_REQUEST['_location_state_province'] ) );
	    }

	    if ( isset( $_REQUEST['_location_postal_code'] ) ) {
	        update_post_meta( $post_id, '_location_postal_code', sanitize_text_field( $_REQUEST['_location_postal_code'] ) );
	    }

		$location_address        = get_post_meta( $post_id, '_location_address', true );
		$location_city           = get_post_meta( $post_id, '_location_city', true );
		$location_state_province = get_post_meta( $post_id, '_location_state_province', true );
		$location_country    	 = get_post_meta( $post_id, '_location_country', true );
		$location_postal_code    = get_post_meta( $post_id, '_location_postal_code', true );

		if(empty($location_country)){$location_country = "South Africa";}

		/* -------------------- Google Map API : Generate Latitude and Longitude values from property address -------------------- */
			    
			$get_location_for_map = "{$location_address}, {$location_city}, {$location_state_province} {$location_postal_code}, {$location_country}";
			    
			// define the address and set sensor to false
			function getLatLng($opts) {
			    /* grab the XML */
			    $url = 'http://maps.googleapis.com/maps/api/geocode/xml?' 
			        . 'address=' . $opts['address'] . '&sensor=' . $opts['sensor'];
			    
			    $dom = new DomDocument();
			    $dom->load($url);
			    
			    /* A response containing the result */
			    $response = array();
			    
			    $xpath = new DomXPath($dom);
			    $statusCode = $xpath->query("//status");

			    /* ensure a valid StatusCode was returned before comparing */
			    if ($statusCode != false && $statusCode->length > 0 
			        && $statusCode->item(0)->nodeValue == "OK") {
			    
			        $latDom = $xpath->query("//location/lat");
			        $lonDom = $xpath->query("//location/lng");
			        $addressDom = $xpath->query("//formatted_address");
			        
			        /* if there's a lat, then there must be lng :) */
			        if ($latDom->length > 0) {
			            
			            $response = array (
			                'status'    => true,
			                'message'   => 'Success',
			                'lat'       => $latDom->item(0)->nodeValue,
			                'lon'       => $lonDom->item(0)->nodeValue,
			                'address'   => $addressDom->item(0)->nodeValue
			            );

			            return $response;
			        }
			    }

			    $response = array (
			        'status' => false,
			        'message' => "Oh snap! Error in Geocoding. Please check Address"
			    );
			    return $response;
			}
			$opt = array (
			    'address' => urlencode($get_location_for_map),
			    'sensor'  => 'false'
			);
			    
			// now simply call the function
			$result = getLatLng($opt);
			    
			// if status was successful, then print the lat/lon ?
			if ($result['status']) {
			    $map_lat = $result['lat'];
			    $map_lon = $result['lon'];      
			}


			save_lat_long($post_id, $map_lat, $map_lon);

			/* -------------------- END : Google Map API : Generate Latitude and Longitude values from property address  */
	}
}
add_action( 'save_post', 'get_latlong_cords_callback' );


function save_lat_long($post_id, $map_lat, $map_lon){
	$postType = 'gm_location';

    // If this isn't a 'gm_location' post, don't update it.
    if ( get_post_type( $post_id ) == $postType) {

		 global $wpdb;

		$CheckLat = $wpdb->get_results("SELECT * FROM wp_postmeta Where post_id = $post_id  AND meta_key = '_location_latitude' ");
		$CheckLon = $wpdb->get_results("SELECT * FROM wp_postmeta Where post_id = $post_id  AND meta_key = '_location_longitude' ");

		if (empty($CheckLat)) {
		    $wpdb->insert('wp_postmeta',array(
			'post_id' => "$post_id",
			'meta_key' => "_location_latitude",
			'meta_value' => "$map_lat"
			)
			,array('%s','%s','%s'));
		}else{
			$wpdb->update('wp_postmeta', array('meta_value'=>$map_lat), array('post_id'=>$post_id, 'meta_key' => "_location_latitude"));
		}

		if (empty($CheckLon)) {
		    $wpdb->insert('wp_postmeta',array(
			'post_id' => "$post_id",
			'meta_key' => "_location_longitude",
			'meta_value' => "$map_lon"
			)
			,array('%s','%s','%s'));
		}else{
			$wpdb->update('wp_postmeta', array('meta_value'=>$map_lon), array('post_id'=>$post_id, 'meta_key' => "_location_longitude"));
		}

	}
}

/*  ---- GET LAT AND LONG END ---- */





















