<?php
/**
 * Formulaire de recherche.
 *
 * @package mrz-theme-exp
 */

defined( 'ABSPATH' ) || exit;

$mrz_search_id = wp_unique_id( 'mrz-search-' );
?>
<form role="search" method="get" class="mrz-search flex gap-2" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $mrz_search_id ); ?>"><?php esc_html_e( 'Search', 'mrz-theme-exp' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $mrz_search_id ); ?>" class="mrz-input flex-1" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search…', 'mrz-theme-exp' ); ?>">
	<button type="submit" class="mrz-btn mrz-btn--primary"><?php esc_html_e( 'Search', 'mrz-theme-exp' ); ?></button>
</form>
