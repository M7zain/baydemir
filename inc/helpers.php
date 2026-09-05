<?php
/**
 * Helper functions.
 *
 * @package Baydemir
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme mod with default.
 */
function baydemir_mod( string $key, mixed $default = '' ): mixed {
	return get_theme_mod( $key, $default );
}

/**
 * Company display name.
 */
function baydemir_company_name(): string {
	return (string) baydemir_mod( 'baydemir_company_name', 'Baydemir İnşaat' );
}

/**
 * Phone number.
 */
function baydemir_phone(): string {
	return (string) baydemir_mod( 'baydemir_phone', '0530 231 40 00' );
}

/**
 * Phone tel: href.
 */
function baydemir_phone_href(): string {
	$phone = preg_replace( '/\D+/', '', baydemir_phone() );
	return 'tel:+' . ( str_starts_with( (string) $phone, '90' ) ? $phone : '90' . ltrim( (string) $phone, '0' ) );
}

/**
 * Email.
 */
function baydemir_email(): string {
	return (string) baydemir_mod( 'baydemir_email', 'ibrhmbaydemir@windowslive.com' );
}

/**
 * Address.
 */
function baydemir_address(): string {
	return (string) baydemir_mod(
		'baydemir_address',
		'Cumhuriyet Mahallesi 2. Gazi Sokak No:2 Kat:2 Merkez / Kütahya'
	);
}

/**
 * Working hours.
 */
function baydemir_hours(): string {
	return (string) baydemir_mod( 'baydemir_hours', 'Pazartesi – Cumartesi: 09:00 – 18:00' );
}

/**
 * WhatsApp number (digits / display string from Customizer).
 */
function baydemir_whatsapp(): string {
	return (string) baydemir_mod( 'baydemir_whatsapp', '905302314000' );
}

/**
 * Digits-only WhatsApp number for wa.me links.
 */
function baydemir_whatsapp_digits(): string {
	return (string) preg_replace( '/\D+/', '', baydemir_whatsapp() );
}

/**
 * Whether the floating WhatsApp button should render.
 */
function baydemir_whatsapp_button_enabled(): bool {
	$show = (string) baydemir_mod( 'baydemir_whatsapp_show', '1' );
	return '1' === $show && '' !== baydemir_whatsapp_digits();
}

/**
 * WhatsApp chat URL, or empty if unavailable.
 */
function baydemir_whatsapp_url(): string {
	$digits = baydemir_whatsapp_digits();
	if ( '' === $digits ) {
		return '';
	}
	return 'https://wa.me/' . $digits;
}

/**
 * Map embed URL.
 */
function baydemir_map_embed(): string {
	return (string) baydemir_mod(
		'baydemir_map_embed',
		'https://maps.google.com/maps?q=K%C3%BCtahya%20Merkez&t=&z=14&ie=UTF8&iwloc=&output=embed'
	);
}

/**
 * CTA page URL (Teklif Al).
 */
function baydemir_cta_url(): string {
	$page_id = (int) baydemir_mod( 'baydemir_cta_page', 0 );
	if ( $page_id ) {
		return (string) get_permalink( $page_id );
	}
	$contact = get_page_by_path( 'iletisim' );
	return $contact ? (string) get_permalink( $contact ) : home_url( '/iletisim/' );
}

/**
 * Contact page URL with project context for Teklif Al.
 */
function baydemir_project_cta_url( int $project_id ): string {
	$url = baydemir_cta_url();
	if ( $project_id <= 0 || 'project' !== get_post_type( $project_id ) ) {
		return $url;
	}
	return add_query_arg( 'proje', $project_id, $url ) . '#bd-contact-form';
}

/**
 * Project referenced on the contact form (?proje=ID).
 */
function baydemir_contact_form_project(): ?WP_Post {
	if ( ! isset( $_GET['proje'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return null;
	}

	$project_id = absint( wp_unslash( $_GET['proje'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $project_id || 'project' !== get_post_type( $project_id ) ) {
		return null;
	}

	$post = get_post( $project_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return null;
	}

	return $post;
}

/**
 * Accent color from customizer.
 */
function baydemir_accent_color(): string {
	return (string) baydemir_mod( 'baydemir_accent_color', '#2f7dff' );
}

/**
 * Brand mark markup (custom image or default house icon).
 * Accepts PNG, JPG, WebP, SVG uploads from Customizer.
 * Used in footer, CTA bar, and similar brand spots.
 */
function baydemir_brand_mark( string $class = 'bd-brand-mark' ): string {
	$id = (int) baydemir_mod( 'baydemir_brand_mark', 0 );
	if ( $id > 0 ) {
		$url = wp_get_attachment_image_url( $id, 'thumbnail' );
		if ( ! $url ) {
			$url = wp_get_attachment_image_url( $id, 'medium' );
		}
		if ( ! $url ) {
			$url = wp_get_attachment_url( $id );
		}
		if ( $url ) {
			return sprintf(
				'<span class="%1$s bd-brand-mark--image" aria-hidden="true"><img src="%2$s" alt="" width="28" height="28" decoding="async" /></span>',
				esc_attr( $class ),
				esc_url( $url )
			);
		}
	}

	$default = BAYDEMIR_DIR . '/assets/images/brand-mark.png';
	if ( file_exists( $default ) ) {
		return sprintf(
			'<span class="%1$s bd-brand-mark--image" aria-hidden="true"><img src="%2$s" alt="" width="28" height="28" decoding="async" /></span>',
			esc_attr( $class ),
			esc_url( baydemir_asset( 'images/brand-mark.png' ) )
		);
	}

	return baydemir_icon( 'house', trim( $class . ' bd-icon' ) );
}

/**
 * Lighten a hex color for hover states.
 */
function baydemir_hex_lighter( string $hex, int $amount = 28 ): string {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) ) {
		return '#4d91ff';
	}

	$r = min( 255, hexdec( substr( $hex, 0, 2 ) ) + $amount );
	$g = min( 255, hexdec( substr( $hex, 2, 2 ) ) + $amount );
	$b = min( 255, hexdec( substr( $hex, 4, 2 ) ) + $amount );

	return sprintf( '#%02x%02x%02x', $r, $g, $b );
}

/**
 * Inline CSS variables from Customizer colors.
 */
function baydemir_theme_colors_css(): string {
	$accent         = baydemir_accent_color();
	$bg             = (string) baydemir_mod( 'baydemir_bg_color', '#07090f' );
	$text           = (string) baydemir_mod( 'baydemir_text_color', '#ffffff' );
	$muted          = (string) baydemir_mod( 'baydemir_muted_color', '#a8b0c0' );
	$hero_eyebrow   = (string) baydemir_mod( 'baydemir_hero_eyebrow_color', $accent );
	$hero_title     = (string) baydemir_mod( 'baydemir_hero_title_color', '#ffffff' );
	$hero_text      = (string) baydemir_mod( 'baydemir_hero_text_color', '#a8b0c0' );
	$hero_highlight = (string) baydemir_mod( 'baydemir_hero_highlight_color', $accent );

	return sprintf(
		':root{--bd-bg:%1$s;--bd-text:%2$s;--bd-muted:%3$s;--bd-accent:%4$s;--bd-accent-rgb:%5$s;--bd-accent-hover:%6$s;--bd-hero-eyebrow:%7$s;--bd-hero-title:%8$s;--bd-hero-text:%9$s;--bd-hero-highlight:%10$s;}',
		esc_attr( $bg ),
		esc_attr( $text ),
		esc_attr( $muted ),
		esc_attr( $accent ),
		esc_attr( baydemir_hex_to_rgb_csv( $accent ) ),
		esc_attr( baydemir_hex_lighter( $accent ) ),
		esc_attr( $hero_eyebrow ),
		esc_attr( $hero_title ),
		esc_attr( $hero_text ),
		esc_attr( $hero_highlight )
	);
}

/**
 * Escape and allow basic HTML in theme text.
 */
function baydemir_kses( string $text ): string {
	return wp_kses(
		$text,
		array(
			'br'     => array(),
			'strong' => array(),
			'em'     => array(),
			'span'   => array( 'class' => true ),
			'a'      => array(
				'href'   => true,
				'target' => true,
				'rel'    => true,
				'class'  => true,
			),
		)
	);
}

/**
 * Highlight words wrapped in [[word]] as accent spans.
 */
function baydemir_highlight( string $text ): string {
	$converted = preg_replace( '/\[\[(.+?)\]\]/', '<span class="bd-accent">$1</span>', $text );
	return baydemir_kses( (string) $converted );
}

/**
 * Theme image URL helper.
 */
function baydemir_asset( string $relative ): string {
	return BAYDEMIR_URI . '/assets/' . ltrim( $relative, '/' );
}

/**
 * Whether the homepage uses the interactive lights banner.
 */
function baydemir_hero_uses_lights_banner(): bool {
	return file_exists( BAYDEMIR_DIR . '/assets/lights-on.jpg' )
		&& file_exists( BAYDEMIR_DIR . '/assets/lights-off.jpg' );
}

/**
 * Interactive hero banner image URLs.
 */
function baydemir_hero_lights_on_url(): string {
	return baydemir_asset( 'lights-on.jpg' );
}

function baydemir_hero_lights_off_url(): string {
	return baydemir_asset( 'lights-off.jpg' );
}

/**
 * Placeholder image URL (local theme asset or picsum).
 */
function baydemir_placeholder( string $key = 'building', int $w = 1200, int $h = 800 ): string {
	$map = array(
		'building'     => 'images/hero-home.jpg',
		'building2'    => 'images/hero-secondary.jpg',
		'villa'        => 'images/category-villa.jpg',
		'commercial'   => 'images/category-ticari.jpg',
		'konut'        => 'images/category-konut.jpg',
		'ongoing'      => 'images/category-ongoing.jpg',
		'interior'     => 'images/placeholder-interior.jpg',
		'construction' => 'images/placeholder-construction.jpg',
		'blueprint'    => 'images/placeholder-blueprint.svg',
	);

	if ( isset( $map[ $key ] ) ) {
		$path = BAYDEMIR_DIR . '/assets/' . $map[ $key ];
		if ( file_exists( $path ) ) {
			return baydemir_asset( $map[ $key ] );
		}
	}

	return sprintf( 'https://picsum.photos/seed/baydemir-%s/%d/%d', rawurlencode( $key ), $w, $h );
}

/**
 * Page hero background: Featured Image, else theme asset path, else placeholder key.
 *
 * @param int         $post_id       Page ID (0 = current).
 * @param string      $fallback_key  Placeholder key when no featured image / asset.
 * @param string|null $fallback_asset Optional relative assets path e.g. images/kalite.jpg.
 */
function baydemir_page_hero_image( int $post_id = 0, string $fallback_key = 'commercial', ?string $fallback_asset = null ): string {
	$id = $post_id > 0 ? $post_id : get_the_ID();
	if ( $id ) {
		$url = get_the_post_thumbnail_url( $id, 'baydemir-hero' );
		if ( ! $url ) {
			$url = get_the_post_thumbnail_url( $id, 'large' );
		}
		if ( $url ) {
			return $url;
		}
	}

	if ( $fallback_asset ) {
		$path = BAYDEMIR_DIR . '/assets/' . ltrim( $fallback_asset, '/' );
		if ( file_exists( $path ) ) {
			return baydemir_asset( $fallback_asset );
		}
	}

	return baydemir_placeholder( $fallback_key, 1600, 900 );
}

/**
 * Homepage category cards: terms used on the front page.
 *
 * @return array<int, array{term: WP_Term, icon: string, blurb: string}>
 */
function baydemir_home_category_cards(): array {
	$defaults = array(
		'konut'      => array(
			'icon'  => 'building',
			'blurb' => __( 'Modern yaşam alanları.', 'baydemir' ),
		),
		'villa'      => array(
			'icon'  => 'home',
			'blurb' => __( 'Özgün tasarım, konforlu yaşam.', 'baydemir' ),
		),
		'ticari'     => array(
			'icon'  => 'shop',
			'blurb' => __( 'Fonksiyonel ve estetik çözümler.', 'baydemir' ),
		),
		'devam-eden' => array(
			'icon'  => 'crane',
			'blurb' => __( 'Geleceği birlikte inşa ediyoruz.', 'baydemir' ),
		),
	);

	$terms = get_terms(
		array(
			'taxonomy'   => 'project_category',
			'hide_empty' => false,
			'number'     => 12,
		)
	);
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	$icons  = array( 'building', 'home', 'shop', 'crane' );
	$cards  = array();
	$i      = 0;
	foreach ( $terms as $term ) {
		$meta  = $defaults[ $term->slug ] ?? null;
		$blurb = $term->description ? $term->description : ( $meta['blurb'] ?? __( 'Projeleri incele.', 'baydemir' ) );
		$cards[] = array(
			'term'  => $term,
			'icon'  => $meta['icon'] ?? $icons[ $i % count( $icons ) ],
			'blurb' => $blurb,
		);
		++$i;
		if ( count( $cards ) >= 4 ) {
			break;
		}
	}

	return $cards;
}

/**
 * Homepage / archive category image URL.
 * Priority: Projeler → Kategoriler term image → Customizer → theme default.
 *
 * @param string|WP_Term $term_or_slug Category slug or term object.
 */
function baydemir_home_category_image( $term_or_slug ): string {
	$term = null;
	$slug = '';

	if ( $term_or_slug instanceof WP_Term ) {
		$term = $term_or_slug;
		$slug = $term->slug;
	} else {
		$slug = (string) $term_or_slug;
		$term = get_term_by( 'slug', $slug, 'project_category' );
	}

	if ( $term && ! is_wp_error( $term ) ) {
		$image_id = (int) get_term_meta( $term->term_id, '_baydemir_category_image', true );
		if ( $image_id > 0 ) {
			$url = wp_get_attachment_image_url( $image_id, 'large' );
			if ( $url ) {
				return $url;
			}
		}
	}

	$mod_map = array(
		'konut'      => 'baydemir_cat_img_konut',
		'villa'      => 'baydemir_cat_img_villa',
		'ticari'     => 'baydemir_cat_img_ticari',
		'devam-eden' => 'baydemir_cat_img_ongoing',
	);

	$placeholder_map = array(
		'konut'      => 'konut',
		'villa'      => 'villa',
		'ticari'     => 'commercial',
		'devam-eden' => 'ongoing',
	);

	$mod_key = $mod_map[ $slug ] ?? '';
	if ( $mod_key ) {
		$id = (int) baydemir_mod( $mod_key, 0 );
		if ( $id > 0 ) {
			$url = wp_get_attachment_image_url( $id, 'large' );
			if ( $url ) {
				return $url;
			}
		}
	}

	$placeholder_key = $placeholder_map[ $slug ] ?? 'building';
	return baydemir_placeholder( $placeholder_key, 640, 420 );
}

/**
 * Project meta getter.
 */
function baydemir_project_meta( int $post_id, string $key, mixed $default = '' ): mixed {
	$value = get_post_meta( $post_id, '_baydemir_' . $key, true );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Whether the Teklif Al button shows on this project's detail page.
 */
function baydemir_project_show_cta( int $post_id ): bool {
	$value = get_post_meta( $post_id, '_baydemir_show_cta', true );
	if ( '' === $value || null === $value ) {
		return true;
	}
	return '1' === (string) $value;
}

/**
 * Convert a YouTube or Vimeo URL to an embeddable iframe src.
 */
function baydemir_video_embed_url( string $url ): string {
	$url = trim( $url );
	if ( '' === $url ) {
		return '';
	}

	if ( preg_match( '#(?:youtube\.com/watch\?(?:[^&]+&)*v=|youtu\.be/|youtube\.com/embed/|youtube\.com/shorts/)([a-zA-Z0-9_-]{11})#', $url, $matches ) ) {
		return 'https://www.youtube.com/embed/' . $matches[1] . '?rel=0';
	}

	if ( preg_match( '#vimeo\.com/(?:video/)?(\d+)#', $url, $matches ) ) {
		return 'https://player.vimeo.com/video/' . $matches[1];
	}

	return '';
}

/**
 * Project category label.
 */
function baydemir_project_category_label( int $post_id ): string {
	$terms = get_the_terms( $post_id, 'project_category' );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return __( 'Proje', 'baydemir' );
	}
	return $terms[0]->name;
}

/**
 * SVG icon by name.
 */
function baydemir_icon( string $name, string $class = 'bd-icon' ): string {
	$icons = array(
		'arrow'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>',
		'building'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M9 10h.01M15 10h.01M9 14h.01M15 14h.01"/></svg>',
		'house'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 20V9.5L12 4l7 5.5V20"/><path d="M10 20v-5h4v5"/><path d="M3.5 20h17"/></svg>',
		'home'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1v-10.5z"/></svg>',
		'shop'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 9l1-5h16l1 5M4 9v11h16V9M9 21V13h6v8"/></svg>',
		'crane'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 21h18M6 21V10l12-6v4M12 8v13M8 21v-4h8v4"/></svg>',
		'star'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m12 3 2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 18.3 6.2 21l1.1-6.5L2.6 9.8l6.5-.9L12 3z"/></svg>',
		'shield'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 3l8 4v5c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V7l8-4z"/><path d="m9 12 2 2 4-4"/></svg>',
		'link'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M10 13a5 5 0 0 0 7.07 0l2.12-2.12a5 5 0 0 0-7.07-7.07L10.7 5.23"/><path d="M14 11a5 5 0 0 0-7.07 0L4.8 13.12a5 5 0 0 0 7.07 7.07L13.3 18.77"/></svg>',
		'cube'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3z"/><path d="M12 12 4 7.5M12 12l8-4.5M12 12v9"/></svg>',
		'leaf'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 21c4-3 7-7 7-12V5a2 2 0 0 0-2-2h-2C8 3 4 8 4 14a7 7 0 0 0 7 7h1z"/><path d="M9 12c3 0 6-3 7-7"/></svg>',
		'target'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>',
		'layers'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m12 3 9 5-9 5-9-5 9-5z"/><path d="m3 12 9 5 9-5"/><path d="m3 17 9 5 9-5"/></svg>',
		'award'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="8" r="5"/><path d="M8.5 13 7 21l5-2 5 2-1.5-8"/></svg>',
		'clock'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
		'users'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="3"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a3 3 0 0 1 0 5.74"/></svg>',
		'map-pin'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 22s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>',
		'phone'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.6a2 2 0 0 1-.4 2.1L8.1 9.9a16 16 0 0 0 6 6l1.5-1.2a2 2 0 0 1 2.1-.4c.8.3 1.7.5 2.6.6a2 2 0 0 1 1.7 2z"/></svg>',
		'mail'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>',
		'play'       => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7L8 5z"/></svg>',
		'grid'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
		'helmet'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 14a8 8 0 0 1 16 0"/><path d="M2 14h20v2a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-2z"/><path d="M12 6v4"/></svg>',
		'pen'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
		'blueprint'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M8 8h8M8 12h5M8 16h6"/><path d="m14 14 4 4M18 14l-4 4"/></svg>',
		'file-check' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="m9 15 2 2 4-4"/></svg>',
		'clipboard'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 13 2 2 4-4"/></svg>',
		'gear'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9c.3.6.9 1 1.6 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>',
		'key'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="8" cy="15" r="4"/><path d="m11 12 9-9 3 3-2 2-2-2-2 2 2 2-2 2"/></svg>',
		'foundation' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 14h16M6 14v4h12v-4M8 10v4M12 8v6M16 10v4M3 20h18"/></svg>',
		'pillar'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 21h12M8 21V9h8v12M5 9h14M7 5h10l-1 4H8L7 5z"/></svg>',
		'brick'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="4" width="18" height="5" rx="0.5"/><rect x="3" y="10" width="8" height="5" rx="0.5"/><rect x="13" y="10" width="8" height="5" rx="0.5"/><rect x="3" y="16" width="18" height="4" rx="0.5"/></svg>',
		'wall'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 7h18v12H3zM3 11h18M3 15h18M9 7v12M15 7v12"/></svg>',
		'bolt'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M13 2 6 13h5l-1 9 8-12h-5l0-8z"/></svg>',
		'pipes'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 8h8v3H4zM12 8h4v8h-4zM16 13h4v3h-4zM7 11v6M5 17h6"/></svg>',
		'roof'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m3 12 9-8 9 8M5 11v9h14v-9"/><path d="M16 6.5c1.2-1.5 3.2-1.2 3.8.4"/></svg>',
		'facade'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="4" y="3" width="16" height="18" rx="1"/><path d="M8 7h2v2H8zM14 7h2v2h-2zM8 12h2v2H8zM14 12h2v2h-2zM8 17h8"/></svg>',
		'trowel'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m14 4 6 6-8 3-4 8-3-3 8-4z"/><path d="m9 17-3 3"/></svg>',
		'rebar'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M7 4v16M12 4v16M17 4v16M5 8h4M10 12h4M15 16h4"/></svg>',
		'mixer'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="7" cy="18" r="2.5"/><circle cx="17" cy="18" r="2.5"/><path d="M4 18h1.5M9.5 18H14M19.5 18H21"/><path d="M5 14h10l3-5H9l-4 5z"/><path d="M14 9V5h4l2 4"/></svg>',
		'thermometer'=> '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"><path d="M10 14.5V6a2 2 0 1 1 4 0v8.5a3.5 3.5 0 1 1-4 0z"/><path d="M12 16v-6"/><path d="M16 7h3M16 10h2"/></svg>',
		'droplet'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"><path d="M12 3c3.5 5 7 8.2 7 12a7 7 0 1 1-14 0c0-3.8 3.5-7 7-12z"/><path d="M12 16a3 3 0 0 0 2.5-4"/></svg>',
		'paint'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h12v4H4z"/><path d="M8 10v8a2 2 0 0 0 4 0v-2h6"/><path d="M18 16v4"/></svg>',
		'broom'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="m14 3 7 7"/><path d="m15.5 4.5-8 8"/><path d="M4 14l6 6 3-1-8-8z"/><path d="M5 19l-1 2M8 20l-1 2M11 19l-1 2"/></svg>',
		'package'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3z"/><path d="M12 12 4 7.5M12 12l8-4.5M12 12v9"/><path d="M12 3v4"/></svg>',
		'quote'      => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.5 7.5C7 7.5 5 9.6 5 12.3c0 1.7.8 3.1 2.1 4L5.8 19.5c-.3.5.2 1.1.8.9C9.8 19.4 12 16.7 12 13.2 12 10 11 7.5 9.5 7.5zm9 0C16 7.5 14 9.6 14 12.3c0 1.7.8 3.1 2.1 4l-1.3 3.2c-.3.5.2 1.1.8.9 3.2-1 5.4-3.7 5.4-7.2 0-3.2-1-5.7-2.5-5.7z"/></svg>',
		'star-fill'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="m12 3 2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 18.3 6.2 21l1.1-6.5L2.6 9.8l6.5-.9L12 3z"/></svg>',
		'check'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25"><path d="m5 12 5 5L20 7"/></svg>',
		'compass'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2.2 5.8-5.8 2.2 2.2-5.8z"/><circle cx="12" cy="12" r="1.2"/></svg>',
		'headset'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 12a9 9 0 0 1 18 0"/><path d="M21 12v4a2 2 0 0 1-2 2h-1"/><path d="M3 12v4a2 2 0 0 0 2 2h1"/><path d="M8 20h8"/><rect x="2" y="11" width="4" height="6" rx="1"/><rect x="18" y="11" width="4" height="6" rx="1"/></svg>',
		'menu'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
		'close'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6 6 18"/></svg>',
		'whatsapp'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.5 3.5A11 11 0 0 0 3.4 17.7L2 22l4.4-1.3A11 11 0 1 0 20.5 3.5zM12 20.2a9.1 9.1 0 0 1-4.6-1.3l-.3-.2-2.7.8.8-2.6-.2-.3A9.1 9.1 0 1 1 12 20.2zm5.2-6.8c-.3-.1-1.7-.8-1.9-.9s-.5-.1-.7.1-.8.9-1 1.1-.4.2-.7.1a7.4 7.4 0 0 1-2.2-1.4 8.2 8.2 0 0 1-1.5-1.9c-.2-.3 0-.4.1-.6l.5-.6c.1-.2.1-.3 0-.5l-.9-2.1c-.2-.5-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4s-1 1-1 2.4 1.1 2.8 1.2 3 2.1 3.2 5.1 4.4c.7.3 1.3.5 1.7.6.7.2 1.4.2 1.9.1.6-.1 1.7-.7 2-1.4.2-.7.2-1.3.2-1.4 0-.1-.3-.2-.6-.3z"/></svg>',
	);

	$svg = $icons[ $name ] ?? $icons['arrow'];
	return sprintf( '<span class="%s" aria-hidden="true">%s</span>', esc_attr( $class ), $svg );
}

/**
 * Default Hakkımızda body copy.
 */
function baydemir_about_default_content(): string {
	return "Baydemir İnşaat, temelleri 2008 yılında atılan güçlü bir ticari birikimin üzerine kurulmuştur. 2008 yılından bu yana özellikle madencilik sektörüne yönelik profesyonel nakliye hizmetleri sunan firmamız, güvenilir hizmet anlayışı, güçlü araç filosu ve operasyonel başarısıyla bölgesinde sektörün öncü ve lider firmaları arasında yer almaktadır.\n\nYıllar içinde kazandığı tecrübe, disiplin ve kalite anlayışını 2020 yılında inşaat sektörüne taşıyan Baydemir İnşaat, kısa sürede modern yaşam alanları ve nitelikli yapı projeleriyle adından söz ettirmeyi başarmıştır.\n\nBugüne kadar 20’nin üzerinde konut, yaşam alanı, ticari yapı ve kurumsal projeyi başarıyla tamamlayan firmamız, farklı ölçeklerde geliştirdiği prestij projeleriyle bulunduğu bölgelere değer katmaktadır. Hâlen yapımı devam eden yeni projeleriyle de geleceğin yaşam standartlarını bugünden inşa etmektedir.\n\nHer projemizde; güven, kalite, sağlam mühendislik, estetik mimari ve zamanında teslim ilkelerini ön planda tutuyor, sadece yapılar değil, uzun yıllar güvenle kullanılacak yaşam alanları inşa ediyoruz.\n\n2008 yılından bu yana oluşturduğumuz güven ve iş disipliniyle, hem madencilik sektöründeki profesyonel nakliye faaliyetlerimizi başarıyla sürdürmeye hem de inşaat alanında prestijli projeler üretmeye kararlılıkla devam ediyoruz.";
}

/**
 * Default feature cards for Hakkımızda.
 *
 * @return array<int, array{icon: string, title: string, text: string}>
 */
function baydemir_about_default_features(): array {
	return array(
		array(
			'icon'  => 'shield',
			'title' => 'Güvenilir İş Ortaklığı',
			'text'  => 'Verdiğimiz sözlerin arkasında durur, projelerimizi şeffaf ve planlı şekilde yürütürüz.',
		),
		array(
			'icon'  => 'blueprint',
			'title' => 'Projeye Özel Çözümler',
			'text'  => 'Her arsa ve her ihtiyaç farklıdır. Size özel projeler geliştiriyoruz.',
		),
		array(
			'icon'  => 'brick',
			'title' => 'Premium Malzeme',
			'text'  => 'Dayanıklılığı kanıtlanmış, yüksek kalite standartlarına sahip malzemeler tercih ediyoruz.',
		),
		array(
			'icon'  => 'helmet',
			'title' => 'Uzman Mühendislik',
			'text'  => 'Mimar, mühendis ve saha ekiplerimiz tüm süreci koordineli yönetir.',
		),
		array(
			'icon'  => 'foundation',
			'title' => 'Deprem Yönetmeliğine Uygun',
			'text'  => 'Güncel yönetmeliklere uygun, güvenli taşıyıcı sistemlerle inşa ediyoruz.',
		),
		array(
			'icon'  => 'leaf',
			'title' => 'Enerji Verimli Yapılar',
			'text'  => 'Isı ve su yalıtımıyla konforlu, uzun ömürlü ve ekonomik yaşam alanları oluşturuyoruz.',
		),
	);
}

/**
 * Hakkımızda settings (editable in admin).
 *
 * @return array{
 *   page_title: string,
 *   eyebrow: string,
 *   content: string,
 *   image_id: int,
 *   story_title: string,
 *   story_content: string,
 *   story_image_id: int,
 *   features: array<int, array{icon: string, title: string, text: string}>
 * }
 */
function baydemir_about_settings(): array {
	$defaults = array(
		'page_title'     => 'Hakkımızda',
		'eyebrow'        => (string) baydemir_mod( 'baydemir_about_title', 'Biz Kimiz?' ),
		'content'        => baydemir_about_default_content(),
		'image_id'       => 0,
		'story_title'    => __( 'Vizyonumuz', 'baydemir' ),
		'story_content'  => __( "Kaliteli malzeme, uzman ekip ve şeffaf süreç yönetimiyle yaşanabilir yapılar üretiyoruz.\n\nHer projede güven, estetik ve zamanında teslim ilkelerini bir arada tutarak uzun ömürlü yaşam alanları inşa ediyoruz.", 'baydemir' ),
		'story_image_id' => 0,
		'features'       => baydemir_about_default_features(),
	);

	$stored = get_option( 'baydemir_about', array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	if ( empty( $stored ) ) {
		$page = get_page_by_path( 'hakkimizda' );
		if ( $page instanceof WP_Post ) {
			if ( $page->post_title ) {
				$defaults['page_title'] = $page->post_title;
			}
			if ( trim( (string) $page->post_content ) ) {
				$defaults['content'] = wp_strip_all_tags( (string) $page->post_content );
			}
			$thumb = (int) get_post_thumbnail_id( $page );
			if ( $thumb ) {
				$defaults['image_id'] = $thumb;
			}
		}
	}

	$settings                    = array_merge( $defaults, $stored );
	$settings['page_title']      = (string) ( $settings['page_title'] ?? $defaults['page_title'] );
	$settings['eyebrow']         = (string) ( $settings['eyebrow'] ?? $defaults['eyebrow'] );
	$settings['content']         = (string) ( $settings['content'] ?? $defaults['content'] );
	$settings['image_id']        = (int) ( $settings['image_id'] ?? 0 );
	$settings['story_title']     = (string) ( $settings['story_title'] ?? $defaults['story_title'] );
	$settings['story_content']   = (string) ( $settings['story_content'] ?? $defaults['story_content'] );
	$settings['story_image_id']  = (int) ( $settings['story_image_id'] ?? 0 );

	$features = array();
	$source   = isset( $settings['features'] ) && is_array( $settings['features'] ) ? $settings['features'] : $defaults['features'];
	foreach ( baydemir_about_default_features() as $i => $default_feature ) {
		$row = isset( $source[ $i ] ) && is_array( $source[ $i ] ) ? $source[ $i ] : array();
		$features[] = array(
			'icon'  => sanitize_key( (string) ( $row['icon'] ?? $default_feature['icon'] ) ),
			'title' => (string) ( $row['title'] ?? $default_feature['title'] ),
			'text'  => (string) ( $row['text'] ?? $default_feature['text'] ),
		);
	}
	$settings['features'] = $features;

	return $settings;
}

/**
 * Hakkımızda hero image URL.
 */
function baydemir_about_image_url(): string {
	$settings = baydemir_about_settings();
	$id       = (int) $settings['image_id'];
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( $url ) {
			return $url;
		}
	}
	return baydemir_placeholder( 'villa', 1000, 800 );
}

/**
 * Hakkımızda alt bölüm (metin + görsel) image URL.
 */
function baydemir_about_story_image_url(): string {
	$settings = baydemir_about_settings();
	$id       = (int) $settings['story_image_id'];
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( $url ) {
			return $url;
		}
	}
	return baydemir_placeholder( 'interior', 1000, 800 );
}

/**
 * Ana sayfa Biz Kimiz görseli (Hakkımızda sayfasından bağımsız).
 */
function baydemir_home_about_image_url(): string {
	$id = (int) baydemir_mod( 'baydemir_home_about_image', 0 );
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, 'large' );
		if ( $url ) {
			return $url;
		}
	}

	return baydemir_placeholder( 'building', 1000, 800 );
}

/**
 * Default İletişim hero cards.
 *
 * @return array<int, array{title: string, text: string, image_id: int}>
 */
function baydemir_contact_hero_default_cards(): array {
	return array(
		array(
			'title'    => 'Tasarım & Planlama',
			'text'     => 'İhtiyaçlarınızı dinler, proje ve planlamayı birlikte şekillendiririz.',
			'image_id' => 0,
		),
		array(
			'title'    => 'İnşaat Süreci',
			'text'     => 'Sahada uzman ekibimizle kaliteli ve planlı üretim yürütürüz.',
			'image_id' => 0,
		),
		array(
			'title'    => 'Anahtar Teslim',
			'text'     => 'Tamamlanan projenizi güvenle teslim eder, sonrasında da yanınızda oluruz.',
			'image_id' => 0,
		),
	);
}

/**
 * İletişim hero settings (main image + 3 cards).
 *
 * @return array{
 *   main_image_id: int,
 *   cards: array<int, array{title: string, text: string, image_id: int}>
 * }
 */
function baydemir_contact_hero_settings(): array {
	$defaults = array(
		'main_image_id' => 0,
		'cards'         => baydemir_contact_hero_default_cards(),
	);

	$stored = get_option( 'baydemir_contact_hero', array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	$settings                  = array_merge( $defaults, $stored );
	$settings['main_image_id'] = (int) ( $settings['main_image_id'] ?? 0 );

	$cards  = array();
	$source = isset( $settings['cards'] ) && is_array( $settings['cards'] ) ? $settings['cards'] : array();
	foreach ( baydemir_contact_hero_default_cards() as $i => $default ) {
		$row     = isset( $source[ $i ] ) && is_array( $source[ $i ] ) ? $source[ $i ] : array();
		$cards[] = array(
			'title'    => (string) ( $row['title'] ?? $default['title'] ),
			'text'     => (string) ( $row['text'] ?? $default['text'] ),
			'image_id' => (int) ( $row['image_id'] ?? 0 ),
		);
	}
	$settings['cards'] = $cards;

	return $settings;
}

/**
 * İletişim hero main building image URL.
 */
function baydemir_contact_hero_main_url(): string {
	$settings = baydemir_contact_hero_settings();
	$id       = (int) $settings['main_image_id'];
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, 'baydemir-hero' );
		if ( ! $url ) {
			$url = wp_get_attachment_image_url( $id, 'large' );
		}
		if ( $url ) {
			return $url;
		}
	}

	$path = BAYDEMIR_DIR . '/assets/images/iletisim-hero.png';
	if ( file_exists( $path ) ) {
		return baydemir_asset( 'images/iletisim-hero.png' );
	}

	return baydemir_placeholder( 'commercial', 1200, 900 );
}

/**
 * İletişim hero card image URL.
 *
 * @param array{image_id?: int} $card Card row.
 * @param string                $fallback_key Placeholder key.
 */
function baydemir_contact_hero_card_url( array $card, string $fallback_key = 'building' ): string {
	$id = (int) ( $card['image_id'] ?? 0 );
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, 'baydemir-card' );
		if ( ! $url ) {
			$url = wp_get_attachment_image_url( $id, 'medium_large' );
		}
		if ( $url ) {
			return $url;
		}
	}

	return baydemir_placeholder( $fallback_key, 640, 420 );
}

/**
 * Empty testimonial item template.
 *
 * @return array<string, mixed>
 */
function baydemir_testimonial_blank_item(): array {
	return array(
		'name'         => '',
		'role'         => '',
		'rating'       => 5,
		'quote'        => '',
		'avatar_id'    => 0,
		'project_icon' => 'home',
		'project_type' => '',
		'project_name' => '',
		'image_id'     => 0,
	);
}

/**
 * Default homepage testimonials.
 *
 * @return array{title: string, title_accent: string, subtitle: string, items: array<int, array<string, mixed>>}
 */
function baydemir_testimonials_defaults(): array {
	return array(
		'title'        => 'Bizden Memnun Olan',
		'title_accent' => 'Müşterilerimiz',
		'subtitle'     => 'Güveniniz ve memnuniyetiniz, en büyük motivasyon kaynağımız.',
		'items'        => array(
			array(
				'name'         => 'Ahmet Yılmaz',
				'role'         => 'Ev Sahibi',
				'rating'       => 5,
				'quote'        => 'Anahtar teslim sürecimiz boyunca her aşamada yanımızdaydılar. Kalite ve iletişim konusunda çok memnun kaldık.',
				'avatar_id'    => 0,
				'project_icon' => 'home',
				'project_type' => 'Konut Projesi',
				'project_name' => 'Müstakil Ev',
				'image_id'     => 0,
			),
			array(
				'name'         => 'Zeynep Demir',
				'role'         => 'Villa Sahibi',
				'rating'       => 5,
				'quote'        => 'Villamızın tasarımından ince işçiliğine kadar her detay mükemmeldi. Zamanında teslim ettiler.',
				'avatar_id'    => 0,
				'project_icon' => 'house',
				'project_type' => 'Villa Projesi',
				'project_name' => 'Özel Villa',
				'image_id'     => 0,
			),
			array(
				'name'         => 'Mehmet Kaya',
				'role'         => 'İş Yeri Sahibi',
				'rating'       => 5,
				'quote'        => 'Ticari alanımızın yapım sürecinde profesyonellikleri ve çözüm odaklı yaklaşımları sayesinde işimiz hiç aksamadı.',
				'avatar_id'    => 0,
				'project_icon' => 'building',
				'project_type' => 'Ticari Proje',
				'project_name' => 'Ofis & İş Merkezi',
				'image_id'     => 0,
			),
			array(
				'name'         => 'Elif Arslan',
				'role'         => 'İş Yeri Sahibi',
				'rating'       => 5,
				'quote'        => 'Mağazamızın hem estetiği hem de kullanışlılığı harika oldu. Baydemir ekibine teşekkür ederiz.',
				'avatar_id'    => 0,
				'project_icon' => 'shop',
				'project_type' => 'Ticari Proje',
				'project_name' => 'Mağaza',
				'image_id'     => 0,
			),
		),
	);
}

/**
 * Normalize a raw testimonial row.
 *
 * @param array<string, mixed> $row Raw item.
 * @return array<string, mixed>
 */
function baydemir_normalize_testimonial_item( array $row ): array {
	$blank = baydemir_testimonial_blank_item();
	$icon  = sanitize_key( (string) ( $row['project_icon'] ?? $blank['project_icon'] ) );
	$allowed = array( 'home', 'house', 'building', 'shop', 'crane' );
	if ( ! in_array( $icon, $allowed, true ) ) {
		$icon = $blank['project_icon'];
	}

	return array(
		'name'         => sanitize_text_field( (string) ( $row['name'] ?? '' ) ),
		'role'         => sanitize_text_field( (string) ( $row['role'] ?? '' ) ),
		'rating'       => max( 1, min( 5, (int) ( $row['rating'] ?? 5 ) ) ),
		'quote'        => sanitize_textarea_field( (string) ( $row['quote'] ?? '' ) ),
		'avatar_id'    => absint( $row['avatar_id'] ?? 0 ),
		'project_icon' => $icon,
		'project_type' => sanitize_text_field( (string) ( $row['project_type'] ?? '' ) ),
		'project_name' => sanitize_text_field( (string) ( $row['project_name'] ?? '' ) ),
		'image_id'     => absint( $row['image_id'] ?? 0 ),
	);
}

/**
 * Testimonials settings (merged with defaults).
 *
 * @return array{title: string, title_accent: string, subtitle: string, items: array<int, array<string, mixed>>}
 */
function baydemir_testimonials_settings(): array {
	$defaults = baydemir_testimonials_defaults();
	$stored   = get_option( 'baydemir_testimonials', array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	$settings                 = array_merge( $defaults, $stored );
	$settings['title']        = (string) ( $settings['title'] ?? $defaults['title'] );
	$settings['title_accent'] = (string) ( $settings['title_accent'] ?? $defaults['title_accent'] );
	$settings['subtitle']     = (string) ( $settings['subtitle'] ?? $defaults['subtitle'] );

	$items  = array();
	$source = isset( $stored['items'] ) && is_array( $stored['items'] ) ? $stored['items'] : $defaults['items'];
	foreach ( $source as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$items[] = baydemir_normalize_testimonial_item( $row );
	}

	if ( ! $items ) {
		$items = $defaults['items'];
	}

	$settings['items'] = array_values( $items );

	return $settings;
}

/**
 * Visible testimonials for the front page (empty name = hidden).
 *
 * @return array<int, array<string, mixed>>
 */
function baydemir_testimonials_items(): array {
	$settings = baydemir_testimonials_settings();
	$visible  = array();
	foreach ( $settings['items'] as $item ) {
		if ( '' === trim( (string) $item['name'] ) ) {
			continue;
		}
		$visible[] = $item;
	}
	return $visible;
}
