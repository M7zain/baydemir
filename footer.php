<?php
/**
 * Footer template.
 *
 * @package Baydemir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<?php get_template_part( 'template-parts/cta', 'bar' ); ?>

<footer class="bd-footer" role="contentinfo">
	<div class="bd-container">
		<div class="bd-footer__grid">
			<div class="bd-footer__brand">
				<?php echo baydemir_brand_mark( 'bd-brand-mark bd-brand-mark--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p><?php echo esc_html( (string) baydemir_mod( 'baydemir_cta_text', 'Geleceği birlikte inşa ediyoruz.' ) ); ?></p>
			</div>

			<nav class="bd-footer__nav" aria-label="<?php esc_attr_e( 'Footer menü', 'baydemir' ); ?>">
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'bd-footer__menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
				} else {
					echo '<ul class="bd-footer__menu">';
					$footer_fallback = array(
						home_url( '/' )            => __( 'Ana Sayfa', 'baydemir' ),
						home_url( '/projeler/' )   => __( 'Projeler', 'baydemir' ),
						home_url( '/hizmetler/' )  => __( 'Hizmetler', 'baydemir' ),
						home_url( '/hakkimizda/' ) => __( 'Hakkımızda', 'baydemir' ),
						home_url( '/kalite/' )     => __( 'Kalite', 'baydemir' ),
						home_url( '/iletisim/' )   => __( 'İletişim', 'baydemir' ),
					);
					foreach ( $footer_fallback as $url => $label ) {
						printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
					}
					echo '</ul>';
				}
				?>
			</nav>

			<div class="bd-footer__action">
				<a class="bd-btn bd-btn--primary" href="<?php echo esc_url( baydemir_cta_url() ); ?>">
					<?php esc_html_e( 'İletişime Geç', 'baydemir' ); ?>
					<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
		</div>

		<div class="bd-footer__bottom">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( baydemir_company_name() ); ?>. <?php esc_html_e( 'Tüm hakları saklıdır.', 'baydemir' ); ?></p>
			<p class="bd-credit"><?php esc_html_e( 'Created by Zain Kara', 'baydemir' ); ?></p>
			<div class="bd-social">
				<?php
				foreach ( array( 'instagram', 'facebook', 'linkedin', 'youtube' ) as $network ) {
					$url = (string) baydemir_mod( 'baydemir_social_' . $network, '' );
					if ( $url ) {
						printf(
							'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
							esc_url( $url ),
							esc_html( ucfirst( $network ) )
						);
					}
				}
				?>
			</div>
		</div>
	</div>
</footer>

<?php
$wa_url = baydemir_whatsapp_url();
if ( baydemir_whatsapp_button_enabled() && $wa_url ) :
	?>
	<a class="bd-whatsapp" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
		<?php echo baydemir_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
