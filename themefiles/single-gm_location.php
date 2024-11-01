<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); 
global $wpdb;
$post_id = get_the_ID();
$meta_data = get_post_meta($post_id);

$CheckLat = $meta_data['_location_latitude'][0];
$CheckLon = $meta_data['_location_longitude'][0];

$location_address = $meta_data['_location_address'][0];
$location_city = $meta_data['_location_city'][0];
$location_province = $meta_data['_location_state_province'][0];
$location_country = $meta_data['_location_country'][0];
$location_postal_code = $meta_data['_location_postal_code'][0];
$location_telephone = $meta_data['_location_telephone'][0];
$location_fax = $meta_data['_location_fax'][0];
$location_email = $meta_data['_location_email'][0];


$terms = wp_get_post_terms( $post_id, "gm_location_categories");
$theTermID = $terms[0]->parent;
if($theTermID == 0){
	$theTermID = $terms[0]->term_id;
}
$saved_data = get_tax_meta($theTermID,'image_field_id',true);

?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script>
function initialize() {
	var mapOptions = {
		zoom: 15,
		center: new google.maps.LatLng(<?php echo $CheckLat?>, <?php echo $CheckLon?>),
		disableDefaultUI: true
	}
	var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	setMarkers(map);
}
function setMarkers(map, locations) {
		var myLatLng = new google.maps.LatLng(<?php echo $CheckLat?>, <?php echo $CheckLon?>);
		var marker = new google.maps.Marker({
		    position: myLatLng,
		    map: map,
		    icon: {
				url: "<?php echo $saved_data['src']; ?>"
			},
		    title: '<?php the_title(); ?>'
		});
}
google.maps.event.addDomListener(window, 'load', initialize);
</script>
	<div id="content" class="page col-full single-page">
		<?php while ( have_posts() ) : the_post(); ?>
		<section id="main" class="col-left">
			<div class="gm-location-container">
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
				<div class="gm-location-deetails">
					<h5>Contact Details</h5>		
					<?php 
						if($location_address){
							echo "<div class='detail'>";
							echo "<span>Address:</span>". $location_address;
							echo "</div>";
						}
						if($location_city){
							echo "<div class='detail'>";
							echo "<span>City / Suburb:</span>". $location_city;
							echo "</div>";
						}
						if($location_province){
							echo "<div class='detail'>";
							echo "<span>Province:</span>". $location_province;
							echo "</div>";
						}
						if($location_country){
							echo "<div class='detail'>";
							echo "<span>Country:</span>". $location_country;
							echo "</div>";
						}
						if($location_country){
							echo "<div class='detail'>";
							echo "<span>Postal Code:</span>". $location_country;
							echo "</div>";
						}
						if($location_telephone){
							echo "<div class='detail'>";
							echo "<span>Telephone:</span>". $location_telephone;
							echo "</div>";
						}
						if($location_fax){
							echo "<div class='detail'>";
							echo "<span>Fax:</span>". $location_fax;
							echo "</div>";
						}
						if($location_email){
							echo "<div class='detail'>";
							echo "<span>E-Mail:</span>". $location_email;
							echo "</div>";
						}
					?>
				</div>
				<nav id="nav-single">
				<span class="nav-previous"><?php previous_post_link( '%link', __( '<span class="meta-nav">&larr;</span> Previous', '' ) ); ?></span>
				<span class="nav-next"><?php next_post_link( '%link', __( 'Next <span class="meta-nav">&rarr;</span>', '' ) ); ?></span>
				</nav><!-- #nav-single -->

				
			</div>
		</section>
		<aside id="sidebar" class="col-right">
			<div id="map-canvas"></div>
		</aside>
		<?php endwhile; // end of the loop. ?>
	</div><!-- #content -->
<?php get_footer(); ?>