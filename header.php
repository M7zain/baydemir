<?php
/**
 * Header template.
 *
 * @package Baydemir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bd-theme' ); ?>>
<?php wp_body_open(); ?>

<header class="bd-header" role="banner">
	<div class="bd-header__inner">
		<a class="bd-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php
				$logo_id = (int) get_theme_mod( 'custom_logo' );
				echo wp_get_attachment_image( $logo_id, 'full', false, array( 'alt' => baydemir_company_name() ) );
				?>
			<?php else : ?>
				<img src="<?php echo esc_url( baydemir_asset( 'images/logo.png' ) ); ?>" alt="<?php echo esc_attr( baydemir_company_name() ); ?>" />
			<?php endif; ?>
		</a>

		<button class="bd-nav-toggle" type="button" aria-expanded="false" aria-controls="bd-primary-nav" aria-label="<?php esc_attr_e( 'Menüyü aç', 'baydemir' ); ?>">
			<?php echo baydemir_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>

		<nav class="bd-nav" id="bd-primary-nav" aria-label="<?php esc_attr_e( 'Ana menü', 'baydemir' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => '',
						'depth'          => 2,
						'fallback_cb'    => false,
					)
				);
			} else {
				echo '<ul>';
				$fallback = array(
					home_url( '/' )           => __( 'Ana Sayfa', 'baydemir' ),
					home_url( '/projeler/' )  => __( 'Projeler', 'baydemir' ),
					home_url( '/hizmetler/' ) => __( 'Hizmetler', 'baydemir' ),
					home_url( '/hakkimizda/' )=> __( 'Hakkımızda', 'baydemir' ),
					home_url( '/kalite/' )    => __( 'Kalite', 'baydemir' ),
					home_url( '/iletisim/' )  => __( 'İletişim', 'baydemir' ),
				);
				foreach ( $fallback as $url => $label ) {
					printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
				}
				echo '</ul>';
			}
			?>
		</nav>

		<div class="bd-header__cta">
			<a class="bd-btn bd-btn--ghost" href="<?php echo esc_url( baydemir_cta_url() ); ?>">
				<span><?php echo esc_html( (string) baydemir_mod( 'baydemir_cta_button', 'Teklif Al' ) ); ?></span>
				<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
	</div>
</header>

<main id="content" class="bd-main">
