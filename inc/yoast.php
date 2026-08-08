<?php
/**
 * Yoast SEO integration for Baydemir CPTs.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Yoast SEO is active.
 */
function baydemir_yoast_active(): bool {
	return defined( 'WPSEO_VERSION' );
}

/**
 * Ensure project (and service) appear in Yoast post type settings / metabox.
 *
 * @param array<string, string> $post_types Post types keyed by name.
 * @return array<string, string>
 */
function baydemir_yoast_accessible_post_types( array $post_types ): array {
	$post_types['project'] = 'project';
	$post_types['service'] = 'service';
	return $post_types;
}
add_filter( 'wpseo_accessible_post_types', 'baydemir_yoast_accessible_post_types' );
add_filter( 'wpseo_public_post_types', 'baydemir_yoast_accessible_post_types' );

/**
 * Yoast meta keys used in the project manager.
 *
 * @return array<string, string> Form field => meta key.
 */
function baydemir_project_yoast_fields(): array {
	return array(
		'seo_focuskw'   => '_yoast_wpseo_focuskw',
		'seo_title'     => '_yoast_wpseo_title',
		'seo_metadesc'  => '_yoast_wpseo_metadesc',
		'seo_canonical' => '_yoast_wpseo_canonical',
		'seo_og_title'  => '_yoast_wpseo_opengraph-title',
		'seo_og_desc'   => '_yoast_wpseo_opengraph-description',
		'seo_og_image'  => '_yoast_wpseo_opengraph-image-id',
	);
}

/**
 * Read Yoast values for a project.
 *
 * @return array<string, string|int>
 */
function baydemir_project_yoast_values( int $post_id ): array {
	$values = array();
	foreach ( baydemir_project_yoast_fields() as $field => $meta_key ) {
		$raw = $post_id ? get_post_meta( $post_id, $meta_key, true ) : '';
		if ( 'seo_og_image' === $field ) {
			$values[ $field ] = absint( $raw );
			continue;
		}
		$values[ $field ] = is_string( $raw ) ? $raw : '';
	}
	return $values;
}

/**
 * Save Yoast meta from the project manager form.
 */
function baydemir_save_project_yoast_meta( int $project_id ): void {
	if ( ! baydemir_yoast_active() ) {
		return;
	}

	foreach ( baydemir_project_yoast_fields() as $field => $meta_key ) {
		if ( 'seo_og_image' === $field ) {
			$image_id = isset( $_POST[ $field ] ) ? absint( $_POST[ $field ] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( $image_id ) {
				update_post_meta( $project_id, '_yoast_wpseo_opengraph-image-id', (string) $image_id );
				$url = wp_get_attachment_url( $image_id );
				if ( $url ) {
					update_post_meta( $project_id, '_yoast_wpseo_opengraph-image', esc_url_raw( $url ) );
				}
			} else {
				delete_post_meta( $project_id, '_yoast_wpseo_opengraph-image-id' );
				delete_post_meta( $project_id, '_yoast_wpseo_opengraph-image' );
			}
			continue;
		}

		$raw = isset( $_POST[ $field ] ) ? wp_unslash( (string) $_POST[ $field ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( 'seo_canonical' === $field ) {
			$value = esc_url_raw( $raw );
		} elseif ( 'seo_metadesc' === $field || 'seo_og_desc' === $field ) {
			$value = sanitize_textarea_field( $raw );
		} else {
			$value = sanitize_text_field( $raw );
		}

		if ( '' === $value ) {
			delete_post_meta( $project_id, $meta_key );
		} else {
			update_post_meta( $project_id, $meta_key, $value );
		}
	}
}
