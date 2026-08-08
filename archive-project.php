<?php
/**
 * Projects archive.
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
			<span><?php esc_html_e( 'Projeler', 'baydemir' ); ?></span>
		</div>
		<h1 class="bd-reveal"><?php esc_html_e( 'Projelerimiz', 'baydemir' ); ?></h1>
		<p class="bd-lead bd-reveal bd-reveal-delay-1">
			<?php esc_html_e( 'Modern mimari ve kaliteli işçilikle hayata geçirdiğimiz konut, villa ve ticari projelerimizi keşfedin.', 'baydemir' ); ?>
		</p>

		<div class="bd-filters bd-reveal bd-reveal-delay-2">
			<div class="bd-filter-list" role="tablist" aria-label="<?php esc_attr_e( 'Proje filtreleri', 'baydemir' ); ?>">
				<button type="button" class="bd-filter is-active" data-project-filter="all">
					<?php echo baydemir_icon( 'grid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php esc_html_e( 'Tüm Projeler', 'baydemir' ); ?>
				</button>
				<?php
				$terms = get_terms(
					array(
						'taxonomy'   => 'project_category',
						'hide_empty' => false,
					)
				);
				$icons = array(
					'konut'      => 'building',
					'villa'      => 'home',
					'ticari'     => 'shop',
					'devam-eden' => 'crane',
				);
				if ( ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$icon = $icons[ $term->slug ] ?? 'building';
						printf(
							'<button type="button" class="bd-filter" data-project-filter="%1$s">%2$s %3$s</button>',
							esc_attr( $term->slug ),
							baydemir_icon( $icon ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							esc_html( $term->name )
						);
					}
				}
				?>
			</div>
			<label class="bd-sort">
				<span><?php esc_html_e( 'Sıralama', 'baydemir' ); ?></span>
				<select data-project-sort>
					<option value="desc"><?php esc_html_e( 'Yeniden Eskiye', 'baydemir' ); ?></option>
					<option value="asc"><?php esc_html_e( 'Eskiden Yeniye', 'baydemir' ); ?></option>
				</select>
			</label>
		</div>
	</div>
</section>

<section class="bd-section" style="padding-top:0;">
	<div class="bd-container">
		<?php if ( have_posts() ) : ?>
			<div class="bd-project-grid">
				<?php
				$i = 0;
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'project-card', array( 'delay' => $i++ ) );
				endwhile;
				?>
			</div>
			<div class="bd-pagination">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => '‹',
						'next_text' => '›',
					)
				);
				?>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'Henüz proje eklenmemiş. Yönetim panelinden yeni proje ekleyebilirsiniz.', 'baydemir' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
