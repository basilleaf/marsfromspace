<?php
/**
 * @package Salmon & Cream WordPress Theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) {

		$hidpi_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'salmoncream-featured@2x' );

		if ( $hidpi_image && ( $hidpi_image[1] === 2000 && $hidpi_image[2] === 1400 ) ) {

			the_post_thumbnail('salmoncream-featured@2x');

		} else {

			the_post_thumbnail('salmoncream-featured');

		}

	} ?>

	<header class="entry-header clearfix">
		<h1 class="entry-title"><?php the_title(); ?></h1>

		<time class="entry-date" datetime="<?php the_time('Y-m-d'); ?>"><?php the_time(get_option('date_format')); ?></time>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'salmoncream' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-meta">
		<?php
			/* translators: used between list items, there is a space after the comma */
			$category_list = get_the_category_list( __( ', ', 'salmoncream' ) );

			/* translators: used between list items, there is a space after the comma */
			$tag_list = get_the_tag_list( '', __( ', ', 'salmoncream' ) );

			if ( ! salmoncream_categorized_blog() ) {
				// This blog only has 1 category so we just need to worry about tags in the meta text
				if ( '' != $tag_list ) {
					$meta_text = __( '<p>This entry was tagged %2$s.</p>', 'salmoncream' );
				} else {
					$meta_text = '';
				}

			} else {
				// But this blog has loads of categories so we should probably display them here
				if ( '' != $tag_list ) {
					$meta_text = __( 'This entry was posted in %1$s and tagged %2$s.', 'salmoncream' );
				} else {
					$meta_text = __( 'This entry was posted in %1$s.', 'salmoncream' );
				}

			} // end check for categories on this blog

			printf(
				$meta_text,
				$category_list,
				$tag_list
			);
		?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->
