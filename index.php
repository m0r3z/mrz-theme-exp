<?php
/**
 * Liste d'articles : blog, archives, recherche, CPT.
 * Page des articles avec des sections (constructeur) : elles passent au-dessus de la
 * liste et portent le h1 (inc/blog.php).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mrz_theme_sections_page = mrz_theme_blog_sections_page();
if ( $mrz_theme_sections_page ) {
	mrz_theme_render_builder( $mrz_theme_sections_page );
}
?>
<div class="<?php echo esc_attr( $mrz_theme_sections_page ? 'mrz-container mrz-blog-list--after-sections pb-16' : 'mrz-container py-16' ); ?>">
	<?php if ( ! $mrz_theme_sections_page ) : ?>
		<header class="mb-12">
			<h1 class="mrz-page-title"><?php echo esc_html( mrz_theme_listing_title() ); ?></h1>
			<?php if ( is_archive() && get_the_archive_description() ) : ?>
				<div class="mrz-prose mt-4 text-muted"><?php the_archive_description(); ?></div>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<?php mrz_theme_blog_categories(); ?>

	<?php if ( have_posts() ) : ?>
		<div class="<?php echo esc_attr( mrz_theme_blog_grid_class() ); ?>">
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
