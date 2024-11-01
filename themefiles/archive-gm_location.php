<?php
get_header(); ?>

	<div id="content" class="page col-full archive">

		<?php if ( have_posts() ) : ?>
			
			<?php /* The loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
			<div class="gm-location-container">
				<h1>
					<a href="<?php the_permalink(); ?> ">
						<?php the_title(); ?>
					</a>
				</h1>
				<div class="gm-location-excerpt">
					<?php the_content(); ?>
				</div>
			</div>
			<?php endwhile; ?>

		<?php endif; ?>

	</div><!-- #content -->

<?php get_footer(); ?>