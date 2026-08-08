<?php
/**
 * Main fallback template.
 *
 * @package Baydemir
 */

get_header();
?>

<section class="bd-page-hero">
	<div class="bd-container">
		<h1 class="bd-reveal"><?php esc_html_e( 'Blog', 'baydemir' ); ?></h1>
	</div>
</section>

<section class="bd-section" style="padding-top:0;">
	<div class="bd-container">
		<?php if ( have_posts() ) : ?>
			<div class="bd-card-grid bd-card-grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article class="bd-card bd-reveal">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="bd-card__media">
								<?php the_post_thumbnail( 'baydemir-card' ); ?>
							</div>
						<?php endif; ?>
						<div class="bd-card__body">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="bd-pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'İçerik bulunamadı.', 'baydemir' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
