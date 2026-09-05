<?php
/**
 * Front page template.
 *
 * @package Baydemir
 */

get_header();

$hero_id         = (int) baydemir_mod( 'baydemir_hero_image', 0 );
$hero_src        = $hero_id ? wp_get_attachment_image_url( $hero_id, 'baydemir-hero' ) : baydemir_placeholder( 'building', 1600, 1000 );
$video           = (string) baydemir_mod( 'baydemir_hero_video', '' );
$lights_banner   = baydemir_hero_uses_lights_banner();
$hero_classes    = 'bd-hero' . ( $lights_banner ? ' bd-hero--lights' : '' );
?>

<section class="<?php echo esc_attr( $hero_classes ); ?>"<?php echo $lights_banner ? ' data-bd-lights-banner' : ''; ?>>
	<div class="bd-hero__media<?php echo $lights_banner ? ' bd-hero__media--lights' : ''; ?>" aria-hidden="true">
		<?php if ( $lights_banner ) : ?>
			<div class="bd-hero__lights-stack">
				<img
					class="bd-hero__lights bd-hero__lights--off"
					src="<?php echo esc_url( baydemir_hero_lights_off_url() ); ?>"
					alt=""
					width="1672"
					height="941"
					decoding="async"
					fetchpriority="high"
				/>
				<img
					class="bd-hero__lights bd-hero__lights--on"
					src="<?php echo esc_url( baydemir_hero_lights_on_url() ); ?>"
					alt=""
					width="1672"
					height="941"
					decoding="async"
					fetchpriority="high"
				/>
			</div>
		<?php else : ?>
			<img src="<?php echo esc_url( $hero_src ); ?>" alt="" />
		<?php endif; ?>
	</div>
	<div class="bd-container">
		<div class="bd-hero__content bd-reveal">
			<span class="bd-eyebrow"><?php echo esc_html( (string) baydemir_mod( 'baydemir_hero_eyebrow', 'Güven • Kalite • Zamanında Teslim' ) ); ?></span>
			<h1><?php echo baydemir_highlight( (string) baydemir_mod( 'baydemir_hero_title', 'Güvenle İnşa, [[Geleceğe]] Bırak.' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			<p><?php echo esc_html( (string) baydemir_mod( 'baydemir_hero_text', 'Modern mimari, sağlam mühendislik ve zamanında teslim ilkeleriyle yaşanabilir yapılar inşa ediyoruz.' ) ); ?></p>
			<div class="bd-btn-group">
				<a class="bd-btn bd-btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ?: home_url( '/projeler/' ) ); ?>">
					<?php esc_html_e( 'Projelerimiz', 'baydemir' ); ?>
					<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<a class="bd-btn bd-btn--ghost" href="<?php echo esc_url( home_url( '/hakkimizda/' ) ); ?>">
					<?php esc_html_e( 'Bizi Tanıyın', 'baydemir' ); ?>
					<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
		</div>
	</div>
	<?php if ( $video ) : ?>
		<a class="bd-hero__video" href="<?php echo esc_url( $video ); ?>" target="_blank" rel="noopener noreferrer">
			<span class="bd-hero__play"><?php echo baydemir_icon( 'play' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<span><?php esc_html_e( 'Videoyu İzle', 'baydemir' ); ?></span>
		</a>
	<?php endif; ?>
</section>

<section class="bd-section">
	<div class="bd-container">
		<div class="bd-card-grid bd-card-grid--4">
			<?php
			$category_cards = baydemir_home_category_cards();
			foreach ( $category_cards as $i => $card ) :
				$term = $card['term'];
				$url  = get_term_link( $term );
				if ( is_wp_error( $url ) ) {
					$url = get_post_type_archive_link( 'project' );
				}
				$is_ongoing = ( 'devam-eden' === $term->slug );
				$classes    = 'bd-card bd-reveal bd-reveal-delay-' . ( ( $i % 4 ) + 1 );
				if ( $is_ongoing ) {
					$classes .= ' bd-card--blueprint';
				}
				?>
				<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $url ); ?>">
					<div class="bd-card__media">
						<img src="<?php echo esc_url( baydemir_home_category_image( $term ) ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy" />
					</div>
					<div class="bd-card__body">
						<span class="bd-icon--box"><?php echo baydemir_icon( $card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3><?php echo esc_html( $term->name ); ?></h3>
						<p><?php echo esc_html( $card['blurb'] ); ?></p>
					</div>
					<?php if ( $is_ongoing ) : ?>
						<span class="bd-card__arrow"><?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="bd-section" style="padding-top:0;">
	<div class="bd-container">
		<div class="bd-page-hero--split" style="min-height:auto;padding:0;gap:2.5rem;">
			<div class="bd-reveal">
				<span class="bd-eyebrow"><?php esc_html_e( 'Hakkımızda', 'baydemir' ); ?></span>
				<h2 style="font-size:clamp(1.8rem,3vw,2.6rem);text-transform:uppercase;margin:0 0 1rem;">
					<?php echo esc_html( (string) baydemir_mod( 'baydemir_about_title', 'Biz Kimiz?' ) ); ?>
				</h2>
				<p style="color:var(--bd-muted);margin:0 0 1.5rem;">
					<?php echo esc_html( (string) baydemir_mod( 'baydemir_about_text', 'Baydemir İnşaat, 2008’den bu yana madencilik nakliyesinde kazandığı tecrübeyi 2020’de inşaat sektörüne taşıyarak prestijli yaşam alanları üretmektedir.' ) ); ?>
				</p>
				<a class="bd-btn bd-btn--outline-accent" href="<?php echo esc_url( home_url( '/hakkimizda/' ) ); ?>">
					<?php esc_html_e( 'Daha Fazla', 'baydemir' ); ?>
					<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
			<div class="bd-reveal bd-reveal-delay-2" style="border-radius:var(--bd-radius);overflow:hidden;border:1px solid var(--bd-border);aspect-ratio:16/11;">
				<img src="<?php echo esc_url( baydemir_home_about_image_url() ); ?>" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;" />
			</div>
		</div>
	</div>
</section>

<?php
$home_about      = baydemir_about_settings();
$home_story_title   = trim( (string) $home_about['story_title'] );
$home_story_content = trim( (string) $home_about['story_content'] );
if ( $home_story_title || $home_story_content ) :
	?>
<section class="bd-section bd-about-story">
	<div class="bd-container">
		<div class="bd-about-story__grid">
			<div class="bd-about-story__media bd-reveal">
				<img src="<?php echo esc_url( baydemir_about_story_image_url() ); ?>" alt="" loading="lazy" />
			</div>
			<div class="bd-about-story__copy bd-reveal bd-reveal-delay-2">
				<?php if ( $home_story_title ) : ?>
					<h2 class="bd-section-title" style="margin-bottom:1rem;"><?php echo esc_html( $home_story_title ); ?></h2>
				<?php endif; ?>
				<?php if ( $home_story_content ) : ?>
					<div class="bd-prose">
						<?php echo wp_kses_post( wpautop( $home_story_content ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="bd-section" style="padding-top:0;">
	<div class="bd-container">
		<div style="display:flex;justify-content:space-between;align-items:end;gap:1rem;margin-bottom:1.75rem;flex-wrap:wrap;" class="bd-reveal">
			<h2 class="bd-section-title" style="margin:0;"><?php esc_html_e( 'Öne Çıkan Projeler', 'baydemir' ); ?></h2>
			<a class="bd-btn bd-btn--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>">
				<?php esc_html_e( 'Tüm Projeler', 'baydemir' ); ?>
				<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
		<div class="bd-project-grid">
			<?php
			$featured = new WP_Query(
				array(
					'post_type'      => 'project',
					'posts_per_page' => 4,
					'meta_key'       => '_baydemir_featured',
					'meta_value'     => '1',
					'orderby'        => array(
						'menu_order' => 'ASC',
						'date'       => 'DESC',
					),
				)
			);
			if ( ! $featured->have_posts() ) {
				$featured = new WP_Query(
					array(
						'post_type'      => 'project',
						'posts_per_page' => 4,
						'orderby'        => array(
							'menu_order' => 'ASC',
							'date'       => 'DESC',
						),
					)
				);
			}
			$i = 0;
			while ( $featured->have_posts() ) :
				$featured->the_post();
				get_template_part( 'template-parts/content', 'project-card', array( 'delay' => $i++ ) );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>

<section class="bd-section" style="padding-top:0;">
	<div class="bd-container">
		<div style="display:flex;justify-content:space-between;align-items:end;gap:1rem;margin-bottom:1.75rem;flex-wrap:wrap;" class="bd-reveal">
			<h2 class="bd-section-title" style="margin:0;"><?php esc_html_e( 'Hizmetlerimiz', 'baydemir' ); ?></h2>
			<a class="bd-btn bd-btn--ghost" href="<?php echo esc_url( home_url( '/hizmetler/' ) ); ?>">
				<?php esc_html_e( 'Tüm Hizmetler', 'baydemir' ); ?>
				<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>
		<div class="bd-card-grid bd-card-grid--6">
			<?php
			$home_services = new WP_Query(
				array(
					'post_type'      => 'service',
					'posts_per_page' => 6,
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
				)
			);

			$service_fallback = array(
				array( 'Konut Projeleri', 'building', 'Modern yaşam alanları.', 'building' ),
				array( 'Villa Projeleri', 'home', 'Özgün tasarım, konforlu yaşam.', 'villa' ),
				array( 'Ticari Yapılar', 'shop', 'Fonksiyonel ve estetik çözümler.', 'commercial' ),
				array( 'Anahtar Teslim İnşaat', 'helmet', 'Baştan sona eksiksiz teslim.', 'construction' ),
				array( 'Mimari Tasarım', 'pen', 'Estetik ve uygulanabilir mimari.', 'building2' ),
				array( 'Proje Yönetimi', 'users', 'Süreçlerin profesyonel yönetimi.', 'interior' ),
			);

			if ( $home_services->have_posts() ) :
				$si = 0;
				while ( $home_services->have_posts() ) :
					$home_services->the_post();
					$icon  = (string) get_post_meta( get_the_ID(), '_baydemir_icon', true ) ?: 'building';
					$thumb = get_the_post_thumbnail_url( get_the_ID(), 'baydemir-card' ) ?: baydemir_placeholder( 'building', 640, 420 );
					?>
					<article class="bd-card bd-reveal bd-reveal-delay-<?php echo esc_attr( (string) ( ( $si % 4 ) + 1 ) ); ?>">
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
					$si++;
				endwhile;
				wp_reset_postdata();
			else :
				foreach ( $service_fallback as $si => $s ) :
					?>
					<article class="bd-card bd-reveal bd-reveal-delay-<?php echo esc_attr( (string) ( ( $si % 4 ) + 1 ) ); ?>">
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

<?php
$tm_settings = baydemir_testimonials_settings();
$tm_items    = baydemir_testimonials_items();
if ( $tm_items ) :
	$placeholders = array( 'villa', 'interior', 'commercial', 'building' );
	?>
<section class="bd-section bd-testimonials">
	<div class="bd-container">
		<header class="bd-testimonials__head bd-reveal">
			<h2 class="bd-testimonials__title">
				<?php echo esc_html( $tm_settings['title'] ); ?>
				<?php if ( $tm_settings['title_accent'] ) : ?>
					<span><?php echo esc_html( $tm_settings['title_accent'] ); ?></span>
				<?php endif; ?>
			</h2>
			<?php if ( $tm_settings['subtitle'] ) : ?>
				<p class="bd-testimonials__subtitle"><?php echo esc_html( $tm_settings['subtitle'] ); ?></p>
			<?php endif; ?>
		</header>

		<div class="bd-testimonials__grid">
			<?php foreach ( $tm_items as $i => $item ) : ?>
				<?php
				$avatar = $item['avatar_id'] ? wp_get_attachment_image_url( (int) $item['avatar_id'], 'thumbnail' ) : '';
				$photo  = $item['image_id'] ? wp_get_attachment_image_url( (int) $item['image_id'], 'large' ) : '';
				if ( ! $photo ) {
					$photo = baydemir_placeholder( $placeholders[ $i % count( $placeholders ) ], 800, 600 );
				}
				$rating = (int) $item['rating'];
				?>
				<article class="bd-testimonial bd-reveal bd-reveal-delay-<?php echo esc_attr( (string) ( ( $i % 4 ) + 1 ) ); ?>">
					<div class="bd-testimonial__top">
						<div class="bd-testimonial__person">
							<span class="bd-testimonial__avatar">
								<?php if ( $avatar ) : ?>
									<img src="<?php echo esc_url( $avatar ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" loading="lazy" width="56" height="56" />
								<?php else : ?>
									<span class="bd-testimonial__avatar-fallback" aria-hidden="true"><?php echo esc_html( function_exists( 'mb_substr' ) ? mb_substr( $item['name'], 0, 1 ) : substr( $item['name'], 0, 1 ) ); ?></span>
								<?php endif; ?>
							</span>
							<div class="bd-testimonial__meta">
								<strong class="bd-testimonial__name"><?php echo esc_html( $item['name'] ); ?></strong>
								<?php if ( $item['role'] ) : ?>
									<span class="bd-testimonial__role"><?php echo esc_html( $item['role'] ); ?></span>
								<?php endif; ?>
								<span class="bd-testimonial__stars" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: star rating */ __( '%d üzerinden 5', 'baydemir' ), $rating ) ); ?>">
									<?php for ( $s = 1; $s <= 5; $s++ ) : ?>
										<span class="bd-testimonial__star<?php echo $s <= $rating ? ' is-on' : ''; ?>"><?php echo baydemir_icon( 'star-fill' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									<?php endfor; ?>
								</span>
							</div>
						</div>

						<div class="bd-testimonial__quote">
							<span class="bd-testimonial__quote-mark" aria-hidden="true"><?php echo baydemir_icon( 'quote' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<p><?php echo esc_html( $item['quote'] ); ?></p>
						</div>

						<div class="bd-testimonial__project">
							<span class="bd-testimonial__project-icon"><?php echo baydemir_icon( $item['project_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="bd-testimonial__project-text">
								<?php if ( $item['project_type'] ) : ?>
									<em><?php echo esc_html( $item['project_type'] ); ?></em>
								<?php endif; ?>
								<?php if ( $item['project_name'] ) : ?>
									<strong><?php echo esc_html( $item['project_name'] ); ?></strong>
								<?php endif; ?>
							</span>
						</div>
					</div>

					<div class="bd-testimonial__photo">
						<img src="<?php echo esc_url( $photo ); ?>" alt="" loading="lazy" />
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();
