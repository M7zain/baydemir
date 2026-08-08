<?php
/**
 * One-click demo pages & sample content.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_baydemir_install_demo', 'baydemir_install_demo_content' );

/**
 * Install demo content.
 */
function baydemir_install_demo_content(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Yetkiniz yok.', 'baydemir' ) );
	}
	check_admin_referer( 'baydemir_install_demo' );

	$pages = array(
		'anasayfa'   => array( 'Ana Sayfa', '' ),
		'hizmetler'  => array( 'Hizmetler', 'page-templates/services.php' ),
		'hakkimizda' => array( 'Hakkımızda', 'page-templates/about.php' ),
		'kalite'     => array( 'Kalite', 'page-templates/quality.php' ),
		'iletisim'   => array( 'İletişim', 'page-templates/contact.php' ),
	);

	$page_ids = array();
	foreach ( $pages as $slug => $data ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$page_ids[ $slug ] = $existing->ID;
			if ( $data[1] ) {
				update_post_meta( $existing->ID, '_wp_page_template', $data[1] );
			}
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_title'   => $data[0],
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => '',
			)
		);
		if ( $id && ! is_wp_error( $id ) && $data[1] ) {
			update_post_meta( $id, '_wp_page_template', $data[1] );
		}
		$page_ids[ $slug ] = $id;
	}

	if ( ! empty( $page_ids['anasayfa'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['anasayfa'] );
	}

	if ( ! empty( $page_ids['iletisim'] ) ) {
		set_theme_mod( 'baydemir_cta_page', (int) $page_ids['iletisim'] );
	}

	/* Menu */
	$menu_name = 'Baydemir Ana Menü';
	$menu      = wp_get_nav_menu_object( $menu_name );
	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );
		$order   = 1;
		$menu_items = array(
			array( 'Ana Sayfa', 'page', $page_ids['anasayfa'] ?? 0 ),
			array( 'Projeler', 'custom', get_post_type_archive_link( 'project' ) ),
			array( 'Hizmetler', 'page', $page_ids['hizmetler'] ?? 0 ),
			array( 'Hakkımızda', 'page', $page_ids['hakkimizda'] ?? 0 ),
			array( 'Kalite', 'page', $page_ids['kalite'] ?? 0 ),
			array( 'İletişim', 'page', $page_ids['iletisim'] ?? 0 ),
		);

		foreach ( $menu_items as $item ) {
			if ( 'custom' === $item[1] ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'    => $item[0],
						'menu-item-url'      => $item[2],
						'menu-item-type'     => 'custom',
						'menu-item-status'   => 'publish',
						'menu-item-position' => $order++,
					)
				);
				continue;
			}
			if ( empty( $item[2] ) ) {
				continue;
			}
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => $item[0],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $item[2],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-position'  => $order++,
				)
			);
		}
		$locations            = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = $menu_id;
		$locations['footer']  = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/* Categories */
	$cats = array(
		'konut'      => 'Konut Projeleri',
		'villa'      => 'Villa Projeleri',
		'ticari'     => 'Ticari Yapılar',
		'devam-eden' => 'Devam Eden Projeler',
	);
	$term_ids = array();
	foreach ( $cats as $slug => $name ) {
		$term = term_exists( $slug, 'project_category' );
		if ( ! $term ) {
			$term = wp_insert_term( $name, 'project_category', array( 'slug' => $slug ) );
		}
		if ( ! is_wp_error( $term ) ) {
			$term_ids[ $slug ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
		}
	}

	/* Sample projects */
	if ( 0 === (int) wp_count_posts( 'project' )->publish ) {
		$projects = array(
			array( 'Mira Konutları', 'konut', 'Çekmeköy / İstanbul', 'Konut Projesi', '2025', '96', 'completed' ),
			array( 'Nova Rezidans', 'konut', 'Kadıköy / İstanbul', 'Konut Projesi', '2024', '64', 'completed' ),
			array( 'Aurora Villaları', 'villa', 'Bodrum / Muğla', 'Villa Projesi', '2025', '12', 'completed' ),
			array( 'Horizon Villa', 'villa', 'Çeşme / İzmir', 'Villa Projesi', '2023', '8', 'completed' ),
			array( 'Plaza Ofis', 'ticari', 'Levent / İstanbul', 'Ticari Yapı', '2024', '40', 'completed' ),
			array( 'Merkez İş Merkezi', 'ticari', 'Kütahya Merkez', 'Ticari Yapı', '2022', '28', 'completed' ),
			array( 'Lumina Konutları', 'devam-eden', 'Üsküdar / İstanbul', 'Konut Projesi', '2027', '120', 'ongoing' ),
			array( 'Verde Yaşam', 'devam-eden', 'Kütahya', 'Konut Projesi', '2026', '72', 'ongoing' ),
		);

		foreach ( $projects as $i => $p ) {
			$id = wp_insert_post(
				array(
					'post_title'   => $p[0],
					'post_status'  => 'publish',
					'post_type'    => 'project',
					'post_content' => 'Modern mimari anlayışı, kaliteli malzeme seçimi ve zamanında teslim ilkesiyle hayata geçirilen prestijli bir projedir. Güvenli yaşam alanları ve fonksiyonel planlarla geleceğe değer katar.',
					'post_excerpt' => 'Modern yaşam standartlarında tasarlanmış prestij proje.',
					'menu_order'   => $i + 1,
				)
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			if ( ! empty( $term_ids[ $p[1] ] ) ) {
				wp_set_object_terms( $id, array( $term_ids[ $p[1] ] ), 'project_category' );
			}
			update_post_meta( $id, '_baydemir_location', $p[2] );
			update_post_meta( $id, '_baydemir_type', $p[3] );
			update_post_meta( $id, '_baydemir_delivery', $p[4] );
			update_post_meta( $id, '_baydemir_units', $p[5] );
			update_post_meta( $id, '_baydemir_status', $p[6] );
			update_post_meta( $id, '_baydemir_featured', $i < 4 ? '1' : '0' );
		}
	}

	/* Sample services */
	if ( 0 === (int) wp_count_posts( 'service' )->publish ) {
		$services = array(
			array( 'Konut Projeleri', 'building', 'Modern ve yaşanabilir konut çözümleri.' ),
			array( 'Villa Projeleri', 'home', 'Özgün tasarım, konforlu yaşam.' ),
			array( 'Ticari Yapılar', 'shop', 'Fonksiyonel ve estetik ticari çözümler.' ),
			array( 'Anahtar Teslim İnşaat', 'helmet', 'Baştan sona eksiksiz teslim.' ),
			array( 'Mimari Tasarım', 'pen', 'Estetik ve uygulanabilir mimari.' ),
			array( 'Proje Yönetimi', 'users', 'Süreçlerin profesyonel yönetimi.' ),
		);
		foreach ( $services as $i => $s ) {
			$id = wp_insert_post(
				array(
					'post_title'   => $s[0],
					'post_status'  => 'publish',
					'post_type'    => 'service',
					'post_excerpt' => $s[2],
					'post_content' => $s[2],
					'menu_order'   => $i + 1,
				)
			);
			if ( ! is_wp_error( $id ) ) {
				update_post_meta( $id, '_baydemir_icon', $s[1] );
			}
		}
	}

	flush_rewrite_rules();

	wp_safe_redirect( admin_url( 'admin.php?page=baydemir-panel&demo=1' ) );
	exit;
}
