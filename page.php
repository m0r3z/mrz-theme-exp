<?php
/**
 * Page : constructeur ACF s'il est rempli, sinon contenu classique.
 * Une page n'affiche jamais son titre : son contenu porte son propre h1
 * (sauf panier, commande et compte de WooCommerce, faits d'un shortcode).
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();

	if ( mrz_theme_has_builder() ) {
		mrz_theme_render_builder();
		continue;
	}
	// Panier, commande, compte : contenu fait d'un shortcode, le h1 vient du titre de la page.
	$mrz_woocommerce_page = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'mrz-container py-16' ); ?>>
		<?php if ( $mrz_woocommerce_page ) : ?>
			<?php the_title( '<h1 class="mrz-page-title">', '</h1>' ); ?>
		<?php endif; ?>
		<div class="mrz-prose"><?php the_content(); ?></div>
	</article>
	<?php
}

get_footer();
