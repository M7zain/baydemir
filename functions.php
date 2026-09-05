<?php
/**
 * Baydemir theme bootstrap.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BAYDEMIR_VERSION', '1.8.6' );
define( 'BAYDEMIR_DIR', get_template_directory() );
define( 'BAYDEMIR_URI', get_template_directory_uri() );

require_once BAYDEMIR_DIR . '/inc/helpers.php';
require_once BAYDEMIR_DIR . '/inc/setup.php';
require_once BAYDEMIR_DIR . '/inc/enqueue.php';
require_once BAYDEMIR_DIR . '/inc/post-types.php';
require_once BAYDEMIR_DIR . '/inc/category-meta.php';
require_once BAYDEMIR_DIR . '/inc/meta-boxes.php';
require_once BAYDEMIR_DIR . '/inc/admin-columns.php';
require_once BAYDEMIR_DIR . '/inc/admin-dashboard.php';
require_once BAYDEMIR_DIR . '/inc/admin-projects.php';
require_once BAYDEMIR_DIR . '/inc/admin-about.php';
require_once BAYDEMIR_DIR . '/inc/admin-contact.php';
require_once BAYDEMIR_DIR . '/inc/admin-testimonials.php';
require_once BAYDEMIR_DIR . '/inc/yoast.php';
require_once BAYDEMIR_DIR . '/inc/customizer.php';
require_once BAYDEMIR_DIR . '/inc/contact-form.php';
require_once BAYDEMIR_DIR . '/inc/patterns.php';
require_once BAYDEMIR_DIR . '/inc/demo-content.php';

/**
 * One-time: refresh Hakkımızda feature cards to the new default set.
 */
add_action(
	'after_setup_theme',
	static function (): void {
		if ( get_option( 'baydemir_about_features_rev' ) === '2' ) {
			return;
		}
		$about = get_option( 'baydemir_about', array() );
		if ( ! is_array( $about ) ) {
			$about = array();
		}
		$about['features'] = baydemir_about_default_features();
		update_option( 'baydemir_about', $about, false );
		update_option( 'baydemir_about_features_rev', '2', false );
	}
);