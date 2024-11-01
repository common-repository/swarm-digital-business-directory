<?php 

	function check_if_link_has_http($custom_link){
			if (strpos($custom_link, 'http') !== false){
				//echo $link;
			}else{
				$custom_link = 'http://'.$custom_link;
			}
			return $custom_link;
		}

/* ------------------- location_main_map Shortcode --------------------*/

	
	function location_main_func( $atts ) {
		global $wpdb;
		extract( shortcode_atts( array(
			'width' => '100%',
			'height' => '500px',
		), $atts ) );

		$qry_args = array(
			'post_status' => 'publish', // optional
			'post_type' => 'gm_location', // Change to match your post_type
			'posts_per_page' => -1, // ALL posts
			);

		$all_posts = new WP_Query( $qry_args );

		$apiKey = get_option('gmap_plugin_options_api_key');

		$apiKey = $apiKey['api_key'];
		if(empty($apiKey)){
			$apiKey = "No Key";
		}

		$zoomLevel = get_option('gmap_plugin_options_zoom_level');
		$zoomLevel = $zoomLevel['zoom_level'];

		if(empty($zoomLevel)){$zoomLevel = 0;}

		$map_lng = get_option('gmap_plugin_options_map_lng');
		$map_lng = $map_lng['map_lng'];

		$map_lat = get_option( 'gmap_plugin_options_center_map' );
		$map_lat = $map_lat['center_map'];

	?>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&key=<?php echo $apiKey; ?>"></script>
	<script>
		function initialize() {
			var mapOptions = {
				zoom: <?php echo $zoomLevel; ?>,
				center: new google.maps.LatLng(<?php echo $map_lat; ?>, <?php echo $map_lng; ?>),
				disableDefaultUI: true
			}
			var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
			setMarkers(map);
		}
		var contentString;
		function setMarkers(map, locations) {
			<?php foreach ($all_posts->posts as $posts => $post) {
						
				$post_id = $post->ID;
				$post_title = $post->post_title;
				$post_link = get_permalink( $post_id );
				$meta_data = get_post_meta($post_id);
				$custom_link = "";
				$theTermID = "";
				

				if(empty($meta_data['_location_address'][0])){
					$location_address = "";
				}else{
					$location_address = $meta_data['_location_address'][0];
				}

				if(empty($meta_data['_location_city'][0])){
					$location_city = "";
				}else{
					$location_city = $meta_data['_location_city'][0];
				}

				if(empty($meta_data['_location_state_province'][0])){
					$location_province = "";
				}else{
					$location_province = $meta_data['_location_state_province'][0];
				}
				
				if(empty($meta_data['_location_country'][0])){
					$location_country = "";
				}else{
					$location_country = $meta_data['_location_country'][0];
				}

				if(empty($meta_data['_location_postal_code'][0])){
					$location_postal_code = "";
				}else{
					$location_postal_code = $meta_data['_location_postal_code'][0];
				}

				if(empty($meta_data['_location_telephone'][0])){
					$location_telephone = "";
				}else{
					$location_telephone = $meta_data['_location_telephone'][0];
				}

				if(empty($meta_data['_location_fax'][0])){
					$location_fax = "";
				}else{
					$location_fax = $meta_data['_location_fax'][0];
				}

				if(empty($meta_data['_location_email'][0])){
					$location_email = "";
				}else{
					$location_email = $meta_data['_location_email'][0];
				}

			

				if(!empty($meta_data['_location_custom_url'][0])){
					$custom_link = $meta_data['_location_custom_url'][0];
				}
				
				if($custom_link){
					$post_link = check_if_link_has_http($custom_link);
				}

				$CheckLat = $meta_data['_location_latitude'][0];
				$CheckLon = $meta_data['_location_longitude'][0];

				$terms = wp_get_post_terms( $post_id, "gm_location_categories");

				if($terms){
					$theTermID = $terms[0]->parent;
				}

				if($theTermID == "0"){
					$theTermID = $terms[0]->term_id;
				}
				
				$saved_data = get_tax_meta($theTermID,'image_field_id',true);
				if($theTermID && !empty($CheckLat) && !empty($CheckLon)){
			?>	
				var LocationLink = <?php echo "'".$post_link."'" ?>;
				var myLatLng = new google.maps.LatLng(<?php echo $CheckLat?>, <?php echo $CheckLon?>);
				var marker = new google.maps.Marker({
				    position: myLatLng,
				    map: map,
				    icon: {
						url: "<?php echo $saved_data['src']; ?>"
					},
				    title: '<?php echo $post_title; ?>',
				    url: LocationLink,
				});
				google.maps.event.addListener(marker, 'mouseover', function() {
					contentString = '<div class="map-info-window"><strong><?php echo $post_title; ?></strong><br/>'+
					'<strong>Details</strong><br/>'+
					'<?php if($location_address){echo $location_address; echo "<br/>";} ?>'+
					'<?php if($location_city){echo $location_city; echo "<br/>";} ?>'+
					'<?php if($location_telephone){echo "Tel: ";echo $location_telephone; echo "<br/>";} ?>';

					infowindow.setContent(contentString);
					infowindow.open(map,this);
				});
				var infowindow = new google.maps.InfoWindow({
					width:500
				});
				google.maps.event.addListener(marker, 'click', function() {
				    window.location.href = this.url;
				    // window.open(this.url, '_blank');
				});
			<?php
				}
			}

					
			?>
		}

		google.maps.event.addDomListener(window, 'load', initialize);

	</script>
			
	<div id="map-canvas" style="width:<?php echo $width ?>; height:<?php echo $height ?>; "></div>
	<?php
		return;
		wp_reset_postdata();
	}
	add_shortcode( 'location_map', 'location_main_func' );

/* ------------------- location_main_map Shortcode End --------------------*/

/* ------------------- location_list Shortcode --------------------*/

function location_list_func( $atts ) {

	$taxonomy_name = "gm_location_categories";
	extract( shortcode_atts( array(
	  'categories' => 'all',
	  'show' => 'posts',
	  'title' => 'Locations'
	), $atts ) );

	$location_list = get_terms( $taxonomy_name, 
		array(
			
		) 
	);
	echo "<h3>{$title}</h3>";
	if($categories != "all"){

		$categories_array = explode(',', $categories);
		$categories_length = sizeof($categories_array);
		
		

		if($show != "posts"){
	     	$categories = array_map('trim', explode(',', $categories));

	     	foreach ($categories as $category ) {
	     		$category_term = get_term_by('name', $category, $taxonomy_name, 'ARRAY_A' );

	     		echo '<h3><a href="' . get_term_link( $category_term['term_id'], $taxonomy_name ) . '">'.$category_term['name'].'</a></h3>';
	     		$category_term_children = get_term_children( $category_term['term_id'], $taxonomy_name );
	     		
	     		echo "<ul id='locations-list'>";
	     		foreach ( $category_term_children as $child ) {
					$term = get_term_by( 'id', $child, $taxonomy_name );
					echo '<li><a href="' . get_term_link( $child, $taxonomy_name ) . '">' . $term->name . '</a></li>';
				}
				echo "</ul>";
			}

     	}elseif($show == "posts"){

     		$args = array(
			    'hide_empty'    => true
			); 
     		$allCategories = get_terms($taxonomy_name,$args);

     		$categories = array_map('trim', explode(',', $categories));
     		
     		$CurCatCount = 0;
     		$DivCount = 0;
     		$catCount = count($allCategories);

     		$qry_args = array(
					'post_status' => 'publish', // optional
					'post_type' => 'gm_location', // Change to match your post_type
					'posts_per_page' => -1, // ALL posts
					
				);

			$cat_posts = new WP_Query( $qry_args );

			function in_array_check($needle, $haystack){
			    foreach ($haystack as $value)
			    {
			        if (strtolower($value) == strtolower($needle))
			        return true;
			    }
			    return false;
			}

     		foreach ($allCategories as $Cat) {

				if($Cat->count == 0){
					$catCount --;
				}
				if($Cat->count != 0 && in_array_check($Cat->name, $categories)){

					if($DivCount == 0){
						//echo "first";
						echo "<div class='region-row'>";
					}

					echo "<div class='location-small-container'>";
						echo "<strong>{$Cat->name}</strong>";

						echo "<ul id='locations-list'>";
							
								foreach ($cat_posts->posts as $posts => $post) {

									$terms = wp_get_post_terms( $post->ID, $taxonomy_name, array("fields" => "names"));
									
						     		if($Cat->name == $terms[0] && $post->ID){
						     			echo '<li><a href="' . $post->guid . '">' .  $post->post_title . '</a></li>';
						     		}
								}

						echo "</ul>";
					echo "</div>";//location-small-container

					$CurCatCount ++;
					$DivCount ++;
				
					if($DivCount == 4 || $CurCatCount == $catCount || $CurCatCount == $categories_length){
						echo "</div>";
						//echo " last";
						$DivCount = 0;
					}
				}
     		}
     		
     		wp_reset_postdata();
     	}
    }else{

		$CurCatCount = 0;
 		$DivCount = 0;
 		$args = array(
			    'hide_empty'    => true
			); 
     	$categories = get_terms( $taxonomy_name,$args );
     	$catCount = count($categories);



    	if($show != "posts"){
    		

	     	foreach ($categories as $category ) {
	     		
	     		if($category->count == 0){
					$catCount --;
				}

				
	     		$category_term = get_term_by('name', $category->name, $taxonomy_name, 'ARRAY_A' );

	     		if($category->count != 0){
						if($DivCount == 0){
							//echo "first";
							echo "<div class='region-row' >";
						}
					echo "<div class='location-small-container'>";
			     		echo '<h3><a href="' . get_term_link( $category_term['term_id'], $taxonomy_name ) . '">'.$category_term['name'].'</a></h3>';
				     		$category_term_children = get_term_children( $category_term['term_id'], $taxonomy_name );
				     		
				     		echo "<ul id='locations-list'>";
				     		foreach ( $category_term_children as $child ) {
								$term = get_term_by( 'id', $child, $taxonomy_name );
								if($term->count != 0){
									echo '<li><a href="' . get_term_link( $child, $taxonomy_name ) . '">' . $term->name . '</a></li>';
								}
							}
						echo "</ul>";
					echo "</div>";
					$CurCatCount ++;
					$DivCount ++;
					//echo $DivCount;
					if($DivCount == 4 || $CurCatCount == $catCount){
						echo "</div>";
						//echo " last";
						$DivCount = 0;
					}
				}
			}
     	}elseif($show == "posts"){
     		    $qry_args = array(
						'post_status' => 'publish', // optional
						'post_type' => 'gm_location', // Change to match your post_type
						'posts_per_page' => -1, // ALL posts
						
					);

				$cat_posts = new WP_Query( $qry_args );
     		  foreach ($categories as $Cat) {
     			

					if($Cat->count == 0){
						$catCount --;
					}
					if($Cat->count != 0){
						if($DivCount == 0){
							//echo "first";
							echo "<div class='region-row' >";
						}
						echo "<div class='location-small-container'>";
							echo "<strong>{$Cat->name}</strong>";

							echo "<ul id='locations-list'>";


									foreach ($cat_posts->posts as $posts => $post) {

										$terms = wp_get_post_terms( $post->ID, $taxonomy_name, array("fields" => "names"));

							     		if($Cat->name == $terms[0] && $post->ID){
							     			echo '<li><a href="' . $post->guid . '">' .  $post->post_title . '</a></li>';
							     		}
									}

							echo "</ul>";
						echo "</div>";
						$CurCatCount ++;
						$DivCount ++;
						//echo $DivCount;
						if($DivCount == 4 || $CurCatCount == $catCount){
							echo "</div>";
							//echo " last";
							$DivCount = 0;
						}
						
					}


     		}

     		wp_reset_postdata();
     		
     	}

    	/*$category_terms = get_terms( $taxonomy_name );
     	
     	echo '<h3>Categories</h3>';
     	echo "<ul id='locations-list'>";
	 	foreach ($category_terms as $category ) {
	 		echo '<li><a href="' . get_term_link( $category, $taxonomy_name ) . '">' . $category->name . '</a></li>';
	 	}
	 	echo "</ul>";*/
    }
}
add_shortcode( 'location_list', 'location_list_func' );




















?>