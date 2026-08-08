<?php
/**
 * Template Name: Hizmetler
 * Template Post Type: page
 *
 * @package Baydemir
 */

get_header();

$hero_src = baydemir_page_hero_image( get_the_ID(), 'commercial' );
?>

<section class="bd-page-hero bd-page-hero--media">
	<div class="bd-hero__media" aria-hidden="true">
		<img src="<?php echo esc_url( $hero_src ); ?>" alt="" />
	</div>
	<div class="bd-container">
		<div class="bd-page-hero__content">
			<div class="bd-breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'baydemir' ); ?></a>
				<span>›</span>
				<span><?php esc_html_e( 'Hizmetler', 'baydemir' ); ?></span>
			</div>
			<h1 class="bd-reveal"><?php echo baydemir_highlight( '[[Hizmetler]]imiz' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			<p class="bd-lead bd-reveal bd-reveal-delay-1">
				<?php esc_html_e( 'Konuttan ticari yapılara, mimari tasarımdan anahtar teslim inşaata kadar uçtan uca çözümler sunuyoruz.', 'baydemir' ); ?>
			</p>
		</div>
	</div>
</section>

<section class="bd-section">
	<div class="bd-container">
		<div class="bd-card-grid bd-card-grid--6">
			<?php
			$services = new WP_Query(
				array(
					'post_type'      => 'service',
					'posts_per_page' => 12,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
				)
			);

			$fallback = array(
				array( 'Konut Projeleri', 'building', 'Modern yaşam alanları.', 'building' ),
				array( 'Villa Projeleri', 'home', 'Özgün tasarım, konforlu yaşam.', 'villa' ),
				array( 'Ticari Yapılar', 'shop', 'Fonksiyonel ve estetik çözümler.', 'commercial' ),
				array( 'Anahtar Teslim İnşaat', 'helmet', 'Baştan sona eksiksiz teslim.', 'construction' ),
				array( 'Mimari Tasarım', 'pen', 'Estetik ve uygulanabilir mimari.', 'building2' ),
				array( 'Proje Yönetimi', 'users', 'Süreçlerin profesyonel yönetimi.', 'interior' ),
			);

			if ( $services->have_posts() ) :
				$i = 0;
				while ( $services->have_posts() ) :
					$services->the_post();
					$icon  = (string) get_post_meta( get_the_ID(), '_baydemir_icon', true ) ?: 'building';
					$thumb = get_the_post_thumbnail_url( get_the_ID(), 'baydemir-card' ) ?: baydemir_placeholder( 'building', 640, 420 );
					?>
					<article class="bd-card bd-reveal bd-reveal-delay-<?php echo esc_attr( (string) ( ( $i % 4 ) + 1 ) ); ?>">
						<div class="bd-card__media">
							<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
						</div>
						<div class="bd-card__body">
							<span class="bd-icon--box"><?php echo baydemir_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3><?php the_title(); ?></h3>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						</div>
					</article>
					<?php
					$i++;
				endwhile;
				wp_reset_postdata();
			else :
				foreach ( $fallback as $i => $s ) :
					?>
					<article class="bd-card bd-reveal bd-reveal-delay-<?php echo esc_attr( (string) ( ( $i % 4 ) + 1 ) ); ?>">
						<div class="bd-card__media">
							<img src="<?php echo esc_url( baydemir_placeholder( $s[3], 640, 420 ) ); ?>" alt="<?php echo esc_attr( $s[0] ); ?>" loading="lazy" />
						</div>
						<div class="bd-card__body">
							<span class="bd-icon--box"><?php echo baydemir_icon( $s[1] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<h3><?php echo esc_html( $s[0] ); ?></h3>
							<p><?php echo esc_html( $s[2] ); ?></p>
						</div>
					</article>
					<?php
				endforeach;
			endif;
			?>
		</div>
	</div>
</section>

<section class="bd-section bd-process-section" style="padding-top:0;">
	<div class="bd-container">
		<h2 class="bd-section-title bd-process-section__title bd-reveal"><?php esc_html_e( 'Çalışma Sürecimiz', 'baydemir' ); ?></h2>
		<ol class="bd-process bd-reveal bd-reveal-delay-1">
			<?php
			$steps = array(
				array(
					'num'   => '01',
					'title' => __( 'Keşif', 'baydemir' ),
					'text'  => __( 'İhtiyaçları analiz eder, doğru çözümler üretiriz.', 'baydemir' ),
					'icon'  => 'map-pin',
				),
				array(
					'num'   => '02',
					'title' => __( 'Projelendirme', 'baydemir' ),
					'text'  => __( 'Mimari ve mühendislik projelerinizi hazırlarız.', 'baydemir' ),
					'icon'  => 'blueprint',
				),
				array(
					'num'   => '03',
					'title' => __( 'Ruhsat', 'baydemir' ),
					'text'  => __( 'Resmi süreçleri yönetir, gerekli izinleri alırız.', 'baydemir' ),
					'icon'  => 'file-check',
				),
				array(
					'num'   => '04',
					'title' => __( 'İnşaat', 'baydemir' ),
					'text'  => __( 'Kaliteli malzeme ve uzman ekibimizle inşa ederiz.', 'baydemir' ),
					'icon'  => 'crane',
				),
				array(
					'num'   => '05',
					'title' => __( 'Kalite Kontrol', 'baydemir' ),
					'text'  => __( 'Her aşamada kontrol sağlar, standartları garanti ederiz.', 'baydemir' ),
					'icon'  => 'shield',
				),
				array(
					'num'   => '06',
					'title' => __( 'Anahtar Teslim', 'baydemir' ),
					'text'  => __( 'Söz verdiğimiz zamanda anahtarınızı teslim ederiz.', 'baydemir' ),
					'icon'  => 'key',
				),
			);
			foreach ( $steps as $i => $step ) :
				?>
				<li class="bd-process__step<?php echo 0 === $i ? ' is-first' : ''; ?>">
					<span class="bd-process__num" aria-hidden="true"><?php echo esc_html( $step['num'] ); ?></span>
					<span class="bd-process__spine" aria-hidden="true"></span>
					<span class="bd-process__icon"><?php echo baydemir_icon( $step['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<h3><?php echo esc_html( $step['title'] ); ?></h3>
					<p><?php echo esc_html( $step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>

<?php
get_footer();
