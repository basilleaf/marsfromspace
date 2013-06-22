<?php
/**
 * The template used for displaying blog tiles
 *
 * @package Salmon & Cream WordPress Theme
 */
?>

<?php if ( have_posts() ) : ?>

	<?php /* Start the Loop */ ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'tile post' ); ?>>

			<?php if ( has_post_thumbnail() ) : ?>

				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">

					<?php $hidpi_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'salmoncream-square@2x' );

					if ( $hidpi_image && ( $hidpi_image[1] === 800 && $hidpi_image[2] === 800 ) ) {

						the_post_thumbnail('salmoncream-square@2x');

					} else {

						the_post_thumbnail('salmoncream-square');

					} ?>
				</a>

			<?php else : ?>

				<img src="<?php echo get_template_directory_uri(); ?>/img/blank.png" alt="" width="800" height="800" />

			<?php endif; ?>

			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="tile-content<?php if ( !has_post_thumbnail())  : ?> no-thumbnail<?php endif; ?>" rel="bookmark">

					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-summary">
						<?php the_excerpt(); ?>
						<?php _e('Continue reading <span class="meta-nav">&rarr;</span>', 'salmoncream'); ?>
					</div><!-- .entry-summary -->

				</a><!-- .tile-content -->

		</article><!-- #post-<?php the_ID(); ?> -->

	<?php endwhile; ?>

<?php endif; ?>