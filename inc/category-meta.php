<?php
/**
 * Project category term image + drag-and-drop order (Projeler → Kategoriler).
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'project_category_add_form_fields', 'baydemir_category_add_image_field' );
add_action( 'project_category_edit_form_fields', 'baydemir_category_edit_image_field' );
add_action( 'created_project_category', 'baydemir_category_save_image' );
add_action( 'edited_project_category', 'baydemir_category_save_image' );
add_action( 'created_project_category', 'baydemir_category_set_default_order', 20 );
add_action( 'admin_enqueue_scripts', 'baydemir_category_admin_assets' );
add_action( 'admin_notices', 'baydemir_category_order_notice' );
add_action( 'wp_ajax_baydemir_reorder_categories', 'baydemir_ajax_reorder_categories' );
add_filter( 'manage_edit-project_category_columns', 'baydemir_category_columns' );
add_filter( 'manage_project_category_custom_column', 'baydemir_category_column_content', 10, 3 );
add_filter( 'get_terms', 'baydemir_sort_project_categories', 10, 3 );

/**
 * Add form: category image.
 */
function baydemir_category_add_image_field(): void {
	?>
	<div class="form-field term-group baydemir-term-image-field">
		<label><?php esc_html_e( 'Kategori Görseli', 'baydemir' ); ?></label>
		<input type="hidden" id="baydemir_cover" name="baydemir_category_image" value="" />
		<div class="bd-cover is-empty" id="baydemir-cover-wrap">
			<div class="bd-cover__preview" id="baydemir-cover-preview"></div>
			<div class="bd-cover__empty"><?php esc_html_e( 'Görsel seçilmedi', 'baydemir' ); ?></div>
			<p class="bd-gallery-actions">
				<button type="button" class="button button-primary" id="baydemir-cover-add"><?php esc_html_e( 'Görsel Seç', 'baydemir' ); ?></button>
				<button type="button" class="button" id="baydemir-cover-clear"><?php esc_html_e( 'Kaldır', 'baydemir' ); ?></button>
			</p>
		</div>
		<p class="description"><?php esc_html_e( 'Ana sayfadaki kategori kartında görünür.', 'baydemir' ); ?></p>
	</div>
	<?php
}

/**
 * Edit form: category image.
 */
function baydemir_category_edit_image_field( WP_Term $term ): void {
	$image_id  = (int) get_term_meta( $term->term_id, '_baydemir_category_image', true );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
	?>
	<tr class="form-field term-group baydemir-term-image-field">
		<th scope="row">
			<label for="baydemir_category_image"><?php esc_html_e( 'Kategori Görseli', 'baydemir' ); ?></label>
		</th>
		<td>
			<input type="hidden" id="baydemir_cover" name="baydemir_category_image" value="<?php echo esc_attr( (string) $image_id ); ?>" />
			<div class="bd-cover<?php echo $image_id ? '' : ' is-empty'; ?>" id="baydemir-cover-wrap">
				<div class="bd-cover__preview" id="baydemir-cover-preview">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="" />
					<?php endif; ?>
				</div>
				<div class="bd-cover__empty"><?php esc_html_e( 'Görsel seçilmedi — varsayılan kullanılır', 'baydemir' ); ?></div>
				<p class="bd-gallery-actions">
					<button type="button" class="button button-primary" id="baydemir-cover-add"><?php esc_html_e( 'Görsel Seç', 'baydemir' ); ?></button>
					<button type="button" class="button" id="baydemir-cover-clear"><?php esc_html_e( 'Kaldır', 'baydemir' ); ?></button>
				</p>
			</div>
			<p class="description"><?php esc_html_e( 'Ana sayfadaki kategori kartında görünür.', 'baydemir' ); ?></p>
		</td>
	</tr>
	<?php
}

/**
 * Save category image term meta.
 */
function baydemir_category_save_image( int $term_id ): void {
	$taxonomy = get_taxonomy( 'project_category' );
	if ( ! $taxonomy || ! current_user_can( $taxonomy->cap->edit_terms ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- term form uses WP core nonce.
	if ( ! isset( $_POST['baydemir_category_image'] ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$image_id = absint( wp_unslash( $_POST['baydemir_category_image'] ) );
	if ( $image_id > 0 ) {
		update_term_meta( $term_id, '_baydemir_category_image', $image_id );
	} else {
		delete_term_meta( $term_id, '_baydemir_category_image' );
	}
}

/**
 * Append new categories to the end of the custom order.
 */
function baydemir_category_set_default_order( int $term_id ): void {
	$existing = get_term_meta( $term_id, '_baydemir_term_order', true );
	if ( '' !== $existing && null !== $existing ) {
		return;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'project_category',
			'hide_empty' => false,
			'fields'     => 'ids',
		)
	);
	$max = 0;
	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $id ) {
			$max = max( $max, (int) get_term_meta( (int) $id, '_baydemir_term_order', true ) );
		}
	}
	update_term_meta( $term_id, '_baydemir_term_order', $max + 1 );
}

/**
 * Assets on category screens.
 */
function baydemir_category_admin_assets( string $hook ): void {
	if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'project_category' !== $screen->taxonomy ) {
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
			'nonce'   => wp_create_nonce( 'baydemir_reorder_categories' ),
			'i18n'    => array(
				'coverTitle'  => __( 'Kategori Görseli', 'baydemir' ),
				'coverButton' => __( 'Seç', 'baydemir' ),
				'orderSaved'  => __( 'Sıra kaydedildi', 'baydemir' ),
				'orderError'  => __( 'Sıra kaydedilemedi', 'baydemir' ),
			),
		)
	);
}

/**
 * Admin notice on category list.
 */
function baydemir_category_order_notice(): void {
	$screen = get_current_screen();
	if ( ! $screen || 'edit-tags' !== $screen->base || 'project_category' !== $screen->taxonomy ) {
		return;
	}
	?>
	<div class="notice notice-info bd-cat-order-notice">
		<p><?php esc_html_e( 'Kategorileri sürükleyerek sıralayabilirsiniz. Bu sıra ana sayfa kartlarında ve proje filtrelerinde kullanılır.', 'baydemir' ); ?></p>
		<p id="baydemir-cat-order-status" class="bd-cat-order-status" hidden></p>
	</div>
	<?php
}

/**
 * List columns: drag handle + image.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function baydemir_category_columns( array $columns ): array {
	$new = array( 'bd_cat_order' => '' );
	foreach ( $columns as $key => $label ) {
		if ( 'name' === $key ) {
			$new['bd_cat_image'] = __( 'Görsel', 'baydemir' );
		}
		$new[ $key ] = $label;
	}
	return $new;
}

/**
 * List column content.
 *
 * @param string $content Content.
 * @param string $column  Column.
 * @param int    $term_id Term ID.
 */
function baydemir_category_column_content( string $content, string $column, int $term_id ): string {
	if ( 'bd_cat_order' === $column ) {
		return '<span class="bd-cat-order-handle" title="' . esc_attr__( 'Sürükle', 'baydemir' ) . '"><span class="dashicons dashicons-menu"></span></span>';
	}

	if ( 'bd_cat_image' !== $column ) {
		return $content;
	}

	$image_id = (int) get_term_meta( $term_id, '_baydemir_category_image', true );
	if ( $image_id ) {
		$url = wp_get_attachment_image_url( $image_id, array( 48, 48 ) );
		if ( $url ) {
			return '<img src="' . esc_url( $url ) . '" alt="" class="bd-admin-thumb" />';
		}
	}

	return '<span class="bd-admin-thumb bd-admin-thumb--empty">—</span>';
}

/**
 * AJAX: save category order.
 */
function baydemir_ajax_reorder_categories(): void {
	check_ajax_referer( 'baydemir_reorder_categories', 'nonce' );

	$taxonomy = get_taxonomy( 'project_category' );
	if ( ! $taxonomy || ! current_user_can( $taxonomy->cap->edit_terms ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	$order = isset( $_POST['order'] ) ? wp_unslash( $_POST['order'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! is_array( $order ) ) {
		wp_send_json_error( array( 'message' => 'invalid' ), 400 );
	}

	$ids = array_values( array_filter( array_map( 'absint', $order ) ) );
	foreach ( $ids as $index => $term_id ) {
		$term = get_term( $term_id, 'project_category' );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}
		update_term_meta( $term_id, '_baydemir_term_order', $index );
	}

	wp_send_json_success();
}

/**
 * Sort project categories by custom term order meta.
 *
 * @param array<int, WP_Term>|WP_Error $terms    Terms.
 * @param array<int, string>|string    $taxonomies Taxonomies.
 * @param array<string, mixed>         $args     Query args.
 * @return array<int, WP_Term>|WP_Error
 */
function baydemir_sort_project_categories( $terms, $taxonomies, array $args ) {
	if ( is_wp_error( $terms ) || empty( $terms ) || ! is_array( $terms ) ) {
		return $terms;
	}

	$taxonomies = (array) $taxonomies;
	if ( ! in_array( 'project_category', $taxonomies, true ) ) {
		return $terms;
	}

	// Respect explicit orderby when caller asks for something else (except default name/none).
	$orderby = $args['orderby'] ?? 'name';
	if ( is_array( $orderby ) ) {
		return $terms;
	}
	if ( is_string( $orderby ) && ! in_array( $orderby, array( 'name', 'none', '', 'term_id' ), true ) ) {
		return $terms;
	}

	if ( ! empty( $args['fields'] ) && 'all' !== $args['fields'] && 'all_with_object_id' !== $args['fields'] ) {
		return $terms;
	}

	usort(
		$terms,
		static function ( $a, $b ): int {
			if ( ! ( $a instanceof WP_Term ) || ! ( $b instanceof WP_Term ) ) {
				return 0;
			}
			$oa = (int) get_term_meta( $a->term_id, '_baydemir_term_order', true );
			$ob = (int) get_term_meta( $b->term_id, '_baydemir_term_order', true );
			if ( $oa === $ob ) {
				return strcasecmp( $a->name, $b->name );
			}
			return $oa <=> $ob;
		}
	);

	return $terms;
}
