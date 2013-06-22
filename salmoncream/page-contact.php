<?php
/**
 * Template Name: Contact
 *
 * @package Salmon & Cream WordPress Theme
 */

get_header(); ?>

	<div id="content" class="site-content clearfix" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<div class="content-left">

				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header><!-- .entry-header -->

				<?php the_content(); ?>

			</div><!-- .content-left -->

			<div class="content-right">

				<div class="entry-content">

					<?php

					// form processing if the input field has been set
					if ( isset( $_POST['submit']) && wp_verify_nonce( $_POST['contact_form_nonce'],'form_submit' ) ) {

						// define markup for error messages
						$error_tag = apply_filters( 'wp-contact-form-template_error_tag', 'p' );

						// output form values for debugging
						//if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
						//	var_dump($_POST);

						$spam    = filter_var( trim( $_POST['spamcheck'] ), FILTER_SANITIZE_STRING);
						$from    = filter_var( trim( strip_tags( $_POST['from'] ) ), FILTER_SANITIZE_STRING);
						$email   = trim( $_POST['email'] );
						$subject = filter_var( trim( $_POST['subject'] ), FILTER_SANITIZE_STRING);
						//$message = filter_var( trim( $_POST['text'] ), FILTER_SANITIZE_STRING);
						// Allow html in message
						$message = wp_kses_post( $_POST['text'] );

						// check for spam input field
						if ( ! empty( $spam ) ) {
							$spam_error = __( 'Spammer? The spam protection field needs to be empty.', 'salmoncream' );
							$has_error  = TRUE;
						}

						// check sender name, string
						if ( empty( $from ) ) {
							$from_error = __( 'Please enter your name.', 'salmoncream' );
							$has_error  = TRUE;
						}

						// check for mail and filter the mail
						// alternative to filter_var a regex via preg_match( $filter, $email )
						// $filter = "/^([a-z0-9äöü]+[-_\\.a-z0-9äöü]*)@[a-z0-9äöü]+([-_\.]?[a-z0-9äöü])+\.[a-z]{2,4}$/i"
						// $filter = "/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i"
						if ( empty( $email ) ) {
							$email_error = __( 'Please enter your e-mail adress.', 'salmoncream' );
							$has_error   = TRUE;
						} else if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
							$email_error = __( 'Please enter a valid e-mail address.', 'salmoncream' );
							$has_error   = TRUE;
						}

						if ( empty( $subject ) ) {
							$subject_error = __( 'Please enter a subject.', 'salmoncream' );
							$has_error     = TRUE;
						}

						if ( empty( $message ) ) {
							$message_error = __( 'Please enter a message.', 'salmoncream' );
							$has_error     = TRUE;
						}

						if ( ! isset( $has_error ) ) {

							// get IP
							if ( isset( $_SERVER ) ) {

								if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
									$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
								} elseif ( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
									$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
								} else {
									$ip_addr = $_SERVER["REMOTE_ADDR"];
								}

							} else {

								if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
									$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
								} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
									$ip_addr = getenv( 'HTTP_CLIENT_IP' );
								} else {
									$ip_addr = getenv( 'REMOTE_ADDR' );
								}

							}
							$ip_addr = filter_var( $ip_addr, FILTER_VALIDATE_IP );

							// use mail address from Options Page or WP Admin
							$contact_email = of_get_option('contact_email');
							$email_to = ( $contact_email ) ? $contact_email : get_option( 'admin_email' );

							$subject  = $subject . ' ' . __( 'via Contact request from', 'salmoncream' ) . ' ' . $from;
							$body     = __( 'Message:', 'salmoncream' ) . ' ' . $message . "\n\n" .
							            __( 'Name:', 'salmoncream' ) . ' ' . $from . "\n" .
							            __( 'E-mail:', 'salmoncream' ) . ' ' . $email . "\n" .
							            __( 'IP:', 'salmoncream' ) . ' ' . $ip_addr . "\n";
							$headers  = 'From: ' . $from . ' <' . $email . '>' . "\r\n";

							// Filter hooks for enhance the mail; sorry for long strings ;)
							$email_to = apply_filters( 'wp-contact-form-template-mail_email_to', $email_to );
							$subject  = apply_filters( 'wp-contact-form-template-mail_subject', $subject );
							$body     = apply_filters( 'wp-contact-form-template-mail_body', $body );

							// send mail via wp mail function
							wp_mail( $email_to, $subject, $body, $headers );

							// successfully mail shipping
							$email_sent = TRUE;
						}

					}

					do_action( 'wp-contact-form-template_form_before' ); ?>

					<form id="contactform" class="contactform" action="<?php the_permalink(); ?>" method="post">

							<?php do_action( 'wp-contact-form-template_form_top' );

							if ( isset( $spam_error ) )
								echo apply_filters( 'wp-contact-form-template_spam_message', '<' . $error_tag . ' class="alert yellow">' . $spam_error . '</' . $error_tag . '>' );
							if ( isset( $email_sent ) )
								echo apply_filters( 'wp-contact-form-template_thanks_message', '<' . $error_tag . ' class="alert green">' . __( 'Thank you for leaving a message.', 'salmoncream' ) . '</' . $error_tag . '>' );

							do_action( 'wp-contact-form-template_form_before_fields' ); ?>

							<div class="field">
								<label for="from">
									<?php _e( 'Name', 'salmoncream' ); ?> *
								</label>
								<input type="text" id="from" name="from" value="<?php if ( isset( $from ) ) echo esc_attr( $from ); ?>" size="30" />
								<?php
								if ( isset( $from_error ) )
									echo '<' . $error_tag . ' class="alert yellow">' . $from_error . '</' . $error_tag . '>';
								?>
							</div>

							<div class="field">
								<label for="email">
									<?php _e( 'Email', 'salmoncream' ); ?> *
								</label>
								<input type="text" id="email" name="email" value="<?php if ( isset( $email ) ) echo esc_attr( $email ); ?>" size="30" />
								<?php
								if ( isset( $email_error ) )
									echo '<' . $error_tag . ' class="alert yellow">' . $email_error . '</' . $error_tag . '>';
								?>
							</div>

							<div class="field">
								<label for="subject">
									<?php _e( 'Subject', 'salmoncream' ); ?> *
								</label>
								<input type="text" id="subject" name="subject" value="<?php if ( isset( $subject ) ) echo esc_attr( $subject ); ?>" size="30" />
								<?php
								if ( isset( $subject_error ) )
									echo '<' . $error_tag . ' class="alert yellow">' . $subject_error . '</' . $error_tag . '>';
								?>
							</div>

							<?php do_action( 'wp-contact-form-template_form_after_fields' ); ?>

							<div class="field">
								<textarea id="text" name="text" cols="45" rows="8"><?php if ( isset( $message ) ) echo esc_textarea( $message ); ?></textarea>
								<?php
								if ( isset( $message_error ) )
									echo '<' . $error_tag . ' class="alert yellow">' . $message_error . '</' . $error_tag . '>';
								?>
							</div>

							<div class="field" style="display: none !important;">
								<label for="text">
									<?php _e( 'Spam protection', 'salmoncream' ); ?>
								</label>
								<input name="spamcheck" class="spamcheck" type="text" />
							</div>

							<p class="form-submit">
								<input class="submit" type="submit" name="submit" value="<?php esc_attr_e( 'Send', 'salmoncream' ); ?>" />
							</p>
							<?php wp_nonce_field( 'form_submit', 'contact_form_nonce' ) ?>
							<?php do_action( 'wp-contact-form-template_form' ); ?>

					</form>

					<?php do_action( 'wp-contact-form-template_form_after' ); ?>

				</div><!-- .entry-content -->

			<?php endwhile; // end of the loop. ?>

		</div><!-- .content-right -->

	</div><!-- #content -->

<?php get_footer(); ?>
