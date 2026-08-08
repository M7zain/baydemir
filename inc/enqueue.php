<?php
/**
 * Asset enqueue.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'baydemir_enqueue_assets' );
add_action( 'enqueue_block_editor_assets', 'baydemir_enqueue_editor_assets' );

/**
 * Front-end assets.
 */
function baydemir_enqueue_assets(): void {
	wp_enqueue_style(
		'baydemir-fonts',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'baydemir-main',
		baydemir_asset( 'css/main.css' ),
		array( 'baydemir-fonts' ),
		BAYDEMIR_VERSION
	);

	wp_add_inline_style( 'baydemir-main', baydemir_theme_colors_css() );

	wp_enqueue_script(
		'baydemir-main',
		baydemir_asset( 'js/main.js' ),
		array(),
		BAYDEMIR_VERSION,
		array( 'strategy' => 'defer', 'in_footer' => true )
	);

	if ( is_front_page() && function_exists( 'baydemir_hero_uses_lights_banner' ) && baydemir_hero_uses_lights_banner() ) {
		wp_enqueue_script(
			'baydemir-hero-lights',
			baydemir_asset( 'js/hero-lights.js' ),
			array(),
			BAYDEMIR_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
		wp_localize_script(
			'baydemir-hero-lights',
			'baydemirLights',
			array(
				'windowsData' => baydemir_asset( 'js/lights-windows-data.json' ),
			)
		);
	}

	if ( is_page_template( 'page-templates/quality.php' ) ) {
		wp_enqueue_script(
			'lordicon',
			'https://cdn.lordicon.com/lordicon.js',
			array(),
			null,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
		wp_enqueue_script(
			'baydemir-quality-3d',
			baydemir_asset( 'js/quality-3d.js' ),
			array(),
			BAYDEMIR_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
	}

	if ( is_page_template( 'page-templates/contact.php' ) || is_page( 'iletisim' ) ) {
		wp_enqueue_script(
			'baydemir-contact-hero',
			baydemir_asset( 'js/contact-hero.js' ),
			array(),
			BAYDEMIR_VERSION,
			array( 'strategy' => 'defer', 'in_footer' => true )
		);
	}

	wp_localize_script(
		'baydemir-main',
		'baydemirData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'baydemir_nonce' ),
			'homeUrl' => home_url( '/' ),
			'i18n'    => array(
				'all'      => __( 'Tüm Projeler', 'baydemir' ),
				'loading'  => __( 'Yükleniyor…', 'baydemir' ),
				'success'  => __( 'Mesajınız gönderildi. Teşekkürler!', 'baydemir' ),
				'error'    => __( 'Bir hata oluştu. Lütfen tekrar deneyin.', 'baydemir' ),
				'lightboxTitle' => __( 'Galeri', 'baydemir' ),
				'lightboxClose' => __( 'Kapat', 'baydemir' ),
				'lightboxPrev'  => __( 'Önceki görsel', 'baydemir' ),
				'lightboxNext'  => __( 'Sonraki görsel', 'baydemir' ),
			),
		)
	);
}

/**
 * Editor assets.
 */
function baydemir_enqueue_editor_assets(): void {
	wp_enqueue_style(
		'baydemir-fonts',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'baydemir-editor',
		baydemir_asset( 'css/main.css' ),
		array( 'baydemir-fonts' ),
		BAYDEMIR_VERSION
	);
}

/**
 * Convert hex to RGB CSV.
 */
function baydemir_hex_to_rgb_csv( string $hex ): string {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) ) {
		return '47,125,255';
	}
	return sprintf(
		'%d,%d,%d',
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) )
	);
}
