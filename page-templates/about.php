<?php
/**
 * Template Name: Hakkımızda
 * Template Post Type: page
 *
 * @package Baydemir
 */

get_header();

$about    = baydemir_about_settings();
$features = array_values(
	array_filter(
		$about['features'],
		static fn( array $f ): bool => '' !== trim( $f['title'] )
	)
);
$top_features  = array_slice( $features, 0, 4 );
$wide_features = array_slice( $features, 4, 2 );
?>

<section class="bd-page-hero bd-page-hero--split">
	<div class="bd-container" style="display:contents;">
		<div class="bd-page-hero__intro">
			<div class="bd-breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'baydemir' ); ?></a>
				<span>›</span>
				<span><?php echo esc_html( $about['page_title'] ); ?></span>
			</div>
			<h1 class="bd-reveal"><?php echo esc_html( $about['page_title'] ); ?></h1>
			<?php if ( $about['eyebrow'] ) : ?>
				<p class="bd-eyebrow bd-reveal bd-reveal-delay-1"><?php echo esc_html( $about['eyebrow'] ); ?></p>
			<?php endif; ?>
			<div class="bd-prose bd-reveal bd-reveal-delay-2">
				<?php echo wp_kses_post( wpautop( $about['content'] ) ); ?>
			</div>
		</div>
		<div class="bd-page-hero__image bd-reveal bd-reveal-delay-3">
			<img src="<?php echo esc_url( baydemir_about_image_url() ); ?>" alt="" />
		</div>
	</div>
</section>

<?php if ( $top_features || $wide_features ) : ?>
<section class="bd-section">
	<div class="bd-container">
		<?php if ( $top_features ) : ?>
			<div class="bd-card-grid bd-card-grid--stats">
				<?php foreach ( $top_features as $i => $f ) : ?>
					<div class="bd-card bd-card--feature bd-reveal bd-reveal-delay-<?php echo esc_attr( (string) ( $i + 1 ) ); ?>">
						<span class="bd-icon--box"><?php echo baydemir_icon( $f['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3><?php echo esc_html( $f['title'] ); ?></h3>
						<p><?php echo esc_html( $f['text'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( $wide_features ) : ?>
			<div class="bd-card-grid bd-card-grid--stats-wide">
				<?php foreach ( $wide_features as $i => $f ) : ?>
					<div class="bd-card bd-card--feature bd-reveal<?php echo $i ? ' bd-reveal-delay-2' : ''; ?>">
						<span class="bd-icon--box"><?php echo baydemir_icon( $f['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<h3><?php echo esc_html( $f['title'] ); ?></h3>
						<p><?php echo esc_html( $f['text'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php
get_footer();
