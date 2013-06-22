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

	<?php
		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || '0' != get_comments_number() )
			comments_template();
	?>

<?php get_footer(); ?>
