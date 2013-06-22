<?php
/**
 * @package Salmon & Cream WordPress Theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>

	<?php if ( has_post_thumbnail() ) : ?>

		<figure class="featured-image">

			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">

				<?php $hidpi_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'salmoncream-featured-small@2x' );

				if ( $hidpi_image && ( $hidpi_image[1] === 600 && $hidpi_image[2] === 420 ) ) {

					the_post_thumbnail('salmoncream-featured-small@2x');
				} else {

					the_post_thumbnail('salmoncream-featured-small');

				} ?>

			</a>

		</figure>

	<?php endif; ?>

	<header class="entry-header">
		<h1 class="entry-title">
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>

		<div class="entry-meta">

			<?php salmoncream_posted_on(); ?>

			<?php $comments = get_comments_number();

			if ( $comments > 0 ) : ?>

				 | <a href="<?php the_permalink(); ?>#comments">

				<?php printf( _nx( '1 Comment', '%1$s Comments', $comments, 'comments title', 'salmoncream' ), number_format_i18n( $comments ) ); ?>

				 </a>

			<?php endif; ?>

		</div><!-- .entry-meta -->

	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->

</article><!-- #post-<?php the_ID(); ?> -->