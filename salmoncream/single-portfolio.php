<?php
/**
 * The template for displaying a single portfolio item.
 *
 * @package Salmon & Cream WordPress Theme
 */

get_header(); ?>

	<div id="content" class="site-content" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>

				<?php

				global $post;

				$description = get_post_meta( $post->ID, '_salmoncream_portfolio_description', true );

				// lisa this is my hack 
				if (!$description) {
				$description = apply_filters('the_content', $post->post_content); 
				}
				$link = get_post_meta( $post->ID, '_salmoncream_portfolio_link', true );
				$terms = wp_get_post_terms( $post->ID, 'skill' );
				$media = get_post_meta( $post->ID, '_salmoncream_portfolio_media', true );

				?>

				<div class="content-left">

					<div class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</div>

					<div class="entry-content">
 

						<?php echo wpautop( $description ); ?>

						<?php if ( isset( $link ) && !empty( $link ) ) : ?>

							<h2><?php _e( 'Link', 'salmoncream' ); ?></h2>

							<p><a href="http://<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a></p>

						<?php endif; ?>

					</div><!-- .entry-content -->

					<div class="entry-meta">

						<?php if ( !empty( $terms ) ) : ?>

							<h2><?php _e( 'Skills', 'salmoncream' ) ?></h2>

							<p><?php echo get_the_term_list( $post->ID, 'skill', '', ', ', '' ); ?></p>

						<?php endif; ?>

					</div><!-- .entry-meta -->

				</div>

				<div class="content-right">

					<div class="entry-media">

							<?php if ( has_post_thumbnail() ) {

								$hidpi_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'salmoncream-featured@2x' );

								if ( $hidpi_image && ( $hidpi_image[1] === 2000 && $hidpi_image[2] === 1400 ) ) {

									the_post_thumbnail('salmoncream-featured@2x');

								} else {

									the_post_thumbnail('salmoncream-featured');

								}

							} ?>

						<?php echo do_shortcode( $media ); ?>

					</div>

				</div>

			</article><!-- #post-## -->

		<?php endwhile; // end of the loop. ?>

	</div><!-- #content -->

	<?php
		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || '0' != get_comments_number() )
			comments_template();
	?>

<?php get_footer(); ?>