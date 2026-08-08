<?php
/**
 * Homepage testimonials manager.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'baydemir_register_testimonials_menu', 23 );
add_action( 'admin_enqueue_scripts', 'baydemir_enqueue_testimonials_manager_assets' );
add_action( 'admin_post_baydemir_save_testimonials', 'baydemir_handle_save_testimonials' );

/**
 * Max testimonials allowed in the panel.
 */
function baydemir_testimonials_max(): int {
	return 20;
}

/**
 * Icons allowed for testimonial project badges.
 *
 * @return array<string, string>
 */
function baydemir_testimonial_project_icons(): array {
	return array(
		'home'     => __( 'Ev', 'baydemir' ),
		'house'    => __( 'Villa / Ev', 'baydemir' ),
		'building' => __( 'Bina', 'baydemir' ),
		'shop'     => __( 'Mağaza / Ticari', 'baydemir' ),
		'crane'    => __( 'İnşaat', 'baydemir' ),
	);
}

/**
 * Register submenu.
 */
function baydemir_register_testimonials_menu(): void {
	add_submenu_page(
		'baydemir-panel',
		__( 'Müşteri Yorumları', 'baydemir' ),
		__( 'Müşteri Yorumları', 'baydemir' ),
		'edit_theme_options',
		'baydemir-testimonials',
		'baydemir_render_testimonials_manager'
	);
}

/**
 * Assets for testimonials manager.
 */
function baydemir_enqueue_testimonials_manager_assets( string $hook ): void {
	if ( 'baydemir_page_baydemir-testimonials' !== $hook ) {
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
				'coverTitle'      => __( 'Görsel Seç', 'baydemir' ),
				'coverButton'     => __( 'Seç', 'baydemir' ),
				'tmCard'          => __( 'Kart %d', 'baydemir' ),
				'tmMax'           => __( 'En fazla %d yorum ekleyebilirsiniz.', 'baydemir' ),
				'tmRemoveConfirm' => __( 'Bu yorumu kaldırmak istiyor musunuz?', 'baydemir' ),
			),
			'tmMax' => baydemir_testimonials_max(),
		)
	);
}

/**
 * Render one admin card (used for loop + JS template).
 *
 * @param int                  $i     Index.
 * @param array<string, mixed> $item  Item data.
 * @param array<string, string> $icons Icon map.
 * @param bool                 $tpl   Whether this is a hidden template.
 */
function baydemir_render_testimonial_admin_card( int $i, array $item, array $icons, bool $tpl = false ): void {
	$avatar_url = ! empty( $item['avatar_id'] ) ? wp_get_attachment_image_url( (int) $item['avatar_id'], 'thumbnail' ) : '';
	$image_url  = ! empty( $item['image_id'] ) ? wp_get_attachment_image_url( (int) $item['image_id'], 'medium' ) : '';
	$index_attr = $tpl ? '__INDEX__' : (string) $i;
	?>
	<div class="bd-pm-section bd-testimonial-admin" data-tm-card<?php echo $tpl ? ' data-tm-template hidden' : ''; ?>>
		<div class="bd-testimonial-admin__head">
			<h3 class="bd-testimonial-admin__title">
				<?php
				echo esc_html(
					$tpl
						? __( 'Kart', 'baydemir' )
						: sprintf( /* translators: %d: card number */ __( 'Kart %d', 'baydemir' ), $i + 1 )
				);
				?>
			</h3>
			<button type="button" class="button-link-delete bd-testimonial-admin__remove" data-tm-remove>
				<?php esc_html_e( 'Kartı Sil', 'baydemir' ); ?>
			</button>
		</div>
		<div class="bd-about-features">
			<div class="bd-about-feature">
				<p>
					<label><?php esc_html_e( 'İsim', 'baydemir' ); ?></label>
					<input type="text" name="items[<?php echo esc_attr( $index_attr ); ?>][name]" value="<?php echo esc_attr( (string) $item['name'] ); ?>" />
				</p>
				<p>
					<label><?php esc_html_e( 'Ünvan', 'baydemir' ); ?></label>
					<input type="text" name="items[<?php echo esc_attr( $index_attr ); ?>][role]" value="<?php echo esc_attr( (string) $item['role'] ); ?>" />
				</p>
				<p>
					<label><?php esc_html_e( 'Puan (1–5)', 'baydemir' ); ?></label>
					<select name="items[<?php echo esc_attr( $index_attr ); ?>][rating]">
						<?php for ( $r = 5; $r >= 1; $r-- ) : ?>
							<option value="<?php echo esc_attr( (string) $r ); ?>" <?php selected( (int) $item['rating'], $r ); ?>><?php echo esc_html( (string) $r ); ?></option>
						<?php endfor; ?>
					</select>
				</p>
				<p class="bd-meta-full">
					<label><?php esc_html_e( 'Yorum', 'baydemir' ); ?></label>
					<textarea name="items[<?php echo esc_attr( $index_attr ); ?>][quote]" rows="3"><?php echo esc_textarea( (string) $item['quote'] ); ?></textarea>
				</p>
			</div>
			<div class="bd-about-feature">
				<p>
					<label><?php esc_html_e( 'Proje ikonu', 'baydemir' ); ?></label>
					<select name="items[<?php echo esc_attr( $index_attr ); ?>][project_icon]">
						<?php foreach ( $icons as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $item['project_icon'], $key ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p>
					<label><?php esc_html_e( 'Proje tipi', 'baydemir' ); ?></label>
					<input type="text" name="items[<?php echo esc_attr( $index_attr ); ?>][project_type]" value="<?php echo esc_attr( (string) $item['project_type'] ); ?>" />
				</p>
				<p>
					<label><?php esc_html_e( 'Proje adı', 'baydemir' ); ?></label>
					<input type="text" name="items[<?php echo esc_attr( $index_attr ); ?>][project_name]" value="<?php echo esc_attr( (string) $item['project_name'] ); ?>" />
				</p>
			</div>
		</div>

		<div class="bd-testimonial-admin__media">
			<div class="bd-media-field<?php echo ! empty( $item['avatar_id'] ) ? '' : ' is-empty'; ?>" data-bd-media>
				<strong><?php esc_html_e( 'Profil fotoğrafı', 'baydemir' ); ?></strong>
				<input type="hidden" class="bd-media-field__input" name="items[<?php echo esc_attr( $index_attr ); ?>][avatar_id]" value="<?php echo esc_attr( (string) (int) $item['avatar_id'] ); ?>" />
				<div class="bd-media-field__preview">
					<?php if ( $avatar_url ) : ?>
						<img src="<?php echo esc_url( $avatar_url ); ?>" alt="" />
					<?php endif; ?>
				</div>
				<div class="bd-media-field__empty"><?php esc_html_e( 'Fotoğraf seçilmedi', 'baydemir' ); ?></div>
				<p class="bd-gallery-actions">
					<button type="button" class="button button-primary bd-media-field__add"><?php esc_html_e( 'Seç', 'baydemir' ); ?></button>
					<button type="button" class="button bd-media-field__clear"><?php esc_html_e( 'Kaldır', 'baydemir' ); ?></button>
				</p>
			</div>
			<div class="bd-media-field<?php echo ! empty( $item['image_id'] ) ? '' : ' is-empty'; ?>" data-bd-media>
				<strong><?php esc_html_e( 'Proje görseli', 'baydemir' ); ?></strong>
				<input type="hidden" class="bd-media-field__input" name="items[<?php echo esc_attr( $index_attr ); ?>][image_id]" value="<?php echo esc_attr( (string) (int) $item['image_id'] ); ?>" />
				<div class="bd-media-field__preview">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="" />
					<?php endif; ?>
				</div>
				<div class="bd-media-field__empty"><?php esc_html_e( 'Görsel seçilmedi — varsayılan kullanılır', 'baydemir' ); ?></div>
				<p class="bd-gallery-actions">
					<button type="button" class="button button-primary bd-media-field__add"><?php esc_html_e( 'Seç', 'baydemir' ); ?></button>
					<button type="button" class="button bd-media-field__clear"><?php esc_html_e( 'Kaldır', 'baydemir' ); ?></button>
				</p>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render testimonials editor.
 */
function baydemir_render_testimonials_manager(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$saved    = isset( $_GET['saved'] ) && '1' === $_GET['saved']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$settings = baydemir_testimonials_settings();
	$icons    = baydemir_testimonial_project_icons();
	$max      = baydemir_testimonials_max();
	?>
	<div class="wrap bd-admin bd-testimonials-manager">
		<div class="bd-admin__header">
			<div>
				<p class="bd-admin__eyebrow"><?php esc_html_e( 'Ana Sayfa', 'baydemir' ); ?></p>
				<h1><?php esc_html_e( 'Müşteri Yorumları', 'baydemir' ); ?></h1>
				<p class="bd-admin__lead">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %d: max testimonials */
							__( 'Anasayfadaki müşteri yorumlarını buradan yönetin. İstediğiniz kadar ekleyebilirsiniz (en fazla %d). Boş isim = kart gizlenir.', 'baydemir' ),
							$max
						)
					);
					?>
				</p>
			</div>
			<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Ana Sayfayı Görüntüle', 'baydemir' ); ?>
			</a>
		</div>

		<?php if ( $saved ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Müşteri yorumları kaydedildi.', 'baydemir' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="bd-admin-panel bd-about-form" data-tm-form>
			<input type="hidden" name="action" value="baydemir_save_testimonials" />
			<?php wp_nonce_field( 'baydemir_save_testimonials', 'baydemir_testimonials_nonce' ); ?>

			<div class="bd-pm-section">
				<h3><?php esc_html_e( 'Bölüm Başlığı', 'baydemir' ); ?></h3>
				<p>
					<label for="baydemir_tm_title"><strong><?php esc_html_e( 'Başlık (beyaz kısım)', 'baydemir' ); ?></strong></label>
					<input type="text" class="large-text" id="baydemir_tm_title" name="title" value="<?php echo esc_attr( $settings['title'] ); ?>" />
				</p>
				<p>
					<label for="baydemir_tm_accent"><strong><?php esc_html_e( 'Vurgu kelime (mavi)', 'baydemir' ); ?></strong></label>
					<input type="text" class="large-text" id="baydemir_tm_accent" name="title_accent" value="<?php echo esc_attr( $settings['title_accent'] ); ?>" />
				</p>
				<p>
					<label for="baydemir_tm_subtitle"><strong><?php esc_html_e( 'Alt yazı', 'baydemir' ); ?></strong></label>
					<input type="text" class="large-text" id="baydemir_tm_subtitle" name="subtitle" value="<?php echo esc_attr( $settings['subtitle'] ); ?>" />
				</p>
			</div>

			<div id="baydemir-tm-cards" data-tm-list>
				<?php foreach ( $settings['items'] as $i => $item ) : ?>
					<?php baydemir_render_testimonial_admin_card( (int) $i, $item, $icons ); ?>
				<?php endforeach; ?>
			</div>

			<?php baydemir_render_testimonial_admin_card( 0, baydemir_testimonial_blank_item(), $icons, true ); ?>

			<p class="bd-testimonial-admin__actions">
				<button type="button" class="button button-secondary" data-tm-add>
					<?php esc_html_e( '+ Yeni Yorum Ekle', 'baydemir' ); ?>
				</button>
			</p>

			<p class="submit">
				<button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Kaydet', 'baydemir' ); ?></button>
			</p>
		</form>
	</div>
	<?php
}

/**
 * Save testimonials.
 */
function baydemir_handle_save_testimonials(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Yetkiniz yok.', 'baydemir' ) );
	}
	check_admin_referer( 'baydemir_save_testimonials', 'baydemir_testimonials_nonce' );

	$defaults  = baydemir_testimonials_defaults();
	$raw_items = isset( $_POST['items'] ) && is_array( $_POST['items'] ) ? wp_unslash( $_POST['items'] ) : array();
	$items     = array();
	$max       = baydemir_testimonials_max();

	foreach ( $raw_items as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		// Skip the JS template placeholder if somehow posted.
		if ( isset( $row['name'] ) && is_string( $row['name'] ) && false !== strpos( $row['name'], '__INDEX__' ) ) {
			continue;
		}
		$items[] = baydemir_normalize_testimonial_item( $row );
		if ( count( $items ) >= $max ) {
			break;
		}
	}

	if ( ! $items ) {
		$items = array( baydemir_testimonial_blank_item() );
	}

	$settings = array(
		'title'        => isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : $defaults['title'],
		'title_accent' => isset( $_POST['title_accent'] ) ? sanitize_text_field( wp_unslash( $_POST['title_accent'] ) ) : $defaults['title_accent'],
		'subtitle'     => isset( $_POST['subtitle'] ) ? sanitize_text_field( wp_unslash( $_POST['subtitle'] ) ) : $defaults['subtitle'],
		'items'        => array_values( $items ),
	);

	update_option( 'baydemir_testimonials', $settings, false );

	wp_safe_redirect( admin_url( 'admin.php?page=baydemir-testimonials&saved=1' ) );
	exit;
}
