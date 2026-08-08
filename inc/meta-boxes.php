<?php
/**
 * Project & service meta boxes.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes', 'baydemir_register_meta_boxes' );
add_action( 'save_post_project', 'baydemir_save_project_meta' );
add_action( 'save_post_service', 'baydemir_save_service_meta' );
add_action( 'admin_enqueue_scripts', 'baydemir_admin_assets' );
add_action( 'admin_notices', 'baydemir_project_featured_image_notice' );

/**
 * Service icon options (key => label).
 *
 * @return array<string, string>
 */
function baydemir_service_icon_options(): array {
	return array(
		'building'  => __( 'Bina', 'baydemir' ),
		'home'      => __( 'Ev', 'baydemir' ),
		'shop'      => __( 'Ticari', 'baydemir' ),
		'crane'     => __( 'Vinç', 'baydemir' ),
		'helmet'    => __( 'Kask', 'baydemir' ),
		'pen'       => __( 'Tasarım', 'baydemir' ),
		'users'     => __( 'Ekip', 'baydemir' ),
		'clipboard' => __( 'Liste', 'baydemir' ),
		'gear'      => __( 'Ayar', 'baydemir' ),
		'key'       => __( 'Anahtar', 'baydemir' ),
		'star'      => __( 'Yıldız', 'baydemir' ),
		'shield'    => __( 'Kalkan', 'baydemir' ),
	);
}

/**
 * Register meta boxes.
 */
function baydemir_register_meta_boxes(): void {
	add_meta_box(
		'baydemir_project_details',
		__( 'Proje Detayları', 'baydemir' ),
		'baydemir_render_project_meta_box',
		'project',
		'normal',
		'high'
	);

	add_meta_box(
		'baydemir_project_gallery',
		__( 'Proje Galerisi', 'baydemir' ),
		'baydemir_render_gallery_meta_box',
		'project',
		'normal',
		'default'
	);

	add_meta_box(
		'baydemir_service_details',
		__( 'Hizmet Detayları', 'baydemir' ),
		'baydemir_render_service_meta_box',
		'service',
		'side',
		'default'
	);
}

/**
 * Project details fields.
 */
function baydemir_render_project_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'baydemir_project_meta', 'baydemir_project_nonce' );

	$location  = (string) get_post_meta( $post->ID, '_baydemir_location', true );
	$type      = (string) get_post_meta( $post->ID, '_baydemir_type', true );
	$delivery  = (string) get_post_meta( $post->ID, '_baydemir_delivery', true );
	$units     = (string) get_post_meta( $post->ID, '_baydemir_units', true );
	$status    = (string) get_post_meta( $post->ID, '_baydemir_status', true );
	$featured  = (string) get_post_meta( $post->ID, '_baydemir_featured', true );
	$show_cta  = baydemir_project_show_cta( $post->ID );
	$video_url = (string) get_post_meta( $post->ID, '_baydemir_video_url', true );

	if ( ! $status ) {
		$status = 'completed';
	}
	?>
	<div class="bd-meta-panel">
		<div class="bd-meta-grid">
			<p>
				<label for="baydemir_location"><?php esc_html_e( 'Lokasyon', 'baydemir' ); ?></label>
				<input type="text" id="baydemir_location" name="baydemir_location" value="<?php echo esc_attr( $location ); ?>" placeholder="Çekmeköy / İstanbul" />
			</p>
			<p>
				<label for="baydemir_type"><?php esc_html_e( 'Proje Tipi', 'baydemir' ); ?></label>
				<input type="text" id="baydemir_type" name="baydemir_type" value="<?php echo esc_attr( $type ); ?>" placeholder="Konut Projesi" />
			</p>
			<p>
				<label for="baydemir_delivery"><?php esc_html_e( 'Teslim Tarihi', 'baydemir' ); ?></label>
				<input type="text" id="baydemir_delivery" name="baydemir_delivery" value="<?php echo esc_attr( $delivery ); ?>" placeholder="2025" />
			</p>
			<p>
				<label for="baydemir_units"><?php esc_html_e( 'Daire / Ünite Sayısı', 'baydemir' ); ?></label>
				<input type="text" id="baydemir_units" name="baydemir_units" value="<?php echo esc_attr( $units ); ?>" placeholder="96" />
			</p>
			<p>
				<label for="baydemir_status"><?php esc_html_e( 'Durum', 'baydemir' ); ?></label>
				<select id="baydemir_status" name="baydemir_status">
					<option value="completed" <?php selected( $status, 'completed' ); ?>><?php esc_html_e( 'Tamamlandı', 'baydemir' ); ?></option>
					<option value="ongoing" <?php selected( $status, 'ongoing' ); ?>><?php esc_html_e( 'Devam Ediyor', 'baydemir' ); ?></option>
				</select>
			</p>
			<p>
				<label class="bd-meta-switch" for="baydemir_featured">
					<input type="checkbox" id="baydemir_featured" name="baydemir_featured" value="1" <?php checked( $featured, '1' ); ?> />
					<span class="bd-meta-switch__text">
						<strong><?php esc_html_e( 'Ana sayfada öne çıkar', 'baydemir' ); ?></strong>
						<span><?php esc_html_e( 'Ana sayfa öne çıkan satırında gösterilir.', 'baydemir' ); ?></span>
					</span>
				</label>
			</p>
			<p>
				<label class="bd-meta-switch" for="baydemir_show_cta">
					<input type="checkbox" id="baydemir_show_cta" name="baydemir_show_cta" value="1" <?php checked( $show_cta ); ?> />
					<span class="bd-meta-switch__text">
						<strong><?php esc_html_e( 'Teklif Al butonunu göster', 'baydemir' ); ?></strong>
						<span><?php esc_html_e( 'Proje detay sayfasındaki Teklif Al butonunu açar veya kapatır.', 'baydemir' ); ?></span>
					</span>
				</label>
			</p>
			<p class="bd-meta-full">
				<label for="baydemir_video_url"><?php esc_html_e( 'Video URL (YouTube / Vimeo)', 'baydemir' ); ?></label>
				<input type="url" id="baydemir_video_url" name="baydemir_video_url" value="<?php echo esc_attr( $video_url ); ?>" placeholder="https://www.youtube.com/watch?v=..." />
				<span class="bd-meta-hint"><?php esc_html_e( 'Boş bırakabilirsiniz. Doldurulursa proje detayında video alanı kullanılır.', 'baydemir' ); ?></span>
			</p>
		</div>
	</div>
	<?php
}

/**
 * Gallery meta box.
 */
function baydemir_render_gallery_meta_box( WP_Post $post ): void {
	$ids = (string) get_post_meta( $post->ID, '_baydemir_gallery', true );
	$has = '' !== trim( $ids, ", \t\n\r\0\x0B" );
	?>
	<div class="bd-gallery<?php echo $has ? '' : ' is-empty'; ?>">
		<input type="hidden" id="baydemir_gallery" name="baydemir_gallery" value="<?php echo esc_attr( $ids ); ?>" />
		<div class="bd-gallery-empty">
			<span><?php esc_html_e( 'Henüz galeri görseli yok', 'baydemir' ); ?></span>
			<button type="button" class="button button-primary" id="baydemir-gallery-add-empty"><?php esc_html_e( 'Görsel Ekle', 'baydemir' ); ?></button>
		</div>
		<div id="baydemir-gallery-preview" class="bd-gallery-preview" aria-live="polite"></div>
		<p class="bd-gallery-actions">
			<button type="button" class="button button-primary" id="baydemir-gallery-add"><?php esc_html_e( 'Görsel Ekle', 'baydemir' ); ?></button>
			<button type="button" class="button" id="baydemir-gallery-clear"><?php esc_html_e( 'Temizle', 'baydemir' ); ?></button>
		</p>
		<p class="description"><?php esc_html_e( 'Sürükleyerek sıralayın. Tek görseli kaldırmak için × kullanın. Proje detay sayfasındaki galeri bölümünde görünür.', 'baydemir' ); ?></p>
	</div>
	<?php
}

/**
 * Service meta.
 */
function baydemir_render_service_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'baydemir_service_meta', 'baydemir_service_nonce' );
	$icon    = (string) get_post_meta( $post->ID, '_baydemir_icon', true );
	$options = baydemir_service_icon_options();
	if ( ! $icon || ! isset( $options[ $icon ] ) ) {
		$icon = 'building';
	}
	?>
	<p>
		<strong><?php esc_html_e( 'İkon', 'baydemir' ); ?></strong>
	</p>
	<input type="hidden" id="baydemir_icon" name="baydemir_icon" value="<?php echo esc_attr( $icon ); ?>" />
	<div class="bd-icon-picker" role="listbox" aria-label="<?php esc_attr_e( 'Hizmet ikonu', 'baydemir' ); ?>">
		<?php foreach ( $options as $key => $label ) : ?>
			<button
				type="button"
				class="bd-icon-picker__btn<?php echo $icon === $key ? ' is-selected' : ''; ?>"
				data-icon="<?php echo esc_attr( $key ); ?>"
				role="option"
				aria-selected="<?php echo $icon === $key ? 'true' : 'false'; ?>"
				title="<?php echo esc_attr( $label ); ?>"
			>
				<?php echo baydemir_icon( $key, 'bd-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="label"><?php echo esc_html( $label ); ?></span>
			</button>
		<?php endforeach; ?>
	</div>
	<p class="description" style="margin-top:10px;"><?php esc_html_e( 'Hizmet kartında görünen ikonu seçin.', 'baydemir' ); ?></p>
	<?php
}

/**
 * Soft notice when project has no featured image.
 */
function baydemir_project_featured_image_notice(): void {
	$screen = get_current_screen();
	if ( ! $screen || 'project' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}

	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $post_id ) {
		return;
	}

	if ( has_post_thumbnail( $post_id ) ) {
		return;
	}
	?>
	<div class="notice notice-warning">
		<p><?php esc_html_e( 'Bu projenin kapak görseli yok. Liste ve kart görünümü için öne çıkan görsel eklemeniz önerilir.', 'baydemir' ); ?></p>
	</div>
	<?php
}

/**
 * Save project meta.
 */
function baydemir_save_project_meta( int $post_id ): void {
	if ( ! isset( $_POST['baydemir_project_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['baydemir_project_nonce'] ) ), 'baydemir_project_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'location'  => 'sanitize_text_field',
		'type'      => 'sanitize_text_field',
		'delivery'  => 'sanitize_text_field',
		'units'     => 'sanitize_text_field',
		'status'    => 'sanitize_text_field',
		'video_url' => 'esc_url_raw',
		'gallery'   => 'sanitize_text_field',
	);

	foreach ( $fields as $key => $sanitize ) {
		$raw = $_POST[ 'baydemir_' . $key ] ?? '';
		update_post_meta( $post_id, '_baydemir_' . $key, $sanitize( wp_unslash( (string) $raw ) ) );
	}

	$status = (string) get_post_meta( $post_id, '_baydemir_status', true );
	if ( ! in_array( $status, array( 'completed', 'ongoing' ), true ) ) {
		update_post_meta( $post_id, '_baydemir_status', 'completed' );
	}

	update_post_meta( $post_id, '_baydemir_featured', isset( $_POST['baydemir_featured'] ) ? '1' : '0' );
	update_post_meta( $post_id, '_baydemir_show_cta', isset( $_POST['baydemir_show_cta'] ) ? '1' : '0' );
}

/**
 * Save service meta.
 */
function baydemir_save_service_meta( int $post_id ): void {
	if ( ! isset( $_POST['baydemir_service_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['baydemir_service_nonce'] ) ), 'baydemir_service_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$icon    = isset( $_POST['baydemir_icon'] ) ? sanitize_text_field( wp_unslash( $_POST['baydemir_icon'] ) ) : 'building';
	$options = baydemir_service_icon_options();
	if ( ! isset( $options[ $icon ] ) ) {
		$icon = 'building';
	}
	update_post_meta( $post_id, '_baydemir_icon', $icon );
}

/**
 * Admin assets for project/service edit screens.
 */
function baydemir_admin_assets( string $hook ): void {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->post_type, array( 'project', 'service' ), true ) ) {
		return;
	}

	wp_enqueue_style(
		'baydemir-admin',
		baydemir_asset( 'css/admin.css' ),
		array(),
		BAYDEMIR_VERSION
	);

	if ( 'project' === $screen->post_type ) {
		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	wp_enqueue_script(
		'baydemir-admin',
		baydemir_asset( 'js/admin.js' ),
		'project' === $screen->post_type ? array( 'jquery', 'jquery-ui-sortable' ) : array( 'jquery' ),
		BAYDEMIR_VERSION,
		true
	);

	wp_localize_script(
		'baydemir-admin',
		'baydemirAdmin',
		array(
			'i18n' => array(
				'galleryTitle'  => __( 'Proje Galerisi', 'baydemir' ),
				'galleryButton' => __( 'Seç', 'baydemir' ),
				'removeImage'   => __( 'Kaldır', 'baydemir' ),
			),
		)
	);
}
