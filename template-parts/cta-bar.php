<?php
/**
 * CTA bar — stats cards + teklif banner.
 *
 * @package Baydemir
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_page_template( 'page-templates/contact.php' ) || is_page( 'iletisim' ) ) {
	return;
}

$cta_label = (string) baydemir_mod( 'baydemir_cta_button', 'Teklif Al' );
$cta_url   = baydemir_cta_url();

$stats = array(
	array(
		'value' => '15+',
		'title' => __( 'Yıllık Tecrübe', 'baydemir' ),
		'text'  => __( 'Sektörde edindiğimiz bilgi ve birikimi her projeye yansıtıyoruz.', 'baydemir' ),
		'image' => 'images/deneyim.png',
	),
	array(
		'value' => '20+',
		'title' => __( 'Tamamlanan Proje', 'baydemir' ),
		'text'  => __( 'Konut, villa ve ticari yapılarda başarıyla teslim edilen projeler.', 'baydemir' ),
		'image' => 'images/tamamlanan-projeler.png',
	),
	array(
		'value' => '%100',
		'title' => __( 'Müşteri Memnuniyeti', 'baydemir' ),
		'text'  => __( 'En büyük referansımız, bizi tavsiye eden müşterilerimizdir.', 'baydemir' ),
		'icon'  => 'users',
	),
	array(
		'value' => '∞',
		'title' => __( 'Satış Sonrası Destek', 'baydemir' ),
		'text'  => __( 'Teslim sonrasında da ihtiyaç duyduğunuz her an yanınızdayız.', 'baydemir' ),
		'icon'  => 'headset',
	),
);

$trust = array(
	array(
		'icon'  => 'shield',
		'title' => __( 'Kaliteli İşçilik', 'baydemir' ),
		'text'  => __( 'En yüksek standartlarda üretiyoruz.', 'baydemir' ),
	),
	array(
		'icon'  => 'award',
		'title' => __( 'Söz Verdiğimiz Gibi', 'baydemir' ),
		'text'  => __( 'Planlı, şeffaf ve zamanında teslim ediyoruz.', 'baydemir' ),
	),
	array(
		'icon'  => 'users',
		'title' => __( 'Güvenilir İş Ortaklığı', 'baydemir' ),
		'text'  => __( 'Her adımda sizinle birlikte, uzun vadeli çözümler sunuyoruz.', 'baydemir' ),
	),
	array(
		'icon'  => 'headset',
		'title' => __( '7/24 Destek', 'baydemir' ),
		'text'  => __( 'Teslim sonrası da her zaman yanınızdayız.', 'baydemir' ),
	),
);
?>
<section class="bd-cta-bar">
	<div class="bd-container">
		<ul class="bd-cta-stats bd-reveal">
			<?php foreach ( $stats as $stat ) : ?>
				<li class="bd-cta-stat">
					<div class="bd-cta-stat__body">
						<span class="bd-cta-stat__value"><?php echo esc_html( $stat['value'] ); ?></span>
						<strong class="bd-cta-stat__title"><?php echo esc_html( $stat['title'] ); ?></strong>
						<p class="bd-cta-stat__text"><?php echo esc_html( $stat['text'] ); ?></p>
					</div>
					<span class="bd-cta-stat__visual" aria-hidden="true">
						<?php if ( ! empty( $stat['image'] ) ) : ?>
							<img
								src="<?php echo esc_url( baydemir_asset( $stat['image'] ) ); ?>"
								alt=""
								loading="lazy"
								decoding="async"
							/>
						<?php else : ?>
							<span class="bd-cta-stat__icon"><?php echo baydemir_icon( $stat['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php endif; ?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="bd-cta-banner bd-reveal">
			<div class="bd-cta-banner__main">
				<div class="bd-cta-banner__art" aria-hidden="true">
					<img
						src="<?php echo esc_url( baydemir_asset( 'images/teklif-al.png' ) ); ?>"
						alt=""
						loading="lazy"
						decoding="async"
					/>
				</div>
				<div class="bd-cta-banner__copy">
					<h2 class="bd-cta-banner__title">
						<?php
						echo wp_kses(
							__( 'Kaliteli yapılar, <span>güvenli yarınlar</span> için.', 'baydemir' ),
							array( 'span' => array() )
						);
						?>
					</h2>
					<p class="bd-cta-banner__lead">
						<?php
						echo wp_kses(
							__( 'Projeniz için bizimle iletişime geçin, size <strong>özel çözümler</strong> sunalım.', 'baydemir' ),
							array( 'strong' => array() )
						);
						?>
					</p>
				</div>
				<div class="bd-cta-banner__action">
					<span class="bd-cta-banner__mesh" aria-hidden="true"></span>
					<a class="bd-cta-banner__btn" href="<?php echo esc_url( $cta_url ); ?>">
						<?php echo esc_html( $cta_label ); ?>
						<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>
			</div>

			<ul class="bd-cta-banner__trust">
				<?php foreach ( $trust as $item ) : ?>
					<li>
						<span class="bd-cta-banner__trust-icon"><?php echo baydemir_icon( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="bd-cta-banner__trust-text">
							<strong><?php echo esc_html( $item['title'] ); ?></strong>
							<em><?php echo esc_html( $item['text'] ); ?></em>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
