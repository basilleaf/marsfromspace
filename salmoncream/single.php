<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Salmon & Cream WordPress Theme
 */

get_header(); ?>

	<div id="content" class="site-content clearfix">

		<div class="content-right" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'single' ); ?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- .content-right -->

		<?php get_sidebar(); ?>

	</div><!-- #content -->

	<div id="author" class="author-area clearfix">

		<h4><?php _e( 'Author', 'salmoncream'); ?></h4>

		<?php echo get_avatar( get_the_author_meta('ID'), 720 ); ?>

		<div class="author-meta">

			<h5><?php the_author_posts_link(); ?></h5>

			<p><?php the_author_meta('description'); ?></p>

		</div>

	</div><!-- #author -->

	<?php
		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || '0' != get_comments_number() )
			comments_template();
	?>

<?php get_footer(); ?>