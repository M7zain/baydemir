<?php
/**
 * Admin list columns & quick actions.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'manage_project_posts_columns', 'baydemir_project_columns' );
add_action( 'manage_project_posts_custom_column', 'baydemir_project_column_content', 10, 2 );
add_filter( 'manage_edit-project_sortable_columns', 'baydemir_project_sortable_columns' );

add_filter( 'manage_service_posts_columns', 'baydemir_service_columns' );
add_action( 'manage_service_posts_custom_column', 'baydemir_service_column_content', 10, 2 );
add_filter( 'manage_edit-service_sortable_columns', 'baydemir_service_sortable_columns' );

add_action( 'admin_enqueue_scripts', 'baydemir_enqueue_list_admin_assets' );
add_action( 'wp_ajax_baydemir_toggle_featured', 'baydemir_ajax_toggle_featured' );
add_action( 'pre_get_posts', 'baydemir_admin_list_orderby' );

/**
 * Project list columns.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function baydemir_project_columns( array $columns ): array {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['bd_thumb'] = __( 'Görsel', 'baydemir' );
			$new[ $key ]     = $label;
			continue;
		}
		$new[ $key ] = $label;
		if ( 'taxonomy-project_category' === $key ) {
			$new['bd_location'] = __( 'Lokasyon', 'baydemir' );
			$new['bd_status']   = __( 'Durum', 'baydemir' );
			$new['bd_featured'] = __( 'Öne çıkan', 'baydemir' );
		}
	}

	if ( ! isset( $new['bd_location'] ) ) {
		$new['bd_location'] = __( 'Lokasyon', 'baydemir' );
		$new['bd_status']   = __( 'Durum', 'baydemir' );
		$new['bd_featured'] = __( 'Öne çıkan', 'baydemir' );
	}

	return $new;
}

/**
 * Project column content.
 */
function baydemir_project_column_content( string $column, int $post_id ): void {
	switch ( $column ) {
		case 'bd_thumb':
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, array( 48, 48 ), array( 'class' => 'bd-admin-thumb' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<span class="bd-admin-thumb bd-admin-thumb--empty">—</span>';
			}
			break;

		case 'bd_location':
			$location = (string) get_post_meta( $post_id, '_baydemir_location', true );
			echo $location ? esc_html( $location ) : '—';
			break;

		case 'bd_status':
			$status = (string) get_post_meta( $post_id, '_baydemir_status', true );
			if ( 'ongoing' === $status ) {
				echo '<span class="bd-status-badge bd-status-badge--ongoing">' . esc_html__( 'Devam Ediyor', 'baydemir' ) . '</span>';
			} else {
				echo '<span class="bd-status-badge bd-status-badge--completed">' . esc_html__( 'Tamamlandı', 'baydemir' ) . '</span>';
			}
			break;

		case 'bd_featured':
			$featured = '1' === (string) get_post_meta( $post_id, '_baydemir_featured', true );
			printf(
				'<button type="button" class="bd-featured-toggle%s" data-post-id="%d" title="%s" aria-pressed="%s"><span class="dashicons dashicons-star-%s" aria-hidden="true"></span><span class="screen-reader-text">%s</span></button>',
				$featured ? ' is-on' : '',
				$post_id,
				esc_attr__( 'Öne çıkanı değiştir', 'baydemir' ),
				$featured ? 'true' : 'false',
				$featured ? 'filled' : 'empty',
				esc_html__( 'Öne çıkanı değiştir', 'baydemir' )
			);
			break;
	}
}

/**
 * Sortable project columns.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function baydemir_project_sortable_columns( array $columns ): array {
	$columns['bd_location'] = 'bd_location';
	$columns['bd_status']   = 'bd_status';
	$columns['bd_featured'] = 'bd_featured';
	return $columns;
}

/**
 * Service list columns.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function baydemir_service_columns( array $columns ): array {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$new['bd_thumb'] = __( 'Görsel', 'baydemir' );
			$new[ $key ]     = $label;
			$new['bd_icon']  = __( 'İkon', 'baydemir' );
			$new['bd_order'] = __( 'Sıra', 'baydemir' );
			continue;
		}
		$new[ $key ] = $label;
	}

	if ( ! isset( $new['bd_icon'] ) ) {
		$new['bd_icon']  = __( 'İkon', 'baydemir' );
		$new['bd_order'] = __( 'Sıra', 'baydemir' );
	}

	return $new;
}

/**
 * Service column content.
 */
function baydemir_service_column_content( string $column, int $post_id ): void {
	switch ( $column ) {
		case 'bd_thumb':
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, array( 48, 48 ), array( 'class' => 'bd-admin-thumb' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				echo '<span class="bd-admin-thumb bd-admin-thumb--empty">—</span>';
			}
			break;

		case 'bd_icon':
			$icon = (string) get_post_meta( $post_id, '_baydemir_icon', true ) ?: 'building';
			echo baydemir_icon( $icon, 'bd-list-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo ' <span class="description">' . esc_html( $icon ) . '</span>';
			break;

		case 'bd_order':
			echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
			break;
	}
}

/**
 * Sortable service columns.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function baydemir_service_sortable_columns( array $columns ): array {
	$columns['bd_order'] = 'menu_order';
	return $columns;
}

/**
 * Handle custom orderby on list tables.
 */
function baydemir_admin_list_orderby( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );
	if ( ! $orderby ) {
		return;
	}

	$post_type = $query->get( 'post_type' );
	if ( 'project' === $post_type ) {
		$map = array(
			'bd_location' => '_baydemir_location',
			'bd_status'   => '_baydemir_status',
			'bd_featured' => '_baydemir_featured',
		);
		if ( isset( $map[ $orderby ] ) ) {
			$query->set( 'meta_key', $map[ $orderby ] );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}

/**
 * Enqueue list-table assets.
 */
function baydemir_enqueue_list_admin_assets( string $hook ): void {
	if ( 'edit.php' !== $hook ) {
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

	if ( 'project' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_script(
		'baydemir-admin-list',
		baydemir_asset( 'js/admin-list.js' ),
		array( 'jquery' ),
		BAYDEMIR_VERSION,
		true
	);

	wp_localize_script(
		'baydemir-admin-list',
		'baydemirAdminList',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'baydemir_toggle_featured' ),
		)
	);
}

/**
 * AJAX: toggle project featured flag.
 */
function baydemir_ajax_toggle_featured(): void {
	check_ajax_referer( 'baydemir_toggle_featured', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
	if ( ! $post_id || 'project' !== get_post_type( $post_id ) ) {
		wp_send_json_error( array( 'message' => 'invalid_post' ), 400 );
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	$current = (string) get_post_meta( $post_id, '_baydemir_featured', true );
	$next    = '1' === $current ? '0' : '1';
	update_post_meta( $post_id, '_baydemir_featured', $next );

	wp_send_json_success(
		array(
			'featured' => '1' === $next,
		)
	);
}
