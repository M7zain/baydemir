<?php
/**
 * 404 template.
 *
 * @package Baydemir
 */

get_header();
?>

<section class="bd-page-hero">
	<div class="bd-container">
		<h1 class="bd-reveal"><?php esc_html_e( 'Sayfa bulunamadı', 'baydemir' ); ?></h1>
		<p class="bd-lead bd-reveal bd-reveal-delay-1"><?php esc_html_e( 'Aradığınız sayfa taşınmış veya silinmiş olabilir.', 'baydemir' ); ?></p>
		<div class="bd-reveal bd-reveal-delay-2" style="margin-top:1.5rem;">
			<a class="bd-btn bd-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Ana Sayfaya Dön', 'baydemir' ); ?>
				<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
	</div>
</section>

<?php
get_footer();
