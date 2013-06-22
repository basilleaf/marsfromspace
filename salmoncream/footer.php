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

			

		</nav><!-- #footer-navigation -->

		<div class="site-info">
			<?php if ( of_get_option('footer_text') ) : ?>

				<p><?php echo of_get_option('footer_text'); ?></p>

			<?php else :  ?>

				

				

			<?php endif; ?>
		</div><!-- .site-info -->

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>