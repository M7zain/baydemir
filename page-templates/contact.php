<?php
/**
 * Template Name: İletişim
 * Template Post Type: page
 *
 * @package Baydemir
 */

get_header();

$hero       = baydemir_contact_hero_settings();
$main_url   = baydemir_contact_hero_main_url();
$fallbacks  = array( 'building', 'construction', 'commercial' );
?>

<section class="bd-contact-hero" data-bd-contact-hero>
	<div class="bd-container">
		<div class="bd-contact-hero__layout">
			<div class="bd-contact-hero__content">
				<div class="bd-breadcrumb bd-reveal">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'baydemir' ); ?></a>
					<span>›</span>
					<span><?php esc_html_e( 'İletişim', 'baydemir' ); ?></span>
				</div>
				<h1 class="bd-reveal"><?php esc_html_e( 'İletişim', 'baydemir' ); ?></h1>
				<p class="bd-lead bd-reveal bd-reveal-delay-1">
					<?php esc_html_e( 'Projeleriniz ve sorularınız için bizimle iletişime geçin. Size en kısa sürede dönüş yapalım.', 'baydemir' ); ?>
				</p>

				<div class="bd-contact-hero__steps bd-reveal bd-reveal-delay-2" data-bd-contact-steps>
					<?php foreach ( $hero['cards'] as $i => $card ) : ?>
						<button
							type="button"
							class="bd-contact-hero__step<?php echo 0 === $i ? ' is-active' : ''; ?>"
							data-bd-contact-step="<?php echo esc_attr( (string) $i ); ?>"
							data-text="<?php echo esc_attr( $card['text'] ); ?>"
							aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>"
						>
							<span class="bd-contact-hero__step-num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
							<span class="bd-contact-hero__step-title"><?php echo esc_html( $card['title'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>

				<p class="bd-contact-hero__detail bd-reveal bd-reveal-delay-2" data-bd-contact-detail>
					<?php echo esc_html( $hero['cards'][0]['text'] ); ?>
				</p>

				<div class="bd-btn-group bd-reveal bd-reveal-delay-3">
					<a class="bd-btn bd-btn--primary" href="#bd-contact-form">
						<?php esc_html_e( 'Mesaj Gönder', 'baydemir' ); ?>
						<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<a class="bd-btn bd-btn--ghost" href="<?php echo esc_url( baydemir_phone_href() ); ?>">
						<?php echo esc_html( baydemir_phone() ); ?>
					</a>
				</div>
			</div>

			<div class="bd-contact-hero__visual bd-reveal bd-reveal-delay-2" data-bd-contact-visual>
				<div class="bd-contact-hero__cards">
					<?php foreach ( $hero['cards'] as $i => $card ) : ?>
						<?php
						$card_url = baydemir_contact_hero_card_url( $card, $fallbacks[ $i ] ?? 'building' );
						?>
						<button
							type="button"
							class="bd-contact-hero__card<?php echo 0 === $i ? ' is-active' : ''; ?>"
							data-bd-contact-card="<?php echo esc_attr( (string) $i ); ?>"
							aria-label="<?php echo esc_attr( $card['title'] ); ?>"
							aria-pressed="<?php echo 0 === $i ? 'true' : 'false'; ?>"
						>
							<img src="<?php echo esc_url( $card_url ); ?>" alt="" loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>" decoding="async" />
							<span class="bd-contact-hero__card-label"><?php echo esc_html( $card['title'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="bd-contact-hero__main">
					<img
						src="<?php echo esc_url( $main_url ); ?>"
						alt=""
						width="1200"
						height="900"
						decoding="async"
						fetchpriority="high"
					/>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="bd-section" style="padding-top:1rem;">
	<div class="bd-container">
		<div class="bd-contact-grid">
			<div class="bd-panel bd-reveal">
				<h2><?php esc_html_e( 'İletişim Bilgileri', 'baydemir' ); ?></h2>
				<div class="bd-info-list">
					<div class="bd-info-item">
						<span class="bd-icon--box"><?php echo baydemir_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div>
							<strong><?php esc_html_e( 'Adres', 'baydemir' ); ?></strong>
							<p><?php echo esc_html( baydemir_address() ); ?></p>
						</div>
					</div>
					<div class="bd-info-item">
						<span class="bd-icon--box"><?php echo baydemir_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div>
							<strong><?php esc_html_e( 'Telefon', 'baydemir' ); ?></strong>
							<p><a href="<?php echo esc_url( baydemir_phone_href() ); ?>"><?php echo esc_html( baydemir_phone() ); ?></a></p>
						</div>
					</div>
					<div class="bd-info-item">
						<span class="bd-icon--box"><?php echo baydemir_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div>
							<strong><?php esc_html_e( 'E-posta', 'baydemir' ); ?></strong>
							<p><a href="mailto:<?php echo esc_attr( baydemir_email() ); ?>"><?php echo esc_html( baydemir_email() ); ?></a></p>
						</div>
					</div>
				</div>
				<div class="bd-map">
					<iframe src="<?php echo esc_url( baydemir_map_embed() ); ?>" loading="lazy" title="<?php esc_attr_e( 'Harita', 'baydemir' ); ?>" referrerpolicy="no-referrer-when-downgrade"></iframe>
				</div>
			</div>

			<div class="bd-panel bd-reveal bd-reveal-delay-2">
				<h2><?php esc_html_e( 'Bize Ulaşın', 'baydemir' ); ?></h2>
				<?php baydemir_render_contact_form(); ?>
			</div>
		</div>

		<div class="bd-contact-strip bd-reveal">
			<div class="bd-contact-strip__item">
				<span class="bd-icon--box"><?php echo baydemir_icon( 'headset' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<strong><?php esc_html_e( 'Hızlı destek için bizimle iletişime geçin', 'baydemir' ); ?></strong>
					<p><?php esc_html_e( 'Projeleriniz için teklif ve danışmanlık.', 'baydemir' ); ?></p>
				</div>
			</div>
			<div class="bd-contact-strip__item">
				<span class="bd-icon--box"><?php echo baydemir_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<strong><?php esc_html_e( 'Çalışma Saatleri', 'baydemir' ); ?></strong>
					<p><?php echo esc_html( baydemir_hours() ); ?></p>
				</div>
			</div>
			<a class="bd-btn bd-btn--ghost" href="<?php echo esc_url( baydemir_phone_href() ); ?>">
				<?php echo esc_html( (string) baydemir_mod( 'baydemir_cta_button', 'Teklif Al' ) ); ?>
				<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
	</div>
</section>

<?php
get_footer();
