<?php
/**
 * Single project.
 *
 * @package Baydemir
 */

get_header();

while ( have_posts() ) :
	the_post();

	$location    = (string) baydemir_project_meta( get_the_ID(), 'location', '—' );
	$type        = (string) baydemir_project_meta( get_the_ID(), 'type', baydemir_project_category_label( get_the_ID() ) );
	$delivery    = (string) baydemir_project_meta( get_the_ID(), 'delivery', '—' );
	$units       = (string) baydemir_project_meta( get_the_ID(), 'units', '—' );
	$gallery     = (string) baydemir_project_meta( get_the_ID(), 'gallery', '' );
	$video_url   = (string) baydemir_project_meta( get_the_ID(), 'video_url', '' );
	$video_embed = baydemir_video_embed_url( $video_url );
	$thumb       = get_the_post_thumbnail_url( get_the_ID(), 'baydemir-hero' ) ?: baydemir_placeholder( 'building', 1400, 900 );

	$title_parts = explode( ' ', get_the_title(), 2 );
	$title_html  = esc_html( $title_parts[0] );
	if ( ! empty( $title_parts[1] ) ) {
		$title_html .= ' <span class="bd-accent">' . esc_html( $title_parts[1] ) . '</span>';
	}
	?>

	<section class="bd-single-hero">
		<div class="bd-container">
			<div class="bd-breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'baydemir' ); ?></a>
				<span>›</span>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>"><?php esc_html_e( 'Projeler', 'baydemir' ); ?></a>
				<span>›</span>
				<span><?php the_title(); ?></span>
			</div>

			<div class="bd-single-hero__grid">
				<div class="bd-single-hero__media bd-reveal">
					<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" />
				</div>
				<div class="bd-reveal bd-reveal-delay-2">
					<h1><?php echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
					<div class="bd-prose"><?php the_excerpt(); ?></div>

					<div class="bd-meta-row">
						<div class="bd-meta-item">
							<span class="bd-icon--box"><?php echo baydemir_icon( 'map-pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<div>
								<strong><?php esc_html_e( 'Lokasyon', 'baydemir' ); ?></strong>
								<span><?php echo esc_html( $location ); ?></span>
							</div>
						</div>
						<div class="bd-meta-item">
							<span class="bd-icon--box"><?php echo baydemir_icon( 'building' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<div>
								<strong><?php esc_html_e( 'Proje Tipi', 'baydemir' ); ?></strong>
								<span><?php echo esc_html( $type ); ?></span>
							</div>
						</div>
						<div class="bd-meta-item">
							<span class="bd-icon--box"><?php echo baydemir_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<div>
								<strong><?php esc_html_e( 'Teslim Tarihi', 'baydemir' ); ?></strong>
								<span><?php echo esc_html( $delivery ); ?></span>
							</div>
						</div>
						<div class="bd-meta-item">
							<span class="bd-icon--box"><?php echo baydemir_icon( 'home' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<div>
								<strong><?php esc_html_e( 'Daire Sayısı', 'baydemir' ); ?></strong>
								<span><?php echo esc_html( $units ); ?></span>
							</div>
						</div>
					</div>

					<?php if ( baydemir_project_show_cta( get_the_ID() ) ) : ?>
						<a class="bd-btn bd-btn--outline-accent" href="<?php echo esc_url( baydemir_project_cta_url( get_the_ID() ) ); ?>">
							<?php echo esc_html( (string) baydemir_mod( 'baydemir_cta_button', 'Teklif Al' ) ); ?>
							<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<?php if ( get_the_content() ) : ?>
		<section class="bd-section" style="padding-top:0;">
			<div class="bd-container bd-prose bd-reveal">
				<?php the_content(); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $video_embed ) : ?>
		<section class="bd-section bd-project-video" style="padding-top:0;">
			<div class="bd-container">
				<h2 class="bd-section-title bd-reveal"><?php esc_html_e( 'Proje Videosu', 'baydemir' ); ?></h2>
				<div class="bd-video-embed bd-reveal">
					<iframe
						src="<?php echo esc_url( $video_embed ); ?>"
						title="<?php echo esc_attr( sprintf( /* translators: %s: project title */ __( '%s videosu', 'baydemir' ), get_the_title() ) ); ?>"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
						referrerpolicy="strict-origin-when-cross-origin"
						allowfullscreen
						loading="lazy"
					></iframe>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="bd-section" style="padding-top:0;">
		<div class="bd-container">
			<h2 class="bd-section-title bd-reveal"><?php esc_html_e( 'Galeri', 'baydemir' ); ?></h2>
			<div class="bd-gallery-slider bd-reveal" data-bd-gallery-slider>
				<div class="bd-gallery-grid" data-bd-gallery>
					<?php
					$ids = array_filter( array_map( 'absint', explode( ',', $gallery ) ) );
					if ( empty( $ids ) ) {
						for ( $i = 0; $i < 4; $i++ ) {
							$placeholder = baydemir_placeholder( 'building' . ( $i % 2 ? '2' : '' ), 800, 600 );
							printf(
								'<button type="button" class="bd-gallery-item" data-bd-lightbox data-full="%1$s" aria-label="%2$s"><img src="%1$s" alt="" loading="lazy" /></button>',
								esc_url( $placeholder ),
								esc_attr__( 'Görseli büyüt', 'baydemir' )
							);
						}
					} else {
						foreach ( $ids as $id ) {
							$url  = wp_get_attachment_image_url( $id, 'baydemir-gallery' );
							$full = wp_get_attachment_image_url( $id, 'full' );
							$alt  = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
							if ( ! $url ) {
								continue;
							}
							printf(
								'<button type="button" class="bd-gallery-item" data-bd-lightbox data-full="%1$s" aria-label="%2$s"><img src="%3$s" alt="%4$s" loading="lazy" /></button>',
								esc_url( $full ?: $url ),
								esc_attr(
									$alt
										? sprintf(
											/* translators: %s: image alt text */
											__( '%s görselini büyüt', 'baydemir' ),
											$alt
										)
										: __( 'Görseli büyüt', 'baydemir' )
								),
								esc_url( $url ),
								esc_attr( $alt )
							);
						}
					}
					?>
				</div>
				<div class="bd-gallery-slider__nav" data-bd-gallery-nav hidden>
					<button type="button" class="bd-gallery-slider__btn bd-gallery-slider__btn--prev" data-bd-gallery-prev aria-label="<?php esc_attr_e( 'Önceki galeri sayfası', 'baydemir' ); ?>">
						<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
					<span class="bd-gallery-slider__status" data-bd-gallery-status aria-live="polite"></span>
					<button type="button" class="bd-gallery-slider__btn bd-gallery-slider__btn--next" data-bd-gallery-next aria-label="<?php esc_attr_e( 'Sonraki galeri sayfası', 'baydemir' ); ?>">
						<?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</button>
				</div>
			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();
