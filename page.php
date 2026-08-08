<?php
/**
 * Default page template.
 *
 * @package Baydemir
 */

get_header();
?>

<section class="bd-page-hero">
	<div class="bd-container">
		<div class="bd-breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'baydemir' ); ?></a>
			<span>›</span>
			<span><?php the_title(); ?></span>
		</div>
		<h1 class="bd-reveal"><?php the_title(); ?></h1>
	</div>
</section>

<section class="bd-section" style="padding-top:0;">
	<div class="bd-container bd-prose bd-reveal">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
</section>

<?php
get_footer();
