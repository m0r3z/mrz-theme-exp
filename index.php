<?php
/**
 * Liste d'articles : blog, archives, recherche, CPT.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="mrz-container py-16">
	<header class="mb-12">
		<h1 class="mrz-page-title"><?php echo esc_html( mrz_theme_listing_title() ); ?></h1>
		<?php if ( is_archive() && get_the_archive_description() ) : ?>
			<div class="mrz-prose mt-4 text-muted"><?php the_archive_description(); ?></div>
		<?php endif; ?>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( have_posts() ) {
				the_post();
				get_template_part( 'parts/content/content', get_post_type() );
			}
			?>
		</div>
		<?php
		the_posts_pagination(
			array(
				'class'     => 'mrz-pagination',
				'prev_text' => __( 'Previous', 'mrz-theme-exp' ),
				'next_text' => __( 'Next', 'mrz-theme-exp' ),
			)
		);
		?>
	<?php else : ?>
		<?php get_template_part( 'parts/content/none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
