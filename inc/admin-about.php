<?php
/**
 * Hakkımızda content manager.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'baydemir_register_about_menu', 21 );
add_action( 'admin_enqueue_scripts', 'baydemir_enqueue_about_manager_assets' );
add_action( 'admin_post_baydemir_save_about', 'baydemir_handle_save_about' );

/**
 * Icons allowed for about feature cards.
 *
 * @return array<string, string>
 */
function baydemir_about_feature_icons(): array {
	return array(
		'shield'     => __( 'Kalkan', 'baydemir' ),
		'blueprint'  => __( 'Proje', 'baydemir' ),
		'brick'      => __( 'Malzeme', 'baydemir' ),
		'helmet'     => __( 'Kask', 'baydemir' ),
		'foundation' => __( 'Temel', 'baydemir' ),
		'leaf'       => __( 'Yaprak', 'baydemir' ),
		'star'       => __( 'Yıldız', 'baydemir' ),
		'award'      => __( 'Ödül', 'baydemir' ),
		'clock'      => __( 'Saat', 'baydemir' ),
		'users'      => __( 'Ekip', 'baydemir' ),
		'building'   => __( 'Bina', 'baydemir' ),
		'home'       => __( 'Ev', 'baydemir' ),
		'crane'      => __( 'Vinç', 'baydemir' ),
		'pen'        => __( 'Tasarım', 'baydemir' ),
		'clipboard'  => __( 'Liste', 'baydemir' ),
		'gear'       => __( 'Ayar', 'baydemir' ),
		'key'        => __( 'Anahtar', 'baydemir' ),
		'shop'       => __( 'Ticari', 'baydemir' ),
	);
}

/**
 * Register submenu.
 */
function baydemir_register_about_menu(): void {
	add_submenu_page(
		'baydemir-panel',
		__( 'Hakkımızda', 'baydemir' ),
		__( 'Hakkımızda', 'baydemir' ),
		'edit_theme_options',
		'baydemir-about',
		'baydemir_render_about_manager'
	);
}

/**
 * Assets for about manager.
 */
function baydemir_enqueue_about_manager_assets( string $hook ): void {
	if ( 'baydemir_page_baydemir-about' !== $hook ) {
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
				'coverTitle'  => __( 'Hakkımızda Görseli', 'baydemir' ),
				'coverButton' => __( 'Seç', 'baydemir' ),
			),
		)
	);
}

/**
 * Render about editor.
 */
function baydemir_render_about_manager(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$saved    = isset( $_GET['saved'] ) && '1' === $_GET['saved']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$settings = baydemir_about_settings();
	$icons    = baydemir_about_feature_icons();
	$image_id = (int) $settings['image_id'];
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
	$story_id  = (int) $settings['story_image_id'];
	?>
	<div class="wrap bd-admin bd-about-manager">
		<div class="bd-admin__header">
			<div>
				<p class="bd-admin__eyebrow"><?php esc_html_e( 'Sayfa İçeriği', 'baydemir' ); ?></p>
				<h1><?php esc_html_e( 'Hakkımızda', 'baydemir' ); ?></h1>
				<p class="bd-admin__lead">
					<?php esc_html_e( 'Hakkımızda sayfasındaki metinleri, görselleri ve özellik kartlarını buradan düzenleyin.', 'baydemir' ); ?>
				</p>
			</div>
			<a class="button" href="<?php echo esc_url( home_url( '/hakkimizda/' ) ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Sayfayı Görüntüle', 'baydemir' ); ?>
			</a>
		</div>

		<?php if ( $saved ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Hakkımızda içeriği kaydedildi.', 'baydemir' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="bd-admin-panel bd-about-form">
			<input type="hidden" name="action" value="baydemir_save_about" />
			<?php wp_nonce_field( 'baydemir_save_about', 'baydemir_about_nonce' ); ?>

			<div class="bd-pm-section">
				<h3><?php esc_html_e( 'Üst Bölüm', 'baydemir' ); ?></h3>
				<p>
					<label for="baydemir_about_page_title"><strong><?php esc_html_e( 'Sayfa Başlığı', 'baydemir' ); ?></strong></label>
					<input type="text" class="large-text" id="baydemir_about_page_title" name="page_title" value="<?php echo esc_attr( $settings['page_title'] ); ?>" />
				</p>
				<p>
					<label for="baydemir_about_eyebrow"><strong><?php esc_html_e( 'Üst Etiket (ör. Biz Kimiz?)', 'baydemir' ); ?></strong></label>
					<input type="text" class="large-text" id="baydemir_about_eyebrow" name="eyebrow" value="<?php echo esc_attr( $settings['eyebrow'] ); ?>" />
				</p>
				<p>
					<label for="baydemir_about_content"><strong><?php esc_html_e( 'Ana Metin', 'baydemir' ); ?></strong></label>
					<textarea id="baydemir_about_content" name="content" rows="14" class="large-text"><?php echo esc_textarea( $settings['content'] ); ?></textarea>
					<span class="description"><?php esc_html_e( 'Paragrafları boş satırla ayırın.', 'baydemir' ); ?></span>
				</p>
			</div>

			<div class="bd-pm-section">
				<h3><?php esc_html_e( 'Hakkımızda Sayfası — Yan Görsel', 'baydemir' ); ?></h3>
				<p class="description" style="margin-top:0;"><?php esc_html_e( 'Sadece /hakkimizda/ sayfası. Ana sayfa Biz Kimiz görseli Özelleştir → Ana Sayfa bölümünden ayarlanır.', 'baydemir' ); ?></p>
				<input type="hidden" id="baydemir_cover" name="image_id" value="<?php echo esc_attr( (string) $image_id ); ?>" />
				<div class="bd-cover<?php echo $image_id ? '' : ' is-empty'; ?>" id="baydemir-cover-wrap">
					<div class="bd-cover__preview" id="baydemir-cover-preview">
						<?php if ( $image_url ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="" />
						<?php endif; ?>
					</div>
					<div class="bd-cover__empty"><?php esc_html_e( 'Görsel seçilmedi — varsayılan kullanılır', 'baydemir' ); ?></div>
					<p class="bd-gallery-actions">
						<button type="button" class="button button-primary" id="baydemir-cover-add"><?php esc_html_e( 'Görsel Seç', 'baydemir' ); ?></button>
						<button type="button" class="button" id="baydemir-cover-clear"><?php esc_html_e( 'Kaldır', 'baydemir' ); ?></button>
					</p>
				</div>
			</div>

			<div class="bd-pm-section">
				<h3><?php esc_html_e( 'Özellik Kartları', 'baydemir' ); ?></h3>
				<p class="description" style="margin-top:0;"><?php esc_html_e( 'Hakkımızda sayfasındaki 6 kart. Boş başlık = kart gizlenir.', 'baydemir' ); ?></p>
				<div class="bd-about-features">
					<?php foreach ( $settings['features'] as $i => $feature ) : ?>
						<div class="bd-about-feature">
							<span class="bd-about-feature__index"><?php echo esc_html( sprintf( __( 'Kart %d', 'baydemir' ), $i + 1 ) ); ?></span>
							<p>
								<label for="baydemir_feature_icon_<?php echo esc_attr( (string) $i ); ?>"><?php esc_html_e( 'İkon', 'baydemir' ); ?></label>
								<select id="baydemir_feature_icon_<?php echo esc_attr( (string) $i ); ?>" name="features[<?php echo esc_attr( (string) $i ); ?>][icon]">
									<?php foreach ( $icons as $key => $label ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $feature['icon'], $key ); ?>><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
							</p>
							<p>
								<label for="baydemir_feature_title_<?php echo esc_attr( (string) $i ); ?>"><?php esc_html_e( 'Başlık', 'baydemir' ); ?></label>
								<input type="text" id="baydemir_feature_title_<?php echo esc_attr( (string) $i ); ?>" name="features[<?php echo esc_attr( (string) $i ); ?>][title]" value="<?php echo esc_attr( $feature['title'] ); ?>" />
							</p>
							<p class="bd-meta-full">
								<label for="baydemir_feature_text_<?php echo esc_attr( (string) $i ); ?>"><?php esc_html_e( 'Açıklama', 'baydemir' ); ?></label>
								<textarea id="baydemir_feature_text_<?php echo esc_attr( (string) $i ); ?>" name="features[<?php echo esc_attr( (string) $i ); ?>][text]" rows="2"><?php echo esc_textarea( $feature['text'] ); ?></textarea>
							</p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="bd-pm-section">
				<h3><?php esc_html_e( 'Alt Bölüm (Metin + Görsel)', 'baydemir' ); ?></h3>
				<p class="description" style="margin-top:0;"><?php esc_html_e( 'Kartların altında ve ana sayfada Biz Kimiz altında görünen ek bölüm. Başlık ve metin boşsa bölüm gizlenir.', 'baydemir' ); ?></p>
				<p>
					<label for="baydemir_story_title"><strong><?php esc_html_e( 'Başlık', 'baydemir' ); ?></strong></label>
					<input type="text" class="large-text" id="baydemir_story_title" name="story_title" value="<?php echo esc_attr( $settings['story_title'] ); ?>" />
				</p>
				<p>
					<label for="baydemir_story_content"><strong><?php esc_html_e( 'Metin', 'baydemir' ); ?></strong></label>
					<textarea id="baydemir_story_content" name="story_content" rows="8" class="large-text"><?php echo esc_textarea( $settings['story_content'] ); ?></textarea>
					<span class="description"><?php esc_html_e( 'Paragrafları boş satırla ayırın.', 'baydemir' ); ?></span>
				</p>
				<?php
				baydemir_render_contact_media_field(
					'story_image_id',
					$story_id,
					__( 'Bölüm görseli', 'baydemir' ),
					__( 'Görsel seçilmedi — varsayılan kullanılır', 'baydemir' )
				);
				?>
			</div>

			<p class="submit">
				<button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Kaydet', 'baydemir' ); ?></button>
			</p>
		</form>
	</div>
	<?php
}

/**
 * Save about settings.
 */
function baydemir_handle_save_about(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Yetkiniz yok.', 'baydemir' ) );
	}
	check_admin_referer( 'baydemir_save_about', 'baydemir_about_nonce' );

	$allowed_icons = array_keys( baydemir_about_feature_icons() );
	$features      = array();
	$raw_features  = isset( $_POST['features'] ) && is_array( $_POST['features'] ) ? wp_unslash( $_POST['features'] ) : array();

	foreach ( baydemir_about_default_features() as $i => $default ) {
		$row   = isset( $raw_features[ $i ] ) && is_array( $raw_features[ $i ] ) ? $raw_features[ $i ] : array();
		$icon  = isset( $row['icon'] ) ? sanitize_key( (string) $row['icon'] ) : $default['icon'];
		if ( ! in_array( $icon, $allowed_icons, true ) ) {
			$icon = $default['icon'];
		}
		$features[] = array(
			'icon'  => $icon,
			'title' => isset( $row['title'] ) ? sanitize_text_field( (string) $row['title'] ) : '',
			'text'  => isset( $row['text'] ) ? sanitize_textarea_field( (string) $row['text'] ) : '',
		);
	}

	$page_title      = isset( $_POST['page_title'] ) ? sanitize_text_field( wp_unslash( $_POST['page_title'] ) ) : 'Hakkımızda';
	$eyebrow         = isset( $_POST['eyebrow'] ) ? sanitize_text_field( wp_unslash( $_POST['eyebrow'] ) ) : '';
	$content         = isset( $_POST['content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) : '';
	$image_id        = isset( $_POST['image_id'] ) ? absint( $_POST['image_id'] ) : 0;
	$story_title     = isset( $_POST['story_title'] ) ? sanitize_text_field( wp_unslash( $_POST['story_title'] ) ) : '';
	$story_content   = isset( $_POST['story_content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['story_content'] ) ) : '';
	$story_image_id  = isset( $_POST['story_image_id'] ) ? absint( $_POST['story_image_id'] ) : 0;

	$settings = array(
		'page_title'     => $page_title ?: 'Hakkımızda',
		'eyebrow'        => $eyebrow,
		'content'        => $content,
		'image_id'       => $image_id,
		'story_title'    => $story_title,
		'story_content'  => $story_content,
		'story_image_id' => $story_image_id,
		'features'       => $features,
	);

	update_option( 'baydemir_about', $settings, false );

	/* Keep homepage / customizer eyebrow in sync. */
	if ( $eyebrow ) {
		set_theme_mod( 'baydemir_about_title', $eyebrow );
	}

	/* Sync WP page title + content when the hakkımızda page exists. */
	$page = get_page_by_path( 'hakkimizda' );
	if ( $page instanceof WP_Post ) {
		wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_title'   => $settings['page_title'],
				'post_content' => $content,
			)
		);
		if ( $image_id ) {
			set_post_thumbnail( $page->ID, $image_id );
		}
	}

	wp_safe_redirect( admin_url( 'admin.php?page=baydemir-about&saved=1' ) );
	exit;
}
