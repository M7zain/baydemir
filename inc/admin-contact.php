<?php
/**
 * Contact & WhatsApp settings in Baydemir admin.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'baydemir_register_contact_menu', 22 );
add_action( 'admin_enqueue_scripts', 'baydemir_enqueue_contact_manager_assets' );
add_action( 'admin_post_baydemir_save_contact', 'baydemir_handle_save_contact' );

/**
 * Register İletişim submenu.
 */
function baydemir_register_contact_menu(): void {
	add_submenu_page(
		'baydemir-panel',
		__( 'İletişim & WhatsApp', 'baydemir' ),
		__( 'İletişim', 'baydemir' ),
		'edit_theme_options',
		'baydemir-contact',
		'baydemir_render_contact_manager'
	);
}

/**
 * Shared admin styles + media picker.
 */
function baydemir_enqueue_contact_manager_assets( string $hook ): void {
	if ( 'baydemir_page_baydemir-contact' !== $hook ) {
		return;
	}

	wp_enqueue_media();

	wp_enqueue_style(
		'baydemir-admin',
		baydemir_asset( 'css/admin.css' ),
		array(),
		BAYDEMIR_VERSION
	);

	wp_enqueue_script(
		'baydemir-admin',
		baydemir_asset( 'js/admin.js' ),
		array( 'jquery' ),
		BAYDEMIR_VERSION,
		true
	);

	wp_localize_script(
		'baydemir-admin',
		'baydemirAdmin',
		array(
			'i18n' => array(
				'coverTitle'  => __( 'İletişim Görseli', 'baydemir' ),
				'coverButton' => __( 'Seç', 'baydemir' ),
			),
		)
	);
}

/**
 * Render a single media field.
 */
function baydemir_render_contact_media_field( string $name, int $image_id, string $label, string $empty ): void {
	$url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
	?>
	<div class="bd-media-field<?php echo $image_id ? '' : ' is-empty'; ?>" data-bd-media>
		<strong><?php echo esc_html( $label ); ?></strong>
		<input type="hidden" class="bd-media-field__input" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $image_id ); ?>" />
		<div class="bd-media-field__preview">
			<?php if ( $url ) : ?>
				<img src="<?php echo esc_url( $url ); ?>" alt="" />
			<?php endif; ?>
		</div>
		<div class="bd-media-field__empty"><?php echo esc_html( $empty ); ?></div>
		<p class="bd-gallery-actions">
			<button type="button" class="button button-primary bd-media-field__add"><?php esc_html_e( 'Seç', 'baydemir' ); ?></button>
			<button type="button" class="button bd-media-field__clear"><?php esc_html_e( 'Kaldır', 'baydemir' ); ?></button>
		</p>
	</div>
	<?php
}

/**
 * Contact settings UI.
 */
function baydemir_render_contact_manager(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$saved = isset( $_GET['saved'] ) && '1' === $_GET['saved']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$show  = '1' === (string) baydemir_mod( 'baydemir_whatsapp_show', '1' );
	$hero  = baydemir_contact_hero_settings();
	?>
	<div class="wrap bd-admin bd-contact-manager">
		<div class="bd-admin__header">
			<div>
				<p class="bd-admin__eyebrow"><?php esc_html_e( 'Site Ayarları', 'baydemir' ); ?></p>
				<h1><?php esc_html_e( 'İletişim & WhatsApp', 'baydemir' ); ?></h1>
				<p class="bd-admin__lead">
					<?php esc_html_e( 'İletişim hero görselleri, kartlar, telefon ve WhatsApp ayarlarını buradan yönetin.', 'baydemir' ); ?>
				</p>
			</div>
			<a class="button" href="<?php echo esc_url( home_url( '/iletisim/' ) ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Sayfayı Görüntüle', 'baydemir' ); ?>
			</a>
		</div>

		<?php if ( $saved ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'İletişim ayarları kaydedildi.', 'baydemir' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="bd-admin-panel bd-about-form">
			<input type="hidden" name="action" value="baydemir_save_contact" />
			<?php wp_nonce_field( 'baydemir_save_contact', 'baydemir_contact_nonce' ); ?>

			<div class="bd-pm-section">
				<h3><?php esc_html_e( 'Hero — Ana Görsel', 'baydemir' ); ?></h3>
				<p class="description" style="margin-top:0;">
					<?php esc_html_e( 'Sağdaki büyük bina kutusu. Boş bırakırsanız tema varsayılanı kullanılır.', 'baydemir' ); ?>
				</p>
				<?php
				baydemir_render_contact_media_field(
					'main_image_id',
					(int) $hero['main_image_id'],
					__( 'Ana kutu görseli', 'baydemir' ),
					__( 'Görsel seçilmedi — varsayılan kullanılır', 'baydemir' )
				);
				?>
			</div>

			<div class="bd-pm-section">
				<h3><?php esc_html_e( 'Hero — 3 Kart', 'baydemir' ); ?></h3>
				<p class="description" style="margin-top:0;">
					<?php esc_html_e( 'Her karta ayrı görsel, başlık ve kısa açıklama ekleyin.', 'baydemir' ); ?>
				</p>
				<div class="bd-about-features">
					<?php foreach ( $hero['cards'] as $i => $card ) : ?>
						<div class="bd-about-feature">
							<span class="bd-about-feature__index"><?php echo esc_html( sprintf( __( 'Kart %d', 'baydemir' ), $i + 1 ) ); ?></span>
							<?php
							baydemir_render_contact_media_field(
								'cards[' . $i . '][image_id]',
								(int) $card['image_id'],
								__( 'Kart görseli', 'baydemir' ),
								__( 'Görsel seçilmedi — varsayılan kullanılır', 'baydemir' )
							);
							?>
							<p>
								<label for="baydemir_card_title_<?php echo esc_attr( (string) $i ); ?>"><?php esc_html_e( 'Başlık', 'baydemir' ); ?></label>
								<input type="text" id="baydemir_card_title_<?php echo esc_attr( (string) $i ); ?>" name="cards[<?php echo esc_attr( (string) $i ); ?>][title]" value="<?php echo esc_attr( $card['title'] ); ?>" />
							</p>
							<p>
								<label for="baydemir_card_text_<?php echo esc_attr( (string) $i ); ?>"><?php esc_html_e( 'Açıklama', 'baydemir' ); ?></label>
								<textarea id="baydemir_card_text_<?php echo esc_attr( (string) $i ); ?>" name="cards[<?php echo esc_attr( (string) $i ); ?>][text]" rows="2"><?php echo esc_textarea( $card['text'] ); ?></textarea>
							</p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="bd-pm-section">
				<h3><?php esc_html_e( 'İletişim Bilgileri', 'baydemir' ); ?></h3>
				<p>
					<label for="baydemir_phone"><strong><?php esc_html_e( 'Telefon', 'baydemir' ); ?></strong></label>
					<input type="text" class="regular-text" id="baydemir_phone" name="phone" value="<?php echo esc_attr( baydemir_phone() ); ?>" />
				</p>
				<p>
					<label for="baydemir_email"><strong><?php esc_html_e( 'E-posta', 'baydemir' ); ?></strong></label>
					<input type="email" class="regular-text" id="baydemir_email" name="email" value="<?php echo esc_attr( baydemir_email() ); ?>" />
				</p>
				<p>
					<label for="baydemir_address"><strong><?php esc_html_e( 'Adres', 'baydemir' ); ?></strong></label>
					<textarea class="large-text" id="baydemir_address" name="address" rows="3"><?php echo esc_textarea( baydemir_address() ); ?></textarea>
				</p>
				<p>
					<label for="baydemir_hours"><strong><?php esc_html_e( 'Çalışma Saatleri', 'baydemir' ); ?></strong></label>
					<input type="text" class="large-text" id="baydemir_hours" name="hours" value="<?php echo esc_attr( baydemir_hours() ); ?>" />
				</p>
			</div>

			<div class="bd-pm-section">
				<h3><?php esc_html_e( 'WhatsApp', 'baydemir' ); ?></h3>
				<p>
					<label for="baydemir_whatsapp"><strong><?php esc_html_e( 'WhatsApp Numarası', 'baydemir' ); ?></strong></label>
					<input type="text" class="regular-text" id="baydemir_whatsapp" name="whatsapp" value="<?php echo esc_attr( baydemir_whatsapp() ); ?>" placeholder="905302314000" />
					<span class="description"><?php esc_html_e( 'Ülke koduyla yazın (ör. 905302314000). Boş bırakırsanız buton görünmez.', 'baydemir' ); ?></span>
				</p>
				<p>
					<label class="bd-meta-switch" for="baydemir_whatsapp_show">
						<input type="checkbox" id="baydemir_whatsapp_show" name="whatsapp_show" value="1" <?php checked( $show ); ?> />
						<span class="bd-meta-switch__text">
							<strong><?php esc_html_e( 'Yüzen WhatsApp butonunu göster', 'baydemir' ); ?></strong>
							<span><?php esc_html_e( 'Kapatırsanız sitedeki yeşil yüzen ikon gizlenir.', 'baydemir' ); ?></span>
						</span>
					</label>
				</p>
			</div>

			<p class="submit">
				<button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Kaydet', 'baydemir' ); ?></button>
			</p>
		</form>
	</div>
	<?php
}

/**
 * Save contact theme mods + hero images.
 */
function baydemir_handle_save_contact(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Yetkiniz yok.', 'baydemir' ) );
	}
	check_admin_referer( 'baydemir_save_contact', 'baydemir_contact_nonce' );

	$fields = array(
		'phone'    => 'baydemir_phone',
		'email'    => 'baydemir_email',
		'address'  => 'baydemir_address',
		'hours'    => 'baydemir_hours',
		'whatsapp' => 'baydemir_whatsapp',
	);

	foreach ( $fields as $post_key => $mod_key ) {
		$raw = isset( $_POST[ $post_key ] ) ? wp_unslash( (string) $_POST[ $post_key ] ) : '';
		set_theme_mod( $mod_key, sanitize_text_field( $raw ) );
	}

	set_theme_mod( 'baydemir_whatsapp_show', isset( $_POST['whatsapp_show'] ) ? '1' : '' );

	$defaults = baydemir_contact_hero_default_cards();
	$raw_cards = isset( $_POST['cards'] ) && is_array( $_POST['cards'] ) ? wp_unslash( $_POST['cards'] ) : array();
	$cards     = array();
	foreach ( $defaults as $i => $default ) {
		$row     = isset( $raw_cards[ $i ] ) && is_array( $raw_cards[ $i ] ) ? $raw_cards[ $i ] : array();
		$cards[] = array(
			'title'    => sanitize_text_field( (string) ( $row['title'] ?? $default['title'] ) ),
			'text'     => sanitize_textarea_field( (string) ( $row['text'] ?? $default['text'] ) ),
			'image_id' => absint( $row['image_id'] ?? 0 ),
		);
	}

	update_option(
		'baydemir_contact_hero',
		array(
			'main_image_id' => absint( $_POST['main_image_id'] ?? 0 ),
			'cards'         => $cards,
		),
		false
	);

	wp_safe_redirect( admin_url( 'admin.php?page=baydemir-contact&saved=1' ) );
	exit;
}
