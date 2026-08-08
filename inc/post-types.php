<?php
/**
 * Custom post types & taxonomies.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'baydemir_register_post_types' );

/**
 * Register Projects & Services CPTs.
 */
function baydemir_register_post_types(): void {
	register_post_type(
		'project',
		array(
			'labels'              => array(
				'name'               => __( 'Projeler', 'baydemir' ),
				'singular_name'      => __( 'Proje', 'baydemir' ),
				'add_new'            => __( 'Yeni Ekle', 'baydemir' ),
				'add_new_item'       => __( 'Yeni Proje Ekle', 'baydemir' ),
				'edit_item'          => __( 'Projeyi Düzenle', 'baydemir' ),
				'new_item'           => __( 'Yeni Proje', 'baydemir' ),
				'view_item'          => __( 'Projeyi Görüntüle', 'baydemir' ),
				'search_items'       => __( 'Proje Ara', 'baydemir' ),
				'not_found'          => __( 'Proje bulunamadı', 'baydemir' ),
				'not_found_in_trash' => __( 'Çöpte proje yok', 'baydemir' ),
				'all_items'          => __( 'Tüm Projeler', 'baydemir' ),
				'menu_name'          => __( 'Projeler', 'baydemir' ),
			),
			'public'              => true,
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'projeler' ),
			'menu_icon'           => 'dashicons-building',
			'menu_position'       => 5,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
			'taxonomies'          => array( 'project_category' ),
		)
	);

	register_taxonomy(
		'project_category',
		'project',
		array(
			'labels'            => array(
				'name'          => __( 'Proje Kategorileri', 'baydemir' ),
				'singular_name' => __( 'Proje Kategorisi', 'baydemir' ),
				'search_items'  => __( 'Kategori Ara', 'baydemir' ),
				'all_items'     => __( 'Tüm Kategoriler', 'baydemir' ),
				'edit_item'     => __( 'Kategoriyi Düzenle', 'baydemir' ),
				'update_item'   => __( 'Kategoriyi Güncelle', 'baydemir' ),
				'add_new_item'  => __( 'Yeni Kategori Ekle', 'baydemir' ),
				'menu_name'     => __( 'Kategoriler', 'baydemir' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'proje-kategori' ),
		)
	);

	register_post_type(
		'service',
		array(
			'labels'              => array(
				'name'               => __( 'Hizmetler', 'baydemir' ),
				'singular_name'      => __( 'Hizmet', 'baydemir' ),
				'add_new'            => __( 'Yeni Ekle', 'baydemir' ),
				'add_new_item'       => __( 'Yeni Hizmet Ekle', 'baydemir' ),
				'edit_item'          => __( 'Hizmeti Düzenle', 'baydemir' ),
				'new_item'           => __( 'Yeni Hizmet', 'baydemir' ),
				'view_item'          => __( 'Hizmeti Görüntüle', 'baydemir' ),
				'search_items'       => __( 'Hizmet Ara', 'baydemir' ),
				'not_found'          => __( 'Hizmet bulunamadı', 'baydemir' ),
				'all_items'          => __( 'Tüm Hizmetler', 'baydemir' ),
				'menu_name'          => __( 'Hizmetler', 'baydemir' ),
			),
			'public'              => true,
			'has_archive'         => false,
			'publicly_queryable'  => true,
			'rewrite'             => array( 'slug' => 'hizmet' ),
			'menu_icon'           => 'dashicons-hammer',
			'menu_position'       => 6,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
		)
	);
}

/**
 * Default project categories on theme switch.
 */
add_action(
	'after_switch_theme',
	static function (): void {
		$defaults = array(
			'konut'      => 'Konut Projeleri',
			'villa'      => 'Villa Projeleri',
			'ticari'     => 'Ticari Yapılar',
			'devam-eden' => 'Devam Eden Projeler',
		);

		foreach ( $defaults as $slug => $name ) {
			if ( ! term_exists( $slug, 'project_category' ) ) {
				wp_insert_term( $name, 'project_category', array( 'slug' => $slug ) );
			}
		}

		flush_rewrite_rules();
	}
);
