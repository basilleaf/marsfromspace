<?php
/**
 * The template for displaying the footer.
 *
 * @package Salmon & Cream WordPress Theme
 */
?>

	</div><!-- #main -->

	<footer id="colophon" class="site-footer clearfix" role="contentinfo">
		<nav id="footer-navigation" class="navigation-footer clearfix" role="navigation">

			<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'depth' => -1 ) ); ?>

		</nav><!-- #footer-navigation -->

		<div class="site-info">
			<?php if ( of_get_option('footer_text') ) : ?>

				<p><?php echo of_get_option('footer_text'); ?></p>

			<?php else :  ?>

				<p><a href="<?php esc_url( 'http://wordpress.org/' ); ?>" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'salmoncream' ); ?>"><?php printf( __( 'Proudly powered by %s', 'salmoncream' ), 'WordPress' ); ?></a></p>

				<p><?php printf( __( '%1$s by %2$s.', 'salmoncream' ), 'Salmon & Cream WordPress Theme', '<a href="' . esc_url( 'http://www.goodbyeagency.com/') . '">goodbye, agency!</a>' ); ?></p>

			<?php endif; ?>
		</div><!-- .site-info -->

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>