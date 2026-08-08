<?php
/**
 * Block patterns registration.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'baydemir_register_pattern_category' );

/**
 * Pattern category.
 */
function baydemir_register_pattern_category(): void {
	register_block_pattern_category(
		'baydemir',
		array( 'label' => __( 'Baydemir', 'baydemir' ) )
	);
}
