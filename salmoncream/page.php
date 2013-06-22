<?php
/**
 * The template for displaying all pages.
 *
 * @package Salmon & Cream WordPress Theme
 */

get_header(); ?>

	<div id="content" class="site-content clearfix">

		<div class="content-right" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- .content-right -->

		<?php get_sidebar( 'page' ); ?>

	</div><!-- #content -->

	

<?php get_footer(); ?>
