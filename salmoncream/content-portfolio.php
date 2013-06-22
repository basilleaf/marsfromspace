<?php
/**
 * Template for portfolio tiles
 *
 * @package Salmon & Cream WordPress Theme
 */
?>

<?php if ( have_posts() ) : ?>

	<?php /* Start the Loop */ ?>
	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class('tile portfolio'); ?>>

			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">

				<?php if ( has_post_thumbnail() ) :

					$hidpi_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'salmoncream-square@2x' );

					if ( $hidpi_image && ( $hidpi_image[1] === 800 && $hidpi_image[2] === 800 ) ) {

						the_post_thumbnail('salmoncream-square@2x');

					} else {

						the_post_thumbnail('salmoncream-square');

					}

				else : ?>

					<img src="<?php echo get_template_directory_uri(); ?>/img/blank.png" alt="" width="800" height="800" />

				<?php endif; ?>

			</a>

			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="tile-content <?php if ( !has_post_thumbnail())  : ?> no-thumbnail<?php endif; ?>" rel="bookmark">

				<h2 class="entry-title"><?php the_title(); ?></h2>

				<?php $featured_skill = get_post_meta( $post->ID, '_salmoncream_portfolio_featured_skill', true ); ?>

				<?php if ( isset( $featured_skill)  && !empty( $featured_skill ) ) : ?>

					<h3><?php echo $featured_skill; ?></h3>

				<?php endif; ?>

			</a><!-- .tile-content -->

		</article><!-- #post-<?php the_ID(); ?> -->

	<?php endwhile; ?>

<?php else : ?>

	<?php get_template_part( 'no-results', 'index' ); ?>

<?php endif; ?>
