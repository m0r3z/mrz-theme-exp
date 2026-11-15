<?php
/**
 * Boutique, catégories et fiches produit WooCommerce.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="mrz-container py-12">
	<?php woocommerce_content(); ?>
</div>
<?php
get_footer();
