<?php
/**
 * The template used for displaying blog stickies
 *
 * @package Salmon & Cream WordPress Theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<figure class="sticky-post-image">

			<?php if ( has_post_thumbnail()) : ?>

				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">

					<?php $hidpi_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'salmoncream-sticky@2x' );

					if ( $hidpi_image && ( $hidpi_image[1] === 1200 && $hidpi_image[2] === 840 ) ) {

						the_post_thumbnail( 'salmoncream-sticky@2x' );

					} else {

						the_post_thumbnail( 'salmoncream-sticky' );

					} ?>

				</a>

			<?php endif; ?>

	</figure><!-- .sticky-post-image -->

	<div class="sticky-post-content">

		<header class="entry-header">
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
		</header><!-- .entry-header -->

		<div class="entry-summary">
			<?php the_excerpt(); ?>
			<a class="read-more" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark"><?php _e('Continue reading <span class="meta-nav">&rarr;</span>', 'salmoncream'); ?></a>
		</div><!-- .entry-summary -->


	</div><!-- .sticky-post-content -->

</article><!-- #post-<?php the_ID(); ?> -->