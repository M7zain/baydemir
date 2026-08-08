<?php
/**
 * Theme setup.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'baydemir_setup' );

/**
 * Register theme supports.
 */
function baydemir_setup(): void {
	load_theme_textdomain( 'baydemir', BAYDEMIR_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 280,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'baydemir' ),
			'footer'  => __( 'Footer Menu', 'baydemir' ),
		)
	);

	add_image_size( 'baydemir-card', 640, 420, true );
	add_image_size( 'baydemir-hero', 1600, 1000, true );
	add_image_size( 'baydemir-gallery', 800, 600, true );
}

add_filter( 'excerpt_length', static fn(): int => 22 );
add_filter( 'excerpt_more', static fn(): string => '…' );

/**
 * Show more projects on archives.
 */
add_action(
	'pre_get_posts',
	static function ( WP_Query $query ): void {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( $query->is_post_type_archive( 'project' ) || $query->is_tax( 'project_category' ) ) {
			$query->set( 'posts_per_page', 12 );
			$query->set(
				'orderby',
				array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				)
			);
		}
	}
);

/**
 * Body classes.
 */
add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'bd-theme';
		if ( is_front_page() ) {
			$classes[] = 'bd-home';
		}
		return $classes;
	}
);
