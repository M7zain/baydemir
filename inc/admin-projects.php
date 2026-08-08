<?php
/**
 * Inline project manager (no WP post editor required).
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'baydemir_register_projects_menu', 20 );
add_action( 'admin_enqueue_scripts', 'baydemir_enqueue_projects_manager_assets' );
add_action( 'admin_post_baydemir_save_project', 'baydemir_handle_save_project' );
add_action( 'admin_post_baydemir_delete_project', 'baydemir_handle_delete_project' );
add_action( 'wp_ajax_baydemir_reorder_projects', 'baydemir_ajax_reorder_projects' );

/**
 * Submenu under Baydemir.
 */
function baydemir_register_projects_menu(): void {
	add_submenu_page(
		'baydemir-panel',
		__( 'Projeler Yönetimi', 'baydemir' ),
		__( 'Projeler', 'baydemir' ),
		'edit_posts',
		'baydemir-projects',
		'baydemir_render_projects_manager'
	);
}

/**
 * Assets for project manager screen.
 */
function baydemir_enqueue_projects_manager_assets( string $hook ): void {
	if ( 'baydemir_page_baydemir-projects' !== $hook ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );

	wp_enqueue_style(
		'baydemir-admin',
		baydemir_asset( 'css/admin.css' ),
		array(),
		BAYDEMIR_VERSION
	);

	wp_enqueue_script(
		'baydemir-admin',
		baydemir_asset( 'js/admin.js' ),
		array( 'jquery', 'jquery-ui-sortable' ),
		BAYDEMIR_VERSION,
		true
	);

	wp_localize_script(
		'baydemir-admin',
		'baydemirAdmin',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'baydemir_reorder_projects' ),
			'i18n'    => array(
				'galleryTitle'  => __( 'Proje Galerisi', 'baydemir' ),
				'galleryButton' => __( 'Seç', 'baydemir' ),
				'removeImage'   => __( 'Kaldır', 'baydemir' ),
				'coverTitle'    => __( 'Kapak Görseli', 'baydemir' ),
				'coverButton'   => __( 'Seç', 'baydemir' ),
				'orderSaved'    => __( 'Sıra kaydedildi', 'baydemir' ),
				'orderError'    => __( 'Sıra kaydedilemedi', 'baydemir' ),
			),
		)
	);
}

/**
 * Manager page URL helper.
 */
function baydemir_projects_manager_url( array $args = array() ): string {
	return add_query_arg(
		array_merge(
			array( 'page' => 'baydemir-projects' ),
			$args
		),
		admin_url( 'admin.php' )
	);
}

/**
 * Render project manager UI.
 */
function baydemir_render_projects_manager(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$saved   = isset( $_GET['saved'] ) && '1' === $_GET['saved']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$deleted = isset( $_GET['deleted'] ) && '1' === $_GET['deleted']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$is_new  = isset( $_GET['new'] ) && '1' === $_GET['new']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$edit_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$post = null;
	if ( $edit_id ) {
		$candidate = get_post( $edit_id );
		if ( $candidate && 'project' === $candidate->post_type && current_user_can( 'edit_post', $edit_id ) ) {
			$post = $candidate;
		} else {
			$edit_id = 0;
		}
	}

	$show_form = $is_new || (bool) $post;

	$projects = get_posts(
		array(
			'post_type'      => 'project',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => 100,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);

	$categories = get_terms(
		array(
			'taxonomy'   => 'project_category',
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $categories ) ) {
		$categories = array();
	}

	$selected_cats = $post ? wp_get_post_terms( $post->ID, 'project_category', array( 'fields' => 'ids' ) ) : array();
	if ( is_wp_error( $selected_cats ) ) {
		$selected_cats = array();
	}

	$title     = $post ? $post->post_title : '';
	$excerpt   = $post ? $post->post_excerpt : '';
	$content   = $post ? $post->post_content : '';
	$status_p  = $post ? $post->post_status : 'publish';
	$location  = $post ? (string) get_post_meta( $post->ID, '_baydemir_location', true ) : '';
	$type      = $post ? (string) get_post_meta( $post->ID, '_baydemir_type', true ) : '';
	$delivery  = $post ? (string) get_post_meta( $post->ID, '_baydemir_delivery', true ) : '';
	$units     = $post ? (string) get_post_meta( $post->ID, '_baydemir_units', true ) : '';
	$pstatus   = $post ? (string) get_post_meta( $post->ID, '_baydemir_status', true ) : 'completed';
	$featured  = $post ? (string) get_post_meta( $post->ID, '_baydemir_featured', true ) : '0';
	$show_cta  = $post ? baydemir_project_show_cta( $post->ID ) : true;
	$video_url = $post ? (string) get_post_meta( $post->ID, '_baydemir_video_url', true ) : '';
	$gallery   = $post ? (string) get_post_meta( $post->ID, '_baydemir_gallery', true ) : '';
	$thumb_id  = $post ? (int) get_post_thumbnail_id( $post->ID ) : 0;
	$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';
	$yoast     = baydemir_project_yoast_values( $post ? (int) $post->ID : 0 );
	$og_id     = (int) $yoast['seo_og_image'];

	if ( ! $pstatus ) {
		$pstatus = 'completed';
	}
	?>
	<div class="wrap bd-admin bd-projects-manager">
		<div class="bd-admin__header">
			<div>
				<p class="bd-admin__eyebrow"><?php esc_html_e( 'İçerik Yönetimi', 'baydemir' ); ?></p>
				<h1><?php esc_html_e( 'Projeler', 'baydemir' ); ?></h1>
				<p class="bd-admin__lead">
					<?php esc_html_e( 'Projeleri buradan ekleyin ve düzenleyin — WordPress yazı editörüne gitmenize gerek yok.', 'baydemir' ); ?>
				</p>
			</div>
			<a class="button button-primary button-hero" href="<?php echo esc_url( baydemir_projects_manager_url( array( 'new' => '1' ) ) ); ?>">
				<?php esc_html_e( 'Yeni Proje', 'baydemir' ); ?>
			</a>
		</div>

		<?php if ( $saved ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Proje kaydedildi.', 'baydemir' ); ?></p></div>
		<?php endif; ?>
		<?php if ( $deleted ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Proje silindi.', 'baydemir' ); ?></p></div>
		<?php endif; ?>

		<div class="bd-pm">
			<aside class="bd-pm__list bd-admin-panel">
				<h2><?php esc_html_e( 'Kayıtlı Projeler', 'baydemir' ); ?></h2>
				<p class="description bd-pm-sort-hint"><?php esc_html_e( 'Sürükleyerek sırayı değiştirin. Site listelerinde de aynı sıra kullanılır.', 'baydemir' ); ?></p>
				<p class="bd-pm-order-status" id="baydemir-order-status" aria-live="polite" hidden></p>
				<?php if ( empty( $projects ) ) : ?>
					<p class="description"><?php esc_html_e( 'Henüz proje yok. “Yeni Proje” ile başlayın.', 'baydemir' ); ?></p>
				<?php else : ?>
					<ul class="bd-pm-items" id="baydemir-project-sort">
						<?php foreach ( $projects as $item ) : ?>
							<?php
							$active    = (int) $item->ID === $edit_id;
							$item_feat = '1' === (string) get_post_meta( $item->ID, '_baydemir_featured', true );
							$item_loc  = (string) get_post_meta( $item->ID, '_baydemir_location', true );
							$thumb     = get_the_post_thumbnail_url( $item->ID, array( 48, 48 ) );
							?>
							<li class="bd-pm-items__row" data-id="<?php echo esc_attr( (string) $item->ID ); ?>">
								<span class="bd-pm-item__handle" title="<?php esc_attr_e( 'Sürükle', 'baydemir' ); ?>" aria-hidden="true">
									<span class="dashicons dashicons-move"></span>
								</span>
								<a class="bd-pm-item<?php echo $active ? ' is-active' : ''; ?>" href="<?php echo esc_url( baydemir_projects_manager_url( array( 'id' => $item->ID ) ) ); ?>">
									<?php if ( $thumb ) : ?>
										<img src="<?php echo esc_url( $thumb ); ?>" alt="" class="bd-pm-item__thumb" />
									<?php else : ?>
										<span class="bd-pm-item__thumb bd-pm-item__thumb--empty"></span>
									<?php endif; ?>
									<span class="bd-pm-item__body">
										<strong><?php echo esc_html( get_the_title( $item ) ); ?></strong>
										<em><?php echo $item_loc ? esc_html( $item_loc ) : esc_html__( 'Lokasyon yok', 'baydemir' ); ?></em>
									</span>
									<?php if ( $item_feat ) : ?>
										<span class="dashicons dashicons-star-filled" title="<?php esc_attr_e( 'Öne çıkan', 'baydemir' ); ?>"></span>
									<?php endif; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</aside>

			<section class="bd-pm__form bd-admin-panel">
				<?php if ( ! $show_form ) : ?>
					<div class="bd-pm-empty">
						<span class="dashicons dashicons-building"></span>
						<p><?php esc_html_e( 'Düzenlemek için soldan bir proje seçin veya yeni proje ekleyin.', 'baydemir' ); ?></p>
						<a class="button button-primary" href="<?php echo esc_url( baydemir_projects_manager_url( array( 'new' => '1' ) ) ); ?>">
							<?php esc_html_e( 'Yeni Proje', 'baydemir' ); ?>
						</a>
					</div>
				<?php else : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="bd-pm-form">
						<input type="hidden" name="action" value="baydemir_save_project" />
						<input type="hidden" name="project_id" value="<?php echo esc_attr( (string) $edit_id ); ?>" />
						<?php wp_nonce_field( 'baydemir_save_project', 'baydemir_project_manager_nonce' ); ?>

						<div class="bd-pm-form__top">
							<h2><?php echo $post ? esc_html__( 'Projeyi Düzenle', 'baydemir' ) : esc_html__( 'Yeni Proje', 'baydemir' ); ?></h2>
							<div class="bd-pm-form__actions">
								<?php if ( $post && 'publish' === $post->post_status ) : ?>
									<a class="button" href="<?php echo esc_url( (string) get_permalink( $post ) ); ?>" target="_blank" rel="noopener noreferrer">
										<?php esc_html_e( 'Önizle', 'baydemir' ); ?>
									</a>
								<?php endif; ?>
								<button type="submit" class="button button-primary"><?php esc_html_e( 'Kaydet', 'baydemir' ); ?></button>
							</div>
						</div>

						<div class="bd-pm-section">
							<h3><?php esc_html_e( 'Temel Bilgiler', 'baydemir' ); ?></h3>
							<p>
								<label for="baydemir_pm_title"><strong><?php esc_html_e( 'Proje Adı', 'baydemir' ); ?></strong></label>
								<input type="text" class="large-text" id="baydemir_pm_title" name="title" value="<?php echo esc_attr( $title ); ?>" required />
							</p>
							<p>
								<label for="baydemir_pm_excerpt"><strong><?php esc_html_e( 'Kısa Özet', 'baydemir' ); ?></strong></label>
								<textarea id="baydemir_pm_excerpt" name="excerpt" rows="2" class="large-text"><?php echo esc_textarea( $excerpt ); ?></textarea>
							</p>
							<p>
								<label for="baydemir_pm_content"><strong><?php esc_html_e( 'Açıklama', 'baydemir' ); ?></strong></label>
								<textarea id="baydemir_pm_content" name="content" rows="6" class="large-text"><?php echo esc_textarea( $content ); ?></textarea>
							</p>
							<p>
								<label for="baydemir_pm_post_status"><strong><?php esc_html_e( 'Yayın Durumu', 'baydemir' ); ?></strong></label>
								<select id="baydemir_pm_post_status" name="post_status">
									<option value="publish" <?php selected( $status_p, 'publish' ); ?>><?php esc_html_e( 'Yayında', 'baydemir' ); ?></option>
									<option value="draft" <?php selected( $status_p, 'draft' ); ?>><?php esc_html_e( 'Taslak', 'baydemir' ); ?></option>
								</select>
							</p>
						</div>

						<div class="bd-pm-section">
							<h3><?php esc_html_e( 'Kapak Görseli', 'baydemir' ); ?></h3>
							<input type="hidden" id="baydemir_cover" name="cover_id" value="<?php echo esc_attr( (string) $thumb_id ); ?>" />
							<div class="bd-cover<?php echo $thumb_id ? '' : ' is-empty'; ?>" id="baydemir-cover-wrap">
								<div class="bd-cover__preview" id="baydemir-cover-preview">
									<?php if ( $thumb_url ) : ?>
										<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
									<?php endif; ?>
								</div>
								<div class="bd-cover__empty"><?php esc_html_e( 'Kapak görseli seçilmedi', 'baydemir' ); ?></div>
								<p class="bd-gallery-actions">
									<button type="button" class="button button-primary" id="baydemir-cover-add"><?php esc_html_e( 'Görsel Seç', 'baydemir' ); ?></button>
									<button type="button" class="button" id="baydemir-cover-clear"><?php esc_html_e( 'Kaldır', 'baydemir' ); ?></button>
								</p>
							</div>
						</div>

						<div class="bd-pm-section">
							<h3><?php esc_html_e( 'Kategori', 'baydemir' ); ?></h3>
							<?php if ( empty( $categories ) ) : ?>
								<p class="description"><?php esc_html_e( 'Kategori bulunamadı. Tema aktivasyonunda varsayılan kategoriler oluşur.', 'baydemir' ); ?></p>
							<?php else : ?>
								<div class="bd-pm-cats">
									<?php foreach ( $categories as $term ) : ?>
										<label>
											<input type="checkbox" name="categories[]" value="<?php echo esc_attr( (string) $term->term_id ); ?>" <?php checked( in_array( (int) $term->term_id, array_map( 'intval', $selected_cats ), true ) ); ?> />
											<?php echo esc_html( $term->name ); ?>
										</label>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>

						<div class="bd-pm-section">
							<h3><?php esc_html_e( 'Proje Detayları', 'baydemir' ); ?></h3>
							<div class="bd-meta-grid">
								<p>
									<label for="baydemir_location"><?php esc_html_e( 'Lokasyon', 'baydemir' ); ?></label>
									<input type="text" id="baydemir_location" name="location" value="<?php echo esc_attr( $location ); ?>" placeholder="Çekmeköy / İstanbul" />
								</p>
								<p>
									<label for="baydemir_type"><?php esc_html_e( 'Proje Tipi', 'baydemir' ); ?></label>
									<input type="text" id="baydemir_type" name="type" value="<?php echo esc_attr( $type ); ?>" placeholder="Konut Projesi" />
								</p>
								<p>
									<label for="baydemir_delivery"><?php esc_html_e( 'Teslim Tarihi', 'baydemir' ); ?></label>
									<input type="text" id="baydemir_delivery" name="delivery" value="<?php echo esc_attr( $delivery ); ?>" placeholder="2025" />
								</p>
								<p>
									<label for="baydemir_units"><?php esc_html_e( 'Daire / Ünite', 'baydemir' ); ?></label>
									<input type="text" id="baydemir_units" name="units" value="<?php echo esc_attr( $units ); ?>" placeholder="96" />
								</p>
								<p>
									<label for="baydemir_status"><?php esc_html_e( 'Proje Durumu', 'baydemir' ); ?></label>
									<select id="baydemir_status" name="project_status">
										<option value="completed" <?php selected( $pstatus, 'completed' ); ?>><?php esc_html_e( 'Tamamlandı', 'baydemir' ); ?></option>
										<option value="ongoing" <?php selected( $pstatus, 'ongoing' ); ?>><?php esc_html_e( 'Devam Ediyor', 'baydemir' ); ?></option>
									</select>
								</p>
								<p>
									<label class="bd-meta-switch" for="baydemir_featured">
										<input type="checkbox" id="baydemir_featured" name="featured" value="1" <?php checked( $featured, '1' ); ?> />
										<span class="bd-meta-switch__text">
											<strong><?php esc_html_e( 'Ana sayfada öne çıkar', 'baydemir' ); ?></strong>
											<span><?php esc_html_e( 'Ana sayfa öne çıkan satırında gösterilir.', 'baydemir' ); ?></span>
										</span>
									</label>
								</p>
								<p>
									<label class="bd-meta-switch" for="baydemir_show_cta">
										<input type="checkbox" id="baydemir_show_cta" name="show_cta" value="1" <?php checked( $show_cta ); ?> />
										<span class="bd-meta-switch__text">
											<strong><?php esc_html_e( 'Teklif Al butonunu göster', 'baydemir' ); ?></strong>
											<span><?php esc_html_e( 'Proje detay sayfasındaki Teklif Al butonunu açar veya kapatır.', 'baydemir' ); ?></span>
										</span>
									</label>
								</p>
								<p class="bd-meta-full">
									<label for="baydemir_video_url"><?php esc_html_e( 'Video URL (YouTube / Vimeo)', 'baydemir' ); ?></label>
									<input type="url" id="baydemir_video_url" name="video_url" value="<?php echo esc_attr( $video_url ); ?>" placeholder="https://www.youtube.com/watch?v=..." />
									<span class="description"><?php esc_html_e( 'YouTube veya Vimeo linki girin. Proje detay sayfasında video alanı olarak gösterilir.', 'baydemir' ); ?></span>
								</p>
							</div>
						</div>

						<div class="bd-pm-section">
							<h3><?php esc_html_e( 'Galeri', 'baydemir' ); ?></h3>
							<?php
							$has_gallery = '' !== trim( $gallery, ", \t\n\r\0\x0B" );
							?>
							<div class="bd-gallery<?php echo $has_gallery ? '' : ' is-empty'; ?>">
								<input type="hidden" id="baydemir_gallery" name="gallery" value="<?php echo esc_attr( $gallery ); ?>" />
								<div class="bd-gallery-empty">
									<span><?php esc_html_e( 'Henüz galeri görseli yok', 'baydemir' ); ?></span>
									<button type="button" class="button button-primary" id="baydemir-gallery-add-empty"><?php esc_html_e( 'Görsel Ekle', 'baydemir' ); ?></button>
								</div>
								<div id="baydemir-gallery-preview" class="bd-gallery-preview" aria-live="polite"></div>
								<p class="bd-gallery-actions">
									<button type="button" class="button button-primary" id="baydemir-gallery-add"><?php esc_html_e( 'Görsel Ekle', 'baydemir' ); ?></button>
									<button type="button" class="button" id="baydemir-gallery-clear"><?php esc_html_e( 'Temizle', 'baydemir' ); ?></button>
								</p>
								<p class="description"><?php esc_html_e( 'Sürükleyerek sıralayın. Tek görseli kaldırmak için × kullanın.', 'baydemir' ); ?></p>
							</div>
						</div>

						<div class="bd-pm-section">
							<h3><?php esc_html_e( 'Yoast SEO', 'baydemir' ); ?></h3>
							<?php if ( ! baydemir_yoast_active() ) : ?>
								<p class="description">
									<?php
									echo wp_kses(
										sprintf(
											/* translators: %s: plugin install URL */
											__( 'Bu alanlar için <strong>Yoast SEO</strong> eklentisinin kurulu ve aktif olması gerekir. <a href="%s">Eklentileri yönet</a>', 'baydemir' ),
											esc_url( admin_url( 'plugins.php' ) )
										),
										array(
											'strong' => array(),
											'a'      => array( 'href' => array() ),
										)
									);
									?>
								</p>
							<?php else : ?>
								<p class="description" style="margin-top:0;">
									<?php esc_html_e( 'Her proje için SEO başlığı, meta açıklama ve sosyal paylaşım ayarları. Yoast ile aynı meta alanlarına kaydedilir.', 'baydemir' ); ?>
								</p>
								<div class="bd-meta-grid">
									<p class="bd-meta-full">
										<label for="baydemir_seo_focuskw"><?php esc_html_e( 'Odak anahtar kelime', 'baydemir' ); ?></label>
										<input type="text" id="baydemir_seo_focuskw" name="seo_focuskw" value="<?php echo esc_attr( (string) $yoast['seo_focuskw'] ); ?>" />
									</p>
									<p class="bd-meta-full">
										<label for="baydemir_seo_title"><?php esc_html_e( 'SEO başlığı', 'baydemir' ); ?></label>
										<input type="text" id="baydemir_seo_title" name="seo_title" value="<?php echo esc_attr( (string) $yoast['seo_title'] ); ?>" maxlength="90" />
										<span class="description"><?php esc_html_e( 'Önerilen uzunluk: yaklaşık 50–60 karakter. %%title%% ve %%page%% gibi Yoast değişkenleri kullanılabilir.', 'baydemir' ); ?></span>
									</p>
									<p class="bd-meta-full">
										<label for="baydemir_seo_metadesc"><?php esc_html_e( 'Meta açıklama', 'baydemir' ); ?></label>
										<textarea id="baydemir_seo_metadesc" name="seo_metadesc" rows="3" maxlength="320"><?php echo esc_textarea( (string) $yoast['seo_metadesc'] ); ?></textarea>
										<span class="description"><?php esc_html_e( 'Önerilen uzunluk: yaklaşık 120–155 karakter.', 'baydemir' ); ?></span>
									</p>
									<p class="bd-meta-full">
										<label for="baydemir_seo_canonical"><?php esc_html_e( 'Canonical URL', 'baydemir' ); ?></label>
										<input type="url" id="baydemir_seo_canonical" name="seo_canonical" value="<?php echo esc_attr( (string) $yoast['seo_canonical'] ); ?>" placeholder="https://" />
									</p>
									<p class="bd-meta-full">
										<label for="baydemir_seo_og_title"><?php esc_html_e( 'Sosyal (Facebook) başlığı', 'baydemir' ); ?></label>
										<input type="text" id="baydemir_seo_og_title" name="seo_og_title" value="<?php echo esc_attr( (string) $yoast['seo_og_title'] ); ?>" />
									</p>
									<p class="bd-meta-full">
										<label for="baydemir_seo_og_desc"><?php esc_html_e( 'Sosyal (Facebook) açıklaması', 'baydemir' ); ?></label>
										<textarea id="baydemir_seo_og_desc" name="seo_og_desc" rows="2"><?php echo esc_textarea( (string) $yoast['seo_og_desc'] ); ?></textarea>
									</p>
									<div class="bd-meta-full">
										<?php
										baydemir_render_contact_media_field(
											'seo_og_image',
											$og_id,
											__( 'Sosyal paylaşım görseli', 'baydemir' ),
											__( 'Görsel seçilmedi — kapak görseli kullanılır', 'baydemir' )
										);
										?>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<div class="bd-pm-form__footer">
							<button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Kaydet', 'baydemir' ); ?></button>
							<a class="button" href="<?php echo esc_url( baydemir_projects_manager_url() ); ?>"><?php esc_html_e( 'İptal', 'baydemir' ); ?></a>
							<?php if ( $post && current_user_can( 'delete_post', $post->ID ) ) : ?>
								<button
									type="submit"
									form="baydemir-delete-project"
									class="button button-link-delete"
									onclick="return confirm('<?php echo esc_js( __( 'Bu projeyi silmek istediğinize emin misiniz?', 'baydemir' ) ); ?>');"
								>
									<?php esc_html_e( 'Projeyi Sil', 'baydemir' ); ?>
								</button>
							<?php endif; ?>
						</div>
					</form>

					<?php if ( $post && current_user_can( 'delete_post', $post->ID ) ) : ?>
						<form id="baydemir-delete-project" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="hidden">
							<input type="hidden" name="action" value="baydemir_delete_project" />
							<input type="hidden" name="project_id" value="<?php echo esc_attr( (string) $post->ID ); ?>" />
							<?php wp_nonce_field( 'baydemir_delete_project', 'baydemir_delete_project_nonce' ); ?>
						</form>
					<?php endif; ?>
				<?php endif; ?>
			</section>
		</div>
	</div>
	<?php
}

/**
 * Save project from manager form.
 */
function baydemir_handle_save_project(): void {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'Yetkiniz yok.', 'baydemir' ) );
	}
	check_admin_referer( 'baydemir_save_project', 'baydemir_project_manager_nonce' );

	$project_id = isset( $_POST['project_id'] ) ? absint( $_POST['project_id'] ) : 0;
	$title      = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$excerpt    = isset( $_POST['excerpt'] ) ? sanitize_textarea_field( wp_unslash( $_POST['excerpt'] ) ) : '';
	$content    = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';
	$post_status = isset( $_POST['post_status'] ) ? sanitize_key( wp_unslash( $_POST['post_status'] ) ) : 'publish';
	if ( ! in_array( $post_status, array( 'publish', 'draft' ), true ) ) {
		$post_status = 'publish';
	}

	if ( '' === $title ) {
		wp_safe_redirect( baydemir_projects_manager_url( array( 'new' => '1' ) ) );
		exit;
	}

	$payload = array(
		'post_title'   => $title,
		'post_excerpt' => $excerpt,
		'post_content' => $content,
		'post_status'  => $post_status,
		'post_type'    => 'project',
	);

	if ( $project_id ) {
		if ( ! current_user_can( 'edit_post', $project_id ) || 'project' !== get_post_type( $project_id ) ) {
			wp_die( esc_html__( 'Yetkiniz yok.', 'baydemir' ) );
		}
		$payload['ID'] = $project_id;
		$result        = wp_update_post( $payload, true );
	} else {
		if ( ! current_user_can( 'publish_posts' ) ) {
			wp_die( esc_html__( 'Yetkiniz yok.', 'baydemir' ) );
		}
		$payload['menu_order'] = baydemir_next_project_menu_order();
		$result                = wp_insert_post( $payload, true );
	}

	if ( is_wp_error( $result ) || ! $result ) {
		wp_die( esc_html__( 'Proje kaydedilemedi.', 'baydemir' ) );
	}

	$project_id = (int) $result;

	$cover_id = isset( $_POST['cover_id'] ) ? absint( $_POST['cover_id'] ) : 0;
	if ( $cover_id ) {
		set_post_thumbnail( $project_id, $cover_id );
	} else {
		delete_post_thumbnail( $project_id );
	}

	$cats = array();
	if ( ! empty( $_POST['categories'] ) && is_array( $_POST['categories'] ) ) {
		$cats = array_map( 'absint', wp_unslash( $_POST['categories'] ) );
		$cats = array_filter( $cats );
	}
	wp_set_object_terms( $project_id, $cats, 'project_category' );

	$fields = array(
		'location'  => 'sanitize_text_field',
		'type'      => 'sanitize_text_field',
		'delivery'  => 'sanitize_text_field',
		'units'     => 'sanitize_text_field',
		'gallery'   => 'sanitize_text_field',
		'video_url' => 'esc_url_raw',
	);
	foreach ( $fields as $key => $sanitize ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( (string) $_POST[ $key ] ) : '';
		update_post_meta( $project_id, '_baydemir_' . $key, $sanitize( $raw ) );
	}

	$project_status = isset( $_POST['project_status'] ) ? sanitize_text_field( wp_unslash( $_POST['project_status'] ) ) : 'completed';
	if ( ! in_array( $project_status, array( 'completed', 'ongoing' ), true ) ) {
		$project_status = 'completed';
	}
	update_post_meta( $project_id, '_baydemir_status', $project_status );
	update_post_meta( $project_id, '_baydemir_featured', isset( $_POST['featured'] ) ? '1' : '0' );
	update_post_meta( $project_id, '_baydemir_show_cta', isset( $_POST['show_cta'] ) ? '1' : '0' );

	baydemir_save_project_yoast_meta( $project_id );

	wp_safe_redirect( baydemir_projects_manager_url( array( 'id' => $project_id, 'saved' => '1' ) ) );
	exit;
}

/**
 * Delete project from manager.
 */
function baydemir_handle_delete_project(): void {
	check_admin_referer( 'baydemir_delete_project', 'baydemir_delete_project_nonce' );

	$project_id = isset( $_POST['project_id'] ) ? absint( $_POST['project_id'] ) : 0;
	if ( ! $project_id || 'project' !== get_post_type( $project_id ) || ! current_user_can( 'delete_post', $project_id ) ) {
		wp_die( esc_html__( 'Yetkiniz yok.', 'baydemir' ) );
	}

	wp_trash_post( $project_id );

	wp_safe_redirect( baydemir_projects_manager_url( array( 'deleted' => '1' ) ) );
	exit;
}

/**
 * Next menu_order for a new project (append to end).
 */
function baydemir_next_project_menu_order(): int {
	global $wpdb;
	$max = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		"SELECT MAX(menu_order) FROM {$wpdb->posts} WHERE post_type = 'project' AND post_status NOT IN ('trash','auto-draft')"
	);
	return $max + 1;
}

/**
 * AJAX: persist project drag order.
 */
function baydemir_ajax_reorder_projects(): void {
	check_ajax_referer( 'baydemir_reorder_projects', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	$order = isset( $_POST['order'] ) ? wp_unslash( $_POST['order'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! is_array( $order ) ) {
		wp_send_json_error( array( 'message' => 'invalid' ), 400 );
	}

	$ids = array_values( array_filter( array_map( 'absint', $order ) ) );
	foreach ( $ids as $index => $post_id ) {
		if ( ! $post_id || 'project' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'         => $post_id,
				'menu_order' => $index,
			)
		);
	}

	wp_send_json_success();
}
