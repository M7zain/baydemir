<?php
/**
 * Contact form handler.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_baydemir_contact', 'baydemir_handle_contact' );
add_action( 'wp_ajax_nopriv_baydemir_contact', 'baydemir_handle_contact' );

/**
 * Handle AJAX contact form.
 */
function baydemir_handle_contact(): void {
	check_ajax_referer( 'baydemir_nonce', 'nonce' );

	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$phone   = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$subject = sanitize_text_field( wp_unslash( $_POST['subject'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
	$source  = sanitize_text_field( wp_unslash( $_POST['source'] ?? '' ) );
	$project_id = isset( $_POST['project_id'] ) ? absint( $_POST['project_id'] ) : 0;
	$honeypot = sanitize_text_field( wp_unslash( $_POST['website'] ?? '' ) );

	if ( $honeypot ) {
		wp_send_json_success( array( 'message' => __( 'Mesajınız gönderildi.', 'baydemir' ) ) );
	}

	if ( ! $name || ! $email || ! $message || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Lütfen zorunlu alanları doldurun.', 'baydemir' ) ), 400 );
	}

	$to      = baydemir_email();
	$mail_subject = sprintf( '[%s] %s', baydemir_company_name(), $subject ?: __( 'İletişim Formu', 'baydemir' ) );

	$project_line = '';
	if ( $project_id && 'project' === get_post_type( $project_id ) ) {
		$project_line = sprintf(
			"Proje: %s\nProje Linki: %s",
			get_the_title( $project_id ),
			(string) get_permalink( $project_id )
		);
	}

	$body_lines = array(
		'Ad Soyad: ' . $name,
		'Telefon: ' . $phone,
		'E-posta: ' . $email,
		'Konu: ' . $subject,
		'Kaynak: ' . $source,
	);
	if ( $project_line ) {
		$body_lines[] = $project_line;
	}
	$body_lines[] = '';
	$body_lines[] = 'Mesaj:';
	$body_lines[] = $message;
	$body    = implode( "\n", $body_lines );

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $mail_subject, $body, $headers );

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => __( 'E-posta gönderilemedi.', 'baydemir' ) ), 500 );
	}

	wp_send_json_success( array( 'message' => __( 'Mesajınız gönderildi. Teşekkürler!', 'baydemir' ) ) );
}

/**
 * Render contact form markup.
 */
function baydemir_render_contact_form(): void {
	$project       = baydemir_contact_form_project();
	$project_id    = $project ? $project->ID : 0;
	$project_title = $project ? get_the_title( $project ) : '';
	$default_subject = $project
		? sprintf(
			/* translators: %s: project title */
			__( 'Teklif: %s', 'baydemir' ),
			$project_title
		)
		: '';
	?>
	<form
		class="bd-contact-form"
		id="bd-contact-form"
		novalidate
		<?php if ( $project_id ) : ?>
			data-project-id="<?php echo esc_attr( (string) $project_id ); ?>"
			data-project-title="<?php echo esc_attr( $project_title ); ?>"
			data-project-subject="<?php echo esc_attr( $default_subject ); ?>"
		<?php endif; ?>
	>
		<?php if ( $project ) : ?>
			<div class="bd-form-project-note">
				<strong><?php esc_html_e( 'Teklif talebi', 'baydemir' ); ?></strong>
				<p>
					<?php
					echo wp_kses(
						sprintf(
							/* translators: 1: project URL, 2: project title */
							__( 'Bu form <a href="%1$s">%2$s</a> projesi için gönderilecektir.', 'baydemir' ),
							esc_url( (string) get_permalink( $project ) ),
							esc_html( $project_title )
						),
						array(
							'a' => array( 'href' => true ),
						)
					);
					?>
				</p>
			</div>
			<input type="hidden" name="project_id" value="<?php echo esc_attr( (string) $project_id ); ?>" />
		<?php endif; ?>
		<div class="bd-form-row bd-form-row--2">
			<label class="bd-field">
				<span class="screen-reader-text"><?php esc_html_e( 'Ad Soyad', 'baydemir' ); ?></span>
				<input type="text" name="name" placeholder="<?php esc_attr_e( 'Ad Soyad *', 'baydemir' ); ?>" required />
			</label>
			<label class="bd-field">
				<span class="screen-reader-text"><?php esc_html_e( 'Telefon', 'baydemir' ); ?></span>
				<input type="tel" name="phone" placeholder="<?php esc_attr_e( 'Telefon', 'baydemir' ); ?>" />
			</label>
		</div>
		<label class="bd-field">
			<span class="screen-reader-text"><?php esc_html_e( 'E-Posta', 'baydemir' ); ?></span>
			<input type="email" name="email" placeholder="<?php esc_attr_e( 'E-Posta *', 'baydemir' ); ?>" required />
		</label>
		<label class="bd-field">
			<span class="screen-reader-text"><?php esc_html_e( 'Konu', 'baydemir' ); ?></span>
			<input type="text" name="subject" placeholder="<?php esc_attr_e( 'Konu', 'baydemir' ); ?>" value="<?php echo esc_attr( $default_subject ); ?>" />
		</label>
		<label class="bd-field">
			<span class="screen-reader-text"><?php esc_html_e( 'Mesaj', 'baydemir' ); ?></span>
			<textarea name="message" rows="5" placeholder="<?php esc_attr_e( 'Mesajınız *', 'baydemir' ); ?>" required></textarea>
		</label>
		<label class="bd-field">
			<span class="screen-reader-text"><?php esc_html_e( 'Bizi nereden buldunuz?', 'baydemir' ); ?></span>
			<select name="source">
				<option value=""><?php esc_html_e( 'Bizi nereden buldunuz?', 'baydemir' ); ?></option>
				<option value="Google"><?php esc_html_e( 'Google', 'baydemir' ); ?></option>
				<option value="Instagram"><?php esc_html_e( 'Instagram', 'baydemir' ); ?></option>
				<option value="Tavsiye"><?php esc_html_e( 'Tavsiye', 'baydemir' ); ?></option>
				<option value="Diğer"><?php esc_html_e( 'Diğer', 'baydemir' ); ?></option>
			</select>
		</label>
		<input type="text" name="website" class="bd-honeypot" tabindex="-1" autocomplete="off" aria-hidden="true" />
		<button type="submit" class="bd-btn bd-btn--primary bd-btn--block">
			<?php esc_html_e( 'Mesaj Gönder', 'baydemir' ); ?>
			<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<p class="bd-form-status" role="status" aria-live="polite" hidden></p>
	</form>
	<?php
}
