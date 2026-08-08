<?php
/**
 * Top-level Baydemir admin dashboard.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'baydemir_register_admin_menu' );
add_action( 'admin_enqueue_scripts', 'baydemir_enqueue_admin_dashboard_assets' );

/**
 * Register Baydemir top-level menu.
 */
function baydemir_register_admin_menu(): void {
	add_menu_page(
		__( 'Baydemir Panel', 'baydemir' ),
		__( 'Baydemir', 'baydemir' ),
		'edit_theme_options',
		'baydemir-panel',
		'baydemir_render_admin_dashboard',
		'dashicons-building',
		58
	);

	add_submenu_page(
		'baydemir-panel',
		__( 'Baydemir Panel', 'baydemir' ),
		__( 'Panel', 'baydemir' ),
		'edit_theme_options',
		'baydemir-panel',
		'baydemir_render_admin_dashboard'
	);
}

/**
 * Enqueue admin CSS on dashboard screen.
 */
function baydemir_enqueue_admin_dashboard_assets( string $hook ): void {
	if ( 'toplevel_page_baydemir-panel' !== $hook ) {
		return;
	}

	wp_enqueue_style(
		'baydemir-admin',
		baydemir_asset( 'css/admin.css' ),
		array(),
		BAYDEMIR_VERSION
	);
}

/**
 * Setup / content status checks.
 *
 * @return array{
 *   pages: bool,
 *   front: bool,
 *   menu: bool,
 *   projects: int,
 *   services: int,
 *   demo_ready: bool
 * }
 */
function baydemir_admin_status(): array {
	$required = array( 'anasayfa', 'hizmetler', 'hakkimizda', 'kalite', 'iletisim' );
	$pages_ok = true;
	foreach ( $required as $slug ) {
		if ( ! get_page_by_path( $slug ) ) {
			$pages_ok = false;
			break;
		}
	}

	$front_ok = ( 'page' === get_option( 'show_on_front' ) ) && (int) get_option( 'page_on_front' ) > 0;

	$locations = get_nav_menu_locations();
	$menu_ok   = ! empty( $locations['primary'] );

	$projects = (int) ( wp_count_posts( 'project' )->publish ?? 0 );
	$services = (int) ( wp_count_posts( 'service' )->publish ?? 0 );

	return array(
		'pages'      => $pages_ok,
		'front'      => $front_ok,
		'menu'       => $menu_ok,
		'projects'   => $projects,
		'services'   => $services,
		'demo_ready' => $pages_ok && $front_ok && $menu_ok && $projects > 0 && $services > 0,
	);
}

/**
 * Render dashboard hub.
 */
function baydemir_render_admin_dashboard(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$status = baydemir_admin_status();
	$done   = isset( $_GET['demo'] ) && '1' === $_GET['demo']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$can_install = current_user_can( 'manage_options' );
	?>
	<div class="wrap bd-admin">
		<div class="bd-admin__header">
			<div>
				<p class="bd-admin__eyebrow"><?php echo esc_html( baydemir_company_name() ); ?></p>
				<h1><?php esc_html_e( 'Baydemir Panel', 'baydemir' ); ?></h1>
				<p class="bd-admin__lead">
					<?php esc_html_e( 'Site içeriğini, projeleri ve marka ayarlarını buradan yönetin.', 'baydemir' ); ?>
				</p>
			</div>
			<?php if ( $can_install ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="bd-admin__cta-form">
					<input type="hidden" name="action" value="baydemir_install_demo" />
					<?php wp_nonce_field( 'baydemir_install_demo' ); ?>
					<button type="submit" class="button button-primary button-hero">
						<?php esc_html_e( 'Örnek İçeriği Yükle', 'baydemir' ); ?>
					</button>
				</form>
			<?php endif; ?>
		</div>

		<?php if ( $done ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Örnek sayfalar, menü, projeler ve hizmetler oluşturuldu.', 'baydemir' ); ?></p></div>
		<?php endif; ?>

		<div class="bd-admin-cards">
			<div class="bd-admin-card <?php echo $status['pages'] ? 'is-ok' : 'is-warn'; ?>">
				<span class="bd-admin-card__label"><?php esc_html_e( 'Sayfalar', 'baydemir' ); ?></span>
				<strong><?php echo $status['pages'] ? esc_html__( 'Hazır', 'baydemir' ) : esc_html__( 'Eksik', 'baydemir' ); ?></strong>
			</div>
			<div class="bd-admin-card <?php echo $status['front'] ? 'is-ok' : 'is-warn'; ?>">
				<span class="bd-admin-card__label"><?php esc_html_e( 'Ana Sayfa', 'baydemir' ); ?></span>
				<strong><?php echo $status['front'] ? esc_html__( 'Ayarlı', 'baydemir' ) : esc_html__( 'Ayarlanmadı', 'baydemir' ); ?></strong>
			</div>
			<div class="bd-admin-card <?php echo $status['menu'] ? 'is-ok' : 'is-warn'; ?>">
				<span class="bd-admin-card__label"><?php esc_html_e( 'Menü', 'baydemir' ); ?></span>
				<strong><?php echo $status['menu'] ? esc_html__( 'Atandı', 'baydemir' ) : esc_html__( 'Eksik', 'baydemir' ); ?></strong>
			</div>
			<div class="bd-admin-card is-info">
				<span class="bd-admin-card__label"><?php esc_html_e( 'Projeler', 'baydemir' ); ?></span>
				<strong><?php echo esc_html( (string) $status['projects'] ); ?></strong>
			</div>
			<div class="bd-admin-card is-info">
				<span class="bd-admin-card__label"><?php esc_html_e( 'Hizmetler', 'baydemir' ); ?></span>
				<strong><?php echo esc_html( (string) $status['services'] ); ?></strong>
			</div>
		</div>

		<div class="bd-admin-grid">
			<section class="bd-admin-panel">
				<h2><?php esc_html_e( 'Hızlı Erişim', 'baydemir' ); ?></h2>
				<div class="bd-admin-links">
					<a class="bd-admin-link" href="<?php echo esc_url( admin_url( 'admin.php?page=baydemir-projects' ) ); ?>">
						<span class="dashicons dashicons-building"></span>
						<span>
							<strong><?php esc_html_e( 'Projeler', 'baydemir' ); ?></strong>
							<em><?php esc_html_e( 'Alanlar, galeri ve kategori — tek ekranda', 'baydemir' ); ?></em>
						</span>
					</a>
					<a class="bd-admin-link" href="<?php echo esc_url( admin_url( 'admin.php?page=baydemir-about' ) ); ?>">
						<span class="dashicons dashicons-info-outline"></span>
						<span>
							<strong><?php esc_html_e( 'Hakkımızda', 'baydemir' ); ?></strong>
							<em><?php esc_html_e( 'Metin, görsel ve özellik kartları', 'baydemir' ); ?></em>
						</span>
					</a>
					<a class="bd-admin-link" href="<?php echo esc_url( admin_url( 'admin.php?page=baydemir-testimonials' ) ); ?>">
						<span class="dashicons dashicons-format-quote"></span>
						<span>
							<strong><?php esc_html_e( 'Müşteri Yorumları', 'baydemir' ); ?></strong>
							<em><?php esc_html_e( 'Anasayfa yorum kartları', 'baydemir' ); ?></em>
						</span>
					</a>
					<a class="bd-admin-link" href="<?php echo esc_url( admin_url( 'edit.php?post_type=service' ) ); ?>">
						<span class="dashicons dashicons-hammer"></span>
						<span>
							<strong><?php esc_html_e( 'Hizmetler', 'baydemir' ); ?></strong>
							<em><?php esc_html_e( 'Hizmet kartlarını yönet', 'baydemir' ); ?></em>
						</span>
					</a>
					<a class="bd-admin-link" href="<?php echo esc_url( admin_url( 'admin.php?page=baydemir-contact' ) ); ?>">
						<span class="dashicons dashicons-phone"></span>
						<span>
							<strong><?php esc_html_e( 'İletişim & WhatsApp', 'baydemir' ); ?></strong>
							<em><?php esc_html_e( 'Numara ve yüzen butonu göster / gizle', 'baydemir' ); ?></em>
						</span>
					</a>
					<a class="bd-admin-link" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">
						<span class="dashicons dashicons-admin-customizer"></span>
						<span>
							<strong><?php esc_html_e( 'Özelleştir', 'baydemir' ); ?></strong>
							<em><?php esc_html_e( 'Marka, iletişim, hero, CTA', 'baydemir' ); ?></em>
						</span>
					</a>
					<a class="bd-admin-link" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>">
						<span class="dashicons dashicons-menu"></span>
						<span>
							<strong><?php esc_html_e( 'Menüler', 'baydemir' ); ?></strong>
							<em><?php esc_html_e( 'Ana menü ve footer menüsü', 'baydemir' ); ?></em>
						</span>
					</a>
					<a class="bd-admin-link" href="<?php echo esc_url( admin_url( 'upload.php' ) ); ?>">
						<span class="dashicons dashicons-format-gallery"></span>
						<span>
							<strong><?php esc_html_e( 'Medya', 'baydemir' ); ?></strong>
							<em><?php esc_html_e( 'Görseller ve galeri', 'baydemir' ); ?></em>
						</span>
					</a>
					<a class="bd-admin-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
						<span class="dashicons dashicons-external"></span>
						<span>
							<strong><?php esc_html_e( 'Siteyi Görüntüle', 'baydemir' ); ?></strong>
							<em><?php esc_html_e( 'Ön yüzü yeni sekmede aç', 'baydemir' ); ?></em>
						</span>
					</a>
				</div>
			</section>

			<section class="bd-admin-panel">
				<h2><?php esc_html_e( 'Nasıl Düzenlenir?', 'baydemir' ); ?></h2>
				<ol class="bd-admin-tips">
					<li><?php esc_html_e( 'Örnek içeriği yükleyin (sayfalar, menü, örnek projeler).', 'baydemir' ); ?></li>
					<li><?php esc_html_e( 'Görünüm → Özelleştir → Baydemir Tema Ayarları ile telefon, adres ve hero metnini güncelleyin.', 'baydemir' ); ?></li>
					<li><?php esc_html_e( 'Baydemir → Hakkımızda ile şirket metnini, görseli ve kartları güncelleyin.', 'baydemir' ); ?></li>
					<li><?php esc_html_e( 'Hizmetler / İletişim / Kalite sayfalarında “Öne çıkan görsel” alanından üst banner görselini değiştirin.', 'baydemir' ); ?></li>
					<li><?php esc_html_e( 'Baydemir → Projeler ekranından yeni proje ekleyin: kapak, kategori, lokasyon ve galeri.', 'baydemir' ); ?></li>
					<li><?php esc_html_e( 'Hizmetler menüsünden kart başlığı, özet ve ikon seçin; sıra için “Sıra” alanını kullanın.', 'baydemir' ); ?></li>
					<li><?php esc_html_e( 'Ana sayfada göstermek istediğiniz projelerde “Öne çıkar” seçeneğini işaretleyin.', 'baydemir' ); ?></li>
				</ol>
				<?php if ( ! $status['demo_ready'] && $can_install ) : ?>
					<p class="bd-admin-note">
						<?php esc_html_e( 'Kurulum tamamlanmamış görünüyor. Örnek içeriği yüklemek iyi bir başlangıçtır; mevcut içeriğin üzerine yazmaz.', 'baydemir' ); ?>
					</p>
				<?php endif; ?>
			</section>
		</div>
	</div>
	<?php
}
