<?php
/**
 * Project card partial.
 *
 * @package Baydemir
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$delay    = isset( $args['delay'] ) ? (int) $args['delay'] : 0;
$terms    = get_the_terms( get_the_ID(), 'project_category' );
$cat_slug = ( ! empty( $terms ) && ! is_wp_error( $terms ) ) ? $terms[0]->slug : '';
$location = (string) baydemir_project_meta( get_the_ID(), 'location', '' );
$thumb    = get_the_post_thumbnail_url( get_the_ID(), 'baydemir-card' );
if ( ! $thumb ) {
	$thumb = baydemir_placeholder( 'building', 640, 420 );
}
?>
<article class="bd-reveal bd-reveal-delay-<?php echo esc_attr( (string) ( ( $delay % 4 ) + 1 ) ); ?>" data-project-cat="<?php echo esc_attr( $cat_slug ); ?>" data-date="<?php echo esc_attr( get_the_date( 'U' ) ); ?>">
	<a class="bd-project-card" href="<?php the_permalink(); ?>">
		<div class="bd-project-card__media">
			<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
			<span class="bd-badge"><?php echo esc_html( baydemir_project_category_label( get_the_ID() ) ); ?></span>
		</div>
		<div class="bd-project-card__meta">
			<div>
				<h3><?php the_title(); ?></h3>
				<?php if ( $location ) : ?>
					<span><?php echo esc_html( $location ); ?></span>
				<?php endif; ?>
			</div>
			<span class="bd-project-card__arrow"><?php echo baydemir_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</div>
	</a>
</article>
