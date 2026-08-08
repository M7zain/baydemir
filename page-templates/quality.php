<?php
/**
 * Template Name: Kalite
 * Template Post Type: page
 *
 * @package Baydemir
 */

get_header();

$quality_image = baydemir_page_hero_image( get_the_ID(), 'building', 'images/kalite.jpg' );
$accent        = baydemir_accent_color();
$lord_colors   = sprintf( 'primary:%1$s,secondary:%1$s', $accent );
?>

<section class="bd-quality-hero">
	<div class="bd-quality-hero__blueprint" aria-hidden="true"></div>
	<div class="bd-container">
		<div class="bd-quality-hero__grid">
			<div class="bd-quality-hero__content bd-reveal">
				<span class="bd-eyebrow"><?php esc_html_e( 'Kalite Anlayışımız', 'baydemir' ); ?></span>
				<h1>
					<?php
					echo wp_kses(
						__( 'Kalite Bir Sonuç Değil, Her Gün Tekrar Ettiğimiz <span class="bd-accent">Bir Disiplindir.</span>', 'baydemir' ),
						array( 'span' => array( 'class' => true ) )
					);
					?>
				</h1>
				<p class="bd-lead">
					<?php esc_html_e( 'Baydemir İnşaat olarak her projeyi aynı titizlikle planlıyor, uyguluyor ve teslim ediyoruz.', 'baydemir' ); ?>
				</p>

				<div class="bd-quality-pillars">
					<?php
					$pillars = array(
						array( 'shield', __( 'Güvenli', 'baydemir' ), __( 'Güvenli yapılar inşa ederiz.', 'baydemir' ) ),
						array( 'link', __( 'Dayanıklı', 'baydemir' ), __( 'Uzun ömürlü ve sağlam yapılar.', 'baydemir' ) ),
						array( 'cube', __( 'Estetik', 'baydemir' ), __( 'Fonksiyonel ve estetik tasarımlar.', 'baydemir' ) ),
						array( 'leaf', __( 'Sürdürülebilir', 'baydemir' ), __( 'Çevreye duyarlı, sürdürülebilir çözümler.', 'baydemir' ) ),
					);
					foreach ( $pillars as $pillar ) :
						?>
						<div class="bd-quality-pillar">
							<span class="bd-quality-pillar__icon"><?php echo baydemir_icon( $pillar[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<p class="bd-quality-pillar__text">
								<strong><?php echo esc_html( $pillar[1] ); ?></strong>
								<?php echo esc_html( ' ' . $pillar[2] ); ?>
							</p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="bd-quality-hero__media bd-reveal bd-reveal-delay-2">
				<img src="<?php echo esc_url( $quality_image ); ?>" alt="<?php esc_attr_e( 'Kalite', 'baydemir' ); ?>" loading="eager" />
			</div>
		</div>
	</div>
</section>

<section class="bd-section bd-quality-assure">
	<div class="bd-container">
		<div class="bd-quality-assure__panel bd-reveal">
			<h2 class="bd-section-title bd-quality-assure__title"><?php esc_html_e( 'Kaliteyi Nasıl Sağlıyoruz?', 'baydemir' ); ?></h2>
			<ol class="bd-quality-steps">
				<?php
				$steps = array(
					array( '01', 'target', __( 'Doğru Proje', 'baydemir' ), __( 'İhtiyaçları doğru analiz eder, projeye en uygun çözümleri üretiriz.', 'baydemir' ) ),
					array( '02', 'layers', __( 'Doğru Malzeme', 'baydemir' ), __( 'Kaliteli ve sertifikalı malzemeleri titizlikle seçeriz.', 'baydemir' ) ),
					array( '03', 'helmet', __( 'Doğru Uygulama', 'baydemir' ), __( 'Projeye ve standartlara uygun şekilde uygulama yaparız.', 'baydemir' ) ),
					array( '04', 'clipboard', __( 'Sürekli Kontrol', 'baydemir' ), __( 'Her aşamada kontrol eder, kaliteyi sürekli denetleriz.', 'baydemir' ) ),
					array( '05', 'key', __( 'Eksiksiz Teslim', 'baydemir' ), __( 'Tüm kontrolleri tamamlayıp anahtar teslim yaparız.', 'baydemir' ) ),
				);
				foreach ( $steps as $step ) :
					?>
					<li class="bd-quality-steps__item">
						<span class="bd-quality-steps__icon"><?php echo baydemir_icon( $step[1] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="bd-quality-steps__num"><?php echo esc_html( $step[0] ); ?></span>
						<h3><?php echo esc_html( $step[2] ); ?></h3>
						<p><?php echo esc_html( $step[3] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>

<section class="bd-section bd-quality-foundations">
	<div class="bd-container">
		<h2 class="bd-section-title bd-quality-foundations__title bd-reveal"><?php esc_html_e( 'Kalitemizin Temelleri', 'baydemir' ); ?></h2>
		<ul class="bd-quality-foundations__grid bd-reveal bd-reveal-delay-1">
			<?php
			$foundations = array(
				array(
					'icon'  => 'materials',
					'title' => __( 'Kaliteli Malzeme', 'baydemir' ),
					'text'  => __( 'Projelerimizde yalnızca kaliteli, sertifikalı ve güvenilir malzemeler kullanırız.', 'baydemir' ),
				),
				array(
					'icon'  => 'team',
					'title' => __( 'Deneyimli Kadro', 'baydemir' ),
					'text'  => __( 'Alanında uzman, deneyimli ve sürekli gelişen ekibimizle çalışırız.', 'baydemir' ),
				),
				array(
					'icon'  => 'project',
					'title' => __( 'Projeye Uygunluk', 'baydemir' ),
					'text'  => __( 'Tüm uygulamalarımızı proje detaylarına ve mühendislik hesaplarına uygun gerçekleştiririz.', 'baydemir' ),
				),
				array(
					'icon'  => 'inspect',
					'title' => __( 'Sürekli Denetim', 'baydemir' ),
					'text'  => __( 'Bağımsız kontroller ve saha denetimleri ile kaliteyi her aşamada güvence altına alırız.', 'baydemir' ),
				),
				array(
					'icon'  => 'safety',
					'title' => __( 'İş Güvenliği', 'baydemir' ),
					'text'  => __( 'Çalışanlarımızın ve proje sahamızın güvenliği her zaman önceliğimizdir.', 'baydemir' ),
				),
				array(
					'icon'  => 'handover',
					'title' => __( 'Anahtar Teslim', 'baydemir' ),
					'text'  => __( 'Son kontrolleri tamamlanmış eksiksiz ve sorunsuz teslimat gerçekleştiririz.', 'baydemir' ),
				),
			);
			foreach ( $foundations as $item ) :
				$icon_src = baydemir_asset( 'lordicon/' . $item['icon'] . '.json' );
				?>
				<li class="bd-quality-foundations__item">
					<span class="bd-quality-foundations__icon" aria-hidden="true">
						<lord-icon
							src="<?php echo esc_url( $icon_src ); ?>"
							trigger="hover"
							colors="<?php echo esc_attr( $lord_colors ); ?>"
							loading="lazy"
							style="width:64px;height:64px"
						></lord-icon>
					</span>
					<h3><?php echo esc_html( $item['title'] ); ?></h3>
					<p><?php echo esc_html( $item['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="bd-section bd-quality-depth">
	<div class="bd-container">
		<div class="bd-quality-depth__grid">
			<article class="bd-quality-depth__panel bd-quality-depth__panel--compose bd-reveal">
				<header class="bd-quality-depth__head">
					<span class="bd-eyebrow"><?php esc_html_e( 'Bir Yapının Kalitesi', 'baydemir' ); ?></span>
					<h2><?php esc_html_e( 'Nelerden Oluşur?', 'baydemir' ); ?></h2>
				</header>

				<div class="bd-quality-depth__compose">
					<?php
					$compose_items = array(
						// icon, label, left%, top%, zoom scale
						array( 'foundation', __( 'Temel ve Zemin', 'baydemir' ), 48, 90, 1.85 ),
						array( 'pillar', __( 'Taşıyıcı Sistem', 'baydemir' ), 30, 68, 2.0 ),
						array( 'brick', __( 'Beton ve Donatı', 'baydemir' ), 55, 74, 1.95 ),
						array( 'layers', __( 'Yalıtım', 'baydemir' ), 72, 58, 2.05 ),
						array( 'wall', __( 'Duvar İşleri', 'baydemir' ), 44, 54, 2.1 ),
						array( 'bolt', __( 'Elektrik Tesisatı', 'baydemir' ), 58, 42, 2.15 ),
						array( 'pipes', __( 'Mekanik Tesisat', 'baydemir' ), 34, 40, 2.1 ),
						array( 'roof', __( 'Çatı ve Su Yalıtımı', 'baydemir' ), 50, 10, 2.2 ),
						array( 'facade', __( 'Cephe Uygulamaları', 'baydemir' ), 78, 36, 2.15 ),
						array( 'trowel', __( 'İnce İşçilik', 'baydemir' ), 56, 24, 2.25 ),
					);
					$construction_img = baydemir_asset( 'images/construction.png' );
					?>
					<div class="bd-quality-building" data-bd-quality-3d>
						<div class="bd-quality-building__stage">
							<div class="bd-quality-building__frame">
								<img
									class="bd-quality-building__img"
									src="<?php echo esc_url( $construction_img ); ?>"
									alt="<?php esc_attr_e( 'İnşaat halindeki yapı iskeleti', 'baydemir' ); ?>"
									width="800"
									height="800"
									decoding="async"
									loading="lazy"
								/>
								<?php
								foreach ( $compose_items as $idx => $item ) :
									$n = $idx + 1;
									?>
									<button
										type="button"
										class="bd-quality-building__pin"
										data-focus="<?php echo esc_attr( (string) $n ); ?>"
										data-ox="<?php echo esc_attr( $item[2] . '%' ); ?>"
										data-oy="<?php echo esc_attr( $item[3] . '%' ); ?>"
										data-scale="<?php echo esc_attr( (string) $item[4] ); ?>"
										style="left:<?php echo esc_attr( (string) $item[2] ); ?>%;top:<?php echo esc_attr( (string) $item[3] ); ?>%"
										aria-label="<?php echo esc_attr( sprintf( /* translators: 1: number, 2: label */ __( '%1$s. %2$s', 'baydemir' ), $n, $item[1] ) ); ?>"
									><?php echo esc_html( (string) $n ); ?></button>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="bd-quality-building__caption" data-bd-quality-caption hidden></div>
						<button type="button" class="bd-quality-building__reset" hidden><?php esc_html_e( 'Genel Görünüm', 'baydemir' ); ?></button>
					</div>

					<ol class="bd-quality-compose-list">
						<?php
						foreach ( $compose_items as $idx => $item ) :
							$n = $idx + 1;
							?>
							<li>
								<button
									type="button"
									class="bd-quality-compose-list__btn"
									data-focus="<?php echo esc_attr( (string) $n ); ?>"
									data-ox="<?php echo esc_attr( $item[2] . '%' ); ?>"
									data-oy="<?php echo esc_attr( $item[3] . '%' ); ?>"
									data-scale="<?php echo esc_attr( (string) $item[4] ); ?>"
								>
									<span class="bd-quality-compose-list__num"><?php echo esc_html( sprintf( '%02d', $n ) ); ?></span>
									<span class="bd-quality-compose-list__icon"><?php echo baydemir_icon( $item[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									<span class="bd-quality-compose-list__label"><?php echo esc_html( $item[1] ); ?></span>
								</button>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>

				<footer class="bd-quality-depth__banner">
					<span class="bd-quality-depth__banner-icon"><?php echo baydemir_icon( 'compass' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<p>
						<?php
						echo wp_kses(
							__( 'Sağlam temelden ince işçiliğe kadar her detay, <strong>yapılarımızın kalitesini</strong> belirler.', 'baydemir' ),
							array( 'strong' => array() )
						);
						?>
					</p>
				</footer>
			</article>

			<article class="bd-quality-depth__panel bd-quality-checks__panel bd-reveal bd-reveal-delay-1">
				<header class="bd-quality-checks__head">
					<div class="bd-quality-checks__intro">
						<span class="bd-eyebrow"><?php esc_html_e( 'Kusursuz Sonuçlar İçin', 'baydemir' ); ?></span>
						<h2 class="bd-quality-checks__title">
							<?php
							echo wp_kses(
								__( 'Kontrol <span>Listemiz</span>', 'baydemir' ),
								array( 'span' => array() )
							);
							?>
						</h2>
						<p class="bd-quality-checks__lead">
							<?php esc_html_e( 'Her aşamada detaylı kontrollerle, güvenli, sağlam ve kaliteli yapılar inşa ediyoruz.', 'baydemir' ); ?>
						</p>
					</div>
					<div class="bd-quality-checks__art" aria-hidden="true">
						<svg class="bd-quality-checks__house" viewBox="0 0 160 120" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M28 98V52l36-28 36 28v46" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
							<path d="M52 98V68h24v30" stroke="currentColor" stroke-width="1.6"/>
							<path d="M40 52h56M40 66h56M40 80h56" stroke="currentColor" stroke-width="1.2" opacity="0.55"/>
							<path d="M48 52v46M64 52v16M80 52v46" stroke="currentColor" stroke-width="1.2" opacity="0.45"/>
							<path d="M100 70h28v28H100z" stroke="currentColor" stroke-width="1.5"/>
							<path d="M108 78h12M108 86h12" stroke="currentColor" stroke-width="1.2" opacity="0.6"/>
							<path d="M20 98h120" stroke="currentColor" stroke-width="1.6"/>
							<path d="M112 42c8-10 22-8 26 4" stroke="currentColor" stroke-width="1.4" opacity="0.7"/>
						</svg>
						<span class="bd-quality-checks__shield"><?php echo baydemir_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</div>
				</header>

				<ul class="bd-quality-checks__grid">
					<?php
					$checks = array(
						array( 'trowel', __( 'Hafriyat Kontrolü', 'baydemir' ) ),
						array( 'rebar', __( 'Demir Donatı Kontrolü', 'baydemir' ) ),
						array( 'pillar', __( 'Kalıp Kontrolü', 'baydemir' ) ),
						array( 'mixer', __( 'Beton Dökümü', 'baydemir' ) ),
						array( 'target', __( 'Numune Takibi', 'baydemir' ) ),
						array( 'brick', __( 'Duvar İşleri Kontrolü', 'baydemir' ) ),
						array( 'bolt', __( 'Elektrik Tesisat Kontrolü', 'baydemir' ) ),
						array( 'gear', __( 'Mekanik Tesisat Kontrolü', 'baydemir' ) ),
						array( 'thermometer', __( 'Isı Yalıtımı Kontrolü', 'baydemir' ) ),
						array( 'droplet', __( 'Su Yalıtımı Kontrolü', 'baydemir' ) ),
						array( 'facade', __( 'Cephe Kontrolü', 'baydemir' ) ),
						array( 'paint', __( 'Boya Kontrolü', 'baydemir' ) ),
						array( 'broom', __( 'Son Temizlik Kontrolü', 'baydemir' ) ),
						array( 'package', __( 'Teslimat Kontrolü', 'baydemir' ) ),
					);
					foreach ( $checks as $check ) :
						?>
						<li class="bd-quality-checks__item">
							<span class="bd-quality-checks__icon"><?php echo baydemir_icon( $check[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="bd-quality-checks__label"><?php echo esc_html( $check[1] ); ?></span>
							<span class="bd-quality-checks__sep" aria-hidden="true"></span>
							<span class="bd-quality-checks__ok"><?php echo baydemir_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</li>
					<?php endforeach; ?>
				</ul>

				<footer class="bd-quality-checks__banner">
					<span class="bd-quality-checks__banner-icon"><?php echo baydemir_icon( 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<p>
						<?php
						echo wp_kses(
							__( 'Her detayın kontrol edildiği disiplinli bir süreç ile <strong>kaliteyi garanti</strong> ediyoruz.', 'baydemir' ),
							array( 'strong' => array() )
						);
						?>
					</p>
					<svg class="bd-quality-checks__sign" viewBox="0 0 120 40" fill="none" aria-hidden="true">
						<path d="M8 28c12-18 22-20 34-8 8 8 14 6 20-2 8-10 18-12 28-4 6 5 12 6 22 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
						<path d="M70 12c6 2 10 8 8 14" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" opacity="0.7"/>
					</svg>
				</footer>
			</article>
		</div>
	</div>
</section>

<?php
get_footer();
