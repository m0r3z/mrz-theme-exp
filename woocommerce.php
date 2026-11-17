<?php
/**
 * Boutique, catégories et fiches produit WooCommerce.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Page Boutique : ses sections (constructeur) au-dessus des produits, h1 compris.
if ( mrz_theme_shop_has_sections() ) {
	mrz_theme_render_builder( wc_get_page_id( 'shop' ) );
}
?>
<div class="mrz-container py-12">
	<?php woocommerce_content(); ?>
</div>
<?php
get_footer();
